<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApprovalLevelPinjaman extends Model
{
    protected  $table = 'approval_level_pinjaman';
    protected $fillable = [
		'level_id',
		'superior_level_id',
		'approval_level'
	];

    public function level()
	{
		return $this->belongsTo(Level::class);
	}

}
