<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KycFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kycForm = new Form();
        $kycForm->name = 'Kyc Form';
        $kycForm->slug = 'kyc-form';
        $kycForm->description = 'Know Your Client Information';
        $kycForm->form_data = "[{\"type\":\"text\",\"required\":true,\"label\":\"Full Name\",\"placeholder\":\"Please enter your ful lname\",\"className\":\"form-control\",\"name\":\"full_name\",\"access\":false,\"subtype\":\"text\",\"maxlength\":100},{\"type\":\"textarea\",\"required\":true,\"label\":\"Address\",\"description\":\"We need your address to verify your identity, one of our team member will go to manually verify you\",\"placeholder\":\"Please enter your full address\",\"className\":\"form-control\",\"name\":\"address\",\"access\":false,\"subtype\":\"textarea\",\"maxlength\":255}]";
        $kycForm->default = 1;
        $kycForm->save();
    }
}
