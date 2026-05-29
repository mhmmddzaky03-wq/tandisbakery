<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['kode' => '1-110', 'nama' => "Cash in Tandi's Bank", 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '1-120', 'nama' => 'Account Receivable', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '1-130', 'nama' => 'Direct Materials', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '1-131', 'nama' => 'Purchase of Bread & Pastry', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '1-132', 'nama' => 'Purchase Discount', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '1-133', 'nama' => 'Purchase Return & Allowances', 'posisi' => 'Credit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '1-140', 'nama' => 'Work in Process', 'posisi' => 'Credit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '1-150', 'nama' => 'Finished Goods', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '1-160', 'nama' => 'Plant Supplies', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '1-170', 'nama' => 'Administration & Selling Supplies', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '1-210', 'nama' => 'Plant Equipment', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Non-Current Asset'],
            ['kode' => '1-211', 'nama' => 'Acc. Depreciation of Plant Equipment', 'posisi' => 'Credit', 'grup' => 'Asset', 'sub_grup' => 'Non-Current Asset'],
            ['kode' => '1-220', 'nama' => 'Admin. & Selling Equipment', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Non-Current Asset'],
            ['kode' => '1-221', 'nama' => 'Acc. Depreciation of Admin. & Selling Equipment', 'posisi' => 'Credit', 'grup' => 'Asset', 'sub_grup' => 'Non-Current Asset'],
            ['kode' => '1-230', 'nama' => 'Vehicle', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Non-Current Asset'],
            ['kode' => '1-231', 'nama' => 'Acc. Depreciation of Vehicle', 'posisi' => 'Credit', 'grup' => 'Asset', 'sub_grup' => 'Non-Current Asset'],
            ['kode' => '1-311', 'nama' => 'Investment', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Non-Current Asset'],
            ['kode' => '2-110', 'nama' => 'Salary Payable', 'posisi' => 'Debit', 'grup' => 'Liability', 'sub_grup' => 'Current Liability'],
            ['kode' => '2-120', 'nama' => 'Account Payable', 'posisi' => 'Credit', 'grup' => 'Liability', 'sub_grup' => 'Current Liability'],
            ['kode' => '2-130', 'nama' => 'Unearned Revenue', 'posisi' => 'Credit', 'grup' => 'Liability', 'sub_grup' => 'Current Liability'],
            ['kode' => '2-140', 'nama' => 'IPHONE Payable', 'posisi' => 'Credit', 'grup' => 'Liability', 'sub_grup' => 'Current Liability'],
            ['kode' => '2-150', 'nama' => 'Interest Payable', 'posisi' => 'Credit', 'grup' => 'Liability', 'sub_grup' => 'Current Liability'],
            ['kode' => '2-160', 'nama' => 'Tax Payable', 'posisi' => 'Credit', 'grup' => 'Liability', 'sub_grup' => 'Current Liability'],
            ['kode' => '2-170', 'nama' => 'Dividend Payable', 'posisi' => 'Credit', 'grup' => 'Liability', 'sub_grup' => 'Current Liability'],
            ['kode' => '2-210', 'nama' => 'Bank Payable BRI', 'posisi' => 'Credit', 'grup' => 'Liability', 'sub_grup' => 'Current Liability'],
            ['kode' => '3-110', 'nama' => "Capital TANDI'S", 'posisi' => 'Credit', 'grup' => 'Equity', 'sub_grup' => 'Paid-In-Capital'],
            ['kode' => '3-120', 'nama' => 'Retained Earning', 'posisi' => 'Credit', 'grup' => 'Equity', 'sub_grup' => 'Retained Earnings'],
            ['kode' => '4-110', 'nama' => 'Sales', 'posisi' => 'Credit', 'grup' => 'Revenues', 'sub_grup' => 'Service Revenues'],
            ['kode' => '4-120', 'nama' => 'Sales Discount', 'posisi' => 'Debit', 'grup' => 'Revenues', 'sub_grup' => 'Service Revenues'],
            ['kode' => '4-130', 'nama' => 'Sales Return and Allowances', 'posisi' => 'Debit', 'grup' => 'Revenues', 'sub_grup' => 'Service Revenues'],
            ['kode' => '5-110', 'nama' => 'Cost of Goods Sold', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-120', 'nama' => 'Service Expense', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-130', 'nama' => 'Depreciation of Vehicle', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-140', 'nama' => 'Insurance Expense', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-150', 'nama' => 'Salary Expense', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-151', 'nama' => 'Salary Expense of the Admin. & Selling Staffs', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-152', 'nama' => 'Salary Expense of Security', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-160', 'nama' => 'Utilities Expense', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-161', 'nama' => 'Utilities Expense of the Admin. & Selling Department', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-170', 'nama' => 'Depr. Exp. of the Admin. & Selling Equipment', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-180', 'nama' => 'Miscellaneous Expense', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Operational Expenses'],
            ['kode' => '5-190', 'nama' => 'Income Tax', 'posisi' => 'Debit', 'grup' => 'Expenses', 'sub_grup' => 'Tax'],
            ['kode' => '6-100', 'nama' => 'Direct Labor', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '6-200', 'nama' => 'Plant Supplies Cost', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '6-300', 'nama' => 'Depreciation of the Plant Equipment', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '6-400', 'nama' => 'Utilities of the Plant', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '6-500', 'nama' => 'Salaries of Manufacturing Supervisors', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
            ['kode' => '6-600', 'nama' => 'Maintenance and Repairs Vehicle', 'posisi' => 'Debit', 'grup' => 'Asset', 'sub_grup' => 'Current Asset'],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['kode' => $account['kode']],
                $account
            );
        }

        $this->remapLegacyJournalAccounts();
    }

    private function remapLegacyJournalAccounts(): void
    {
        $map = [
            '1-220' => '1-230',
            '1-221' => '1-231',
        ];

        foreach ($map as $from => $to) {
            if (! Account::where('kode', $to)->exists()) {
                continue;
            }

            \Illuminate\Support\Facades\DB::table('journal_entries')
                ->where('account_kode', $from)
                ->update(['account_kode' => $to]);
        }

        if (Account::where('kode', '2-160')->exists()) {
            \Illuminate\Support\Facades\DB::table('journal_entries')
                ->where('account_kode', '2-120')
                ->where('credit', '>', 0)
                ->where('debit', 0)
                ->update(['account_kode' => '2-160']);
        }
    }
}
