<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Antonio',
                'phone' => '076369175',
                'company_name' => 'Ceyca',
                'company_ruc' => '20311227913',
            ],
            [
                'name' => 'veshi',
                'phone' => '123456789',
                'company_name' => 'Apo Print',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Jaime',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'campo verde',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Omniciencias',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Jorge Zelada',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'METALMARC',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'VICENTE',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'LIMAY',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'MICKY',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'JULIA',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Yuli',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'ALGORITMO',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'Julia',
                'phone' => '123456789',
                'company_name' => 'CEYCA',
                'company_ruc' => '20311227913',
            ],
            [
                'name' => 'Juan Jauregui',
                'phone' => '967279331',
                'company_name' => 'PERRICHOLI',
                'email'=>'laperricholi.restaurante@hotmail.com',
                'address'=>'Jr.lloque yupanqui #260, Los Baños del Inca',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Samantha',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Nalda',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Julia Guerrero',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Ana cardenas',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Olimpo 69',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Ramon Castilla',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Andina',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'ISSUM',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'VIALUC',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Ana Cubas',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'Yuliana',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'TRIATHLON',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'INEI',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'CONSULTORA DIAZ',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'ALEX',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'DIAS',
                'company_ruc' => '12345678',
            ],
            [
                'name' => 'cliente',
                'phone' => '123456789',
                'company_name' => 'SANTA TERESA',
                'company_ruc' => '12345678',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
