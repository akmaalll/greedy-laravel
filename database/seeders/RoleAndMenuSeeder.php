<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleAndMenuSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Client', 'slug' => 'client'],
            ['name' => 'Photographer', 'slug' => 'photographer'],
        ];

        $roleIds = [];
        foreach ($roles as $role) {
            $roleIds[$role['slug']] = DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            // Get the actual ID
            $roleIds[$role['slug']] = DB::table('roles')->where('slug', $role['slug'])->first()->id;
        }

        // 2. Menus
        $menus = [
            // Dashboard - Semua Role
            ['name' => 'Dashboard', 'slug' => 'dashboard', 'path' => '/', 'icon' => 'ri-home-smile-line', 'order_no' => 1],
            
            // User Management - Admin Only
            ['name' => 'User Management', 'slug' => 'user-management', 'path' => null, 'icon' => 'ri-user-settings-line', 'order_no' => 2],
            ['parent' => 'User Management', 'name' => 'Users', 'slug' => 'user.index', 'path' => '/user', 'icon' => 'ri-user-line', 'order_no' => 1],
            ['parent' => 'User Management', 'name' => 'Roles', 'slug' => 'role.index', 'path' => '/role', 'icon' => 'ri-shield-user-line', 'order_no' => 2],
            ['parent' => 'User Management', 'name' => 'Menus', 'slug' => 'menu.index', 'path' => '/menu', 'icon' => 'ri-menu-search-line', 'order_no' => 3],
            ['parent' => 'User Management', 'name' => 'Permissions', 'slug' => 'permission.index', 'path' => '/permission', 'icon' => 'ri-lock-password-line', 'order_no' => 4],
            
            // Master Data - Admin Only
            ['name' => 'Master Data', 'slug' => 'master-data', 'path' => null, 'icon' => 'ri-database-2-line', 'order_no' => 3],
            ['parent' => 'Master Data', 'name' => 'Kategori', 'slug' => 'kategori.index', 'path' => '/kategori', 'icon' => 'ri-folder-line', 'order_no' => 1],
            ['parent' => 'Master Data', 'name' => 'Layanan', 'slug' => 'layanan.index', 'path' => '/layanan', 'icon' => 'ri-service-line', 'order_no' => 2],
            ['parent' => 'Master Data', 'name' => 'Paket Layanan', 'slug' => 'paket-layanan.index', 'path' => '/paket-layanan', 'icon' => 'ri-price-tag-3-line', 'order_no' => 3],
            
            // Pesanan - Admin & Client
            ['name' => 'Pesanan', 'slug' => 'pesanan.index', 'path' => '/pesanan', 'icon' => 'ri-shopping-cart-line', 'order_no' => 4],
            
            // Jadwal & Penugasan - Admin & Photographer
            ['name' => 'Jadwal & Penugasan', 'slug' => 'jadwal-penugasan', 'path' => null, 'icon' => 'ri-calendar-check-line', 'order_no' => 5],
            ['parent' => 'Jadwal & Penugasan', 'name' => 'Penugasan Fotografer', 'slug' => 'penugasan-fotografer.index', 'path' => '/penugasan-fotografer', 'icon' => 'ri-user-star-line', 'order_no' => 2],
            
            // Ketersediaan - Photographer Only
            ['name' => 'Ketersediaan Saya', 'slug' => 'ketersediaan.index', 'path' => '/ketersediaan', 'icon' => 'ri-calendar-check-fill', 'order_no' => 6],
            
            // Pembayaran - Admin & Client
            ['name' => 'Pembayaran', 'slug' => 'pembayaran.index', 'path' => '/pembayaran', 'icon' => 'ri-money-dollar-circle-line', 'order_no' => 7],
            
            // Rating & Ulasan - Client & Admin
            ['name' => 'Rating & Ulasan', 'slug' => 'rating', 'path' => null, 'icon' => 'ri-star-line', 'order_no' => 8],
            ['parent' => 'Rating & Ulasan', 'name' => 'Rating Fotografer', 'slug' => 'rating-fotografer.index', 'path' => '/rating-fotografer', 'icon' => 'ri-user-star-line', 'order_no' => 1],
            ['parent' => 'Rating & Ulasan', 'name' => 'Rating Layanan', 'slug' => 'rating-layanan.index', 'path' => '/rating-layanan', 'icon' => 'ri-star-smile-line', 'order_no' => 2],
            
            // Laporan - Admin Only
            ['name' => 'Laporan', 'slug' => 'laporan', 'path' => null, 'icon' => 'ri-file-chart-line', 'order_no' => 9],
            ['parent' => 'Laporan', 'name' => 'Laporan Pesanan', 'slug' => 'laporan.pesanan', 'path' => '/laporan/pesanan', 'icon' => 'ri-file-list-3-line', 'order_no' => 1],
            ['parent' => 'Laporan', 'name' => 'Laporan Pendapatan', 'slug' => 'laporan.pendapatan', 'path' => '/laporan/pendapatan', 'icon' => 'ri-money-dollar-box-line', 'order_no' => 2],
            ['parent' => 'Laporan', 'name' => 'Laporan Fotografer', 'slug' => 'laporan.fotografer', 'path' => '/laporan/fotografer', 'icon' => 'ri-user-search-line', 'order_no' => 3],
            
            // Activity Log - Admin Only
            ['name' => 'Activity Log', 'slug' => 'activity-log.index', 'path' => '/activity-log', 'icon' => 'ri-history-line', 'order_no' => 10],
        ];

        $menuIdMap = [];
        foreach ($menus as $m) {
            $parentId = isset($m['parent']) ? ($menuIdMap[$m['parent']] ?? null) : null;
            
            DB::table('menus')->updateOrInsert(
                ['slug' => $m['slug']],
                [
                    'parent_id' => $parentId,
                    'name' => $m['name'],
                    'path' => $m['path'],
                    'icon' => $m['icon'],
                    'order_no' => $m['order_no'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            $dbMenu = DB::table('menus')->where('slug', $m['slug'])->first();
            $menuIdMap[$m['name']] = $dbMenu->id;

            // Assign to Super Admin by default (full access)
            DB::table('role_menu')->updateOrInsert(
                ['role_id' => $roleIds['super-admin'], 'menu_id' => $dbMenu->id],
                [
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Assign to Admin (full access to all menus)
            DB::table('role_menu')->updateOrInsert(
                ['role_id' => $roleIds['admin'], 'menu_id' => $dbMenu->id],
                [
                    'can_create' => true,
                    'can_read' => true,
                    'can_update' => true,
                    'can_delete' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Assign to Client (specific menus only)
            $clientMenus = [
                'dashboard',
                'pesanan.index',
                'pembayaran.index',
                'rating',
                'rating-fotografer.index',
                'rating-layanan.index',
            ];
            
            if (in_array($m['slug'], $clientMenus)) {
                DB::table('role_menu')->updateOrInsert(
                    ['role_id' => $roleIds['client'], 'menu_id' => $dbMenu->id],
                    [
                        'can_create' => in_array($m['slug'], ['pesanan.index', 'rating-fotografer.index', 'rating-layanan.index', 'pembayaran.index']),
                        'can_read' => true,
                        'can_update' => in_array($m['slug'], ['pesanan.index']),
                        'can_delete' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            // Assign to Photographer (specific menus only)
            $photographerMenus = [
                'dashboard',
                'jadwal-penugasan',
                'penugasan-fotografer.index',
                'ketersediaan.index',
            ];
            
            if (in_array($m['slug'], $photographerMenus)) {
                DB::table('role_menu')->updateOrInsert(
                    ['role_id' => $roleIds['photographer'], 'menu_id' => $dbMenu->id],
                    [
                        'can_create' => in_array($m['slug'], ['ketersediaan.index']),
                        'can_read' => true,
                        'can_update' => in_array($m['slug'], ['ketersediaan.index', 'penugasan-fotografer.index']),
                        'can_delete' => in_array($m['slug'], ['ketersediaan.index']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
