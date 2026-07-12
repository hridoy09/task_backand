<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Services\AbilitiesGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        AbilitiesGenerator::generate();

        $role = Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['title' => 'Super Admin']
        );

        $role->abilities()->sync(Ability::pluck('id')->all());

        $admin               = Admin::firstOrNew(['email' => 'admin@example.com']);
        $admin->name         = 'Administrator';
        $admin->username     = 'admin';
        $admin->image        = 'assets/images/admin-images/68f3db3a4a0500.25358588.png';
        $admin->phone_number = $admin->phone_number ?? '0000000000';

        if (! $admin->exists) {
            $admin->password = Hash::make('admin');
        }

        $admin->save();

        $admin->roles()->sync([$role->id]);
    }
}
