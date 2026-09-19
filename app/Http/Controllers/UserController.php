<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Available roles in system.
     */
    public const ROLES = [
        'admin' => 'Administrator',
        'gudang' => 'Staf Gudang',
        'purchasing' => 'Staf Purchasing',
        'produksi' => 'Staf Produksi',
        'owner' => 'Owner / Manager',
    ];

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = self::ROLES;
        $moduleGroups = User::MODULE_GROUPS;

        return view('users.index', compact('users', 'roles', 'moduleGroups'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = self::ROLES;
        $moduleGroups = User::MODULE_GROUPS;
        return view('users.create', compact('roles', 'moduleGroups'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(User::getAllPermissionKeys())],
        ]);

        $permissions = $request->input('permissions', []);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'permissions' => $permissions,
        ]);

        // Audit Log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE_USER',
            'module' => 'MANAJEMEN_USER',
            'record_id' => $user->id,
            'new_values' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'permissions' => $permissions,
            ],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')->with('success', "Akun '{$user->name}' berhasil ditambahkan dengan hak akses khusus.");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = self::ROLES;
        $moduleGroups = User::MODULE_GROUPS;
        return view('users.edit', compact('user', 'roles', 'moduleGroups'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(User::getAllPermissionKeys())],
        ]);

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'permissions' => $user->permissions,
        ];

        $permissions = $request->input('permissions', []);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'permissions' => $permissions,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Audit Log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_USER',
            'module' => 'MANAJEMEN_USER',
            'record_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'permissions' => $permissions,
            ],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')->with('success', "Data akun '{$user->name}' dan hak akses berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan.');
        }

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $userName = $user->name;
        $user->delete();

        // Audit Log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_USER',
            'module' => 'MANAJEMEN_USER',
            'record_id' => $user->id,
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')->with('success', "Akun '{$userName}' berhasil dihapus.");
    }
}
