<?php

namespace App\Helpers;

use App\Services\AbilitiesGenerator as ServiceAbilitiesGenerator;

class AbilitiesGenerator
{
    public static function generate(): void
    {
        ServiceAbilitiesGenerator::generate();
    }
}
