<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLogin;
use App\Models\AdminNotification;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transactions() {
        goIfUserCan('view-reports.transactions');

        $title = __('Transactions');

        $transactions = Transaction::with(['user'])
            ->category(request('category'))   
            ->searching(['trx', 'user:email', 'user:username', 'details'])
            ->paginate();
        
        return view('admin.report.transaction.list', compact('title', 'transactions'));
    }
    
    public function adminLogins()
    {
        goIfUserCan('view-reports.admin-logins');

        $title = 'Admin Logins';

        $adminLogins = AdminLogin::searching(['ip','os','browser'])->latest()->paginate();
     

        return view('admin.report.admin_logins', compact('title', 'adminLogins'));
    }

    public function notifications()
    {
        goIfUserCan('view-reports.notifications');
        $title = 'Notifications';
        $notifications = AdminNotification::searching(['details'])->latest()->paginate();
        $unreadNotification= AdminNotification::unRead()->count();
        return view('admin.report.notifications', compact('title', 'notifications','unreadNotification'));
    }

    public function markAllAsRead()
    {
        goIfUserCan('save-reports.notifications');

        AdminNotification::unRead()->update(['is_read' => 1]);
        return back()->withSuccess(__('Notifications marked as read'));
    }

    public function deleteNotification($id)
    {
        goIfUserCan('delete-reports.notifications');

        $notification = AdminNotification::findOrFail($id);

        $notification->delete();

        return back()->withSuccess(__('Notification deleted successfully'));
    }

    public function notificationRead($id)
    {
        goIfUserCan('view-reports.notifications');

        $notification = AdminNotification::findOrFail($id);

        $notification->is_read = 1;
        $notification->save();

        if ($notification->link) {
            return redirect($notification->link);
        }

        return back();
    }
}
