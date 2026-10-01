<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['department', 'roles']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users       = $query->latest()->paginate(10)->withQueryString();
        $roles       = Role::all();
        $departments = Department::where('status', 'active')->get();

        return view('admin.users.index', compact('users', 'roles', 'departments'));
    }

    public function create()
    {
        $roles       = Role::all();
        $departments = Department::where('status', 'active')->get();
        return view('admin.users.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8|confirmed',
            'nip'           => 'nullable|string|max:20|unique:users',
            'jabatan'       => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'role'          => 'required|exists:roles,name',
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'        => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars');
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create(collect($validated)->except('role')->toArray());
        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->load(['department', 'roles']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles       = Role::all();
        $departments = Department::where('status', 'active')->get();
        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'      => 'nullable|string|min:8|confirmed',
            'nip'           => 'nullable|string|max:20|unique:users,nip,' . $user->id,
            'jabatan'       => 'nullable|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'role'          => 'required|exists:roles,name',
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'        => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update(collect($validated)->except('role')->toArray());
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
