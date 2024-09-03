<?php

namespace App\Imports;

use App\TsDeposits;
use App\Deposit;
use App\TsDepositsDetail;
use App\TotalDepositMember;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\GlobalController;
use App\Helpers\cutOff;

class TsDepositsImport implements ToModel, WithStartRow
{
    /**
    * @param Collection $collection
    */

    function __construct()
    {
        $this->globalFunc = new GlobalController();
    }

    public function model(array $row)
    {
        // $deposit = Deposit::findOrFail($row[3]);

        $tsDeposit                   = new TsDeposits();
        $tsDeposit->id               = $row[0];
        $tsDeposit->member_id        = $row[2];
        $tsDeposit->deposit_number   = $row[1];
        $tsDeposit->ms_deposit_id    = $row[3];
        $tsDeposit->type             = $row[4];
        $tsDeposit->deposits_type    = $row[5];
        $tsDeposit->total_deposit    = $this->globalFunc->revive($row[6]);
        $tsDeposit->status           = $row[7];
        $tsDeposit->post_date        = $row[8];
        $tsDeposit->desc             = $row[9];
        $tsDeposit->save();

        $pokok_detail = new TsDepositsDetail();
        $pokok_detail->transaction_id = $tsDeposit->id;
        $pokok_detail->deposits_type = $row[5];
        $pokok_detail->debit = ($row[4] == "debit") ? $this->globalFunc->revive($row[6]) : 0;
        $pokok_detail->credit = ($row[4] == "credit") ? $this->globalFunc->revive($row[6]) : 0;
        $pokok_detail->total = $this->globalFunc->revive($row[6]);
        $pokok_detail->status = $row[7];
        $pokok_detail->payment_date = cutOff::getCutoff();
        $pokok_detail->save();

        $totalDepositMember = TotalDepositMember::where([
            'member_id' => $row[2],
            'ms_deposit_id' => $row[3]
        ])->first();

        if(isset($totalDepositMember)){
            $value = $totalDepositMember['value'] + $row[6];
            $totalDepositMember->value = $value;
            $totalDepositMember->save();
        }else{
            $totalDepositMember = new TotalDepositMember();
            $totalDepositMember->member_id = $row[2];
            $totalDepositMember->ms_deposit_id = $row[3];
            $totalDepositMember->value = $row[6];
            $totalDepositMember->save();
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
