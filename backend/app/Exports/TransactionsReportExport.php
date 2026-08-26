<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class TransactionsReportExport implements FromCollection
{
    public function collection(): Collection
    {
        return Transaction::all();
    }
}
