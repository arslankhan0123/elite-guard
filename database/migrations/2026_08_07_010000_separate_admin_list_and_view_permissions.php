<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->whereNotNull('admin_permissions')->orderBy('id')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $permissions = json_decode($user->admin_permissions, true) ?: [];
                foreach ($permissions as &$actions) {
                    $actions['list'] = (bool) ($actions['view'] ?? false);
                    $actions['view'] = false;
                }
                unset($actions);

                DB::table('users')->where('id', $user->id)->update([
                    'admin_permissions' => json_encode($permissions),
                ]);
            }
        });
    }

    public function down(): void
    {
        DB::table('users')->whereNotNull('admin_permissions')->orderBy('id')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $permissions = json_decode($user->admin_permissions, true) ?: [];
                foreach ($permissions as &$actions) {
                    $actions['view'] = (bool) (($actions['view'] ?? false) || ($actions['list'] ?? false));
                    unset($actions['list']);
                }
                unset($actions);

                DB::table('users')->where('id', $user->id)->update([
                    'admin_permissions' => json_encode($permissions),
                ]);
            }
        });
    }
};
