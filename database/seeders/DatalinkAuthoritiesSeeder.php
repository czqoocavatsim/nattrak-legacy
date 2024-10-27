<?php

namespace Database\Seeders;

use App\Enums\DatalinkAuthorities;
use App\Models\DatalinkAuthority;
use Illuminate\Database\Seeder;

class DatalinkAuthoritiesSeeder extends Seeder
{
    public function run(): void
    {
        DatalinkAuthority::updateOrCreate([
            'id' => 'CZQO',
            'name' => 'Gander',
            'prefix' => 'CZQO',
            'auto_acknowledge_participant' => true,
            'valid_rcl_target' => true,
            'system' => false,
        ]);

        DatalinkAuthority::updateOrCreate([
            'id' => 'EGGX',
            'name' => 'Shanwick',
            'prefix' => 'EGGX',
            'auto_acknowledge_participant' => true,
            'valid_rcl_target' => true,
            'system' => false,
        ]);

        DatalinkAuthority::updateOrCreate([
            'id' => 'BIRD',
            'name' => 'Reykjavik',
            'prefix' => 'BIRD',
            'auto_acknowledge_participant' => true,
            'valid_rcl_target' => true,
            'system' => false,
        ]);

        DatalinkAuthority::updateOrCreate([
            'id' => 'KZNY',
            'name' => 'New York',
            'prefix' => 'NY',
            'auto_acknowledge_participant' => true,
            'valid_rcl_target' => false,
            'system' => false,
        ]);

        DatalinkAuthority::updateOrCreate([
            'id' => 'LPPO',
            'name' => 'Santa Maria',
            'prefix' => 'LPPO',
            'auto_acknowledge_participant' => true,
            'valid_rcl_target' => false,
            'system' => false,
        ]);

        DatalinkAuthority::updateOrCreate([
            'id' => 'NAT',
            'name' => 'North Atlantic Bandbox',
            'prefix' => 'NAT',
            'auto_acknowledge_participant' => true,
            'valid_rcl_target' => false,
            'system' => false,
        ]);

        DatalinkAuthority::updateOrCreate([
            'id' => 'CZQXD',
            'name' => 'Gander Domestic',
            'prefix' => 'CZQX',
            'auto_acknowledge_participant' => false,
            'valid_rcl_target' => false,
            'system' => false,
        ]);

        DatalinkAuthority::updateOrCreate([
            'id' => 'CZQXM',
            'name' => 'Moncton Domestic',
            'prefix' => 'CZQM',
            'auto_acknowledge_participant' => false,
            'valid_rcl_target' => false,
            'system' => false,
        ]);

        DatalinkAuthority::updateOrCreate([
            'id' => 'EISN',
            'name' => 'Shannon Domestic',
            'prefix' => 'EISN',
            'auto_acknowledge_participant' => false,
            'valid_rcl_target' => false,
            'system' => false,
        ]);

        DatalinkAuthority::updateOrCreate([
            'id' => 'SYST',
            'name' => 'natTrak System',
            'prefix' => 'SYST',
            'auto_acknowledge_participant' => false,
            'valid_rcl_target' => false,
            'system' => true,
        ]);

        DatalinkAuthority::updateOrCreate([
            'id' => 'OCEN',
            'name' => 'Oceanic Controller',
            'prefix' => 'OCEN',
            'auto_acknowledge_participant' => true,
            'valid_rcl_target' => false,
            'system' => true,
        ]);
    }
}
