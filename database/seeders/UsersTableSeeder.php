<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'id_role' => 1,
                'nama_pegawai' => 'Nama Pegawai',
                'jabatan' => 'Jabatan',
                'username' => 'test',
                'password' => Hash::make('Test1234'),
            ],
        ]);
    }
}
