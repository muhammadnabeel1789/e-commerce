<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAddressController extends Controller
{
    // Menampilkan daftar alamat
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();
        return view('customer.addresses.index', compact('addresses'));
    }

    // ✅ DITAMBAHKAN: Menampilkan form tambah alamat (halaman terpisah)
    public function create()
    {
        return view('customer.addresses.create');
    }

    // Menyimpan alamat baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string',
            'province'       => 'required|string',
            'city'           => 'required|string',
            'district'       => 'required|string',
            'village'        => 'required|string',
            'postal_code'    => 'required|numeric',
        ]);

        Auth::user()->addresses()->create($validated);

        // ✅ Jika request dari AJAX (fetch di checkout), return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Alamat berhasil ditambahkan.']);
        }

        // ✅ Support redirect_back (dari checkout) atau redirect_to (dari halaman lain)
        $redirectTo = $request->input('redirect_back')
                   ?? $request->input('redirect_to')
                   ?? route('addresses.index');

        return redirect($redirectTo)->with('success', 'Alamat berhasil ditambahkan.');
    }

    // Menampilkan form edit
    public function edit(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // ✅ DIPERBAIKI: path view yang benar
        return view('customer.addresses.edit', compact('address'));
    }

    // Update alamat
    public function update(Request $request, UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string',
            'province'       => 'required|string',
            'city'           => 'required|string',
            'district'       => 'required|string',
            'village'        => 'required|string',
            'postal_code'    => 'required|numeric',
        ]);

        $address->update($validated);

        // ✅ Jika AJAX dari checkout
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Alamat berhasil diperbarui.']);
        }

        $redirectTo = $request->input('redirect_back')
                   ?? $request->input('redirect_to')
                   ?? route('addresses.index');

        return redirect($redirectTo)->with('success', 'Alamat berhasil diperbarui.');
    }

    // Hapus alamat
    public function destroy(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->back()->with('success', 'Alamat dihapus.');
    }

    // ✅ DITAMBAHKAN: setPrimary (dipanggil oleh route addresses.setPrimary)
    public function setPrimary(UserAddress $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Alamat utama berhasil diubah.');
    }

    // ✅ DIPERBAIKI: setDefault dengan success message
    public function setDefault($id)
    {
        $user = Auth::user();
        $user->addresses()->update(['is_default' => false]);

        $address = $user->addresses()->findOrFail($id);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Alamat utama berhasil diubah.');
    }
}