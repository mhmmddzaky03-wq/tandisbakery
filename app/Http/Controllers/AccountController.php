<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private AccountingService $accounting)
    {
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $accounts = $this->accounting->accountsWithBalances($search);

        return view('admin.coa', compact('accounts', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'unique:accounts,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'posisi' => ['required', 'string', 'in:Debit,Credit'],
            'grup' => ['required', 'string', 'max:100'],
        ]);

        Account::create($data);

        return redirect()->back()->with('success', __('Akun berhasil ditambahkan.'));
    }

    public function update(Request $request, string $kode)
    {
        $account = Account::findOrFail($kode);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'posisi' => ['required', 'string', 'in:Debit,Credit'],
            'grup' => ['required', 'string', 'max:100'],
        ]);

        $account->update($data);

        return redirect()->back()->with('success', __('Akun berhasil diperbarui.'));
    }

    public function destroy(string $kode)
    {
        Account::findOrFail($kode)->delete();

        return redirect()->back()->with('success', __('Akun berhasil dihapus.'));
    }
}
