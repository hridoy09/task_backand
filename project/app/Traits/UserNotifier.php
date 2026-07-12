<?php 

namespace App\Traits;

trait UserNotifier {
    public function userGroups() {
        return [
            'all-users' => __('All Users'),
            'email-unverified-users' => "Email Unverified Users"
        ];
    }
}