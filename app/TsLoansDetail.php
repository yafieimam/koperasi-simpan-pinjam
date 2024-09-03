<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TsLoansDetail extends Model
{
	protected $table='ts_loan_details';

	public function loan()
	{
		return $this->belongsTo(TsLoans::class, 'loan_id');
	}

	public function pelunasanDaily($data)
    {
        if(isset($data->pay_date)){
			$countLunas = self::where('loan_id', $data->loan_id)->where(function($q) {
				$q->where('approval', 'disetujui')
				  ->orWhere('approval', 'belum lunas');
			})->count();

			if($countLunas <= 1){
				$tsLoans = TsLoans::findOrFail($data->loan_id);
				$tsLoans->in_period += 1;
				$tsLoans->status = 'lunas';
				$tsLoans->save();
			}

            $data->approval = 'lunas';
            $data->save();
        }
        return false;
    }
}
