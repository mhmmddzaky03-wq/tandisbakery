<?php

namespace App\Http\Controllers;

use App\Models\JournalTransaction;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function __construct(private AccountingService $accounting)
    {
    }

    public function index(Request $request)
    {
        $sourceOptions = $this->accounting->journalSourceOptions();
        $source = $request->input('source', '');
        if ($source !== '' && ! array_key_exists($source, $sourceOptions)) {
            $source = '';
        }

        $from = $request->input('from');
        $to = $request->input('to');

        $journals = $this->accounting->journalGroups($source ?: null, $from, $to);
        $totals = $this->accounting->journalTotals($source ?: null, $from, $to);

        return view('admin.jurnal-umum', compact(
            'journals',
            'source',
            'sourceOptions',
            'from',
            'to',
            'totals'
        ));
    }

    public function destroy(int $id)
    {
        $blocked = $this->accounting->journalDeleteBlockedReason($id);
        if ($blocked) {
            return redirect()->back()->with('error', $blocked);
        }

        JournalTransaction::findOrFail($id)->delete();

        return redirect()->back()->with('success', __('messages.flash.journal_deleted'));
    }
}
