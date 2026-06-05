<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\JournalEntry;
use App\Services\AccountingService;
use App\Support\FormatHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function __construct(private AccountingService $accounting)
    {
    }

    public function index(Request $request)
    {
        $coaGroupMap = config('coa.groups', []);
        $groupFilter = $request->input('grup');
        $totalAccountCount = Account::count();

        $accounts = $this->accounting->accountsWithBalances(null);

        if ($groupFilter && array_key_exists($groupFilter, $coaGroupMap)) {
            $accounts = $accounts->filter(fn ($row) => $row['account']->grup === $groupFilter)->values();
        }

        return view('admin.coa', compact('accounts', 'coaGroupMap', 'groupFilter', 'totalAccountCount'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Account::create($data);

        return redirect()->back()->with('success', __('messages.flash.account_created'));
    }

    public function update(Request $request, string $kode)
    {
        $account = Account::findOrFail($kode);

        $data = $this->validated($request, $account);

        $account->update($data);

        return redirect()->back()->with('success', __('messages.flash.account_updated'));
    }

    public function destroy(string $kode)
    {
        $account = Account::findOrFail($kode);

        if (JournalEntry::where('account_kode', $kode)->exists()) {
            return redirect()->back()->with(
                'error',
                __('messages.flash.account_delete_has_journal')
            );
        }

        if (ExpenseCategory::where('account_kode', $kode)->exists()) {
            return redirect()->back()->with(
                'error',
                __('messages.flash.account_delete_has_expense_category')
            );
        }

        $account->delete();

        return redirect()->back()->with('success', __('messages.flash.account_deleted'));
    }

    private function validated(Request $request, ?Account $account = null): array
    {
        $allowedGroups = array_keys(config('coa.groups', []));
        $allowedSubGroups = collect(config('coa.groups', []))->flatten()->unique()->values()->all();

        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'posisi' => ['required', 'string', 'in:Debit,Credit'],
            'grup' => ['required', 'string', Rule::in($allowedGroups)],
            'sub_grup' => ['required', 'string', Rule::in($allowedSubGroups)],
        ];

        if (! $account) {
            $rules['kode'] = ['required', 'string', 'unique:accounts,kode'];
        }

        $data = $request->validate($rules);
        $groupSubs = config('coa.groups.'.$data['grup'], []);

        if (! in_array($data['sub_grup'], $groupSubs, true)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'sub_grup' => __('messages.validation.sub_grup_mismatch'),
            ]);
        }

        return FormatHelper::applyTitleCase($data, ['nama']);
    }
}
