<?php

namespace App;

use App\Notifications\NewLoanNotification;
// use Approval\Models\Approval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use NotificationChannels\OneSignal\OneSignalChannel;
use Auth;

class TsLoans extends Model
{
	protected $table='ts_loans';
    protected $casts = [
        'approvemans' => 'json',
        'notes' => 'json',
    ];


    /**
     * @param null $approvals
     * @param string $status
     * @param string $note
     * @param bool $is_approve
     * @param bool $is_revision
     * @param bool $is_reject
     * @param bool $is_waiting
     * @return array
     */
    public function generateApprovalsLoan($approvals = null, $status = 'menunggu', $note = '', $is_approve = false, $is_revision = false, $is_reject = false, $is_waiting = false)
    {
        $dataApproval = [];
        $numLayer = 1;
        foreach ($approvals as $approveman) {
            $dataApproval['fk'] = $this->id;
            $dataApproval['model'] = 'App\TsLoans';
            $dataApproval['is_approve'] = $is_approve;
            $dataApproval['is_revision'] = $is_revision;
            $dataApproval['id_reject'] = $is_reject;
            $dataApproval['is_waiting'] = $is_waiting;
            $dataApproval['is_approve'] = $is_approve;
            $dataApproval['layer'] = $numLayer;
            $dataApproval['status'] = $status;
            $dataApproval['note'] = $note;
            $approveman['status'] = $status;
            $approval = new Approveman($approveman->toArray());
            $dataApproval['approval'] = $approval;
            $dataApproval['position_id'] = $approval->position_id;
            Approvals::create($dataApproval);

            $numLayer++;
        }
        return $dataApproval;
    }

    public function approval(){
        return $this->belongsTo(Approvals::class, 'id', 'fk');
    }

	public function detail()
    {
        return $this->hasMany(TsLoansDetail::class, 'loan_id', 'id');
	}

	public function member()
	{
		return $this->belongsTo(Member::class, 'member_id');
	}

	public function ms_loans()
	{
		return $this->belongsTo(Loan::class, 'loan_id');
	}

    public function penjamin()
	{
		return $this->belongsTo(User::class, 'penjamin');
	}

    public function approve_user()
	{
		return $this->belongsTo(Position::class, 'status_by');
	}

	static function totalLoans($id){
        // $ts = TsLoans::leftJoin('ts_loan_details', function($join) {
        //     $join->on('ts_loans.id', '=', 'ts_loan_details.loan_id');
        // })->where('ts_loans.member_id', Auth::user()->member->id)
        // ->whereIn('ts_loan_details.approval', array('belum lunas','disetujui','menunggu'))->get(['ts_loan_details.value', 'ts_loans.biaya_jasa']);
        // $sum = 0;
        // foreach ($ts as $el) {
        //     $sum += $el->value;
        // }
        $sum = 0;
        $ts = TsLoans::where('member_id', Auth::user()->member->id)->where(function($q) {
            $q->where('status', 'disetujui')
              ->orWhere('status', 'belum lunas');
        })->get();
        foreach ($ts as $el) {
            // $sum += $el->value;
            $loanDataDetail = TsLoansDetail::where('loan_number', $el->loan_number)->get();
            $sisa = collect($loanDataDetail);
            $dataSisa = $sisa->filter(function ($item)
            {
                return $item->approval == 'disetujui' || $item->approval == 'belum lunas';
            });
            $sum += $dataSisa->sum('value') + $el->biaya_jasa;
        }

        return $sum;
	}

	public function getJasa()
	{

		return $this->value * ($this->rate_of_interest / 100);
	}

	public static function getLoanArea($region){
        $selected = self::where('status', '!=', 'ditolak')->where('status', '!=', 'dibatalkan')->get();
	    if(!empty($region)){
            $selected = self::where('status', '!=', 'ditolak')->where('status', '!=', 'dibatalkan')->whereHas('member', function ($query) {
                return $query->where('region_id', '=', auth()->user()->region['id']);
            })->get();
        }


        return $selected;
    }

    public static function getTopPinjamanArea($region){
        $selected = self::whereHas('ms_loans')->with('ms_loans')
            ->select('loan_id', DB::raw('sum(value) as total'), DB::raw('count(id) as total_user'))
            ->groupBy('loan_id')->orderBy('total', 'DESC')
            ->limit(5);

        if(!empty($region)){
            $selected = self::whereHas('ms_loans')->whereHas('member', function ($query) {
                $query->where('region_id', '=', auth()->user()->region['id']);
                })
                ->with('ms_loans')
                ->select('loan_id', DB::raw('sum(value) as total'), DB::raw('count(id) as total_user'))
                ->groupBy('loan_id')->orderBy('total', 'DESC')
                ->limit(5);
        }

        return $selected;
    }

    public static function getTopPeminjamArea($region){
        $selected = self::whereHas('ms_loans')->with('member')
            ->select('member_id', DB::raw('sum(value) as total'), DB::raw('count(member_id) as total_pinjaman'))
            ->groupBy('member_id')->orderBy('total', 'DESC')
            ->limit(5);

        if(!empty($region)){
            $selected = self::whereHas('ms_loans')->whereHas('member', function ($query) {
                $query->where('region_id', '=', auth()->user()->region['id']);
            })
                ->select('member_id', DB::raw('sum(value) as total'), DB::raw('count(member_id) as total_pinjaman'))
                ->groupBy('member_id')->orderBy('total', 'DESC')
                ->limit(5);
        }

        return $selected;
    }

    public function newLoanBlastTo($users)
    {
        foreach ($users as $user)
        {
            $user->notify(new NewLoanNotification($this));
        }
    }

    public static function totalPendapatanProvisiDate($month, $area, $proyek, $member_id){
        return self::where([
            'ts_loans.status' => 'lunas',
            ])->whereMonth('ts_loans.start_date','>=', $month->format('m'))
            ->whereYear('ts_loans.start_date','>=', $month->format('Y'))
            ->whereMonth('ts_loans.start_date','<=',$month->format('m'))
            ->whereYear('ts_loans.start_date','<=',$month->format('Y'))
            ->leftJoin('ms_members', 'ms_members.id', '=', 'ts_loans.member_id')
            ->when($area != 'ALL', function ($query) use ($area) {
                return $query->where('ms_members.region_id', $area);
            })->when($proyek != 'ALL', function ($query) use ($proyek) {
                return $query->where('ms_members.project_id', $proyek);
            })->when($member_id != 'ALL', function ($query) use ($member_id){
                return $query->where('ms_members.id', $member_id);
            })
            ->sum('biaya_provisi');
    }

    public static function totalPendapatanJasaDate($month, $area, $proyek, $member_id){
        return self::where([
            'ts_loans.status' => 'lunas',
            ])->whereMonth('ts_loans.start_date','>=', $month->format('m'))
            ->whereYear('ts_loans.start_date','>=', $month->format('Y'))
            ->whereMonth('ts_loans.start_date','<=',$month->format('m'))
            ->whereYear('ts_loans.start_date','<=',$month->format('Y'))
            ->leftJoin('ms_members', 'ms_members.id', '=', 'ts_loans.member_id')
            ->when($area != 'ALL', function ($query) use ($area) {
                return $query->where('ms_members.region_id', $area);
            })->when($proyek != 'ALL', function ($query) use ($proyek) {
                return $query->where('ms_members.project_id', $proyek);
            })->when($member_id != 'ALL', function ($query) use ($member_id){
                return $query->where('ms_members.id', $member_id);
            })
            ->sum('biaya_jasa');
    }

    public static function totalPendapatanAdminDate($month, $area, $proyek, $member_id){
        return self::where([
            'ts_loans.status' => 'lunas',
            ])->whereMonth('ts_loans.start_date','>=', $month->format('m'))
            ->whereYear('ts_loans.start_date','>=', $month->format('Y'))
            ->whereMonth('ts_loans.start_date','<=',$month->format('m'))
            ->whereYear('ts_loans.start_date','<=',$month->format('Y'))
            ->leftJoin('ms_members', 'ms_members.id', '=', 'ts_loans.member_id')
            ->when($area != 'ALL', function ($query) use ($area) {
                return $query->where('ms_members.region_id', $area);
            })->when($proyek != 'ALL', function ($query) use ($proyek) {
                return $query->where('ms_members.project_id', $proyek);
            })->when($member_id != 'ALL', function ($query) use ($member_id){
                return $query->where('ms_members.id', $member_id);
            })
            ->sum('biaya_admin');
    }

    public static function totalPencairanPinjaman($month, $loan_type, $area, $proyek, $member_id){
        return self::whereYear('ts_loans.start_date', $month->format('Y'))
            ->whereMonth('ts_loans.start_date', $month->format('m'))
            ->where('loan_id', $loan_type)
            ->leftJoin('ms_members', 'ms_members.id', '=', 'ts_loans.member_id')
            ->when($area != 'ALL', function ($query) use ($area) {
                return $query->where('ms_members.region_id', $area);
            })->when($proyek != 'ALL', function ($query) use ($proyek) {
                return $query->where('ms_members.project_id', $proyek);
            })->when($member_id != 'ALL', function ($query) use ($member_id){
                return $query->where('ms_members.id', $member_id);
            })
            ->select(DB::raw('YEAR(ts_loans.start_date) year, MONTH(ts_loans.start_date) month'), DB::raw('sum(value) as total'))
            ->groupBy('year', 'month')->orderBy('total', 'DESC')
            ->first();
    }

    public static function totalPiutangPinjaman($month, $loan_type, $area, $proyek, $member_id){
        $sisaPinjaman = 0;

        $data = self::whereYear('ts_loans.start_date', $month->format('Y'))
            ->whereMonth('ts_loans.start_date', $month->format('m'))
            ->where('loan_id', $loan_type)
            ->where(function($q) {
                $q->where('ts_loans.status', 'disetujui')
                ->orWhere('ts_loans.status', 'belum lunas');
            })
            ->leftJoin('ms_members', 'ms_members.id', '=', 'ts_loans.member_id')
            ->when($area != 'ALL', function ($query) use ($area) {
                return $query->where('ms_members.region_id', $area);
            })->when($proyek != 'ALL', function ($query) use ($proyek) {
                return $query->where('ms_members.project_id', $proyek);
            })->when($member_id != 'ALL', function ($query) use ($member_id){
                return $query->where('ms_members.id', $member_id);
            })->with('detail')->get();

        foreach($data as $val){
            $loanDataDetail = TsLoansDetail::where('loan_number', $val->loan_number)->get();
            $sisa = collect($loanDataDetail);
            $dataSisa = $sisa->filter(function ($item)
            {
                return $item->approval == 'disetujui' || $item->approval == 'belum lunas';
            });
            if($dataSisa->sum('value') == 0){
                $sisaPinjaman += 0;
            }else{
                $sisaPinjaman += $dataSisa->sum('value') + $val->biaya_jasa;
            }
        }

        return $sisaPinjaman;
    }
}
