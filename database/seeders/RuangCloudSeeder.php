<?php

namespace Database\Seeders;

use App\Models\StorageNode;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RuangCloudSeeder extends Seeder
{
    public function run(): void
    {
        $gb = 1024 ** 3;

        $users = [
            ['Administrator',  'admin@ruangcloud.id',  'admin', 1000, true],
            ['Naufal Azmi',    'naufal@ruangcloud.id', 'user',  100,  true],
            ['Sona Mardiana',  'sona@ruangcloud.id',   'user',  100,  true],
            ['Anggraeni Ghea', 'ghea@ruangcloud.id',   'user',  100,  true],
            ['Muhamad Aziz',   'aziz@ruangcloud.id',   'user',  100,  false],
        ];

        foreach ($users as [$name, $email, $role, $quotaGb, $active]) {
            $user = User::firstOrNew(['email' => $email]);
            $user->forceFill([
                'name'        => $name,
                'password'    => Hash::make('ruangcloud2026'),
                'role'        => $role,
                'quota_bytes' => $quotaGb * $gb,
                'is_active'   => $active,
            ])->save();
        }

        // 3 storage node. Di pengembangan = 3 folder lokal; di server nyata arahkan ke mount/server berbeda.
        foreach ([['jakarta', 'Jakarta'], ['bandung', 'Bandung'], ['surabaya', 'Surabaya']] as [$code, $name]) {
            StorageNode::updateOrCreate(['code' => $code], [
                'name'           => $name,
                'path'           => storage_path('app/nodes/' . $code),
                'capacity_bytes' => 100 * $gb,
                'status'         => 'active',
            ]);
        }
    }
}
