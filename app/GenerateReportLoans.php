<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenerateReportLoans extends Model
{
    //
    protected $table = 'generate_report_loans';
    protected $fillable = ['name','start','end','status','attachment'];
}
