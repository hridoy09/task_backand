<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KycSubmission;
use App\Services\DynamicFormHandler;
use App\Services\FileManager;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function kycForm()
    {        
        if(auth()->user()->kyc_verified == 1) {
            return to_route('user.dashboard')->withInfo(__('You are already KYC verified'));
        }
        
        $title = __('Submit your KYC data');

        return theme('user.kyc.form', compact('title'));
    }

    public function kycSubmit(Request $request)
    {
        if(auth()->user()->kyc_verified == 1) {
            return to_route('user.dashboard')->withInfo(__('You are already KYC verified'));
        }

        $formHandler = new DynamicFormHandler('kyc-form');
        $data = $formHandler->validate($request);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }

        $active = KycSubmission::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($active) {
            return back()->withErrors([
                'kyc' => __('You already have a :status KYC. Please wait for review.', [
                    'status' => $active->status,
                ]),
            ])->withInput();
        }

        try {
            $kyc = new KycSubmission();
            $kyc->submitted_data = $data;
            $kyc->user_id = auth()->id();
            $kyc->status = 'pending';
            $kyc->save();
        } catch(\Exception $e) {
            dd($e->getMessage());
        }

        $user = auth()->user();

        if ($user?->email) {
            sendTemplatedNotification(
                $user->email,
                'KYC_SUBMITTED',
                [
                    'user_name' => $user->name,
                    'kyc_status' => ucfirst($kyc->status),
                    'kyc_submitted_at' => $kyc->created_at?->toDayDateTimeString() ?? now()->toDayDateTimeString(),
                    'kyc_dashboard_url' => route('user.kyc.form'),
                ]
            );
        }

        $adminRecipients = admin_notification_recipients();
        if (!empty($adminRecipients)) {
            sendTemplatedNotification(
                $adminRecipients,
                'ADMIN_KYC_SUBMISSION',
                [
                    'user_name' => $user?->name ?? 'N/A',
                    'kyc_status' => ucfirst($kyc->status),
                    'kyc_submitted_at' => $kyc->created_at?->toDayDateTimeString() ?? now()->toDayDateTimeString(),
                    'kyc_review_url' => route('admin.user.kyc.data', $user?->id),
                ]
            );
        }

        return redirect()->route('user.kyc.form')->withSuccess(__('Your KYC has been submitted and is under review.'));
    }
}
