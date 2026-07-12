<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\KycSubmission;
use App\Models\User;
use App\Traits\Controlling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use Controlling;

    protected $model = User::class;

    protected $listView = 'admin.users.list';
    protected ?string $viewPermission = 'view-user';
    protected ?string $createPermission = 'save-user';
    protected ?string $updatePermission = 'save-user';
    protected ?string $deletePermission = 'delete-user';

    protected $searching = ['username', 'email', 'first_name', 'last_name'];
    
    public function kycData($userId)
    {
        goIfUserCan('view-user');

        $title = __('Kyc Data');

        $user = User::findOrFail($userId);
        
        $kycData = KycSubmission::where('user_id', $user->id)
            ->latest('id')
            ->first(); 

        return view('admin.user.kyc_data', compact('title', 'user', 'kycData'));
    }

    public function kycReject(Request $request, $userId)
    {
        goIfUserCan('save-user');

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $kyc = KycSubmission::where('user_id', $userId)
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if (!$kyc) {
            return back()->withErrors(__('No pending KYC found for this user.'));
        }

        $user = User::findOrFail($userId);

        DB::transaction(function () use ($kyc, $request) {
            $kyc->status = 'rejected';
            $kyc->review_note = $request->reason;
            $kyc->save();
        });

        if ($user->email) {
            sendTemplatedNotification(
                $user->email,
                'KYC_REJECTED',
                [
                    'user_name' => $user->name,
                    'kyc_review_note' => $kyc->review_note,
                    'kyc_resubmit_url' => route('user.kyc.form'),
                ]
            );
        }

        return back()->withSuccess(__('KYC rejected successfully.'));
    }

    public function kycApprove(Request $request, $userId)
    {
        goIfUserCan('save-user');

        $kyc = KycSubmission::where('user_id', $userId)
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        if (!$kyc) {
            return back()->withErrors(__('No pending KYC found for this user.'));
        }

        $user = User::findOrFail($userId);

        DB::transaction(function () use ($kyc, $userId) {
            $kyc->status = 'approved';
            $kyc->save();

            $user = User::find($userId);
            $user->kyc_verified = 1;
            $user->save();
        });

        if ($user->email) {
            sendTemplatedNotification(
                $user->email,
                'KYC_APPROVED',
                [
                    'user_name' => $user->name,
                    'kyc_review_note' => __('Your verification has been approved.'),
                    'kyc_dashboard_url' => route('user.kyc.form'),
                ]
            );
        }

        return back()->withSuccess(__('KYC approved successfully.'));
    }


    public function delete($id)
    {
        goIfUserCan('delete-user');

        $user = User::findOrFail($id);

        $user->delete();

        return back()->withSuccess(__('User deleted successfully'));
    }

    public function loginUser($id)
    {
        goIfUserCan('save-user');

        Auth::loginUsingId($id);
        return to_route('user.dashboard');
    }

    protected function listQuery($query)
    {
        return $query->withExists(['kycSubmissions as has_pending_kyc' => function ($q) {
            $q->where('status', 'pending');
        }]);
    }
    
    public function list()
    {
        goIfUserCan('view-user');

        return $this->data();
    }

    public function kycPending() {
        goIfUserCan('view-user');

        return $this->data('KYC Pending', 'kycPending');
    }
    
    public function emailUnverified()
    {
        goIfUserCan('view-user');

        return $this->data('Email Unverified', 'emailUnverified');
    }

    public function incompleteProfile()
    {
        goIfUserCan('view-user');

        return $this->data('Incomplete Profile', 'incompleteProfile');
    }


    public function mobileUnverified()
    {
        goIfUserCan('view-user');

        return $this->data('Incomplete Profile', 'phoneNumberUnverified');
    }


    public function newUsers()
    {
        goIfUserCan('view-user', 'new');

        return $this->data('New Users', 'new');
    }

    public function new()
    {
        goIfUserCan('save-user');

        $title = 'Add New User';

        return view('admin.users.add', compact('title'));
    }


    public function edit($id)
    {
        goIfUserCan('save-user');

        $title = 'Edit User';

        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('title', 'user'));
    }

    public function details($id)
    {
        
        goIfUserCan('view-user');

        $title = 'User Details';

        $user = User::findOrFail($id);

        $widget = [
            'number_of_transactions' => $user->transactions()->count(),
            'total_payment' => $user->payments()->successful()->sum('amount')
        ];

        $notifications = AdminNotification::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.users.details', compact('title', 'user', 'widget', 'notifications'));
    }

    public function save(Request $request, $id = null)
    {
        goIfUserCan('save-user');

        $rules = [
            'username'     => [
                'required',
                Rule::unique('users')->ignore($id)
            ],
            'first_name'   => 'required',
            'last_name'    => 'required',
            'password'     => $id ? 'nullable' : 'required|confirmation',
            'email'        => 'required_without:phone_number',
            'phone_number' => 'required_without:email',
            'country_code' => 'required',
            'zipcode'      => 'nullable',
            'city'         => 'nullable',
            'pc'           => 'required|in:0,1',
            'kyc_verified' => 'required|in:0,1',
            'kyc_required' => 'required|in:0,1',
            'email_verified' => 'required|in:0,1',
        ];

        $request->validate($rules);

        $user               = $id ? User::findOrFail($id) : new User();
        $user->first_name   = $request->first_name;
        $user->last_name    = $request->last_name;
        $user->email        = $request->email;
        $user->username     = $request->username;
        $user->phone_number = $request->phone_number;
        $user->zipcode      = $request->zipcode;
        $user->country_code = $request->country_code;
        $user->city         = $request->city;
        $user->kyc_required = $request->kyc_required;
        $user->kyc_verified = $request->kyc_verified;
        $user->pc           = $request->pc;
        $user->email_verified_at = $request->email_verified ? now() : null;
        $user->save();

        return back()->withSuccess(__('User saved successfully'));
    }

    public function notify(Request $request, $id)
    {
        goIfUserCan('save-user');

        $data = $request->validate([
            'channel' => 'required|in:email,sms',
            'subject' => 'required_if:channel,email|nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $user = User::findOrFail($id);

        $recipient = $data['channel'] === 'sms' ? $user->phone_number : $user->email;

        if (!$recipient) {
            return back()->withErrors([
                'channel' => __('Selected channel is not available for this user.')
            ]);
        }

        $sent = sendSystemNotification(
            $recipient,
            $data['subject'] ?? __('Notification from :app', ['app' => config('app.name')]),
            $data['message'],
            $data['channel'],
            [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_username' => $user->username,
            ]
        );

        if (!$sent) {
            return back()->withErrors([
                'message' => __('Failed to send notification. Please check notification settings.')
            ]);
        }

        $log = new AdminNotification();
        $log->user_id = $user->id;
        $log->link = route('admin.user.details', $user->id);
        $log->is_read = 1;
        $log->details = __(':channel notification sent to :recipient', [
            'channel' => strtoupper($data['channel']),
            'recipient' => $data['channel'] === 'sms' ? $user->phone_number : $user->email,
        ]);
        $log->save();

        return back()->withSuccess(__('Notification sent successfully.'));
    }
}
