<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Hanya catat jika user sudah login
        if (auth()->check()) {
            $user = auth()->user();
            $method = $request->method();
            $routeName = $request->route() ? $request->route()->getName() : null;
            $path = $request->path();

            // Abaikan request untuk halaman audit-logs itu sendiri agar tidak spamming/loop
            if (str_starts_with($path, 'audit-logs') && $method === 'GET') {
                return $response;
            }

            // Tentukan Modul berdasarkan Rute / Path
            $module = 'GENERAL';
            if (str_contains($path, 'barangs')) $module = 'KATALOG_BARANG';
            elseif (str_contains($path, 'categories')) $module = 'KATEGORI_BARANG';
            elseif (str_contains($path, 'units')) $module = 'SATUAN_BARANG';
            elseif (str_contains($path, 'warehouses')) $module = 'GUDANG';
            elseif (str_contains($path, 'locations')) $module = 'LOKASI_RAK';
            elseif (str_contains($path, 'suppliers')) $module = 'SUPPLIER';
            elseif (str_contains($path, 'permintaan-barang')) $module = 'MATERIAL_REQUEST';
            elseif (str_contains($path, 'permintaan-pembelian')) $module = 'PURCHASE_REQUEST';
            elseif (str_contains($path, 'purchase-orders')) $module = 'PURCHASE_ORDER';
            elseif (str_contains($path, 'barang-masuk')) $module = 'BARANG_MASUK';
            elseif (str_contains($path, 'barang-keluar')) $module = 'BARANG_KELUAR';
            elseif (str_contains($path, 'stock-opnames')) $module = 'STOCK_OPNAME';
            elseif (str_contains($path, 'stock-adjustments')) $module = 'STOCK_ADJUSTMENT';
            elseif (str_contains($path, 'stock-mutations')) $module = 'STOCK_MUTATION';
            elseif (str_contains($path, 'reports')) $module = 'LAPORAN';
            elseif (str_contains($path, 'dashboard')) $module = 'DASHBOARD';
            elseif (str_contains($path, 'profile')) $module = 'PROFIL_USER';

            // Tentukan Aksi (NAVIGATE / CREATE / UPDATE / DELETE / POSTING / APPROVE)
            $action = 'NAVIGATE';
            if ($method === 'GET') {
                $action = 'VIEW_' . strtoupper(basename($path));
                if ($action === 'VIEW_') $action = 'VIEW_DASHBOARD';
            } elseif ($method === 'POST') {
                if (str_contains($path, 'approve')) $action = 'APPROVE';
                elseif (str_contains($path, 'reject')) $action = 'REJECT';
                elseif (str_contains($path, 'cancel')) $action = 'CANCEL';
                else $action = 'CREATE';
            } elseif (in_array($method, ['PUT', 'PATCH'])) {
                $action = 'UPDATE';
            } elseif ($method === 'DELETE') {
                $action = 'DELETE';
            }

            // Ambil record_id jika ada pada route parameter
            $recordId = null;
            if ($request->route()) {
                $parameters = $request->route()->parameters();
                $firstParam = reset($parameters);
                if (is_object($firstParam) && isset($firstParam->id)) {
                    $recordId = $firstParam->id;
                } elseif (is_numeric($firstParam)) {
                    $recordId = (int)$firstParam;
                }
            }

            // Filter payload sensitif (password, token, dll)
            $payload = $request->except(['_token', '_method', 'password', 'password_confirmation']);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'module' => $module,
                'record_id' => $recordId,
                'new_values' => $method !== 'GET' ? $payload : ['url' => $request->fullUrl()],
                'ip_address' => $request->ip(),
            ]);
        }

        return $response;
    }
}
