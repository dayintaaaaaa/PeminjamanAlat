<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [5, 10, 25]) ? (int) $request->per_page : 5;

        $users = User::with('roles')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->role, fn($q, $r) => $q->role($r))
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        $roles = Role::orderBy('id')->pluck('name');

        return view('users.management-user', compact('users', 'perPage', 'roles'));
    }

    // halaman form tambah
    public function create()
    {
        $roles = Role::orderBy('id')->pluck('name');

        return view('users.add-user', compact('roles'));
    }

    // simpan user baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role'     => ['required', Rule::in(Role::pluck('name')->all())],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('users.management-user')
            ->with('success', 'User successfully added.');
    }

    // halaman form edit
    public function edit(User $user)
    {
        $roles = Role::orderBy('id')->pluck('name');

        return view('users.edit-user', compact('user', 'roles'));
    }

    // simpan perubahan
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', Password::defaults()],
            'role'     => ['required', Rule::in(Role::pluck('name')->all())],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];

        // password hanya diganti kalau diisi
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $user->syncRoles([$data['role']]);

        return redirect()->route('users.management-user')
            ->with('success', 'User successfully updated.');
    }

    // hapus user
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.management-user')
                ->with('error', 'You cannot delete your own account.');
        }

        try {
            $user->delete();
        } catch (QueryException $e) {
            // misalnya user masih tercatat di tabel peminjaman
            return redirect()->route('users.management-user')
                ->with('error', 'Failed to delete this data. It is still used by other records.');
        }

        return redirect()->route('users.management-user')
            ->with('success', 'Successfully deleted this data');
    }
}
