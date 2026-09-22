<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('module', 'like', "%{$request->search}%")
                  ->orWhere('action', 'like', "%{$request->search}%");
            });
        }

        $logs = $query->paginate(20);

        return view('audit-logs.index', compact('logs'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        $categories = \App\Models\Category::pluck('name', 'id')->toArray();
        $units = \App\Models\Unit::pluck('name', 'id')->toArray();
        $locations = \App\Models\Location::pluck('name', 'id')->toArray();
        $warehouseTypes = \App\Models\WarehouseType::pluck('name', 'id')->toArray();

        return view('audit-logs.show', compact('auditLog', 'categories', 'units', 'locations', 'warehouseTypes'));
    }
}

