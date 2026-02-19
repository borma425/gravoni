<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            ['name' => 'Cairo', 'shipping_fee' => 5],
            ['name' => 'Giza', 'shipping_fee' => 50],
            ['name' => 'Alexandria', 'shipping_fee' => 80],
            ['name' => 'Qalyubia', 'shipping_fee' => 60],
            ['name' => 'Dakahlia', 'shipping_fee' => 90],
            ['name' => 'Monufia', 'shipping_fee' => 85],
            ['name' => 'Gharbia', 'shipping_fee' => 85],
            ['name' => 'Kafr El Sheikh', 'shipping_fee' => 95],
            ['name' => 'Sharqia', 'shipping_fee' => 90],
        ];

        foreach ($governorates as $governorate) {
            Governorate::updateOrCreate(
                ['name' => $governorate['name']],
                ['shipping_fee' => $governorate['shipping_fee']]
            );
        }

        $this->command->info('تم إضافة المحافظات بنجاح!');
    }
}
