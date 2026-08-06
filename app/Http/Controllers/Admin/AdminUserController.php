<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])
            ->orderByDesc('role')
            ->orderBy('name')
            ->get();

        return view('admin.admins.index', ['admins' => $admins]);
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.admins.index')
            ->with('status', 'success')
            ->with('message', 'New admin account created.');
    }

    public function promote(User $admin)
    {
        if ($admin->id === Auth::id()) {
            return back()->with('status', 'error')->with('message', 'You already have super admin access.');
        }

        $admin->update(['role' => User::ROLE_SUPER_ADMIN]);

        return redirect()->route('admin.admins.index')
            ->with('status', 'success')
            ->with('message', '"'.$admin->name.'" was promoted to super admin.');
    }

    public function destroy(User $admin)
    {
        if ($admin->id === Auth::id()) {
            return back()->with('status', 'error')->with('message', "You can't remove your own account.");
        }

        if ($admin->isSuperAdmin() && User::where('role', User::ROLE_SUPER_ADMIN)->count() <= 1) {
            return back()->with('status', 'error')->with('message', 'There must be at least one super admin.');
        }

        $name = $admin->name;
        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('status', 'success')
            ->with('message', '"'.$name.'" was removed.');
    }
}