<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenerateReportPencairanPinjaman extends Model
{
    //
    protected $table = 'generate_report_pencairan_pinjaman';
    protected $fillable = ['name','start','end','status','attachment'];
}
