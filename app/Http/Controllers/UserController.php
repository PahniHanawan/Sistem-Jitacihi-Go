<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user (Admin & Owner)
     */
    public function index()
    {
        $users = User::with('role')
            ->orderBy('user_id', 'asc')
            ->get();

        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

        /**
     * Ambil data user untuk edit (AJAX)
     */
    public function editData(User $user)
    {
        return response()->json([
            'username' => $user->username,
            'email' => $user->email,
            'full_name' => $user->full_name,
            'role_id' => $user->role_id,
            'status' => $user->status,
        ]);
    }

    /**
     * Menyimpan user baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string|max:100',
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', '❌ Gagal menambahkan user. Periksa kembali data yang diinput.');
        }

        try {
            User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'full_name' => $request->full_name,
                'role_id' => $request->role_id,
                'status' => $request->status,
            ]);

            return redirect()->route('users.index')
                ->with('success', '✅ User "' . $request->full_name . '" berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data user
     */
    public function update(Request $request, User $user)
    {
        // Cegah user mengubah data sendiri
        if ($user->user_id === Auth::id()) {
            return redirect()->back()
                ->with('error', '❌ Anda tidak dapat mengubah data sendiri. Gunakan menu Profil.');
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'full_name' => 'required|string|max:100',
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', '❌ Gagal memperbarui user. Periksa kembali data yang diinput.');
        }

        try {
            $user->update([
                'username' => $request->username,
                'email' => $request->email,
                'full_name' => $request->full_name,
                'role_id' => $request->role_id,
                'status' => $request->status,
            ]);

            return redirect()->route('users.index')
                ->with('success', '✅ User "' . $user->full_name . '" berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    /**
     * Reset password user
     */
    public function resetPassword(Request $request, User $user)
    {
        // Cegah reset password sendiri
        if ($user->user_id === Auth::id()) {
            return redirect()->back()
                ->with('error', '❌ Anda tidak dapat mereset password sendiri. Gunakan fitur profil.');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', '❌ Gagal reset password. Password minimal 6 karakter.');
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('users.index')
                ->with('success', '✅ Password user "' . $user->full_name . '" berhasil direset.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Gagal reset password: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleStatus(User $user)
    {
        // Cegah menonaktifkan diri sendiri
        if ($user->user_id === Auth::id()) {
            return redirect()->back()
                ->with('error', '❌ Anda tidak dapat menonaktifkan akun sendiri.');
        }

        try {
            $newStatus = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
            $user->update(['status' => $newStatus]);

            $statusText = $newStatus === 'aktif' ? 'Aktif' : 'Nonaktif';

            return redirect()->route('users.index')
                ->with('success', '✅ Status user "' . $user->full_name . '" berhasil diubah menjadi ' . $statusText . '.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        // Cegah menghapus diri sendiri
        if ($user->user_id === Auth::id()) {
            return redirect()->back()
                ->with('error', '❌ Anda tidak dapat menghapus akun sendiri.');
        }

        // Cegah menghapus Owner terakhir
        $ownerCount = User::where('role_id', 1)->count();
        if ($user->role_id == 1 && $ownerCount <= 1) {
            return redirect()->back()
                ->with('error', '❌ Tidak dapat menghapus Owner terakhir. Pastikan ada minimal 1 Owner.');
        }

        try {
            $userName = $user->full_name;
            $user->delete();

            return redirect()->route('users.index')
                ->with('success', '✅ User "' . $userName . '" berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
