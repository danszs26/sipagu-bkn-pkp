<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,bendahara,petugas'],
        ]);

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::catat('created', 'User', $user->id, "Membuat user {$user->name} dengan role {$user->role}");

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $old = $user->only(['role', 'is_active']);

        $validated = $request->validate([
            'role' => ['required', 'in:admin,bendahara,petugas'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $user->update($validated);

        ActivityLog::catat(
            'updated', 'User', $user->id,
            "Mengubah role/status user {$user->name}",
            $old, $user->only(['role', 'is_active'])
        );

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }
}
