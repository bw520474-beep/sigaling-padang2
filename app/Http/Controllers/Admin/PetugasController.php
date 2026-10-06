<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
    public function index()
    {
        $petugasList = User::where('role', 'petugas')
            ->with(['opd'])
            ->withCount('penanganans')
            ->orderBy('nama', 'asc')
            ->paginate(15);

        $opds = Opd::orderBy('nama_opd', 'asc')->get();

        return view('admin.petugas.index', compact('petugasList', 'opds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'no_hp' => 'nullable|string|max:15',
            'opd_id' => 'required|exists:opd,id',
        ], [
            'opd_id.required' => 'Instansi OPD penempatan petugas wajib dipilih.',
            'opd_id.exists' => 'Instansi OPD yang dipilih tidak valid.',
        ]);

        User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'no_hp' => $validated['no_hp'] ?? null,
            'opd_id' => $validated['opd_id'],
            'role' => 'petugas',
        ]);

        return back()->with('success', "Petugas lapangan {$validated['nama']} berhasil didaftarkan.");
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', 'petugas')->findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$id}",
            'no_hp' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:6',
            'opd_id' => 'required|exists:opd,id',
        ], [
            'opd_id.required' => 'Instansi OPD penempatan petugas wajib dipilih.',
        ]);

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->no_hp = $validated['no_hp'] ?? null;
        $user->opd_id = $validated['opd_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', "Data petugas {$user->nama} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $user = User::where('role', 'petugas')->findOrFail($id);
        $user->delete();

        return back()->with('success', 'Petugas lapangan berhasil dinonaktifkan/dihapus.');
    }
}
