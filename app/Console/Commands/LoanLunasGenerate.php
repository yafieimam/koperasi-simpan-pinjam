<?php

namespace App\Console\Commands;

use App\Jobs\LoanLunasGenerator;
use Illuminate\Console\Command;

class LoanLunasGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan-lunas:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pelunasan Pinjaman Otomatis';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        LoanLunasGenerator::dispatchNow();
    }
}
