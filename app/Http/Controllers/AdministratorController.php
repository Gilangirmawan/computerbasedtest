<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class AdministratorController extends Controller
{
    /**
     * Menampilkan daftar semua administrator.
     */
    public function index()
    {
        // Ambil semua user yang memiliki role_id = 1 (admin)
        $administrators = User::where('role_id', 1)->latest()->get();
        return view('pages.administrator.index', compact('administrators'));
    }

    /**
     * Menampilkan form untuk menambah administrator baru.
     */
    public function create()
    {
        return view('pages.administrator.create');
    }

    /**
     * Menyimpan administrator baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id' => 1, // 1 = Admin
            'status' => 'approved',
            'is_superadmin' => $request->has('is_superadmin'),
        ]);

        return redirect()->route('administrator.index')->with('swal_success', 'Administrator baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit administrator.
     */
    public function edit(User $administrator)
    {
        return view('pages.administrator.edit', compact('administrator'));
    }

    /**
     * Memperbarui data administrator di database.
     */
    public function update(Request $request, User $administrator)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $administrator->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        if (Auth::user()->id === $administrator->id && !$request->has('is_superadmin')) {
            return back()->withErrors(['is_superadmin' => 'Anda tidak dapat menghapus status Super Admin dari akun Anda sendiri.']);
        }

        $administrator->name = $request->name;
        $administrator->username = $request->username;
        $administrator->is_superadmin = $request->has('is_superadmin');

        if ($request->filled('password')) {
            $administrator->password = Hash::make($request->password);
        }
        
        $administrator->save();

        return redirect()->route('administrator.index')->with('swal_success', 'Data administrator berhasil diperbarui.');
    }

    /**
     * Menghapus data administrator dari database.
     */
    public function destroy(User $administrator)
    {
        if (Auth::user()->id == $administrator->id) {
            return redirect()->route('administrator.index')
                ->with('swal_error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $administrator->delete();

        return redirect()->route('administrator.index')->with('swal_success', 'Administrator berhasil dihapus.');
    }
}