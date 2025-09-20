<?php

namespace Database\Seeders;

use App\Constants\Status;
use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'id' => 1,
                'name' => 'Super Admin',
                'email' => 'admin@site.com',
                'username' => 'SuperAdmin',
                'email_verified_at' => null,
                'status' => Status::ADMIN_ACTIVE,
                'image' => '6346ab2e6449f1665575726.png',
                'password' => Hash::make('D@24shashidu57611309'),
                'remember_token' => Str::random(60),
            ],
        ];

        foreach ($admins  as $admin) {
            $admin = Admin::create($admin);

            // Assign all permissions
            $allPermissions = Permission::all();
            $admin->syncPermissions($allPermissions);
            $admin->syncRoles(['super admin']);
        }
    }
}
