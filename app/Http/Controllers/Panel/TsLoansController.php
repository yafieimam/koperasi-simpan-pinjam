<?php

namespace App\Http\Controllers\Panel;

use App\Approvals;
use App\Helpers\cutOff;
use App\Notifications\LoanApplicationStatusUpdated;
use App\Notifications\LoanApplicationStatusRejected;
use App\Notifications\LoanApprovalNotification;
use App\Notifications\WaitLoanApplication;
use Auth;
use App\Loan;
use App\Helpers\ApprovalUser;
use App\Member;
use App\Policy;
use App\Project;
use App\Region;
use App\TsDeposits;
use App\User;
use App\Position;
use App\TsLoans;
use App\TotalDepositMember;
use \Carbon\Carbon;
use App\TsLoansDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\GlobalController;

class TsLoansController extends GlobalController
{
    public function index()
    {

        $isCaneViewLoan = auth()->user()->can('view.transaction.member.loan');
        $isCaneUpdateLoan = auth()->user()->can('update.member.loan');

        if(auth()->user()->isSu() || auth()->user()->isPow()){
            $selected = TsLoans::where('status', '!=', 'ditolak')->where('status', '!=', 'dibatalkan')->get();
        }else{
            $selected = TsLoans::where('member_id', auth()->user()->member->id)->where('status', '!=', 'menunggu')->where('status', '!=', 'ditolak')->where('status', '!=', 'dibatalkan')->get();
        }

        $this->i  = 1;
        if (request()->ajax()) {
            return DataTables::of($selected)
            ->editColumn('member', function ($selected) {
                return $selected->member['first_name'] . ' ' . $selected->member['last_name'];
			})
			->editColumn('loan_type', function ($selected) {
                return $selected->ms_loans['loan_name'];
            })
            ->editColumn('no', function ($selected) {
                return $this->i++;
            })
            ->editColumn('loan_number', function ($selected) {
                return $selected->loan_number;
            })
            ->editColumn('value', function ($selected) {
                return 'Rp '. number_format($selected->value);
            })
            ->editColumn('status', function ($selected) {
                $status = $selected->status;
                $getApproval = Approvals::where([
                    'fk' => $selected->id,
                    'layer' => $selected->approval_level + 1
                ])->first();
                if(!empty($getApproval)){
                    $arrApproval = $getApproval->approval;
                }else{
                    $arrApproval = [];
                }
                if($selected->status == 'dibatalkan') {
                    $status = 'Telah di dibatalkan';
                } else if($selected->status == 'ditolak') {
                    $position = Position::find($selected->status_by);
                    if(isset($position->name)){
                            $status = 'Ditolak ' . $position->name;
                    }else{
                            $status = 'Ditolak';
                    }
                } else if($selected->status == 'belum lunas') {
                    $status = 'Belum Lunas';
                } else if($selected->status == 'lunas') {
                    $status = 'Lunas';
                } else if($selected->status == 'menunggu') {
                    if($selected->approval_level + 1 == 1){
                        if(auth()->user()->id == $arrApproval['id']){
                            $status = 'Menunggu Persetujuan Anda';
                        }else{
                            $status = 'Menunggu';
                        }
                    }else{
                        $position = Position::find($selected->status_after);
                        if(isset($position->name)){
                            $status = 'Menunggu Persetujuan ' . $position->name;
                        }else{
                            $status = 'Menunggu';
                        }
                    }
                }
                return ucwords($status);
            })
            ->addColumn('action',function($selected){
            	$idRecord              = \Crypt::encrypt($selected->id);
            	if($selected->status == '') {
            		$action            = '<a  class="btn btn-info btn-sm tooltips" href="/loan-detail/'.$idRecord.'"><i class="ion ion-aperture" title="Liat pengajuan pinjaman"></i></a>
                                         <a class="btn btn-danger tooltips btn-sm"  onclick="modifyData('."'update-loan'".','."'".$idRecord."'".','."'". csrf_token() ."'".','."'listTsLoans'".','."'canceled'".')" data-container="body" data-placement="right" data-html="true" title="Batalkan pengajuan" ><i class="fa fa-undo"></i></a>';
            	} else{
            		$action            = '<a  class="btn btn-info btn-sm tooltips" href="/loan-detail/'.$idRecord.'"><i class="ion ion-aperture" title="Liat pengajuan pinjaman"></i></a>';
            	}
                return
                '<center>'.$action.'</center>';
            })
            ->make(true);
        }
        return view('transaction.loan.ts_loan_list');
    }
    public function list_approval($el='')
    {
        $this->i  = 1;
        $loanId = $this->decrypter($el);
        $query = Approvals::where(['fk' => $loanId])->with('position')->get();
		return \DataTables::of($query)
			->editColumn('no', function($approval){
				return $this->i++;
			})
			->editColumn('position', function($approval){
				return $approval->position->name;
			})
            ->editColumn('status', function($approval){
				return $approval->status;
			})
            ->editColumn('updated_at', function($approval){
				return $approval->updated_at;
			})
            ->editColumn('notes', function($approval){
				return $approval->note;
			})->make(true);
    }
    public function list_sisa_pinjaman($el='', $es='')
    {
        $memberId = $this->decrypter($el);
        $loanId = $this->decrypter($es);
        $query = TsLoans::where('member_id', $memberId)->where('loan_number', '!=', $loanId)->where(function($q) {
            $q->where('status', 'disetujui')
              ->orWhere('status', 'belum lunas');
        })->with('ms_loans', 'detail')->get();
		return \DataTables::of($query)
			->editColumn('loan_no', function($pinjaman){
				return $pinjaman->loan_number;
			})
			->editColumn('loan_type', function($pinjaman){
				return $pinjaman->ms_loans->loan_name;
			})
			->editColumn('pokok_pinjaman', function($pinjaman){
				return number_format($pinjaman->value);
			})
            ->editColumn('jasa', function($pinjaman){
				return number_format($pinjaman->biaya_jasa);
			})
            ->editColumn('tenor', function($pinjaman){
				return $pinjaman->period . ' Bulan';
			})
			->editColumn('sisa_pinjaman', function($pinjaman){
                $sisa = collect($pinjaman->detail);
                $dataSisa = $sisa->filter(function ($item)
                {
                    return $item->approval == 'menunggu' || $item->approval == 'belum lunas';
                });
				return number_format($dataSisa->sum('value') + $dataSisa->sum('service'));
			})->make(true);
    }
    public function list_sisa_pinjaman_reschedule($el='')
    {
        $memberId = $this->decrypter($el);
        $query = TsLoans::where('member_id', $memberId)->where(function($q) {
            $q->where('status', 'disetujui')
              ->orWhere('status', 'belum lunas');
        })->with('ms_loans', 'detail')->get();
		return \DataTables::of($query)
			->editColumn('loan_type', function($pinjaman){
				return $pinjaman->ms_loans->loan_name;
			})
			->editColumn('pokok_pinjaman', function($pinjaman){
				return number_format($pinjaman->value);
			})
            ->editColumn('jasa', function($pinjaman){
				return number_format($pinjaman->biaya_jasa);
			})
            ->editColumn('tenor', function($pinjaman){
				return $pinjaman->period . ' Bulan';
			})
			->editColumn('sisa_pinjaman', function($pinjaman){
                $sisa = collect($pinjaman->detail);
                $dataSisa = $sisa->filter(function ($item)
                {
                    return $item->approval == 'menunggu' || $item->approval == 'belum lunas';
                });
				return number_format($dataSisa->sum('value') + $dataSisa->sum('service'));
			})->make(true);
    }
    public function loanDetail($el='')
    {
        $idRecord            = $this->decrypter($el);
        $finder              = TsLoans::findOrFail($idRecord);
        $member              = Member::findOrFail($finder->member_id);
        // $penjamin            = User::where('id', $finder->penjamin)->first();
        $project             = Project::findOrFail($member->project_id);
        $getUser             = User::findOrFail(auth()->user()->id);
        $getPosition         = Position::findOrFail($getUser->position_id);
        $loanData            = TsLoans::where('member_id', $finder->member_id)->where(function($q) {
            $q->where('status', 'disetujui')
            ->orWhere('status', 'belum lunas')
            ->orWhere('status', 'lunas');
        })->orderBy('value', 'desc')->get();
        $loanDataSisa            = TsLoans::where('member_id', $finder->member_id)->where(function($q) {
            $q->where('status', 'disetujui')
            ->orWhere('status', 'belum lunas');
        })->with('detail')->get();
        $loanDataDetail      = TsLoansDetail::where('loan_number', $finder->loan_number)->get();
        $totalPengajuan      = $loanData->count();
        $sisa                = collect($loanDataDetail);
        $dataSisa = $sisa->filter(function ($item)
        {
            return $item->approval == 'disetujui' || $item->approval == 'belum lunas';
        });
        if($dataSisa->sum('value') == 0){
            $sisaPinjaman        = 0;
        }else{
            $sisaPinjaman        = $dataSisa->sum('value') + $finder->biaya_jasa;
        }
        $pinjamanTerbesar    = ($totalPengajuan > 0) ? $loanData[0] : 0;
        $dataSimpananWajib   = TotalDepositMember::totalDepositWajib($finder->member_id);
        $dataSimpananSukarela   = TotalDepositMember::totalDepositSukarela($finder->member_id);
        $getApproval = Approvals::where([
            'fk' => $finder->id,
            'layer' => $finder->approval_level + 1
        ])->first();
        if(!empty($getApproval)){
            $arrApproval = $getApproval->approval;
            // var_dump($finder->approval_level + 1);
            $getApprovalUser = User::where('id', $arrApproval['id'])->first();
        }else{
            $getApprovalUser = [];
        }
        $status = $finder->status;
        if($finder->status == 'dibatalkan') {
            $status = 'Telah di dibatalkan';
        } else if($finder->status == 'ditolak') {
            $position = Position::find($finder->status_by);
            if(isset($position->name)){
                    $status = 'Ditolak ' . $position->name;
            }else{
                    $status = 'Ditolak';
            }
        } else if($finder->status == 'belum lunas') {
            $status = 'Belum Lunas';
        } else if($finder->status == 'lunas') {
            $status = 'Lunas';
        } else if($finder->status == 'menunggu') {
            if($finder->approval_level + 1 == 1){
                if(auth()->user()->id == $arrApproval['id']){
                    $status = 'Menunggu Persetujuan Anda';
                }else{
                    $status = 'Menunggu';
                }
            }else{
                $position = Position::find($finder->status_after);
                if(isset($position->name)){
                    $status = 'Menunggu Persetujuan ' . $position->name;
                }else{
                    $status = 'Menunggu';
                }
            }
        }
        $finder->status_data = ucwords($status);
        // var_dump(json_encode(['finder' => $finder, 'member' => $member, 'penjamin' => $penjamin, 'project' => $project, 'totalPengajuan' => $totalPengajuan, 'pinjamanTerbesar' => $pinjamanTerbesar, 'totalSimpanan' => $totalSimpanan]));
        if($finder->status == 'disetujui') {
            return view('transaction.loan.loan-detail-approved', ['finder' => $finder, 'member' => $member, 'project' => $project, 'totalPengajuan' => $totalPengajuan, 'pinjamanTerbesar' => $pinjamanTerbesar, 'dataSimpananWajib' => $dataSimpananWajib, 'dataSimpananSukarela' => $dataSimpananSukarela, 'sisaPinjaman' => $sisaPinjaman, 'getApproval' => $getApproval, 'position' => $getPosition, 'getApprovalUser' => $getApprovalUser, 'layer' => $finder->approval_level + 1]);
        }else {
            return view('transaction.loan.loan-detail-approved', ['finder' => $finder, 'member' => $member, 'project' => $project, 'totalPengajuan' => $totalPengajuan, 'pinjamanTerbesar' => $pinjamanTerbesar, 'dataSimpananWajib' => $dataSimpananWajib, 'dataSimpananSukarela' => $dataSimpananSukarela, 'sisaPinjaman' => $sisaPinjaman, 'getApproval' => $getApproval, 'position' => $getPosition, 'getApprovalUser' => $getApprovalUser, 'layer' => $finder->approval_level + 1]);
        }
    }
    public function detailApproved()
    {
        $input        = Input::all();
        $loan_id      = $this->decrypter($input['loan_id']);
        $findDetail   = TsLoansDetail::where('loan_id',$loan_id)->orderBy('in_period')->get();
        if($findDetail){
            $data     = array(
                            'error' => 0,
                            'msg'   => 'Data ditemukan.',
                            'json'   => $findDetail,
                        );
        } else{
            $data     = array(
                            'error' => 1,
                            'msg'   => 'Data cicilan tidak ditemukan.',
                        );
        }
        return response()->json($data);

    }

    public function updateLoan()
    {
    	$input = Input::all();
        $reason = $input['reason'];
        $id = $this->decrypter($input['id']);

        $findData  = TsLoans::where([
                    'id'        => $id,
                    ])->first();
        $user = User::find(auth()->user()->id);
        $position = Position::find($user->position_id);

        $getAfterApp = Approvals::where([
            'fk' => $findData->id,
            'layer' => $findData->approval_level + 1
        ])->first();

        $getAfterLastApp = Approvals::where([
            'fk' => $findData->id,
            'layer' => $findData->approval_level + 2
        ])->first();
       
    	if ($findData){
            if($input['action'] == 'approved') {
                Approvals::where([
                    'fk' => $findData->id,
                    'layer' => $findData->approval_level + 1
                ])->update([
                    'status' => 'disetujui',
                    'note' => $reason
                ]);

                if(isset($getAfterLastApp->layer)){
                    // $user_after = User::find($getAfterLastApp->user_id);
                    $position_after = Position::find($getAfterLastApp->position_id);

                    $findData->notes = $reason;
                    $findData->approval_level = $findData->approval_level + 1;
                    $findData->status_by = $position->level_id;
                    $findData->status_after = $position_after->level_id;
                    $findData->save();
                    $data = 'Success';

                    $getNextApproval = Approvals::where([
                        'fk' => $findData->id,
                        'layer' => $findData->approval_level + 1
                    ])->first();

                    if($findData->approval_level + 1 == 1){
                        $arrApproval = $getNextApproval->approval;
                        $getNextUser = User::where('id', $arrApproval['id'])->first();
                        $getNextUser->notify(new WaitLoanApplication($findData)); 
                    }else{
                        $getNextUser = User::where('position_id', $getNextApproval->position_id)->where('region_id', $user->region_id)->get();
                        // var_dump($getNextUser);
                        foreach($getNextUser as $val){
                            $val->notify(new WaitLoanApplication($findData)); 
                        }
                    }
                }else{
                    $toDate = $this->dateTime(now(), 'full');
                    $nextMonth = $toDate->addMonths(1)->format('Y-m-d');
                    $getRate = $findData->value * ($findData->rate_of_interest / 100);
                    TsLoansDetail::where('loan_id', $findData->id)->update([
                        'approval' => 'disetujui'
                    ]);
                    $findData->start_date = $this->dateTime(now(), 'date');
                    $findData->end_date = $nextMonth;
                    $findData->status = 'disetujui';
                    $findData->approval_level = $findData->approval_level + 1;
                    $findData->status_by = $position->level_id;
                    $findData->save();
                    $data = 'Success';
                }

                $findData->member->user->notify(new LoanApplicationStatusUpdated($findData));   
            }
            
            if($input['action'] == 'canceled'){
                if(!isset($reason)){
                    $data = 'Reason';
                    return response()->json($data);
                }

                if($findData->status == 'ditolak' || $findData->status == 'disetujui' || $findData->status == 'lunas' || $findData->status == 'belum lunas'){
                    $data = 'Failed';
                    return response()->json($data);
                }else {
                    Approvals::where([
                        'fk' => $findData->id,
                        'layer' => $findData->approval_level + 1
                    ])->update([
                        'status' => 'ditolak',
                        'note' => $reason
                    ]);

                    $findData->status = 'ditolak';
                    $findData->notes = $reason;
                    $findData->approval_level = $findData->approval_level + 1;
                    $findData->status_by = $position->level_id;
                    $findData->save();  
                    TsLoansDetail::where('loan_id', $findData->id)->update([
                        'approval' => 'ditolak'
                    ]);
                    $data = 'Success';
                }

                if($findData->loan_id == 15){
                    $adminApproval = User::BusinessApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
                    $approvemans = collect($adminApproval);
                }else if($findData->loan_id == 16){
                    $adminApproval = User::PerseroanApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
                    $approvemans = collect($adminApproval);
                }else{
                    $userSubmision = $findData->member->user;
                    $penjamin = User::where('id', $findData->penjamin)->get();
                    $adminApproval = User::AdminApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
                    $approvals = ApprovalUser::getApproval($userSubmision);
                    $approvemans = collect($penjamin)->merge($approvals)->merge($adminApproval)->merge([$userSubmision]);
                }

                foreach ($approvemans as $user)
                {
                    $user->notify(new LoanApplicationStatusRejected($findData));
                }
            }
        }
         // NOTIFY APPLICANT

         return response()->json($data);
    }

    public function revisiLoan()
    {
        $input = Input::all();
        $id = $this->decrypter($input['id']);
        $findData  = TsLoans::where(['id' => $id])->with('ms_loans')->first();

        return response()->json($findData);
    }

    public function updateRevisiLoan(Request $request){
        $input = Input::all();
        $loan_id = $input['loan_id'];
        $value = $this->revive($input['value']);
        $tenor = $this->revive($input['tenor']);
        $biaya_admin = $this->revive($input['biaya_admin']);
        $biaya_transfer = $this->revive($input['biaya_transfer']);
        $biaya_bunga_berjalan = $this->revive($input['biaya_bunga_berjalan']);
        $keterangan = $input['description'];

        $tsLoans = TsLoans::with('ms_loans')->find($loan_id);
        // $tsLoans->member->user->notify(new LoanApprovalNotification($tsLoans, 'direvisi', $value, $tsLoans->value));
        // $period = $tsLoans->period;
        $loan_value = $value / $tenor;
        $loan_value = ceil($loan_value);
        $biayaJasa = ceil($value * ($tsLoans->ms_loans->rate_of_interest/100));
        $biayaProvisi = ceil($value * ($tsLoans->ms_loans->provisi / 100));
        // $biayaBungaBerjalan = cutOff::getBungaBerjalan($loan_value, $tsLoans->ms_loans->biaya_bunga_berjalan, now()->format('Y-m-d'));
        $payDate = cutOff::getCutoff();

        $tsLoans->value = $value;
        $tsLoans->old_value = $tsLoans->value;
        $tsLoans->period = $tenor;
        $tsLoans->biaya_admin = $biaya_admin;
        $tsLoans->biaya_transfer = $biaya_transfer;
        $tsLoans->biaya_bunga_berjalan = $biaya_bunga_berjalan;
        $tsLoans->notes = $keterangan;
        $tsLoans->biaya_jasa = $biayaJasa;
        $tsLoans->biaya_provisi = $biayaProvisi;
        // $tsLoans->biaya_bunga_berjalan = $biayaBungaBerjalan;
        $tsLoans->status = 'menunggu';
        $tsLoans->save();

        $tsLoansDetail = TsLoansDetail::where('loan_id', $loan_id)->delete();

        $b1 = 1;
        for($i = 0; $i < $tenor; $i++){
            $loan_detail = new TsLoansDetail();
            $loan_detail->loan_id = $loan_id;
            $loan_detail->loan_number = $tsLoans->loan_number;
            $loan_detail->value = $loan_value;
            $loan_detail->service = $biayaJasa;
            $loan_detail->pay_date = Carbon::parse($payDate)->addMonth($i);
            $loan_detail->in_period = $b1 + $i;
            $loan_detail->approval = 'menunggu';
            $loan_detail->save();
        }

        return redirect('persetujuan-pinjaman')->with('success', 'Perubahan pinjaman berhasil');
    }

    public function agreeRevisiLoan(Request $request){
        $input = Input::all();
        $loan_id = \Crypt::decrypt($input['loan_id']);
        $tsLoans = TsLoans::with('ms_loans', 'detail')->find($loan_id);
        $tsLoans->status = 'belum lunas';
        $tsLoans->save();
        foreach ($tsLoans->detail as $loan_detail){
            $loan_detail->approval = 'belum lunas';
            $loan_detail->save();
        }
        return [
            'data' => 1
        ];

    }

    public function getLoans()
    {
        $selected = TsLoans::getLoanArea(auth()->user()->region);

        $this->i  = 1;
        if (request()->ajax()) {
            return DataTables::of($selected)
            ->editColumn('member', function ($selected) {
                return $selected->member['first_name'] . ' ' . $selected->member['last_name'];
            })
            ->editColumn('loan_type', function ($selected) {
                return $selected->ms_loans['loan_name'];
            })
            ->editColumn('loan_number', function ($selected) {
                return $selected->loan_number;
            })
            ->editColumn('value', function ($selected) {
                return 'Rp '. number_format($selected->value);
            })
            ->editColumn('status', function ($selected) {
                $getApproval = Approvals::where([
                    'fk' => $selected->id,
                    'layer' => $selected->approval_level + 1
                ])->first();
                if(!empty($getApproval)){
                    $arrApproval = $getApproval->approval;
                }else{
                    $arrApproval = [];
                }
                // $arrApproval = $getApproval->approval;
                if($selected->status == 'dibatalkan') {
                   $status = 'Telah di dibatalkan';
                } else if($selected->status == 'ditolak') {
                   $position = Position::find($selected->status_by);
                   if(isset($position->name)){
                        $status = 'Ditolak ' . $position->name;
                   }else{
                        $status = 'Ditolak';
                   }
                } else if($selected->status == 'belum lunas') {
                   $status = 'Belum Lunas';
                } else if($selected->status == 'lunas') {
                   $status = 'Lunas';
                } else if($selected->status == 'menunggu') {
                    if($selected->approval_level + 1 == 1){
                        if(auth()->user()->id == $arrApproval['id']){
                            $status = 'Menunggu Persetujuan Anda';
                        }else{
                            $status = 'Menunggu';
                        }
                    }else{
                        $position = Position::find($selected->status_after);
                        if(isset($position->name)){
                            $status = 'Menunggu Persetujuan ' . $position->name;
                        }else{
                            $status = 'Menunggu';
                        }
                    }
                } else {
                    $status = $selected->status;
                }

                return ucwords($status);
            })
            ->addColumn('action',function($selected){
                $idRecord              = \Crypt::encrypt($selected->id);
                $getApproval = Approvals::where([
                    'fk' => $selected->id,
                    'layer' => $selected->approval_level + 1
                ])->first();
                if($selected->status == 'menunggu' && $getApproval->position_id == auth()->user()->position_id) {
                    $action            = '<a  class="btn btn-info btn-sm tooltips" href="/loan-detail/'.$idRecord.'"><i class="ion ion-aperture" title="Liat pengajuan pinjaman"></i></a>
                                          <a class="btn btn-danger tooltips btn-sm"  onclick="modifyData('."'update-loan'".','."'".$idRecord."'".','."'". csrf_token() ."'".','."'listTsLoansAll'".','."'canceled'".')" data-container="body" data-placement="right" data-html="true" title="Batalkan pengajuan" ><i class="fa fa-undo"></i></a>
                                          <a class="btn btn-success tooltips btn-sm"  onclick="modifyData('."'update-loan'".','."'".$idRecord."'".','."'". csrf_token() ."'".','."'listTsLoansAll'".','."'approved'".')" data-container="body" data-placement="right" data-html="true" title="Setujui peminjaman" ><i class="fa fa-check"></i></a>';
                } else if($selected->status == 'menunggu' && (auth()->user()->isSu() || auth()->user()->isPow())) {
                    $action            = '<a  class="btn btn-info btn-sm tooltips" href="/loan-detail/'.$idRecord.'"><i class="ion ion-aperture" title="Liat pengajuan pinjaman"></i></a>
                                          <a class="btn btn-danger tooltips btn-sm"  onclick="modifyData('."'update-loan'".','."'".$idRecord."'".','."'". csrf_token() ."'".','."'listTsLoansAll'".','."'canceled'".')" data-container="body" data-placement="right" data-html="true" title="Batalkan pengajuan" ><i class="fa fa-undo"></i></a>
                                          <a class="btn btn-success tooltips btn-sm"  onclick="modifyData('."'update-loan'".','."'".$idRecord."'".','."'". csrf_token() ."'".','."'listTsLoansAll'".','."'approved'".')" data-container="body" data-placement="right" data-html="true" title="Setujui peminjaman" ><i class="fa fa-check"></i></a>';
                } else{
                    $action            = '<a  class="btn btn-info btn-sm tooltips" href="/loan-detail/'.$idRecord.'"><i class="ion ion-aperture" title="Liat pengajuan pinjaman"></i></a>';
                }
                return
                '<div style="min-width: 120px">'.$action.'</div>';
            })
            ->make(true);
        }
        return view('transaction.loan.ts_loan_all');
    }
    public function addTenor()
    {
        $input           = Input::all();
        if(isset($input['specific'])){
            $id          = $input['loan_id'];
            $getDetail   = TsLoansDetail::find($id);
            if($getDetail){
                    $getLoans = TsLoans::find($getDetail->loan_id);
                    $data     = array(
                                    'error'    => 0,
                                    'msg'      => 'Berhasil.',
                                    'ms_loans' => $getLoans->ms_loans,
                                    'json'     => $getDetail,
                                );
                } else{
                    $data     = array(
                                    'error' => 1,
                                    'msg'   => 'Data cicilan tidak ditemukan.',
                                );
            }
            return response()->json($data);  
        } else {
            $id          = $this->decrypter($input['loan_id']);
            $getDetail   = TsLoansDetail::where('loan_id', $id)
                           ->orderBy('in_period', 'pay_date')
                           ->first();
            $getLoans = TsLoans::find($getDetail->loan_id);
            if($getDetail){
                $service = $getLoans->value * ($getLoans->ms_loans->rate_of_interest / 100);
                // generate new Nomor Pinjaman
                $getLoanNumber     = $this->getLoanNumber();
                $period            = Carbon::parse($getDetail->pay_date);
                $nextMonth         = $period->addMonths(1)->format('Y-m-d');
                $data              = new TsLoansDetail();
                $data->id          = $data::max('id')+1;
                $data->value       = $input['nominal'];
                $data->service     = $service;
                $data->loan_id     = $id;
                $data->pay_date    = $nextMonth;
                $data->loan_number = $getLoans->loan_number;
                $data->in_period   = $getDetail->in_period + 1;
                $data->approval      = 'belum lunas';
                $data->save();

                ++$getLoans->period;
                ++$getLoans->add_period;
                $getLoans->save();
                // get detail loan
                $findDetail   = TsLoansDetail::where('loan_id',$id)->orderBy('in_period')->get();
                if($findDetail){
                    $data     = array(
                                    'error' => 0,
                                    'msg'   => 'Penambahan Tenor baru berhasil.',
                                    'json'   => $findDetail,
                                );
                } else{
                    $data     = array(
                                    'error' => 1,
                                    'msg'   => 'Data cicilan tidak ditemukan.',
                                );
                }
                return response()->json($data);        
            }
        }
    }
	public function changeStatus()
    {
        $input         = Input::all();
        $detail_id     = $input['detail_id'];
        $getDetail     = TsLoansDetail::find($detail_id);
        $loan = TsLoans::where('id', $getDetail->loan_id)->first();
        if($input['status'] == 'belum lunas' || $input['status'] == 'lunas') {
            if ($getDetail) {
                $getDetail->approval = $input['status'];
                if($input['status'] == 'lunas'){
                    $loan->in_period = $getDetail->in_period;
                    $loan->save();
                }
                if($input['status'] == 'belum lunas'){
                    $loan->in_period = $getDetail->in_period - 1;
                    $loan->save();
                }
                $getDetail->save();  
         
            } else {
                $data      = array(
                                'error' => 1,
                                'msg'   => 'Gagal memperbaharui status data.',
                            );
                return response()->json($data);

            }
        } else {
            // rule untuk penangguhan pembayaran
            // update value jadi 0
            // add value to the next month
            if (!empty($getDetail)) {
                $value             = $getDetail->value;
                $tenor             = $getDetail->in_period;
                // find for tenor next month
                $findTenor         = TsLoansDetail::where(['in_period' => $tenor, 'loan_id' => $getDetail->loan_id])->first();
                if($findTenor) {
                    // 0 value
                    $getDetail->approval = $input['status'];
                    $getDetail->value  = 0;
                    $getDetail->service  = 0;
                    // add more value
                    $findTenor->value  = $value + $findTenor->value;
                    // save data
                    $findTenor->save();
                    $getDetail->save();
                } else {
                    $data  = array(
                                'error' => 1,
                                'msg'   => 'Maaf, cicilan untuk bulan depan tidak ditemukan. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                            );
                    return response()->json($data);

                }
         
            } else {
                $data      = array(
                                'error' => 1,
                                'msg'   => 'Gagal memperbaharui status data.',
                            );
                return response()->json($data);

            }
        }
        // get all data again
        $findDetail    = TsLoansDetail::where('loan_id',$getDetail->loan_id)->orderBy('in_period')->get();
        if($findDetail){
            $data      = array(
                            'error' => 0,
                            'msg'   => 'Data berhasil diperbaharui.',
                            'json'   => $findDetail,
                        );
        } else{
            $data      = array(
                            'error' => 1,
                            'msg'   => 'Gagal memperbaharui status data.',
                        );
        }
        return response()->json($data); 
    }
    public function updateStatus()
    {
        $input         = Input::all();
        $loan_id       = $this->decrypter($input['loan_id']);
        $getLoan       = TsLoans::findOrFail($loan_id);
        
        if ($getLoan) {
            if($input['status'] == 'belum lunas'){
                $getLoan->status = $input['status'];
                $getLoan->in_period = 0;
                $getLoan->save();  

                TsLoansDetail::where(['loan_id' => $loan_id])->where('approval', '!=', 'lunas')->update(['approval' => $input['status']]);

            } else if($input['status'] == 'lunas'){
                $getLoan->status = $input['status'];
                $getLoan->in_period = $getLoan->period;
                $getLoan->save(); 

                $countLoanDetail = TsLoansDetail::where(['loan_id' => $loan_id])->where('approval', 'lunas')->count();

                if($countLoanDetail < $getLoan->period){
                    $getLoanDetail = TsLoansDetail::where('loan_id',  $loan_id)
                        ->where(function($q) {
                            $q->where('approval', 'disetujui')
                            ->orWhere('approval', 'belum lunas');
                        })->orderBy('in_period', 'asc')->get();
                    $i = 0;
                    foreach($getLoanDetail as $value){
                        if ($i == 0) {
                            $dataDetail = TsLoansDetail::findOrFail($value->id);
                            $dataDetail->approval = 'lunas';
                            $dataDetail->save();
                        }else{
                            $dataDetail = TsLoansDetail::findOrFail($value->id);
                            $dataDetail->service = 0;
                            $dataDetail->approval = 'lunas';
                            $dataDetail->save();
                        }
                        $i++;
                    }
                }
            }
            // update to lunas 
            
            // update detail loan
            $data      = array(
                        'error' => 0,
                        'msg'   => 'Data berhasil diperbaharui.',
                    );
        } else {
            $data      = array(
                            'error' => 1,
                            'msg'   => 'Gagal memperbaharui status data.',
                        );
            return response()->json($data);

        }
        return response()->json($data); 
    }

    public function checkReschedule(){
        $selected = TsLoans::with('detail')->where('member_id', auth()->user()->member->id)->whereIn('loan_id', array(1, 2))->whereIn('status', array('menunggu', 'disetujui', 'belum lunas'))->first();
        if(!isset($selected->value)){
            session()->flash('errors',collect(['Anda Belum Memiliki Pinjaman Tunai yang Berjalan']));
            return redirect()->back();
        }
        $halfValue = ceil($selected->value / 2);
        $sisa = collect($selected->detail);
        $findLoan = Loan::findOrFail($selected->loan_id);
        $penjamin = ApprovalUser::getPenjamin(auth()->user());
        $dataSisa = $sisa->filter(function ($item)
        {
            return $item->approval == 'menunggu' || $item->approval == 'belum lunas';
        });
        $sisaPinjaman = $dataSisa->sum('value');
        $tenors = [];
        $tenor = 0;
        for ($a=0; $a<$findLoan['tenor']; $a++){
            $tenor += 1;
            array_push($tenors, $tenor);
        }
        $getMember    = Member::findOrFail(Auth::user()->member->id);
        $project      = Project::findOrFail($getMember->project_id);
        $policy       = Policy::where('id', 2)->first();
        $dayBungaBerjalan = cutOff::getDayBungaBerjalan(now()->format('Y-m-d'));
        $checkCount = TsLoans::where('member_id', auth()->user()->member->id)->where('reschedule', 1)->whereYear('created_at', date('Y'))->count();
        if($checkCount <= 3){
            if($sisaPinjaman < $halfValue){
                return view('dashboards.loan-reschedule', compact('selected', 'sisaPinjaman', 'findLoan', 'getMember', 'tenors', 'dayBungaBerjalan', 'penjamin', 'project', 'policy'));
            }else{
                session()->flash('errors',collect(['Sisa Pinjaman Masih Melebihi 50% dari Total Pinjaman']));
                return redirect()->back();
            }
        }else{
            session()->flash('errors',collect(['Anda sudah mengajukan reschedule sebanyak 3 kali tahun ini']));
            return redirect()->back();
        }
    }
}
