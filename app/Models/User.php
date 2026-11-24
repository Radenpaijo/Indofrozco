<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Grouped list of all modules and feature permissions in the WMS system.
     */
    public const MODULE_GROUPS = [
        'Master Data - Barang' => [
            'barangs' => 'Lihat Katalog Barang',
            'barangs_create' => 'Tambah Barang Baru',
            'barangs_edit' => 'Edit / Ubah Data Barang',
            'barangs_delete' => 'Hapus Data Barang',
        ],
        'Master Data - Referensi & Gudang' => [
            'categories' => 'Lihat Kategori Barang',
            'categories_manage' => 'Kelola Kategori (Tambah, Edit, Hapus)',
            'units' => 'Lihat Satuan Barang',
            'units_manage' => 'Kelola Satuan (Tambah, Edit, Hapus)',
            'locations' => 'Lihat Lokasi / Rak Gudang',
            'locations_manage' => 'Kelola Lokasi / Rak (Tambah, Edit, Hapus)',
            'warehouses' => 'Lihat Data Gudang',
            'warehouses_manage' => 'Kelola Data Gudang & Tipe Gudang',
            'suppliers' => 'Lihat Data Supplier',
            'suppliers_manage' => 'Kelola Data Supplier (Tambah, Edit, Hapus)',
        ],
        'Operasional Gudang & Transaksi' => [
            'barang_masuk' => 'Lihat Transaksi Barang Masuk',
            'barang_masuk_create' => 'Input Transaksi Barang Masuk',
            'barang_keluar' => 'Lihat Transaksi Barang Keluar',
            'barang_keluar_create' => 'Input Transaksi Barang Keluar',
            'stock_opnames' => 'Stock Opname (Cek Fisik)',
            'stock_adjustments' => 'Stock Adjustment (Penyesuaian Stok)',
            'stock_mutations' => 'Mutasi Lokasi Rak',
            'permintaan_barang' => 'Lihat Permintaan Barang (Produksi)',
            'permintaan_barang_create' => 'Buat Permintaan Barang (Produksi)',
            'permintaan_barang_approve' => 'Setujui / Tolak Permintaan Barang',
        ],
        'Pembelian (PR & PO)' => [
            'permintaan_pembelian' => 'Lihat Permintaan Pembelian (PR)',
            'permintaan_pembelian_create' => 'Buat Permintaan Pembelian (PR)',
            'purchase_orders' => 'Lihat Purchase Order (PO)',
            'purchase_orders_manage' => 'Buat & Kelola Purchase Order (PO)',
        ],
        'Analisis & Administrator' => [
            'reports' => 'Laporan & Export Data',
            'audit_logs' => 'Audit Log Histori',
            'users' => 'Manajemen Akun User',
        ],
    ];

    /**
     * Get flat list of all feature keys.
     */
    public static function getAllPermissionKeys(): array
    {
        $keys = [];
        foreach (self::MODULE_GROUPS as $group) {
            foreach ($group as $key => $label) {
                $keys[] = $key;
            }
        }
        return $keys;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    /**
     * Check if user has a specific role or one of many roles.
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    /**
     * Check if user has permission to access a specific feature.
     */
    public function hasPermission(string $permission): bool
    {
        // 1. Super Admin dan Owner selalu memiliki akses ke SEMUA fitur
        if (in_array($this->role, ['admin', 'owner'])) {
            return true;
        }

        // 2. Admin Gudang selalu memiliki akses CRUD lengkap untuk seluruh Master Data & Operasional Gudang
        if ($this->role === 'admin_gudang') {
            // Hanya batasi modul sistem (users & audit_logs) jika tidak diberikan izin khusus
            if (in_array($permission, ['users', 'audit_logs'])) {
                return is_array($this->permissions) && in_array($permission, $this->permissions);
            }
            return true;
        }

        // 3. Jika pengguna memiliki pengaturan hak akses kustom di database
        if (is_array($this->permissions)) {
            return in_array($permission, $this->permissions);
        }

        // 4. Fallback default permissions berdasarkan peranan (Role)
        return in_array($permission, self::getDefaultPermissionsForRole($this->role ?? ''));
    }

    /**
     * Get default permissions template for a specific role.
     */
    public static function getDefaultPermissionsForRole(?string $role): array
    {
        if (!$role) {
            return [];
        }

        return match ($role) {
            'admin', 'owner' => self::getAllPermissionKeys(),
            'admin_gudang' => [
                'barangs',
                'barangs_create',
                'barangs_edit',
                'barangs_delete',
                'categories',
                'categories_manage',
                'units',
                'units_manage',
                'locations',
                'locations_manage',
                'warehouses',
                'warehouses_manage',
                'barang_masuk',
                'barang_masuk_create',
                'barang_keluar',
                'barang_keluar_create',
                'stock_opnames',
                'stock_adjustments',
                'stock_mutations',
                'permintaan_pembelian',
                'permintaan_pembelian_create',
                'permintaan_barang',
                'permintaan_barang_approve',
            ],
            'gudang' => [
                'barangs', // Hanya lihat katalog barang, tidak bisa tambah/edit/hapus
                'locations',
                'warehouses',
                'barang_masuk',
                'barang_masuk_create',
                'barang_keluar',
                'barang_keluar_create',
                'stock_opnames',
                'stock_adjustments',
                'stock_mutations',
                'permintaan_pembelian',
                'permintaan_pembelian_create',
                'permintaan_barang',
                'permintaan_barang_approve',
            ],
            'purchasing' => [
                'suppliers',
                'suppliers_manage',
                'permintaan_pembelian',
                'purchase_orders',
                'purchase_orders_manage',
                'barang_masuk',
                'reports',
            ],
            'produksi' => [
                'barangs',
                'permintaan_barang',
                'permintaan_barang_create',
                'barang_keluar',
            ],
            default => [],
        };
    }
}
