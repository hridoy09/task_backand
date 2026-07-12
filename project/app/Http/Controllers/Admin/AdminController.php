<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BlogPost;
use App\Models\PageView;
use App\Models\Payment;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PDO;

class AdminController extends Controller
{

    public function dashboard()
    {
        goIfUserCan('view-dashboard');

        $title = 'Dashboard';

        $canViewUsers    = userCan('view-user');
        $canViewTickets  = userCan('view-support-tickets');
        $canViewPayments = userCan('view-reports.transactions');

        $widget = [
            'total_users'              => $canViewUsers ? User::count() : null,
            'total_payment'            => $canViewPayments ? Payment::sum('amount') : null,
            'new_users'                => $canViewUsers ? User::new()->count() : null,
            'incmoplete_profile_users' => $canViewUsers ? User::incompleteProfile()->count() : null,
            'email_unverified_users'   => $canViewUsers ? User::emailUnverified()->count() : null,
            'mobile_unverified_users'  => $canViewUsers ? User::phoneNumberUnverified()->count() : null,
            'total_blog_posts'         => BlogPost::count(),
            'published_blog_posts'     => BlogPost::published()->count(),
            'total_site_visits'        => PageView::sum('views'),
            'total_members'            => Admin::count(),
            'total_tickets'            => $canViewTickets ? SupportTicket::count() : null,
            'open_tickets'             => $canViewTickets ? SupportTicket::open()->count() : null,
            'closed_tickets'           => $canViewTickets ? SupportTicket::closed()->count() : null,
            'answered_tickets'         => $canViewTickets ? SupportTicket::answered()->count() : null,
        ];

        // ---- Transactions chart (last 30 days) ----
        $chartTransactions = null;

        if ($canViewPayments) {
            $days = 30;
            $from = now()->subDays($days - 1)->startOfDay();

            $rows = Transaction::query()
                ->selectRaw('DATE(created_at) as d')
                ->selectRaw("SUM(CASE WHEN trx_type = 'credit' THEN amount ELSE 0 END) AS credit")
                ->selectRaw("SUM(CASE WHEN trx_type = 'debit'  THEN amount ELSE 0 END) AS debit")
                ->where('created_at', '>=', $from)
                ->groupBy('d')
                ->orderBy('d')
                ->get()
                ->keyBy('d');

            $labels = [];
            $credit = [];
            $debit  = [];
            for ($i = 0; $i < $days; $i++) {
                $date = $from->clone()->addDays($i)->format('Y-m-d');
                $labels[] = $date;
                $credit[] = (float) ($rows[$date]->credit ?? 0);
                $debit[]  = (float) ($rows[$date]->debit  ?? 0);
            }

            $chartTransactions = [
                'labels' => $labels,
                'credit' => $credit,
                'debit'  => $debit,
            ];
        }

        return view('admin.dashboard', compact('title','widget','chartTransactions','canViewUsers','canViewTickets','canViewPayments'));
    }

    public function serverInformation()
    {
        goIfUserCan('view-settings.server-information');

        $title = __('Server Information');

        $phpInfo = [
            'Version'             => phpversion(),
            'Memory Limit'        => ini_get('memory_limit'),
            'Max Execution Time'  => ini_get('max_execution_time') . 's',
            'Upload Max Filesize' => ini_get('upload_max_filesize'),
            'Post Max Size'       => ini_get('post_max_size'),
            'Disabled Functions'  => ini_get('disable_functions') ?: 'None',
            'Timezone'            => date_default_timezone_get(),
            'GD Library'          => extension_loaded('gd') ? 'Enabled' : 'Disabled',
            'cURL'                => extension_loaded('curl') ? 'Enabled' : 'Disabled',
            'Mbstring'            => extension_loaded('mbstring') ? 'Enabled' : 'Disabled',
        ];

        $laravelInfo = [
            'Version'          => app()->version(),
            'Environment'      => config('app.env'),
            'Debug Mode'       => config('app.debug') ? 'Enabled' : 'Disabled',
            'App URL'          => config('app.url'),
            'Timezone'         => config('app.timezone'),
            'Cache Driver'     => config('cache.default'),
            'Session Driver'   => config('session.driver'),
            'Queue Connection' => config('queue.default'),
            'Maintenance Mode' => app()->isDownForMaintenance() ? 'Enabled' : 'Disabled',
        ];

        $dbInfo = [];
        try {
            $pdo = DB::connection()->getPdo();
            $dbDriver = DB::connection()->getDriverName();
            $dbInfo = [
                'Driver'            => $dbDriver,
                'Version'           => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
                'Host'              => DB::connection()->getConfig('host'),
                'Port'              => DB::connection()->getConfig('port'),
                'Database Name'     => DB::connection()->getDatabaseName(),
                'Username'          => DB::connection()->getConfig('username'),
                'Connection Status' => 'Connected',
            ];
        } catch (\Exception $e) {
            $dbInfo['Error']  = 'Could not connect to database: ' . $e->getMessage();
            $dbInfo['Driver'] = config('database.default');
            $dbInfo['Host']   = config('database.connections.' . config('database.default') . '.host');
        }

        $serverEnvInfo = [
            'Server Software'   => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'Server OS'         => php_uname('s') . ' ' . php_uname('r'),
            'Server IP Address' => $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname()),
            'Server Hostname'   => $_SERVER['SERVER_NAME'] ?? gethostname(),
            'Document Root'     => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
            'PHP SAPI'          => php_sapi_name(),
            'OpenSSL Version'   => defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : (extension_loaded('openssl') ? 'Enabled (version not readable)' : 'Disabled'),
        ];

        if (function_exists('disk_free_space') && function_exists('disk_total_space')) {
            // $serverEnvInfo['Disk Free Space']  = bytesToHumanReadable(disk_free_space('/'));
            // $serverEnvInfo['Disk Total Space'] = bytesToHumanReadable(disk_total_space('/'));
        }


        $composerJsonPath = base_path('composer.json');
        $requiredPhpVersion = 'N/A';

        if (File::exists($composerJsonPath)) {
            $composerConfig = json_decode(File::get($composerJsonPath), true);
            $requiredPhpVersion = $composerConfig['require']['php'] ?? 'N/A';
        }

        $projectInfo = [
            'Application Name'                     => config('app.name'),
            'Project Root'                         => base_path(),
            'PHP Version Required (composer.json)' => $requiredPhpVersion,
            '.env File Exists'                     => File::exists(base_path('.env')) ? 'Yes' : 'No',
            'Storage Directory Writable'           => is_writable(storage_path()) ? 'Yes' : 'No',

            // 'Cache Directory Writable' => is_writable(bootstrap_path('cache')) ? 'Yes' : 'No',
            'Log File' => storage_path('logs/laravel.log') . (File::exists(storage_path('logs/laravel.log')) ? ' (Exists)' : ' (Not Found)'),
        ];


        $serverInformation = [
            'PHP Configuration'   => $phpInfo,
            'Laravel Application' => $laravelInfo,
            'Database'            => $dbInfo,
            'Server Environment'  => $serverEnvInfo,
        ];

        return view('admin.setting.server_information', compact('title', 'serverInformation'));
    }

    public function profile()
    {
        goIfUserCan('view-settings.profile');

        $title = 'Profile';

        $admin = admin();

        return view('admin.setting.profile', compact('title', 'admin'));
    }

    public function password()
    {
        goIfUserCan('view-settings.password');

        $title = 'Password';

        $admin = admin();

        return view('admin.setting.password', compact('title', 'admin'));
    }

    public function updatePassword(Request $request)
    {
        goIfUserCan('save-settings.password');

        $request->validate([
            'old_password' => 'required',
            'password'     => 'required|confirmed'
        ]);

        if (!Hash::check($request->old_password, admin()->password)) {
            return back()->withError(__('Old password does not match'));
        }

        $admin           = admin();
        $admin->password = Hash::make($request->password);
        $admin->save();

        if ($admin->email) {
            sendTemplatedNotification(
                $admin->email,
                'USER_PASSWORD_CHANGED',
                [
                    'user_name' => $admin->name,
                    'changed_at' => now()->toDayDateTimeString(),
                    'login_url' => route('admin.login'),
                    'request_ip' => $request->ip(),
                ]
            );
        }

        return back()->withSuccess(__('Password updated successfully'));
    }

    public function updateProfile(Request $request)
    {
        goIfUserCan('save-settings.profile');

        $request->validate([
            'name'  => 'required|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . auth('admin')->id(),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $admin = auth('admin')->user();
        $admin->fill([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->hasFile('image')) {
            $old = $admin->image;

            $admin->image = FileManager::uploadToAssets(
                $request->file('image'),
                filePath('adminProfile'),
                $old,
                handleResize('adminProfile')
            );
        }

        $changedFields = array_keys($admin->getDirty());

        $admin->save();

        if ($admin->email && !empty($changedFields)) {
            $labels = [
                'name' => __('Name'),
                'email' => __('Email'),
                'image' => __('Profile Image'),
            ];

            $updatedFields = collect($changedFields)
                ->map(fn ($field) => $labels[$field] ?? Str::title(str_replace('_', ' ', $field)))
                ->implode(', ');

            sendTemplatedNotification(
                $admin->email,
                'ADMIN_PROFILE_UPDATED',
                [
                    'admin_name' => $admin->name,
                    'updated_at' => now()->toDayDateTimeString(),
                    'profile_url' => route('admin.setting.profile'),
                    'updated_fields' => $updatedFields,
                ]
            );
        }

        return back()->withSuccess(__('Profile updated successfully'));
    }
}
