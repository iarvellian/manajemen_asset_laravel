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
                'nama_pegawai' => 'Pegawai_1',
                'nik' => 'NIK123456',
                'jabatan' => 'Jabatan_1',
                'username' => 'superadmin',
                'password' => Hash::make('Superadmin1234'),
            ],
            [
                'id' => 2,
                'id_role' => 2,
                'nama_pegawai' => 'Pegawai_2',
                'nik' => 'NIK234567',
                'jabatan' => 'Jabatan_2',
                'username' => 'admin',
                'password' => Hash::make('Admin1234'),
            ],
        ]);
    }
}
