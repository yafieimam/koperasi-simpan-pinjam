<?php

namespace App\Http\Controllers;

use App\Helpers\DownloadReport;
use Illuminate\Http\Request;
use PDF;

class GenerateFormResignController extends Controller
{
    public function download($id){
        $genResign = DownloadReport::generateFormResign($id);
        $pdf = PDF::loadView('report.deposit.report-form-resign', ['data' => $genResign])->setPaper('a4', 'portrait');
        return $pdf->stream('Form Resign.pdf');
    }
}
