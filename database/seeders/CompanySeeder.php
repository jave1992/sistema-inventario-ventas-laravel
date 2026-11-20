<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'razon_social' => 'Empresa 1',
                'ruc' => '20161515648',
                'direccion' => 'Direccion 1',
                'sol_user' => 'MODDATOS',
                'sol_pass' => 'MODDATOS',
                'client_id' => 'test-85e5b0ae-255c-4891-a595-0b98c65c9854',
                'client_secret' => 'test-Hty/M6QshYvPgItX2P0+Kw==',
                'production' => false,
                'user_id' => 1
            ],
            [
                'razon_social' => 'Empresa 2',
                'ruc' => '10469129379',
                'direccion' => 'Direccion 2',
                'sol_user' => 'MODDATOS',
                'sol_pass' => 'MODDATOS',
                'client_id' => 'test-85e5b0ae-255c-4891-a595-0b98c65c9854',
                'client_secret' => 'test-Hty/M6QshYvPgItX2P0+Kw==',
                'production' => false,
                'user_id' => 1
            ]
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
