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
        'Master Data' => [
            'categories' => 'Kategori Barang',
            'units' => 'Satuan Barang',
            'warehouse_types' => 'Tipe Gudang',
            'warehouses' => 'Data Gudang',
            'locations' => 'Lokasi / Rak Gudang',
            'barangs' => 'Katalog Barang',
            'suppliers' => 'Data Supplier',
        ],
        'Pembelian (PR & PO)' => [
            'permintaan_pembelian' => 'Requisition Pembelian (PR)',
            'purchase_orders' => 'Purchase Order (PO)',
        ],
        'Transaksi & Operasi Gudang' => [
            'permintaan_barang' => 'Permintaan Barang (Produksi)',
            'barang_masuk' => 'Transaksi Barang Masuk',
            'barang_keluar' => 'Transaksi Barang Keluar',
            'stock_opnames' => 'Stock Opname',
            'stock_adjustments' => 'Stock Adjustment',
            'stock_mutations' => 'Mutasi Lokasi Rak',
        ],
        'Analisis & Administrator' => [
            'reports' => 'Laporan & Export',
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
        // If user has a custom permissions array set explicitly
        if (is_array($this->permissions)) {
            return in_array($permission, $this->permissions);
        }

        // Admin and Owner automatically have access to all features if permissions column is null
        if (in_array($this->role, ['admin', 'owner'])) {
            return true;
        }

        // Fallback default permissions based on Role if permissions column is null
        return match ($this->role) {
            'gudang' => in_array($permission, [
                'barangs', 'warehouse_types', 'warehouses', 'locations', 'barang_masuk', 'barang_keluar',
                'stock_opnames', 'stock_adjustments', 'stock_mutations', 'permintaan_pembelian'
            ]),
            'purchasing' => in_array($permission, [
                'suppliers', 'permintaan_pembelian', 'purchase_orders', 'barang_masuk', 'reports'
            ]),
            'produksi' => in_array($permission, [
                'barangs', 'permintaan_barang', 'barang_keluar'
            ]),
            default => false,
        };
    }
}
