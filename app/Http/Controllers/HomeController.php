<?php

namespace App\Http\Controllers;

use App\Deposit;
use App\Helpers\dateConvert;
use App\Helpers\grafikData;
use App\Loan;
use App\Resign;
use App\TotalDepositMember;
use App\TsDeposits;
use DB;
use Auth;
use App\user;
use App\Member;
use App\TsLoans;
use App\Region;
use App\Project;
use App\Position;
use Carbon\Carbon;
use App\MemberPlafon;
use App\TsLoansDetail;
use App\DepositTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class HomeController extends GlobalController
{
	function __construct()
	{
        $this->currMonth   = Carbon::now()->format('m');
		$this->date        = Carbon::now()->format('Y-m-d');
	}
    public function index()
    {
//        dd(auth()->user()->can('view.account.register'));
        $startlasteYear = now()->subYear(1)->firstOfYear()->format('Y-m-d');
        $endlastYear = now()->subYear(1)->lastOfYear()->format('Y-m-d');
        $start_thisYear = now()->firstOfYear()->format('Y-m-d');
        $end_thisYear = now()->lastOfYear()->format('Y-m-d');
        if (auth()->user()->isPow() || auth()->user()->isSu()) {
            $data['allMember'] = Member::getMemberArea(auth()->user()->region)->where('is_active', 1)->count();
            $data['newMember'] = Member::getMemberArea(auth()->user()->region)->where('created_at','LIKE', '%-'.$this->currMonth.'%')->count();
            $data['getProj']   = Project::getProjectArea(auth()->user()->region);
            $data['tsDeposit'] = TotalDepositMember::getDepositArea(auth()->user()->region)->sum('value');
            $data['countM']    = Member::getMemberAreaCount(auth()->user()->region)->select([DB::raw('DATE_FORMAT(join_date, "%Y-%b") as month, count(*) as counter')])
                                 ->orderBy(DB::raw('month'), 'desc')
                                 ->groupBy(DB::raw('month'))
                                 ->take(12)
                                 ->get();
            $data['countMResign']    = Resign::getMemberAreaCount(auth()->user()->region)->select([DB::raw('DATE_FORMAT(date, "%Y-%b") as month, count(*) as counter')])
//                ->where('is_active', 0)
                ->orderBy(DB::raw('month'), 'desc')
                ->groupBy(DB::raw('month'))
                ->take(12)
                ->get();
            $data['first']     = Member::orderBy('created_at')->first();
            if ($data['first']) {
                $data['first'] = Carbon::parse($data['first']->created_at);
            } else {
                $data['first'] = Carbon::today();
            }
            $data['topPinjaman'] = TsLoans::getTopPinjamanArea(auth()->user()->region)->get();
            $data['topPeminjam'] = TsLoans::getTopPeminjamArea(auth()->user()->region)->get();
            $data['topSimpanan'] = TotalDepositMember::getTopDepositArea(auth()->user()->region)->get();
            $data['grafikLastYear'] = grafikData::simpananYearly($startlasteYear, $endlastYear, auth()->user()->region);
            $data['grafikThisYear'] = grafikData::simpananYearly($start_thisYear, $end_thisYear, auth()->user()->region);
        } else if(auth()->user()->isSuperVisor() || auth()->user()->isPengurusSatu() || auth()->user()->isPengurusDua() || auth()->user()->isPengawasSatu() || auth()->user()->isPengawasDua()){
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
            
            if(auth()->user()->member->hasProject())
            {
                $data['getProj']   = (Member::where('id', Auth::user()->member->id)->first())->project->project_name;
            }

            $data['getProj']   = '';
            $data['tsLoan']    = $sum;
            $data['plafon']    = MemberPlafon::where('member_id', Auth::user()->member->id)->sum('nominal');
            $data['tsDeposit'] = TotalDepositMember::where('member_id', Auth::user()->member->id)->sum('value');
            $data['countM']    = Member::getMemberAreaCount(auth()->user()->region)->select([DB::raw('DATE_FORMAT(join_date, "%Y-%b") as month, count(*) as counter')])
                                 ->orderBy(DB::raw('month'), 'desc')
                                 ->groupBy(DB::raw('month'))
                                 ->take(12)
                                 ->get();
            $data['countMResign']    = Resign::getMemberAreaCount(auth()->user()->region)->select([DB::raw('DATE_FORMAT(date, "%Y-%b") as month, count(*) as counter')])
//                ->where('is_active', 0)
                ->orderBy(DB::raw('month'), 'desc')
                ->groupBy(DB::raw('month'))
                ->take(12)
                ->get();
            $data['first']     = Member::orderBy('created_at')->first();
            if ($data['first']) {
                $data['first'] = Carbon::parse($data['first']->created_at);
            } else {
                $data['first'] = Carbon::today();
            }
            $data['topPinjaman'] = TsLoans::getTopPinjamanArea(auth()->user()->region)->get();
            $data['topPeminjam'] = TsLoans::getTopPeminjamArea(auth()->user()->region)->get();
            $data['topSimpanan'] = TotalDepositMember::getTopDepositArea(auth()->user()->region)->get();
            $data['grafikLastYear'] = grafikData::simpananYearly($startlasteYear, $endlastYear, auth()->user()->region);
            $data['grafikThisYear'] = grafikData::simpananYearly($start_thisYear, $end_thisYear, auth()->user()->region);
        } else {
            // $ts = TsLoans::leftJoin('ts_loan_details', function($join) {
            //          $join->on('ts_loans.id', '=', 'ts_loan_details.loan_id');
            //      })
            //      ->where('ts_loans.member_id', Auth::user()->member->id)
            //      ->whereIn('ts_loan_details.approval', array('belum lunas','disetujui','menunggu'))
            //      ->get(['ts_loan_details.value', 'ts_loans.biaya_jasa']);
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
            
            if(auth()->user()->member->hasProject())
            {
                $data['getProj']   = (Member::where('id', Auth::user()->member->id)->first())->project->project_name;
            }

            $data['getProj']   = '';
            $data['tsLoan']    = $sum;
            $data['plafon']    = MemberPlafon::where('member_id', Auth::user()->member->id)->sum('nominal');
            $data['tsDeposit'] = TotalDepositMember::where('member_id', Auth::user()->member->id)->sum('value');
            $data['countDep']  = DepositTransaction::select([DB::raw('DATE_FORMAT(created_at, "%b-%Y") as month, sum(total_deposit) as deposit')])->orderBy(DB::raw('month'), 'desc')
                                 ->groupBy(DB::raw('month'))
                                 ->where('status', 'paid')->get();
        }
        return view('dashboards.main', $data);
    }
    public function memberActive()
    {
        $selected = Member::getMemberAreaCount(auth()->user()->region)->whereIsActive('aktif')->get();
        if (request()->ajax()) {
            return DataTables::of($selected)
            ->editColumn('fullname', function ($selected) {
                return $selected->first_name .' '.$selected->last_name;
            })
            ->editColumn('project', function ($selected) {
                if($selected->hasProject())
                {
                    return $selected->project->project_name;
                }
               return '';
            })
            ->addColumn('action',function($selected){
                return
                '<center>
                <a  class="btn btn-info btn-sm btnEdit" href="/profile-member/'.Crypt::encrypt($selected->user_id).'"  data-toggle="tooltip" title="Cek data"><i class="ion ion-aperture"></i></a>
                </a>
                </center>';
            })
            ->make(true);
        }
    }
    public function profileMember($el='')
    {
        $decrypter = Crypt::decrypt($el);
        $getData   = User::leftJoin('ms_members', function($join) {
                         $join->on('ms_members.user_id', '=', 'users.id');
                     })
                     ->leftJoin('ms_banks', function($join) {
                         $join->on('ms_members.id', '=', 'ms_banks.member_id');
                     })
                     ->leftJoin('positions', function($join) {
                         $join->on('ms_members.position_id', '=', 'positions.id');
                     })
                    ->where('users.id', $decrypter)
                    ->first();
        $region    = Region::all();
        $pst       = Position::fMemberOnly(false)->get();
        $spcMember = Member::where('user_id', $decrypter)->first();
        if(isset($getData)){
            return view('dashboards.profile-member', compact('getData', 'region', 'spcMember', 'pst'));
        }else{
            session()->flash('errors',collect(['Anggota Tersebut Belum Mengisi Data Bank dan Posisi Secara Lengkap']));
            return redirect()->back();
        }
    }

    public function myProfile($el='')
    {
        $user = auth()->user();
        $getData   = User::leftJoin('ms_members', function($join) {
            $join->on('ms_members.user_id', '=', 'users.id');
        })
            ->leftJoin('ms_banks', function($join) {
                $join->on('ms_members.id', '=', 'ms_banks.member_id');
            })
            ->leftJoin('positions', function($join) {
                $join->on('ms_members.position_id', '=', 'positions.id');
            })
            ->where('users.id', $user->id)
            ->first();
        $region    = Region::all();
        $pst       = Position::fMemberOnly()->get();
        $spcMember = Member::where('user_id', $user->id)->first();
        return view('dashboards.my-profile', compact('getData', 'region', 'spcMember', 'pst'));
    }

    public function countMember()
    {
        return Member::getMemberArea(auth()->user()->region)->count();
    }
}
