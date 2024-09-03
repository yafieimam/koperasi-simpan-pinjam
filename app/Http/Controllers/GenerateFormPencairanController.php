<?php

namespace App\Http\Controllers;

set_time_limit(0);

use App\Helpers\DownloadReport;
use Illuminate\Http\Request;
use PDF;

class GenerateFormPencairanController extends Controller
{
    public function download($id){
        $genFormPencairan = DownloadReport::generateFormPencairan($id);
        // var_dump($genFormPencairan['sukarela'][1]['tanggal']);
        $pdf = PDF::loadView('report.deposit.report-form-pengambilan-simpanan', ['data' => $genFormPencairan])->setPaper('a4', 'portrait');
        return $pdf->stream('Rekap Pengambilan Simpanan Sukarela.pdf');
    }
}
