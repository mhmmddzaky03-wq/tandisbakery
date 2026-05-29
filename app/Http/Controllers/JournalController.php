<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalTransaction;
use App\Services\AccountingService;
use App\Support\FormatHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    public function __construct(private AccountingService $accounting)
    {
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $from = $request->input('from');
        $to = $request->input('to');

        $journals = $this->accounting->journalGroups($search, $from, $to);
        $accounts = Account::orderBy('kode')->get();

        $totalDebit = JournalEntry::sum('debit');
        $totalKredit = JournalEntry::sum('credit');
        $totalTransaksi = JournalTransaction::count();

        return view('admin.jurnal-umum', compact(
            'journals',
            'accounts',
            'search',
            'from',
            'to',
            'totalDebit',
            'totalKredit',
            'totalTransaksi'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'deskripsi' => ['required', 'string', 'max:255'],
            'ref' => ['nullable', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_kode' => ['required', 'string', 'exists:accounts,kode'],
            'lines.*.debit' => ['required', 'integer', 'min:0'],
            'lines.*.credit' => ['required', 'integer', 'min:0'],
        ]);

        $totalDebit = collect($data['lines'])->sum('debit');
        $totalCredit = collect($data['lines'])->sum('credit');

        if ($totalDebit !== $totalCredit || $totalDebit === 0) {
            return back()->withErrors(['lines' => __('Total debit dan kredit harus sama dan lebih dari nol.')])->withInput();
        }

        $data = FormatHelper::applyTitleCase($data, ['deskripsi']);

        DB::transaction(function () use ($data) {
            $tx = JournalTransaction::create([
                'tanggal' => $data['tanggal'],
                'deskripsi' => $data['deskripsi'],
                'ref' => $data['ref'] ?? null,
            ]);

            foreach ($data['lines'] as $line) {
                JournalEntry::create([
                    'journal_transaction_id' => $tx->id,
                    'account_kode' => $line['account_kode'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                ]);
            }
        });

        return redirect()->back()->with('success', __('ui.flash_journal_created'));
    }

    public function destroy(int $id)
    {
        JournalTransaction::findOrFail($id)->delete();

        return redirect()->back()->with('success', __('ui.flash_journal_deleted'));
    }
}
