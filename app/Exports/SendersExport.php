<?php

namespace App\Exports;

use App\Sender;
use Maatwebsite\Excel\Concerns\FromCollection;

class SendersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Sender::all();
    }
}
