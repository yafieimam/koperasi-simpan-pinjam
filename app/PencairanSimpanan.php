<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PencairanSimpanan extends Model
{
	protected  $table = 'pencairan_simpanan';
    protected $fillable = [
		'member_id',
		'bank_id',
		'date',
		'phone',
		'jumlah',
		'approval_level',
		'status',
		'status_by'
	];

	public function member()
	{
		return $this->belongsTo(Member::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public static function getPencairanSimpananArea($region){
		$approvalPosition = [1, 2, 6, 7, 8, 9, 10, 15, 16, 17, 21];
		$position = Position::find(auth()->user()->position['id']);
		$selected = (object)[];
		if(in_array($position->level_id, $approvalPosition)){
			$selected = self::where('approval_level', '<=', 4)->orderBy('id', 'asc')->get();
		}else{
			if(!empty($region)){
				if($position->level_id == 8){
					$selected = self::where('approval_level', 1)->where('approval_level', '<=', 4)->where('status', '!=', 'rejected')->whereHas('member', function ($query) {
						return $query->where('region_id', '=', auth()->user()->region['id']);
					})->orderBy('id', 'asc')->get();
				}else if($position->level_id == 6 || $position->level_id == 7){
					$selected = self::where('approval_level', 2)->orWhere('approval_level', 3)->where('approval_level', '<=', 4)->where('status', '!=', 'rejected')->whereHas('member', function ($query) {
						return $query->where('region_id', '=', auth()->user()->region['id']);
					})->orderBy('id', 'asc')->get();
				}
				
			}
		}

        return $selected;
    }

	public static function getPencairanSimpananMember($id){
		$selected = self::where('member_id', $id)->where('status', '!=', 'rejected')->orderBy('id', 'desc')->get();

        return $selected;
    }
}
