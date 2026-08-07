<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users=[
            [
                'name'=>'Edgar Samael',
                'last_name'=>'Quiliche Zambrano',
                'dni'=>'75794033',
                'username'=>'equiliche',
                'password'=>bcrypt('gatoNegro2026+'),
                'role'=>'admin',
                'status'=>'active',
                'must_change_password'=>false
            ],
            [
                'name'=>'Jorge',
                'last_name'=>'Zelada',
                'dni'=>'87654321',
                'username'=>'jzelada',
                'password'=>bcrypt('gatoNegro2026+'),
                'role'=>'visit',
                'status'=>'active',
                'must_change_password'=>false
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
