<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('name', 'asc')->get();
        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'in:super_admin,warehouse_staff,sales_staff,guest'],
        ]);

        $user = User::findOrFail($id);

        // Security check: Prevent the logged-in super admin from accidentally changing their own role.
        if ($user->id === auth()->id() && $request->input('role') !== 'super_admin') {
            return redirect()->back()->withErrors(['error' => 'Anda tidak diperbolehkan mengubah role Anda sendiri demi keamanan akses.']);
        }

        $newRole = $request->input('role');
        $updateData = [
            'role' => $newRole,
        ];
        
        // If user is upgraded from guest, clear the pending request status
        if ($newRole !== 'guest') {
            $updateData['admin_request_status'] = null;
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('status', "Role untuk pengguna {$user->name} berhasil diperbarui menjadi " . $this->getRoleName($user->role) . ".");
    }

    public function applyAdmin(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->role === 'guest') {
            $request->validate([
                'role' => ['required', 'string', 'in:super_admin,warehouse_staff,sales_staff'],
            ]);

            $requestedRole = $request->input('role');
            
            $user->update([
                'admin_request_status' => 'pending:' . $requestedRole,
            ]);

            return redirect()->back()->with('status', 'Pengajuan sebagai ' . $this->getRoleName($requestedRole) . ' berhasil dikirim. Menunggu persetujuan Super Admin.');
        }

        return redirect()->back()->withErrors(['error' => 'Anda sudah memiliki akses admin.']);
    }

    private function getRoleName(string $role): string
    {
        return match ($role) {
            'super_admin' => 'Super Admin',
            'warehouse_staff' => 'Staff Gudang',
            'sales_staff' => 'Staff Penjualan',
            'guest' => 'Guest',
            default => $role,
        };
    }
}
