<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseType;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Barang;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_categories' => Category::count(),
            'active_categories' => Category::where('status', 'active')->count(),
            'total_units' => Unit::count(),
            'active_units' => Unit::where('status', 'active')->count(),
            'total_warehouses' => Warehouse::count(),
            'active_warehouses' => Warehouse::where('status', 'active')->count(),
            'total_warehouse_types' => WarehouseType::count(),
            'active_warehouse_types' => WarehouseType::where('status', 'active')->count(),
            'total_locations' => Location::count(),
            'active_locations' => Location::where('status', 'active')->count(),
            'total_suppliers' => Supplier::count(),
            'active_suppliers' => Supplier::where('is_active', true)->count(),
            'total_users' => User::count(),
            'total_barangs' => Barang::count(),
            'recent_audit_logs' => AuditLog::with('user')->latest()->take(5)->get(),
        ];

        return view('admin.index', compact('stats'));
    }
}
