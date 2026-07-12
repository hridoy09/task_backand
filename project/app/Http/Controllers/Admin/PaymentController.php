<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function list()
    {
        goIfUserCan('view-payments');

        $title = __('Payments');

        $payments = Payment::query()
            ->with(['user:id,name,username,email', 'paymentGateway:id,name,key,image,manual']) // eager load
            ->status(request('status'))
            ->searching([
                'transaction_no',
                'method',
                'currency',
                'user:name',
                'user:username',
                'user:email',
            ])
            ->latest()
            ->sorting()
            ->paginate();

        return view('admin.payment.list', compact('title', 'payments'));
    }

    public function pending()
    {
        goIfUserCan('view-payments');

        $title = __('Pending Payments');

        $payments = Payment::query()
            ->pending()
            ->with(['user:id,name,username,email', 'paymentGateway:id,name,key,image,manual']) // eager load
            ->status(request('status'))
            ->searching([
                'transaction_no',
                'method',
                'currency',
                'user:name',
                'user:username',
                'user:email',
            ])
            ->latest()
            ->sorting()
            ->paginate();

        return view('admin.payment.list', compact('title', 'payments'));
    }

    public function failed()
    {
        goIfUserCan('view-payments');

        $title = __('Pending Payments');

        $payments = Payment::query()
            ->failed()
            ->with(['user:id,name,username,email', 'paymentGateway:id,name,key,image,manual']) // eager load
            ->status(request('status'))
            ->searching([
                'transaction_no',
                'method',
                'currency',
                'user:name',
                'user:username',
                'user:email',
            ])
            ->latest()
            ->sorting()
            ->paginate();

        return view('admin.payment.list', compact('title', 'payments'));
    }

    public function successfull()
    {
        goIfUserCan('view-payments');

        $title = __('Pending Payments');

        $payments = Payment::query()
            ->successful()
            ->with(['user:id,name,username,email', 'paymentGateway:id,name,key,image,manual']) // eager load
            ->status(request('status'))
            ->searching([
                'transaction_no',
                'method',
                'currency',
                'user:name',
                'user:username',
                'user:email',
            ])
            ->latest()
            ->sorting()
            ->paginate();

        return view('admin.payment.list', compact('title', 'payments'));
    }


    public function deletePayment($id)
    {
        goIfUserCan('delete-payments');

        $payment = Payment::findOrFail($id);
        $payment->delete();
        return back()->withSuccess(__('Payment deleted sucessfully'));
    }
}
