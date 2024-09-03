<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use App\TsLoansDetail;

class LoanLunasGenerator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dataDetail = TsLoansDetail::where('pay_date', Carbon::now()->format('Y-m-d'))->where(function($q) {
            $q->where('approval', 'disetujui')
              ->orWhere('approval', 'belum lunas');
        })->get();
        if($dataDetail->count() > 0){
            foreach ($dataDetail as $data) {
                $data->pelunasanDaily($data);
            }
        }
    }
}
