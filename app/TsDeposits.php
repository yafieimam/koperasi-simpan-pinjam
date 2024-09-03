<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Matrix\Builder;

class TsDeposits extends Model
{
	//

	protected $table = 'ts_deposits';

	public function detail()
    {
        return $this->hasMany(TsDepositsDetail::class, 'transaction_id', 'id');
	}

	public function member()
	{
		return $this->belongsTo(Member::class, 'member_id');
	}

	public function ms_deposit()
	{
		return $this->belongsTo(Deposit::class, 'ms_deposit_id');
	}

	public function totalDeposit($id){
		return $this->where(['status' => 'paid','member_id' => $id])->sum('total_deposit');
	}

	public static function getDepositArea($region){
        $selected = self::all();
        if(!empty($region)){
            $selected = self::whereHas('member', function ($query) {
                return $query->where('region_id', '=', auth()->user()->region['id']);
            })->get();
        }


        return $selected;
    }

    public static function getDepositAreaPending($region){
        $selected = self::all();
        if(!empty($region)){
            $selected = self::where('status', 'pending')->whereHas('member', function ($query) {
                return $query->where('region_id', '=', auth()->user()->region['id']);
            })->orderBy('id', 'asc')->get();
        }

        return $selected;
    }

    public static function getDepositTypeArea($region, $type){
        $selected = self::where('ms_deposit_id', $type)->get();
        if(!empty($region)){
            $selected = self::where('ms_deposit_id', $type)->whereHas('member', function ($query) {
                return $query->where('region_id', '=', auth()->user()->region['id']);
            })->get();
        }


        return $selected;
    }

    public static function totalDepositPokok($id){
        $debit =  self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 1,
            'type' => 'debit'
        ])->sum('total_deposit');

        $credit =  self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 1,
            'type' => 'credit'
        ])->sum('total_deposit');

        return $debit - $credit;
    }

    public static function totalDepositWajib($id){
        $debit =  self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 2,
            'type' => 'debit'
        ])->sum('total_deposit');

        $credit =  self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 2,
            'type' => 'credit'
        ])->sum('total_deposit');
        return $debit - $credit;
    }

    public static function totalDepositSukarela($id){
        $debit = self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 3,
            'type' => 'debit'
        ])->sum('total_deposit');

        $credit = self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 3,
            'type' => 'credit'
        ])->sum('total_deposit');

        return $debit - $credit;
    }

    public static function totalDepositBerjangka($id){
        return self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 4
        ])->sum('total_deposit');
    }

    public static function totalDepositShu($id){
        $debit = self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 5,
            'type' => 'debit'
        ])->sum('total_deposit');

        $credit = self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 5,
            'type' => 'credit'
        ])->sum('total_deposit');

        return $debit - $credit;
    }

    public static function totalDepositLainnya($id){
        $debit = self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 6,
            'type' => 'debit'
        ])->sum('total_deposit');

        $credit = self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 6,
            'type' => 'credit'
        ])->sum('total_deposit');

        return $debit - $credit;
    }

    public static function totalDepositPokokDate($id,$month){
        if($id == "ALL"){
            return self::where([
                'status' => 'paid',
                'ms_deposit_id' => 1
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
        }else{
            return self::where([
                'status' => 'paid',
                'member_id' => $id,
                'ms_deposit_id' => 1
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
        }
    }

    public static function totalDepositWajibDate($id,$month){
        if($id == "ALL"){
            return self::where([
                'status' => 'paid',
                'ms_deposit_id' => 2
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
        }else{
            return self::where([
                'status' => 'paid',
                'member_id' => $id,
                'ms_deposit_id' => 2
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
        }
    }

    public static function totalDepositSukarelaDate($id,$month){
        if($id == "ALL"){
            $debit = self::where([
                'status' => 'paid',
                'ms_deposit_id' => 3,
                'type' => 'debit'
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
    
            $credit = self::where([
                'status' => 'paid',
                'ms_deposit_id' => 3,
                'type' => 'credit'
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
    
            return $debit - $credit;
        }else{
            $debit = self::where([
                'status' => 'paid',
                'member_id' => $id,
                'ms_deposit_id' => 3,
                'type' => 'debit'
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
    
            $credit = self::where([
                'status' => 'paid',
                'member_id' => $id,
                'ms_deposit_id' => 3,
                'type' => 'credit'
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
    
            return $debit - $credit;
        }
    }

    public static function totalDepositSukarelaTypeDate($id,$month,$type){
        if($id == "ALL"){
            return self::where([
                'status' => 'paid',
                'ms_deposit_id' => 3,
                'type' => $type
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
        }else{
            return self::where([
                'status' => 'paid',
                'member_id' => $id,
                'ms_deposit_id' => 3,
                'type' => $type
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
        }
    }

    public static function totalDepositBerjangkaDate($id,$month){
        return self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 4
        ])->whereMonth('post_date','>=', $month->format('m'))
            ->whereYear('post_date','>=', $month->format('Y'))
            ->whereMonth('post_date','<=',$month->format('m'))
            ->whereYear('post_date','<=',$month->format('Y'))
            ->sum('total_deposit');
    }

    public static function totalDepositShuDate($id,$month){
        if($id == "ALL"){
            return self::where([
                'status' => 'paid',
                'ms_deposit_id' => 5
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
        }else{
            return self::where([
                'status' => 'paid',
                'member_id' => $id,
                'ms_deposit_id' => 5
            ])->whereMonth('post_date','>=', $month->format('m'))
                ->whereYear('post_date','>=', $month->format('Y'))
                ->whereMonth('post_date','<=',$month->format('m'))
                ->whereYear('post_date','<=',$month->format('Y'))
                ->sum('total_deposit');
        }
    }

    public static function totalDepositLainnyaDate($id,$month){
        return self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 6
        ])->whereMonth('post_date','>=', $month->format('m'))
        ->whereYear('post_date','>=', $month->format('Y'))
        ->whereMonth('post_date','<=',$month->format('m'))
        ->whereYear('post_date','<=',$month->format('Y'))
        ->sum('total_deposit');
    }

    public static function totalDepositPokokRangeDate($id,$start_date,$end_date){
        return self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 1
        ])->whereDate('post_date','>=', $start_date)
            ->whereDate('post_date','<=',$end_date)
            ->sum('total_deposit');
    }

    public static function totalDepositWajibRangeDate($id,$start_date,$end_date){
        return self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 2
        ])->whereDate('post_date','>=', $start_date)
        ->whereDate('post_date','<=',$end_date)
            ->sum('total_deposit');
    }

    public static function totalDepositSukarelaRangeDate($id,$start_date,$end_date){
        $debit = self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 3,
            'type' => 'debit'
        ])->whereDate('post_date','>=', $start_date)
        ->whereDate('post_date','<=',$end_date)
            ->sum('total_deposit');

        $credit = self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 3,
            'type' => 'credit'
        ])->whereDate('post_date','>=', $start_date)
        ->whereDate('post_date','<=',$end_date)
            ->sum('total_deposit');

        return $debit - $credit;
    }

    public static function totalDepositBerjangkaRangeDate($id,$start_date,$end_date){
        return self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 4
        ])->whereDate('post_date','>=', $start_date)
        ->whereDate('post_date','<=',$end_date)
            ->sum('total_deposit');
    }

    public static function totalDepositShuRangeDate($id,$start_date,$end_date){
        return self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 5
        ])->whereDate('post_date','>=', $start_date)
        ->whereDate('post_date','<=',$end_date)
            ->sum('total_deposit');
    }

    public static function totalDepositLainnyaRangeDate($id,$start_date,$end_date){
        return self::where([
            'status' => 'paid',
            'member_id' => $id,
            'ms_deposit_id' => 6
        ])->whereDate('post_date','>=', $start_date)
        ->whereDate('post_date','<=',$end_date)
        ->sum('total_deposit');
    }

    public function scopefTypeDeposit($query, $id_deposit){
	    return $query->where('ms_deposit_id', $id_deposit);
    }

    public static function getYearlyDeposit($month, $region){
        $selected = self::whereYear('post_date', $month->format('Y'))
            ->whereMonth('post_date', $month->format('m'))
            ->where('status', 'paid')->select(DB::raw('YEAR(post_date) year, MONTH(post_date) month'), DB::raw('sum(total_deposit) as total'))
            ->groupBy('year', 'month')->orderBy('total', 'DESC')
            ->first();

        if(!empty($region)){
            $selected = self::whereYear('post_date', $month->format('Y'))
                ->whereMonth('post_date', $month->format('m'))
                ->where('status', 'paid')->whereHas('member', function ($query) {
                    $query->where('region_id', '=', auth()->user()->region['id']);
                })
                ->where('status', 'paid')->select(DB::raw('YEAR(post_date) year, MONTH(post_date) month'), DB::raw('sum(total_deposit) as total'))
                ->groupBy('year', 'month')->orderBy('total', 'DESC')
                ->first();
        }
        return $selected;
    }

    public static function getYearlyDepositType($month, $type, $deposit_type, $area, $proyek, $member_id){
        return self::whereYear('post_date', $month->format('Y'))
            ->whereMonth('post_date', $month->format('m'))
            ->where('type', $type)
            ->where('ms_deposit_id', $deposit_type)
            ->leftJoin('ms_members', 'ms_members.id', '=', 'ts_deposits.member_id')
            ->when($area != 'ALL', function ($query) use ($area) {
                return $query->where('ms_members.region_id', $area);
            })->when($proyek != 'ALL', function ($query) use ($proyek) {
                return $query->where('ms_members.project_id', $proyek);
            })->when($member_id != 'ALL', function ($query) use ($member_id){
                return $query->where('ms_members.id', $member_id);
            })
            ->select(DB::raw('YEAR(post_date) year, MONTH(post_date) month'), DB::raw('sum(total_deposit) as total'))
            ->groupBy('year', 'month')->orderBy('total', 'DESC')
            ->first();
    }

    public static function getDateDepositType($date, $type, $deposit_type, $status){
        return self::where('post_date', $date->format('Y-m-d'))
            ->where('type', $type)
            ->where('ms_deposit_id', $deposit_type)
            ->when($status == 'pending', function ($query) {
                return $query->where('status', 'pending');
            })->when($status == 'paid', function ($query) {
                return $query->where('status', 'paid');
            })->when($status == 'all', function ($query) {
                return $query->whereIn('status', array('pending', 'paid', 'unpaid'));
            })
            ->select('post_date', DB::raw('sum(total_deposit) as total'))
            ->groupBy('post_date')->orderBy('total', 'DESC')
            ->first();
    }

    public static function totalPotonganSimpanan($month, $type, $area, $proyek){
        return self::whereYear('post_date', $month->format('Y'))
            ->whereMonth('post_date', $month->format('m'))
            ->where('type', 'debit')
            ->where('ms_deposit_id', $type)
            ->leftJoin('ms_members', 'ms_members.id', '=', 'ts_deposits.member_id')
            ->leftJoin('ms_projects', 'ms_members.project_id', '=', 'ms_projects.id')
            
            ->when($area != 'ALL', function ($query) use ($area) {
                return $query->where('ms_members.region_id', $area);
            })->when($proyek != 'ALL', function ($query) use ($proyek) {
                return $query->where('ms_members.project_id', $proyek);
            })
            ->select(DB::raw('sum(total_deposit) as total'))
            ->groupBy('ts_deposits.member_id')->orderBy('total', 'DESC')
            ->first();
    }
}
