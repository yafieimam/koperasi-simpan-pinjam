<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Approvals extends Model
{
    protected $table = 'approvals';
    protected $fillable = [
        'fk',
        'position_id',
        'model',
        'approval',
        'is_approve',
        'is_reject',
        'is_revision',
        'is_waiting',
        'layer',
        'status',
        'note'
    ];

    protected $casts = [
      'approval' => 'json'
    ];

    public function ts_loans(){
        return $this->belongsTo(TsLoans::class, 'fk');
    }

    public function resign(){
        return $this->belongsTo(Resign::class, 'fk');
    }

    // public function user(){
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    public function position(){
        return $this->belongsTo(Position::class, 'position_id');
    }

}
