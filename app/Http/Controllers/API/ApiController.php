<?php

namespace App\Http\Controllers\API;

use Auth;
use App\Approvals;
use App\Article;
use App\Bank;
use App\TotalDepositMember;
use App\ConfigDepositMembers;
use App\ChangeDeposit;
use App\Deposit;
use App\DepositTransactionDetail;
use App\Helpers\ApprovalUser;
use App\Helpers\cutOff;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\LoadController;
use App\Loan;
use App\MemberPlafon;
use App\Qna;
use App\PencairanSimpanan;
use App\Position;
use App\Region;
use App\Project;
use App\Location;
use App\Policy;
use App\Member;
use App\Notifications\LoanApprovalNotification;
use App\Notifications\LoanApplicationStatusUpdated;
use App\Notifications\LoanApplicationStatusRejected;
use App\Notifications\WaitLoanApplication;
use App\Notifications\WaitPencairanSimpananApplication;
use App\Notifications\WaitChangeDepositApplication;
use App\Notifications\WaitPenambahanSimpananApplication;
use App\Notifications\WaitResignApplication;

use App\Helpers\ResponseHelper;
use App\Resign;
use App\TsDeposits;
use App\TsLoansDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MemberRegistrationRequest;

use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Validator;
use App\Exceptions\UnauthorizeFeatureException;
use App\Exceptions\UnhandledException;
use NotificationChannels\OneSignal\OneSignalChannel;
use OneSignal;
use App\TsLoans;
use App\DepositTransaction;
use App\User;
use App\ApprovalLevelPinjaman;
use DB;
use Mail; 
use Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator as Validator2;

class ApiController extends GlobalController
{
	//
	public function regions()
	{
		$region = Region::get();
		$data = [
			'status' => 'success',
			'region' => $region
		];
		return $data;
	}

	public function projects()
	{
		$project = Project::get();
		$data = [
			'status' => 'success',
			'project' => $project
		];
		return $data;
	}

	public function locations()
	{
		$location = Location::get();
		$data = [
			'status' => 'success',
			'location' => $location
		];
		return $data;
	}

	public function getPolicy($id)
	{
		$policy = Policy::where('id', $id)->first();
		$data = [
			'status' => 'success',
			'policy' => $policy
		];
		return $data;
	}

    public function getQna()
    {
        $DataQna = Qna::where('id', '!=', 2)->get();
        $data = [
            'status' => 'success',
            'qna' => $DataQna
        ];
		return $data;
    }

	public function register(Request $request){
        $global = new GlobalController();

        $input = Input::all();
//    	$getPosition           = Position::where('name', 'like', '%'.$input['position_id'].'%')->first();
        $getPosition = Position::where('name', 'like', '%anggota%')->first();
        $getPokok = Deposit::find(1);
        $getWajib = Deposit::find(2);
        $getSukarela = Deposit::find(3);

        $find = User::where('email', $request->email)->count();
        $findM = Member::where('nik_bsp', $request->nik_bsp)->count();

        if($find  != null || $findM != null){
            if($find){
                $msg  = 'Maaf, alamat email  yang anda masukkan sudah dipakai sebelumnya.';
            }else{
                $msg = 'Maaf, NIK Koperasi  yang anda masukkan sudah dipakai sebelumnya.';
            }
            $data =  [
                'error' => true,
                'message' => $msg
            ];
            return $data;
        }

        if ($input['wajib'] == "0" || $input['wajib'] == '') {
            $data =  [
                'error' => true,
                'message' => "Simpanan wajib tidak boleh kosong atau 0."
            ];
            return $data;
        }


        $user = new User();
        $user->id = $user::max('id') + 1;
        $user->name = strtoupper($input['fullname']);
        $user->email = $input['email'];
        $user->username = $global->getBspNumber();

        // send start email
        $LoadController = new LoadController();
        $LoadController->sendEmail($user->username, $input['email'], $input['password']);
        // send end   email
        $user->password = \Hash::make($input['password']);
        $user->position_id = $getPosition->id;
        $user->save();
        //assign role based on position->level
        $user->assignRole($getPosition->level->name);

        // insert into table member
        $member = new Member();
        $member->nik_bsp = $input['nik_bsp'];
        $member->nik_koperasi = $user->username;
        $member->first_name = strtoupper($input['fullname']);
        $member->nik = $input['nik'];
        $member->user_id = $user->id;
        $member->email = $user->email;
        $member->salary = $input['salary'];
        $member->position_id = $user->position_id;
        $member->join_date = new \DateTime();
        $member->save();

        // insert into table deposit
        //Deposit Wajib
        $depositWajib = new DepositTransaction();
        $depositWajib->member_id = $member->id;
        $depositWajib->ms_deposit_id = $getWajib->id;
        $depositWajib->deposit_number = $global->getDepositNumber();
        $depositWajib->total_deposit = (int)$input['wajib'];
        $depositWajib->deposits_type = 'wajib';
        $depositWajib->type = 'debit';
        $depositWajib->post_date = cutOff::getCutoff();
        $depositWajib->save();

        $depositWajibDetail = new DepositTransactionDetail();
        $depositWajibDetail->transaction_id = $depositWajib->id;
        $depositWajibDetail->deposits_type = 'wajib';
        $depositWajibDetail->debit = (int)$input['wajib'];
        $depositWajibDetail->credit = 0;
        $depositWajibDetail->payment_date = $depositWajib->post_date;
        $depositWajibDetail->total = (int)$input['wajib'];
        $depositWajibDetail->save();
        // End Deposit Wajib

        // Deposit Sukarela
        if ($input['sukarela'] != "0") {
            $depositSukarela = new DepositTransaction();
            $depositSukarela->member_id = $member->id;
            $depositSukarela->ms_deposit_id = $getSukarela->id;
            $depositSukarela->deposit_number = $global->getDepositNumber();
            $depositSukarela->total_deposit = (int)$input['sukarela'];
            $depositWajib->deposits_type = 'sukarela';
            $depositSukarela->type = 'debit';
            $depositSukarela->post_date = cutOff::getCutoff();
            $depositSukarela->save();

            $depositSukarelaDetail = new DepositTransactionDetail();
            $depositSukarelaDetail->transaction_id = $depositSukarela->id;
            $depositSukarelaDetail->deposits_type = 'sukarela';
            $depositSukarelaDetail->debit = (int)$input['sukarela'];
            $depositSukarelaDetail->credit = 0;
            $depositSukarelaDetail->payment_date = $depositSukarela->post_date;
            $depositSukarelaDetail->total = (int)$input['sukarela'];
            $depositSukarelaDetail->save();
        }
        // End Deposit Sukarela

        // Deposit Pokok
        if ($input['pemotongan'] == 1) {

            $depositPokok = new DepositTransaction();
            $depositPokok->member_id = $member->id;
            $depositPokok->ms_deposit_id = $getPokok->id;
            $depositPokok->deposit_number = $global->getDepositNumber();
            $depositPokok->total_deposit = $getPokok->deposit_minimal;
            $depositWajib->deposits_type = 'pokok';
            $depositPokok->type = 'debit';
            $depositPokok->post_date = cutOff::getCutoff();
            $depositPokok->save();

            $depositPokokDetail = new DepositTransactionDetail();
            $depositPokokDetail->transaction_id = $depositPokok->id;
            $depositPokokDetail->deposits_type = 'pokok';
            $depositPokokDetail->debit = (int)$getPokok->deposit_minimal;
            $depositPokokDetail->credit = 0;
            $depositPokokDetail->payment_date = $depositPokok->post_date;
            $depositPokokDetail->total = (int)$getPokok->deposit_minimal;
            $depositPokokDetail->save();

        } else {
            for ($i = 1; $i <= $input['pemotongan']; $i++) {
                $depositPokok = new DepositTransaction();
                $depositPokok->member_id = $member->id;
                $depositPokok->ms_deposit_id = $getPokok->id;
                $depositPokok->type = 'debit';
                $depositPokok->deposit_number = $global->getDepositNumber();
                $depositPokok->total_deposit = $getPokok->deposit_minimal / 2;
                $depositWajib->deposits_type = 'pokok';
                $depositPokok->post_date = cutOff::getCutoff();
                $depositPokok->save();

                $depositPokokDetail = new DepositTransactionDetail();
                $depositPokokDetail->transaction_id = $depositPokok->id;
                $depositPokokDetail->deposits_type = 'pokok';
                $depositPokokDetail->debit = $getPokok->deposit_minimal / 2;
                $depositPokokDetail->credit = 0;
                $depositPokokDetail->payment_date = $depositPokok->post_date;
                $depositPokokDetail->total = $getPokok->deposit_minimal / 2;
                $depositPokokDetail->save();
            }
        }

        $this->configDeposit($member->id, 'wajib', $input['wajib']);
        $this->configDeposit($member->id, 'pokok', $getPokok->deposit_minimal);
        $this->configDeposit($member->id, 'sukarela', $input['sukarela']);

        $data = array(
            'error' => 0,
            'msg' => 'Akun berhasil dibuat. Silahkan check email anda untuk proses aktivasi.',
        ); 

        $admins = User::FAdminRegister()->get();
        $member->newMemberBlastTo($admins, ['database', OneSignalChannel::class]);

        $data =  [
            'error' => false,
            'message' => "Akun berhasil dibuat. Silahkan check email anda untuk proses aktivasi",
        ];
        return $data;
	}

    public function configDeposit($member, $type, $value){
        $configDeposit = new ConfigDepositMembers;
        $configDeposit->member_id = $member;
        $configDeposit->type = $type;
        $configDeposit->value = $value;
        $configDeposit->save();
    }

	public function crontest()
	{
		\Log::info('via crontab');
	}

	public function postOnesignal()
	{
		$parameters = array(
			'large_icon' => 'https://www.dropbox.com/s/9wevk72p5v5s17b/bsp.png?dl=1',
			'headings' => array(
				'en' => 'halo semuanya ini heading'
			),
			'included_segments' => array('All'),
			'subtitle' => array(
				'en' => 'halo semuanya ini subtitle'
			),
			'contents' => array(
				'en' => 'ini isi dari berita notifikasi'
			),
			'data' => array(
				'user_id' => 2,
				'id' => 1,
				'type' => 'berita'
			),
			'android_accent_color' => 'FFFF0000',
			'big_picture' => 'http://www.bspguard.co.id/wp-content/uploads/2015/09/Slide-2-1.jpg',
			'android_sound' => 'goodmorning'
			// 'android_background_layout' => array(
			// 	'image' => 'https://www.urbanairship.com/images/uploads/blog/push-notification-examples-ios-screenshots.jpg',
			// 	'headings_color' => 'FFFF0000',
			// 	'contents_color' => '000000'
			// )
		);
		$push = OneSignal::sendNotificationCustom($parameters);


		return $push;
	}

	public function getProfile($id){

	   try {
			$getData   = User::leftJoin('ms_members', function($join) {
						$join->on('ms_members.user_id', '=', 'users.id');
						})
						->leftJoin('ms_banks', function($join) {
							$join->on('ms_members.id', '=', 'ms_banks.member_id');
						})
						->leftJoin('positions', function($join) {
							$join->on('ms_members.position_id', '=', 'positions.id');
						})
						->where('users.id', $id)
						->first();

				$data = [
					'status' => 'success',
					'profile' => $getData
				];
				return $data;


	   } catch (\Throwable $th) {
				$data = [
					'status' => 'failed',
					'profile' => ''
				];
				return $data;
	   }
	}

	public function getDataDashboard(Request $request){
		try {
				 $user = auth()->user();
				 $member = $user->member;
                 $path = public_path('images/');
                // if($member['picture'] != null){
                //     $picture = url()->previous().'/images/'.$member->picture;
                // }else{
                //     $picture = url()->previous().'/images/security-guard.png';
                // }
                if($member['picture'] != null){
                    $picture = $member->picture;
                }else{
                    $picture = 'security-guard.png';
                }
				 $totalDeposit = TotalDepositMember::where('member_id', auth()->user()->member->id)->sum('value');
				 $totalLoan = TsLoans::totalLoans($member["id"]);
                //  var_dump($totalLoan);
                if(file_exists($path . $picture)) {
                    $user->member["picture"] = base64_encode(file_get_contents($path . $picture));
                }else{
                    $user->member["picture"] = "";
                }
				 $user->member["total_deposit"] =  number_format($totalDeposit);
				 $user->member["total_loan"] = number_format($totalLoan);

                 

				 $data = [
					 'status' => 'success',
					 'error' => '',
					 'member' => $user->member
				 ];
				 return $data;

		} catch (\Throwable $th) {
				 $data = [
					 'status' => 'failed',
					 'member' => '',
					 'error' => $th
				 ];
				 return $data;

		}

	 }

    public function getlocation(){
         $LocationDet = Region::with('branch', 'project.locations')->where('id',2)->get();
         return $LocationDet;
    }

    public function getJabatan(){
        $position = Position::fMemberOnly()->get();
        $data = [
            'status' => 'success',
            'jabatan' => $position
        ];
        return $data;
    }

    public function getDeposit(){
        try {
            $deposit = Deposit::get();
            $data = [
                'status' => 'success',
                'data' => $deposit,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function getLoan(){
        try {
            $getUser = User::findOrFail(auth()->user()->id);
            $position = Position::find($getUser->position_id);
            // if($position->level_id == 18 || $position->level_id == 14){
            //     $data     = Loan::Publish()->whereNotIn('id', array(15, 16))->get();
            // }else if($position->level_id == 18 || $position->level_id == 14){
            //     $data     = Loan::Publish()->get();
            // }else{
            //     $data     = Loan::Publish()->whereNotIn('id', array(15, 16, 17))->get();
            // }
            if($position->level_id == 14){
                $data     = Loan::Publish()->whereNotIn('id', array(17))->get();
            }else{
                $data     = Loan::Publish()->get();
            }
            $data->dayBungaBerjalan = cutOff::getDayBungaBerjalan(now()->format('Y-m-d'));
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function myLoan(){
        try {
            // $getMember = Member::findOrFail(auth()->user()->member->id);
            // $position = Position::find($getMember->position_id);
            // if($position->level_id == 18 || $position->level_id == 14){
            //     $data = TsLoans::with('ms_loans:id,loan_name,logo')->where('member_id', auth()->user()->member->id)->get();
            // }else{
            //     $data = TsLoans::with('ms_loans:id,loan_name,logo')->where('member_id', auth()->user()->member->id)->where('id', '!=', 17)->get();
            //     // $getLoans     = Loan::Publish()->where('id', '!=', 17)->get();
            // }
            $data = TsLoans::with('ms_loans:id,loan_name,logo')->where('member_id', auth()->user()->member->id)->get();
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function checkReschedule(){
        try {
            $selected = TsLoans::with('detail')->where('member_id', auth()->user()->member->id)->whereIn('loan_id', array(1, 2))->whereIn('status', array('menunggu', 'disetujui', 'belum lunas'))->first();
            if(!isset($selected->value)){
                $data = [
                    'status' => 'failed',
                    'error' => 'Anda Belum Memiliki Pinjaman Tunai yang Berjalan'
                ];
                return $data;
            }
            $halfValue = ceil($selected->value / 2);
            $sisa = collect($selected->detail);
            $findLoan = Loan::findOrFail($selected->loan_id);
            $penjamin = ApprovalUser::getPenjamin(auth()->user());
            $dataSisa = $sisa->filter(function ($item)
            {
                return $item->approval == 'menunggu' || $item->approval == 'disetujui' || $item->approval == 'belum lunas';
            });
            $sisaPinjaman = $dataSisa->sum('value') + $dataSisa->sum('service');
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
            $listSisaPinjaman = TsLoans::where('member_id', Auth::user()->member->id)->where(function($q) {
                $q->where('status', 'menunggu')
                  ->orWhere('status', 'belum lunas')->orWhere('status', 'disetujui');
            })->with('ms_loans', 'detail')->get();
            $selected->sisaPinjaman = $sisaPinjaman;
            $selected->findLoan = $findLoan;
            $selected->getMember = $getMember;
            $selected->tenors = $tenors;
            $selected->dayBungaBerjalan = $dayBungaBerjalan;
            $selected->penjamin = $penjamin;
            $selected->project = $project;
            $selected->policy = $policy;
            $selected->listSisaPinjaman = $listSisaPinjaman;
            $checkCount = TsLoans::where('member_id', auth()->user()->member->id)->where('reschedule', 1)->whereYear('created_at', date('Y'))->count();
            if($checkCount <= 3){
                if($sisaPinjaman < $halfValue){
                    $data = [
                        'status' => 'success',
                        'data' => $selected,
                        'error' => false
                    ];
                }else{
                    $data = [
                        'status' => 'failed',
                        'error' => 'Sisa Pinjaman Masih Melebihi 50% dari Total Pinjaman'
                    ];
                }
            }else{
                $data = [
                    'status' => 'failed',
                    'error' => 'Anda sudah mengajukan reschedule sebanyak 3 kali tahun ini'
                ];
            }
            
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function list_approval($loanId)
    {
        try {
            $data = Approvals::where(['fk' => $loanId])->with('position')->get();
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function sisaLoan($id, $loanId)
    {
        try {
            $data = TsLoans::where('member_id', $id)->where('loan_number', '!=', $loanId)->where(function($q) {
                $q->where('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('ms_loans', 'detail')->get();
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function sisaLoanReschedule($id)
    {
        try {
            $data = TsLoans::where('member_id', $id)->where(function($q) {
                $q->where('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('ms_loans', 'detail')->get();
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function listUnpaidLoan()
    {
        try {
            $data = TsLoans::where(function($q) {
                $q->where('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('ms_loans', 'detail')->get();
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function myDetailLoan($id){
        try {
            $data = TsLoans::with('detail','ms_loans:id,loan_name,logo')->find($id);
            $member              = Member::findOrFail($data->member_id);
            $path                = public_path('images/pinjaman/');
            $lampiran            = (isset($data->attachment)) ? base64_encode(file_get_contents($path . $data->attachment)) : '';
            $penjamin            = User::where('id', $data->penjamin)->first();
            $project             = Project::findOrFail($member->project_id);
            $getUser             = User::findOrFail(auth()->user()->id);
            $getPosition         = Position::findOrFail($getUser->position_id);
            $loanData            = TsLoans::where('member_id', $data->member_id)->where(function($q) {
                $q->where('status', 'disetujui')
                ->orWhere('status', 'belum lunas')
                ->orWhere('status', 'lunas');
            })->orderBy('value', 'desc')->get();
            $loanDataSisa            = TsLoans::where('member_id', $data->member_id)->where(function($q) {
                $q->where('status', 'disetujui')
                ->orWhere('status', 'belum lunas');
            })->with('detail')->get();
            $loanDataDetail      = TsLoansDetail::where('loan_number', $data->loan_number)->get();
            $totalPengajuan      = $loanData->count();
            $pinjamanTerbesar    = ($totalPengajuan > 0) ? $loanData[0] : 0;
            $dataSimpananWajib   = TotalDepositMember::totalDepositWajib($data->member_id);
            $dataSimpananSukarela   = TotalDepositMember::totalDepositSukarela($data->member_id);
            $sisa                = collect($loanDataDetail);
            $dataSisa = $sisa->filter(function ($item)
            {
                return $item->approval == 'disetujui' || $item->approval == 'belum lunas';
            });
            if($dataSisa->sum('value') == 0){
                $sisaPinjaman        = 0;
            }else{
                $sisaPinjaman        = $dataSisa->sum('value') + $data->biaya_jasa;
            }
            $getApproval = Approvals::where([
                'fk' => $data->id,
                'layer' => $data->approval_level + 1
            ])->first();
            if(!empty($getApproval)){
                $arrApproval = $getApproval->approval;
                $getApprovalUser = User::where('id', $arrApproval['id'])->first();
            }else{
                $getApprovalUser = [];
            }
            $status = $data->status;
            if($data->status == 'dibatalkan') {
                $status = 'Telah di dibatalkan';
            } else if($data->status == 'ditolak') {
                $position = Position::find($data->status_by);
                if(isset($position->name)){
                        $status = 'Ditolak ' . $position->name;
                }else{
                        $status = 'Ditolak';
                }
            } else if($data->status == 'belum lunas') {
                $status = 'Belum Lunas';
            } else if($data->status == 'lunas') {
                $status = 'Lunas';
            } else if($data->status == 'menunggu') {
                if($data->approval_level + 1 == 1){
                    if(auth()->user()->id == $arrApproval['id']){
                        $status = 'Menunggu Persetujuan Anda';
                    }else{
                        $status = 'Menunggu';
                    }
                }else{
                    $position = Position::find($data->status_after);
                    if(isset($position->name)){
                        $status = 'Menunggu Persetujuan ' . $position->name;
                    }else{
                        $status = 'Menunggu';
                    }
                }
            }
            $data = [
                'status' => 'success',
                'data' => $data,
                'member' => $member,
                'penjamin' => $penjamin,
                'project' => $project,
                'totalPengajuan' => $totalPengajuan,
                'pinjamanTerbesar' => $pinjamanTerbesar,
                'totalSimpananWajib' => $dataSimpananWajib,
                'totalSimpananSukarela' => $dataSimpananSukarela,
                'sisaPinjaman' => $sisaPinjaman,
                'getApproval' => $getApproval,
                'getApprovalUser' => $getApprovalUser,
                'layer' => $data->approval_level + 1,
                'lampiran' => $lampiran,
                'status_data' => ucwords($status),
                'position' => $getPosition,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function news(){
        try {
            $data = Article::get();
            $path = public_path('images/news/');
            foreach($data as $value){
                if($value->image_name != null){
                    $picture = $value->image_name;
                }else{
                    $picture = 'bsp.png';
                }
                if(file_exists($path . $picture)) {
                    $value->image_name = base64_encode(file_get_contents($path . $picture));
                }else{
                    $value->image_name = "";
                }
            }
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function sliderNews(){
        try {
            $data = Article::limit(5)->orderBy('id', 'DESC')->get();
            $path = public_path('images/news/');
            foreach($data as $value){
                if($value->image_name != null){
                    $picture = $value->image_name;
                }else{
                    $picture = 'bsp.png';
                }
                if(file_exists($path . $picture)) {
                    $value->image_name = base64_encode(file_get_contents($path . $picture));
                }else{
                    $value->image_name = "";
                }
            }
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function filterLoan(Request $request){
        try {
            $member_id = auth()->user()->member->id;
            $r = in_array('semua', $request->input('filter'), true);
            $data = TsLoans::with('ms_loans:id,loan_name,logo')->where('member_id', $member_id)->get();
            if($request->input('filter') == []){
                $data = TsLoans::with('ms_loans:id,loan_name,logo')->where('member_id', $member_id)->get();
            }
            if(!$r && $request->input('filter') != []){
                $data = TsLoans::with('ms_loans:id,loan_name,logo')->whereIn('approval', $request->input('filter'))->where('member_id', $member_id)->get();
            }
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function myDeposit(){
        try {
            $simpananMember = TotalDepositMember::with('ms_deposit:id,deposit_name')->where('member_id', auth()->user()->member->id)->get();

            $data = $simpananMember;
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function myDetailDeposit($id){
        try {
            $data = TsDeposits::with('detail','ms_deposit:id,deposit_name')
                ->where('member_id', auth()->user()->member->id)
                ->where('ms_deposit_id', $id)
                ->get();
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function filterDeposit(Request $request, $id){
        try {
            $member_id = auth()->user()->member->id;
            $r = in_array('semua', $request->input('filter'), true);
            $data = TsDeposits::with('detail','ms_deposit:id,deposit_name')
                ->where('member_id', auth()->user()->member->id)
                ->where('ms_deposit_id', $id);
            if($request->input('filter') == []){
                $data = $data->get();
            }
            if(!$r && $request->input('filter') != []){
                $terbaru = in_array('terbaru', $request->input('filter'), true);
                $terlama = in_array('terlama', $request->input('filter'), true);
                $paid = in_array('paid', $request->input('filter'), true);
                $unpaid = in_array('unpaid', $request->input('filter'), true);
                $debit = in_array('debit', $request->input('filter'), true);
                $credit = in_array('credit', $request->input('filter'), true);

                if($terbaru){
                    $data = $data->orderBy('post_date', 'DESC');
                }
                if($terlama){
                    $data = $data->orderBy('post_date', 'ASC');
                }
                if($paid){
                    $data = $data->where('status', 'paid');
                }
                if($unpaid){
                    $data = $data->where('status', 'unpaid');
                }
                if($debit){
                    $data = $data->where('type', 'debit');
                }
                if($credit){
                    $data = $data->orderBy('type', 'credit');
                }

                $data = $data->get();
            }
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function myProfile(){
        try {

            $member = Member::with('position:id,name', 'project:id,project_name', 'region:id,name_area')->where('id', auth()->user()->member->id)->first();
            $path = public_path('images/');
            // if($member->picture != null){
            //     $member['picture'] = url()->previous().'/images/'.$member->picture;
            // }else{
            //     $member['picture'] = url()->previous().'/images/security-guard.png';
            // }
            if($member->picture != null){
                $picture = $member->picture;
            }else{
                $picture = 'security-guard.png';
            }
            if(file_exists($path . $picture)) {
                $member['picture'] = base64_encode(file_get_contents($path . $picture));
            }else{
                $member['picture'] = "";
            }
            $data = [
                'status' => 'success',
                'data' => $member,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function listResign(){
        try {
            $query = Resign::with('member')->get();
            $data = [
                'status' => 'success',
                'data' => $query,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
	}

    public function postResign(Request $request){
        try {
            $offTrans = false;
            if ($offTrans) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Transaksi ini sedang tidak dapat dilakukan.',
                    'error' => true
                ];

                return $data;
            }

            $spcMember = Member::where('user_id', auth()->user()->id)->first();
            $checkRsn  = $this->checkRsn($spcMember->id);
            if ($checkRsn) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Anda telah melakukan pengajuan pengunduran diri. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                    'error' => true
                ];
                return $data;
            }
            // cek simpanan cukup untuk menutup hutang
            $close = $this->close($spcMember->id);
            if ($close) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Pengunduran diri tidak bisa dilakukan. Karena, simpanan anda tidak cukup untuk menutup pinjaman yang belum lunas.',
                    'error' => true
                ];
                return $data;
            }
            // jika validasi terlewati
            $newRsn = new Resign();
            $newRsn->member_id = $spcMember->id;
            $newRsn->date = $request->date;
            $newRsn->reason = $request->reason;
            $newRsn->status = 'waiting';
            $newRsn->save();

            // $approvals = User::FUserApproval()->get();
            // $newRsn->newResignBlastTo($approvals, ['database', OneSignalChannel::class]);

            if($spcMember->position_id == 14){
                if(isset(auth()->user()->region['id'])){
                    $getApproveUser = User::where('region_id', auth()->user()->region['id'])
                    ->where('position_id', 13)->first();
                }else{
                    $getApproveUser = User::where('position_id', 13)->first();
                }
        
                if(empty($getApproveUser)){
                    $getApproveUser = User::where('position_id', 13)->first();
                }

                $getApproveUser->notify(new WaitResignApplication($newRsn)); 
        
                // var_dump($getApproveUser);
                // foreach($getApproveUser as $value){
                //     $value->notify(new WaitResignApplication($newRsn)); 
                // }
            }else if($spcMember->position_id == 20){
                if(isset(auth()->user()->region['id'])){
                    $getApproveUser = User::where('region_id', auth()->user()->region['id'])
                    ->where('position_id', 22)->first();
                }else{
                    $getApproveUser = User::where('position_id', 22)->first();
                }
        
                if(empty($getApproveUser)){
                    $getApproveUser = User::where('position_id', 22)->first();
                }
                
                $getApproveUser->notify(new WaitResignApplication($newRsn)); 

                // var_dump($getApproveUser);
                // foreach($getApproveUser as $value){
                //         $value->notify(new WaitResignApplication($newRsn)); 
                // }
            }

            $data = [
                'status' => 'success',
                'data' => $newRsn,
                'message' => 'Pengunduran diri berhasil diajukan',
                'error' => false
            ];

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function approveResign(Request $request){
        try {
            $user = auth()->user();
            $listApprovalPositionAnggota = [13, 12, 9, 8, 7, 6];
            $listApprovalPositionPengelola = [22, 9, 8, 7, 6];

            $approval = $request->input('status');
            $note = $request->input('note');
            $id = $request->input('id');
            $user = User::find(auth()->user()->id);
            $position = Position::find($user->position_id);

            $resign = Resign::find($id);
            $getMember = Member::find($resign->member_id);
            if(!empty($resign)) {
                if(($position->level_id == 12 || $position->level_id == 22) && !isset($request->images)){
                    $data = [
                        'status' => 'failed',
                        'message' => 'Lampiran wajib diisi',
                        'error' => true
                    ];

                    return $data;
                }

                $name = '';
                if (isset($request->images)) {
                    $imgdata = base64_decode($request->images);
                    $f = finfo_open();
                    $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                    $fileName = time() . $imgdata->getClientOriginalName().'.'.explode('/', $mime_type)[1];
                    file_put_contents(public_path('file/resign/') . $fileName, $imgdata);
                }
                
                if ($approval == 'approved') {
                    $resign->status = $approval;
                } elseif ($approval == 'rejected') {
                    $resign->status = $approval;
                } else {
                    $resign->status = $approval;
                }
                $resign->attachment = $fileName;
                $resign->approval_level = $resign->approval_level + 1;
                $resign->status_by = $position->level_id;
                $resign->note = $note;
                $resign->update();

                if(($position->level_id == 6) && $approval == 'approved'){
                    $memberResign = Member::find($resign->member_id);
                    $memberResign->is_active = 0;
                    $memberResign->status = 'resign';
                    $memberResign->save();
                }

                if($getMember->position_id == 14){
                    if($approval == 'approved'){
                        if(isset(auth()->user()->region['id'])){
                            $getApproveUser = User::where('region_id', auth()->user()->region['id'])
                            ->where('position_id', $listApprovalPositionAnggota[$resign->approval_level])->first();
                        }else{
                            $getApproveUser = User::where('position_id', $listApprovalPositionAnggota[$resign->approval_level])->first();
                        }
        
                        if(empty($getApproveUser)){
                            $getApproveUser = User::where('position_id', $listApprovalPositionAnggota[$resign->approval_level])->first();
                        }

                        $getApproveUser->notify(new WaitResignApplication($resign)); 
        
                        // var_dump($getApproveUser);
                        // foreach($getApproveUser as $value){
                        // 	$value->notify(new WaitResignApplication($resign)); 
                        // }
                    }else if($approval == 'rejected'){
                        if(isset(auth()->user()->region['id'])){
                            $getApproveUser = User::where('region_id', auth()->user()->region['id'])
                            ->whereIn('position_id', $listApprovalPositionAnggota)->first();
                        }else{
                            $getApproveUser = User::whereIn('position_id', $listApprovalPositionAnggota)->first();
                        }
        
                        if(empty($getApproveUser)){
                            $getApproveUser = User::whereIn('position_id', $listApprovalPosition)->first();
                        }

                        $getApproveUser->notify(new ResignApplicationStatusRejected($resign)); 

                        foreach($getApproveUser as $value){
                            $value->notify(new ResignApplicationStatusRejected($resign)); 
                        }

                        $resign->member->user->notify(new ResignApplicationStatusRejected($resign));
                    }
                }else if($getMember->position_id == 20){
                    if($approval == 'approved'){
                        if(isset(auth()->user()->region['id'])){
                            $getApproveUser = User::where('region_id', auth()->user()->region['id'])
                            ->where('position_id', $listApprovalPositionPengelola[$resign->approval_level])->get();
                        }else{
                            $getApproveUser = User::where('position_id', $listApprovalPositionPengelola[$resign->approval_level])->get();
                        }
        
                        if(empty($getApproveUser)){
                            $getApproveUser = User::where('position_id', $listApprovalPositionPengelola[$resign->approval_level])->get();
                        }
        
                        // var_dump($getApproveUser);
                        foreach($getApproveUser as $value){
                            $value->notify(new WaitResignApplication($resign)); 
                        }
                    }else if($approval == 'rejected'){
                        if(isset(auth()->user()->region['id'])){
                            $getApproveUser = User::where('region_id', auth()->user()->region['id'])
                            ->whereIn('position_id', $listApprovalPositionPengelola)->get();
                        }else{
                            $getApproveUser = User::whereIn('position_id', $listApprovalPositionPengelola)->get();
                        }
        
                        if(empty($getApproveUser)){
                            $getApproveUser = User::whereIn('position_id', $listApprovalPositionPengelola)->get();
                        }

                        foreach($getApproveUser as $value){
                            $value->notify(new ResignApplicationStatusRejected($resign)); 
                        }

                        $resign->member->user->notify(new ResignApplicationStatusRejected($resign));
                    }
                }

                if($approval == 'approved'){
                    $resign->member->user->notify(new ResignApplicationStatusUpdated($resign));
                }

                $data = [
                    'status' => 'success',
                    'message' => 'Berhasil diupdate.',
                    'error' => false
                ];

                return $data;
            }else{
                $data = [
                    'status' => 'failed',
                    'message' => 'Gagal diupdate.',
                    'error' => true
                ];

                return $data;
            }
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
	}

    public function getStatusResign(Request $request){
        try {
            $input               = Input::all();
            $idRecord            = $input['id'];
            $selected            = Resign::findOrFail($idRecord);
            $position            = Position::find(auth()->user()->position_id);
            $member              = Member::find($selected->member_id);
            $position_approval   = Position::find($selected->status_by);
            $selected->position  = $position;
            $selected->member    = $member;
            $selected->position_approval = $position_approval;
            $selected->position_user = auth()->user()->position_id;
            if($selected){
                $data = [
                    'status' => 'success',
                    'data' => $selected,
                    'error' => false
                ];

                return $data;

            } else{
                $data = [
                    'status' => 'failed',
                    'message' => 'Data resign tidak ditemukan.',
                    'error' => true
                ];

                return $data;
            }
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
	}

    public function updateRevisiLoan(Request $request){
        try {
            $loan_id = $request->loan_id;
            $value = $request->value;
            $tenor = $request->tenor;
            $biaya_admin = $request->biaya_admin;
            $biaya_transfer = $request->biaya_transfer;
            $biaya_bunga_berjalan = $request->biaya_bunga_berjalan;
            $keterangan = $request->description;

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

            $data = [
                'status' => 'success',
                'message' => 'Perubahan Pinjaman berhasil dilakukan',
                'error' => false
            ];

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function postRetrieveDeposit(Request $request){
        $offTrans = false;
        if ($offTrans) {
            $data = [
                'status' => 'failed',
                'message' => 'Transaksi ini sedang tidak dapat dilakukan.',
                'error' => true
            ];

            return $data;
        }
        $member = auth()->user()->member;
        $sukarela = TotalDepositMember::totalDepositSukarela($member->id);
        if($request->jumlah > $sukarela){
            $data = [
                'status' => 'failed',
                'message' => 'Dana yang diajukan lebih besar dari jumlah simpanan.',
                'error' => true
            ];
            return $data;
        }
        $bank =  $member->bank[0];

        if(empty($bank)){
            $data = [
                'status' => 'failed',
                'message' => 'Anda belum memiliki data bank, silahkan tambahkan informasi bank anda.',
                'error' => true
            ];
            return $data;
        }

        $pencairan = new PencairanSimpanan();
        $pencairan->member_id = $member->id;
        $pencairan->bank_id = $bank->id;
        $pencairan->jumlah = $request->jumlah;
        $pencairan->date = $request->date;
        $pencairan->phone = $member->phone_number;
        $pencairan->status = 'waiting';
        $pencairan->save();

        if(isset(auth()->user()->region['id'])){
            $getApproveUser = User::where('region_id', auth()->user()->region['id'])
            ->where('position_id', 9)->get();
        }else{
            $getApproveUser = User::where('position_id', 9)->get();
        }

        if(empty($getApproveUser)){
            $getApproveUser = User::where('position_id', 9)->get();
        }

        // var_dump($getApproveUser);
        foreach($getApproveUser as $value){
            $value->notify(new WaitPencairanSimpananApplication($pencairan)); 
        }

        $data = [
            'status' => 'success',
            'data' => $pencairan,
            'message' => 'Pengajuan pencairan berhasil',
            'error' => false
        ];

        return $data;
    }

    public function ajukanPerubahanSimpanan(Request $request){
        $member = auth()->user()->member;

        if($request->wajib == 0 || $request->wajib == ''){
            $data = [
                'status' => 'failed',
                'message' => 'Dana yang diubah tidak boleh nol 0',
                'error' => true
            ];
            return $data;
		}

        $perubahan = new ChangeDeposit();
		$perubahan->member_id = $member->id;
		$perubahan->date = now()->format('Y-m-d');
		$perubahan->last_wajib = $request->last_wajib;
		$perubahan->last_sukarela = $request->last_sukarela;
		$perubahan->new_wajib = $request->wajib;
		$perubahan->new_sukarela = $request->sukarela;
		$perubahan->status = false;
		$perubahan->save();

        // $users = User::where('id', 5)->whereNotNull('os_token')->get();
		// $perubahan->blastTo($users,['mail',OneSignalChannel::class]);

        if(isset(auth()->user()->region['id'])){
            $getApproveUser = User::where('region_id', auth()->user()->region['id'])
            ->where('position_id', 9)->get();
        }else{
            $getApproveUser = User::where('position_id', 9)->get();
        }

        if(empty($getApproveUser)){
            $getApproveUser = User::where('position_id', 9)->get();
        }

        // var_dump($getApproveUser);
        foreach($getApproveUser as $value){
            $value->notify(new WaitChangeDepositApplication($perubahan)); 
        }

        $data = [
            'status' => 'success',
            'message' => 'Pengajuan perubahan simpanan berhasil',
            'error' => false
        ];

        return $data;
    }

    public function changeAvatar(Request $request){
        $id = auth()->user()->member->id;
        $getMember = Member::findOrFail($id);
        if(isset($request->images)) {
            if($getMember->picture !== null){
                $path = public_path('images/'.$getMember->picture);
                unlink($path);
            }
            $imgdata = base64_decode($request->images);
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
            $fileName = $getMember->nik_koperasi.'.'.explode('/', $mime_type)[1];
            file_put_contents(public_path('images/') . $fileName, $imgdata);
            $getMember->picture = $fileName;
            $getMember->save();
            $data = [
                'status' => 'success',
                'message' => 'Pembaharuan foto profile berhasil.',
                'error' => false
            ];
        }else{
            $data = [
                'status' => 'failed',
                'message' => 'Anda belum memasukkan foto profile.',
                'error' => true
            ];
        }
        
        return $data;

    }

    public function policy($id){

	    $policy = Policy::find($id);
        $data = [
            'status' => 'success',
            'data' => $policy,
            'error' => false
        ];

        return $data;

    }

    public function myBank(){
        try {
            $bank = auth()->user()->member->bank->first();
            $data = [
                'status' => 'success',
                'data' => $bank,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function updateMyBank(Request $request){
        try {
            $bank = auth()->user()->member->bank->first();
            $bank->bank_name = $request->bank_name;
            $bank->bank_account_name = $request->bank_account_name;
            $bank->bank_account_number = $request->bank_account_number;
            $bank->bank_branch = $request->bank_branch;
            $bank->save();
            $data = [
                'status' => 'success',
                'data' => $bank,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function postLoan(Request $request)
    {
        try {
            $offTrans = false;
            if ($offTrans) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Transaksi ini sedang tidak dapat dilakukan.',
                    'error' => true
                ];

                return $data;
            }
            // $userSubmision = auth()->user();
            // $penjamin = User::where('id', $request->penjamin)->get();
            // $adminApproval = User::AdminApproval()->get();
            // $approvals = ApprovalUser::getApproval($userSubmision);
            // $approvemans = collect($penjamin)->merge($approvals)->merge($adminApproval);
            // $approvemans = collect($penjamin)->merge($approvals)->merge($adminApproval)->merge([$userSubmision]);
//            $penjamin = ApprovalUser::getPenjamin($userSubmision);

            $userSubmision = auth()->user();
            $memberSubmision = $userSubmision->member;

            if($request->loan_id == 15){
                $penjamin = User::where('id', auth()->user()->id)->get();
                $adminApproval = User::BusinessApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
                $approvemans = collect($adminApproval);
            }else if($request->loan_id == 16){
                $penjamin = User::where('id', auth()->user()->id)->get();
                $adminApproval = User::PerseroanApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
                $approvemans = collect($adminApproval);
            }else{
                // $penjamin = User::where('id', $request->penjamin)->get();
                $adminApproval = User::AdminApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
                // $approvals = ApprovalUser::getApproval($userSubmision);
                // $approvemans = collect($penjamin)->merge($approvals)->merge($adminApproval);
                $approvemans = collect($adminApproval);
            }

            $start = now()->format('Y-m-d');
            $end = now()->addMonth($request->tenor);
            $value = $request->value;
            $totalDepositWajib = TotalDepositMember::where('member_id', $memberSubmision->id)->where('ms_deposit_id', 2)->first();

            if(!isset($request->keterangan)){
                $data = [
                    'status' => 'failed',
                    'message' => 'Alasan / Keperluan Pinjaman wajib diisi',
                    'error' => true
                ];
                return $data;
            }

            if(($request->loan_id == 3 || $request->loan_id == 9 || $request->loan_id == 13) && (!isset($request->jenis_barang) || !isset($request->merk_barang) || !isset($request->type_barang))){
                $data = [
                    'status' => 'failed',
                    'message' => 'Jenis, Merk, Type Barang / Kendaraan wajib diisi',
                    'error' => true
                ];
                return $data;
            }

            if($request->loan_id == 2 && $value > ceil($totalDepositWajib->value)){
                $data = [
                    'status' => 'failed',
                    'message' => 'Nominal Pinjaman Melebihi 90% dari Simpanan Wajib Anda',
                    'error' => true
                ];
                return $data;
              }

            $checkPlafon  = MemberPlafon::where('member_id',$memberSubmision->id)->first();
            $checkLoan    = TsLoans::where([
                ['member_id', $memberSubmision->id],
                ['status', 'belum lunas'],
                ['status', 'disetujui'],
                ['loan_id', $request->loan_id]
            ])->first();

            $checkApply   = TsLoans::where('member_id', $memberSubmision->id)
                ->where('status', 'menunggu')
                ->where('loan_id', $request->loan_id)
                ->first();
            
            $checkLoanTunai  = TsLoans::where([
                ['member_id', $memberSubmision->id],
                ['status', '!=', 'lunas'],
                ['loan_id', 1]
                ])->first();
            
            if(($checkLoanTunai != null || !empty($checkLoanTunai)) && $request->loan_id == 2) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Anda memiliki pinjaman tunai yang masih berjalan.',
                    'error' => true
                ];
                return $data;
                }

            $totalWajib = TotalDepositMember::totalDepositWajib(auth()->user()->member->id);
            // $totalPokok = TotalDepositMember::totalDepositPokok(auth()->user()->member->id);
            $totalSukarela = TotalDepositMember::totalDepositSukarela(auth()->user()->member->id);

            if($value > ceil($totalWajib + $totalSukarela)){
                $data = [
                    'status' => 'failed',
                    'message' => 'Nominal Pinjaman Melebihi Total Simpanan Anda.',
                    'error' => true
                ];
                return $data;
                }

            $checkTsDep   = DepositTransaction::where(['member_id' => $memberSubmision->id, 'status' => 'paid']);
            if($checkTsDep->count() < 2) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Deposit keanggotaan anda masih kurang dari 2 bulan atau belum lunas. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                    'error' => true
                ];
                return $data;
            }

            if(!$userSubmision->member->isActive()){
                $data = [
                    'status' => 'failed',
                    'message' => 'Keanggotaan anda belum aktif. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                    'error' => true
                ];
                return $data;
            }

            if(!$checkPlafon) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Batas nominal cicilan anda belum tersedia. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                    'error' => true
                ];
                return $data;
            }else {
                if($value > $checkPlafon->nominal){
                    $data = [
                        'status' => 'failed',
                        'message' => 'Batas nominal cicilan yang anda masukkan melebihi batas. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                        'error' => true
                    ];
                    return $data;
                } else {
                    if($checkLoan) {
                        $data = [
                            'status' => 'failed',
                            'message' => 'Anda masih memiliki pinjaman yang belum lunas.',
                            'error' => true
                        ];
                        return $data;
                    } elseif ($checkApply) {
                        $data = [
                            'status' => 'failed',
                            'message' => 'Anda telah melakukan pengajuan pinjaman sebelumnya. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                            'error' => true
                        ];
                        return $data;
                    }
                }
            }

            $msLoans = Loan::find($request->loan_id);

            $from = Carbon::parse($start);
            $to = Carbon::parse($end);
            $diff_in_months = $to->diffInMonths($from);


            $cutoff = cutOff::getCutoff();
            $gte_loan = $to->gte($cutoff);

            if(!$gte_loan){
                $startDate = $start;
            }else{
                $startDate = $cutoff;
            }

            $findLoan = Loan::findOrFail($request->loan_id);
            if($findLoan->attachment && !isset($request->lampiran)){
                $data = [
                    'status' => 'failed',
                    'message' => 'Lampiran pinjaman wajib diisi',
                    'error' => true
                ];
                return $data;
            }

            $name = '';
            if (isset($request->lampiran)) {
                $imgdata = base64_decode($request->lampiran);
                $f = finfo_open();
                $mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
                $name = time() .'loan-'.$memberSubmision->nik_koperasi.'.'.explode('/', $mime_type)[1];
                file_put_contents(public_path('images/pinjaman/') . $name, $imgdata);
            }

            $payDate = cutOff::getCutoff();
            $loan_value = $value / $diff_in_months;
            $loan_value = ceil($loan_value);
            $biayaJasa = ceil($value * ($msLoans->rate_of_interest/100));
            $biayaProvisi = ceil($value * ($msLoans->provisi / 100));
            $biayaBungaBerjalan = cutOff::getBungaBerjalan($value, $msLoans->rate_of_interest, now()->format('Y-m-d'));

            $loanNumber = new GlobalController();
            $loan = new TsLoans();
            $loan->loan_number = $loanNumber->getLoanNumber();
            $loan->member_id = $memberSubmision->id;
            $loan->start_date = Carbon::parse($startDate)->format('Y-m-25');
            $loan->end_date = Carbon::parse($end)->format('Y-m-25');
            $loan->loan_id = $msLoans->id;
            // $loan->penjamin = ($msLoans->id == 15 || $msLoans->id == 16) ? auth()->user()->id : $request->penjamin;
            $loan->penjamin = auth()->user()->id;
            $loan->value = $value;
            $loan->biaya_jasa = $biayaJasa;
            $loan->biaya_admin = ($msLoans->id == 3 || $msLoans->id == 9) ? $msLoans->biaya_admin : 0;
            $loan->biaya_transfer = ($request->metode_pencairan == "Transfer") ? $findLoan->biaya_transfer : 0;
            $loan->biaya_provisi = ($msLoans->id == 3 || $msLoans->id == 10) ? 0 : (($diff_in_months > 2) ? $biayaProvisi : 0);
            $loan->biaya_bunga_berjalan = ($msLoans->id == 3 || $msLoans->id == 13 || $msLoans->id == 9) ? 0 : $biayaBungaBerjalan;
            $loan->status = 'menunggu';
            $loan->period = $diff_in_months;
            $loan->in_period = 0;
            $loan->rate_of_interest = $msLoans->rate_of_interest;
            $loan->plafon = $msLoans->plafon;
            $loan->attachment       = $name;
            $loan->metode_pencairan = $request->metode_pencairan;
            $loan->keterangan       = ($request->keterangan == null) ? null : $request->keterangan;
            $loan->jenis_barang     = ($request->jenis_barang == 'null') ? null : $request->jenis_barang;
            $loan->merk_barang      = ($request->merk_barang == 'null') ? null : $request->merk_barang;
            $loan->type_barang      = ($request->type_barang == 'null') ? null : $request->type_barang;
            $loan->save();
            $loan->generateApprovalsLoan($approvemans);
            $b1 = 1;
            for ($a1 = 0; $a1 < $diff_in_months; $a1++) {

                $paydated = Carbon::parse($startDate)->addMonth($a1);
                $val = $value / $diff_in_months;
                $service = $value * ($msLoans->rate_of_interest / 100);
                if($request->pembayaran_angsuran != null){
                    if($a1 == ($diff_in_months - 1)){
                        if($request->pembayaran_angsuran == 'Jasa'){
                            $val = $value;
                        }else{
                            $val = $value / $diff_in_months;
                        }
                    }else{
                        if($request->pembayaran_angsuran == 'Jasa'){
                            $val = 0;
                        }else{
                            $val = $value / $diff_in_months;
                        } 
                    }
                }else{
                    $val = $value / $diff_in_months;
                }

                $loan_detail = new TsLoansDetail();
                $loan_detail->loan_id = $loan->id;
                $loan_detail->loan_number = $loan->loan_number;
                $loan_detail->value = $val;
                $loan_detail->service = $service;
                $loan_detail->pay_date = $paydated;
                $loan_detail->in_period = $b1 + $a1;
                $loan_detail->approval = 'menunggu';
                $loan_detail->save();

            }

            $getNextApproval = Approvals::where([
                'fk' => $loan->id,
                'layer' => $loan->approval_level + 1
            ])->first();

            $getNextUser = User::where('position_id', $getNextApproval->position_id)->where('region_id', $userSubmision->region_id)->get();
            // var_dump($getNextUser);
            foreach($getNextUser as $val){
                $val->notify(new WaitLoanApplication($loan)); 
            }
    
            // $getNextUser = User::findOrFail($getNextApproval->user_id);
            // $getNextUser->notify(new WaitLoanApplication($loan)); 

            // $penjamin[0]->notify(new WaitLoanApplication($loan)); 
    
            // $approvals = User::FUserApproval()->get();
            // $approvals = collect($penjamin)->merge($approvals)->merge($adminApproval)->merge([$userSubmision]);
            // $loan->newLoanBlastTo($approvals);

            $data = [
                'status' => 'success',
                'data' => $loan,
                'error' => false
            ];
            return $data;

        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function postReschedule(Request $request)
    {
        try {
            $offTrans = false;
            if ($offTrans) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Transaksi ini sedang tidak dapat dilakukan.',
                    'error' => true
                ];

                return $data;
            }
            $period       = $request->tenor;
            $value        = $request->value;
            $sisaPinjaman = $request->sisa_pinjaman;
            $toDay        = Carbon::now()->format('Y-m-d');
            $loan_id      = $request->loan_id;
            // $member_id    = $request->member_id;
            $loan_number_old    = $request->loan_number_old;
            $start = now()->format('Y-m-d');
            $end = now()->addMonth($request->tenor);

            $userSubmision = auth()->user();
            $memberSubmision = $userSubmision->member;
            // $penjamin = User::where('id', $request->penjamin)->get();
            $adminApproval = User::AdminApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
            // $approvals = ApprovalUser::getApproval($userSubmision);
            // $approvemans = collect($penjamin)->merge($approvals)->merge($adminApproval);
            $approvemans = collect($adminApproval);
            // $approvemans = collect($penjamin)->merge($approvals)->merge($adminApproval)->merge([$userSubmision]);
//            $penjamin = ApprovalUser::getPenjamin($userSubmision);

            $checkPlafon  = MemberPlafon::where('member_id',$memberSubmision->id)->first();
            $checkLoan    = TsLoans::where([
                ['member_id', $memberSubmision->id],
                ['status', 'belum lunas'],
                ['status', 'disetujui'],
                ['loan_id', $loan_id],
                ['loan_number', '!=', $loan_number_old]
            ])->first();

            $checkApply   = TsLoans::where('member_id', $memberSubmision->id)
                ->where('status', 'menunggu')
                ->where('loan_id', $loan_id)
                ->where('loan_number', '!=', $loan_number_old)
                ->first();

            $checkTsDep   = DepositTransaction::where(['member_id' => $memberSubmision->id, 'status' => 'paid']);
            if($checkTsDep->count() < 2) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Deposit keanggotaan anda masih kurang dari 2 bulan atau belum lunas. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                    'error' => true
                ];
                return $data;
            }

            if(!$userSubmision->member->isActive()){
                $data = [
                    'status' => 'failed',
                    'message' => 'Keanggotaan anda belum aktif. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                    'error' => true
                ];
                return $data;
            }

            $totalWajib = TotalDepositMember::totalDepositWajib(auth()->user()->member->id);
            // $totalPokok = TotalDepositMember::totalDepositPokok(auth()->user()->member->id);
            $totalSukarela = TotalDepositMember::totalDepositSukarela(auth()->user()->member->id);

            if($value > ceil($totalWajib + $totalSukarela)){
                $data = [
                    'status' => 'failed',
                    'message' => 'Nominal Pinjaman Melebihi Total Simpanan Anda.',
                    'error' => true
                ];
                return $data;
            }

            if(!$checkPlafon) {
                $data = [
                    'status' => 'failed',
                    'message' => 'Batas nominal cicilan anda belum tersedia. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                    'error' => true
                ];
                return $data;
            }else {
                if($value > $checkPlafon->nominal){
                    $data = [
                        'status' => 'failed',
                        'message' => 'Batas nominal cicilan yang anda masukkan melebihi batas. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                        'error' => true
                    ];
                    return $data;
                } else {
                    if($checkLoan) {
                        $data = [
                            'status' => 'failed',
                            'message' => 'Anda masih memiliki pinjaman yang belum lunas.',
                            'error' => true
                        ];
                        return $data;
                    } elseif ($checkApply) {
                        $data = [
                            'status' => 'failed',
                            'message' => 'Anda telah melakukan pengajuan pinjaman sebelumnya. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                            'error' => true
                        ];
                        return $data;
                    }
                }
            }

            $msLoans = Loan::find($loan_id);

            $from = Carbon::parse($start);
            $to = Carbon::parse($end);
            $diff_in_months = $to->diffInMonths($from);


            $cutoff = cutOff::getCutoff();
            $gte_loan = $to->gte($cutoff);

            if(!$gte_loan){
                $startDate = $start;
            }else{
                $startDate = $cutoff;
            }

            $getLoanOld = TsLoans::where('loan_number', $loan_number_old)->first();

            if ($getLoanOld) {
            $getLoanOld->in_period = $getLoanOld->period;
            $getLoanOld->status    = 'lunas';
            $getLoanOld->save();  
            // update detail loan
            TsLoansDetail::where(['loan_number' => $loan_number_old])->update(['approval' => 'lunas']);
            }

            $totalVal = $value - $sisaPinjaman;
            $loan_value = $value / $diff_in_months;
            $loan_value = ceil($loan_value);
            $biayaJasa = ceil($value * ($msLoans->rate_of_interest/100));
            $biayaProvisi = ceil($value * ($msLoans->provisi / 100));
            $biayaBungaBerjalan = cutOff::getBungaBerjalan($value, $msLoans->rate_of_interest, now()->format('Y-m-d'));

            $loanNumber = new GlobalController();
            $loan = new TsLoans();
            $loan->loan_number = $loanNumber->getLoanNumber();
            $loan->member_id = $memberSubmision->id;
            $loan->start_date = Carbon::parse($startDate)->format('Y-m-25');
            $loan->end_date = Carbon::parse($end)->format('Y-m-25');
            $loan->loan_id = $msLoans->id;
            $loan->penjamin = auth()->user()->id;
            // $loan->penjamin = $request->penjamin;
            $loan->value = $totalVal;
            $loan->biaya_jasa = $biayaJasa;
            $loan->biaya_admin = 0;
            $loan->biaya_transfer   = ($request->metode_pencairan == "Transfer") ? $findLoan->biaya_transfer : 0;
            $loan->biaya_provisi = ($diff_in_months > 2) ? $biayaProvisi : 0;
            $loan->biaya_bunga_berjalan = $biayaBungaBerjalan;
            $loan->status = 'menunggu';
            $loan->reschedule       = 1;
            $loan->period = $diff_in_months;
            $loan->in_period = 0;
            $loan->rate_of_interest = $msLoans->rate_of_interest;
            $loan->metode_pencairan = $request->metode_pencairan;
            $loan->keterangan       = ($request->keterangan == null) ? null : $request->keterangan;
            $loan->jenis_barang     = null;
            $loan->merk_barang      = null;
            $loan->type_barang      = null;
            $loan->save();
            $loan->generateApprovalsLoan($approvemans);
            $b1 = 1;
            for ($a1 = 0; $a1 < $diff_in_months; $a1++) {

                $paydated = Carbon::parse($startDate)->addMonth($a1);
                $val = $value / $diff_in_months;
                $service = $val * ($msLoans->rate_of_interest / 100);

                $loan_detail = new TsLoansDetail();
                $loan_detail->loan_id = $loan->id;
                $loan_detail->loan_number = $loan->loan_number;
                $loan_detail->value = $val;
                $loan_detail->service = $service;
                $loan_detail->pay_date = $paydated;
                $loan_detail->in_period = $b1 + $a1;
                $loan_detail->approval = 'menunggu';
                $loan_detail->save();

            }

            // $approvals = User::FUserApproval()->get();
            // $loan->newLoanBlastTo($approvals, ['database', OneSignalChannel::class]);

            $getNextApproval = Approvals::where([
                'fk' => $loan->id,
                'layer' => $loan->approval_level + 1
            ])->first();

            $getNextUser = User::where('position_id', $getNextApproval->position_id)->where('region_id', $userSubmision->region_id)->get();
            // var_dump($getNextUser);
            foreach($getNextUser as $val){
                $val->notify(new WaitLoanApplication($loan)); 
            }
    
            // $getNextUser = User::findOrFail($getNextApproval->user_id);
            // $getNextUser->notify(new WaitLoanApplication($loan)); 

            // $penjamin[0]->notify(new WaitLoanApplication($loan)); 
    
            $approvals = User::FUserApproval()->get();
            $loan->newLoanBlastTo($approvals);

            $data = [
                'status' => 'success',
                'data' => $loan,
                'error' => false
            ];
            return $data;

        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function getLoanApproval()
    {
        try {
            $approvalID = auth()->user()->position_id;
            $approvalUserID = auth()->user()->id;
            $approvalLoan = Approvals::with('ts_loans.ms_loans', 'ts_loans.member')
                ->whereJsonContains('approval', ['position_id' => $approvalID])->get();
            
            $approvalLoan = $approvalLoan->filter(function ($item) {
                // var_dump($item->layer);
                if($item->layer == 1){
                    return $item->approval['id'] == auth()->user()->id;
                }else{
                    return $item;
                }
            })->values();
            $data = [
                'status' => 'success',
                'data' => $approvalLoan,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function approveLoan(Request $request){
        try {
            
            $approvalID = auth()->user()->id;
            $approvalLoan = Approvals::with('ts_loans.ms_loans','ts_loans.detail')->where('fk', $request->loan_id)->whereJsonContains('approval', ['id' => $approvalID])->first();
            // var_dump($approvalLoan);
            $loans = $approvalLoan->ts_loans;
            $detailLoans = $loans->detail;
            $approveman = collect($approvalLoan->approval);


            if($request->approve_status == 'direvisi'){
                $value = $request->revision_value;
                $loan_value = $value / $loans->period;
                $loan_value = ceil($loan_value);
                $service = ceil(($loan_value/100) * $loans->rate_of_interest);
                $loans->value = $value;
                foreach ($detailLoans as $detail){
                    $detail->value = $loan_value;
                    $detail->service = $service;
//                    $detail->save();
                }
//                $loans->save();
                $approvalLoan->is_revision = true;
            }

            if($request->approve_status == 'disetujui'){
                // if($approvalLoan->user->isMember()){
                    $loans->approval = $request->approve_status;
                    foreach ($detailLoans as $detail){
                        $detail->approval = $request->approve_status;
//                    $detail->save();
                    }
//                $loans->save();
                // }
            }

            if($request->approve_status == 'ditolak'){
                $loans->approval = $request->approve_status;
                foreach ($detailLoans as $detail){
                    $detail->approval = $request->approve_status;
//                    $detail->save();
                }
//                $loans->save();
                $approvalLoan->is_revision = true;
            }

            return $loans;
            $data = [
                'status' => 'success',
                'data' => $loans,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function approvalLoan(Request $request)
    {
        try {
            $reason = $request->reason;
            $id = $request->id;

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
                if($request->action == 'approved') {
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
                        $data = [
                            'status' => 'success',
                            'message' => 'Pinjaman berhasil disetujui',
                            'error' => false
                        ];
                        
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
                        $data = [
                            'status' => 'success',
                            'message' => 'Pinjaman berhasil disetujui',
                            'error' => false
                        ];
                    }

                    $findData->member->user->notify(new LoanApplicationStatusUpdated($findData));  
                }

                if($request->action == 'canceled'){
                    if(!isset($reason)){
                        $data = [
                            'status' => 'failed',
                            'error' => true,
                            'message' => 'Alasan Harus Diisi'
                        ];
                        return $data;
                    }

                    if($findData->status == 'ditolak' || $findData->status == 'disetujui' || $findData->status == 'lunas' || $findData->status == 'belum lunas'){
                        $data = [
                            'status' => 'failed',
                            'error' => true,
                            'message' => 'Data Tidak Bisa Diubah'
                        ];
                        return $data;
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
                        $data = [
                            'status' => 'success',
                            'message' => 'Pinjaman berhasil ditolak',
                            'error' => false
                        ];
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

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function getTopLoan(){
        try {
            $data = TsLoans::getTopPinjamanArea([])->get();

            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function notifications(Request $request){
        try {
            $perpage =  $request->input('limit', 15);
            $page =  $request->input('page', 1);

            $user = auth()->user();
            $data =  DatabaseNotification::where('notifiable_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->orderBy('read_at', 'asc')
                ->paginate($perpage, ['*'], 'page', $page);

            return $data;

            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function markAsReadNotification($id){

	    try{
            $notifications = auth()->user()->notifications()
                ->whereNull('read_at')
                ->where('id', $id);
            if($notifications->count() > 0)
            {
                $notifications->update(['read_at' => now()->toDateTimeString()]);
            }

            $data = [
                'status' => 'success',
                'data' => $notifications,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function markAllAsReadNotification(){

        try{
            $notifications = auth()->user()->notifications()
                ->whereNull('read_at');
            if($notifications->count() > 0)
            {
                $notifications->update(['read_at' => now()->toDateTimeString()]);
            }

            $data = [
                'status' => 'success',
                'data' => $notifications,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }

    }

    public function countNotification(){
        try{
            $notifications = auth()->user()->notifications()
                ->whereNull('read_at')->count();
            $data = [
                'status' => 'success',
                'data' => [
                    'totalUnread' => $notifications
                ],
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function deleteNotification($id){
        try{
            $notifications = auth()->user()->notifications()
                ->whereNull('read_at')
                ->where('id', $id);
            if($notifications->count() > 0)
            {
                $notifications->delete();
            }

            $data = [
                'status' => 'success',
                'data' => $notifications,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function penjaminLoan(){
        try{
            $penjamin = ApprovalUser::getPenjamin(auth()->user());
            $data = [
                'status' => 'success',
                'data' => $penjamin,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function getBungaBerjalan(){
        try{
            $dayBungaBerjalan = cutOff::getDayBungaBerjalan(now()->format('Y-m-d'));
            $data = [
                'status' => 'success',
                'data' => $dayBungaBerjalan,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
    }

    public function getRincianSimpanan()
	{
        try{
            $dat = [];
            $member = auth()->user()->member;
            $deposit_info = $member->configDeposits;
            $data['wajib'] = 0;
            $data['sukarela'] = 0;
            foreach ($deposit_info as $val){
    //			dd($val->value);
                if($val->type == 'wajib'){
                    $data['wajib'] = $val->value;
                }else if($val->type == 'sukarela'){
                    $data['sukarela'] = $val->value;
                }

            }
            $data = [
                'status' => 'success',
                'data' => $data,
                'error' => false
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }
	}

    public function postContact(Request $request){
        try{
            $this->validate($request, [
                'judul' => 'required',
                'pesan' => 'required',
            ]);

            $users = User::find(auth()->user()->id);

            // var_dump($users->name);

            $LoadController = new LoadController();
            $LoadController->sendEmailContactUs($users->name, $users->email, $request->judul, $request->pesan);

            $data = [
                'status' => 'success',
                'message' => 'Pesan Berhasil Terkirim',
            ];

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status' => 'failed',
                'error' => $th->getMessage()
            ];
            return $data;
        }

    }

    public function updatePassword(Request $request){
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required_with:new_password|same:new_password|min:6',
        ]);

        $hashedPassword = auth()->user()->password;

        if (\Hash::check($request->old_password , $hashedPassword )) {

            if (!\Hash::check($request->new_password , $hashedPassword)) {

                $users = User::find(auth()->user()->id);
                $users->password = bcrypt($request->new_password);
                User::where( 'id' , auth()->user()->id)->update( array( 'password' =>  $users->password));
                $data = [
                    'status' => 'success',
                    'message' => 'Password Berhasil diubah',
                ];
                // send start email
                $LoadController = new LoadController();
                $LoadController->sendEmailUpdatePassword($users->email);
                // send end   email
            } else{
                $data = [
                    'status' => 'failed',
                    'message' => 'Password Baru Tidak Boleh Sama dengan Password Lama',
                ];
            }

        } else {
            $data = [
                'status' => 'failed',
                'message' => 'Password Lama yang Anda Masukan Salah',
            ];
        }

        return $data;

    }

    public function updateEmail(Request $request){
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $hashedPassword = auth()->user()->password;
        if (\Hash::check($request->password , $hashedPassword )) {

            if (!\Hash::check($request->email , auth()->user()->email)) {

                $users = User::find(auth()->user()->id);
                $users->email = $request->email;
                User::where( 'id' , auth()->user()->id)->update( array( 'email' =>  $users->email));
                Member::where( 'user_id' , auth()->user()->id)->update( array( 'email' =>  $users->email));
                
                // send start email
                $LoadController = new LoadController();
                $LoadController->sendEmailUpdateEmail($$users->email);
                // send end   email

                $data = [
                    'status' => 'success',
                    'message' => 'Email Berhasil diubah',
                ];    
            } else{
                $data = [
                    'status' => 'failed',
                    'message' => 'Email Baru Tidak Boleh Sama dengan email lama',
                ];
            }

        } else {
            $data = [
                'status' => 'failed',
                'message' => 'Password Lama yang Anda Masukan Salah',
            ];
        }

        return $data;

    }

    public function updateSalary(Request $request)
    {
        $findM = Member::where('user_id', auth()->user()->id)->first();

        $input = Input::all();

        $member = Member::find($findM->id);
        $member->salary = $input['salary'];
        $member->save();

        $data = [
            'status' => 'success',
            'message' => 'Gaji Berhasil di Ubah',
        ];

        return $data;
    }

    public function updateBank(Request $request)
    {
        $bank = auth()->user()->member->bank->first();
        $bank->bank_name = $request->bank_name;
        $bank->bank_account_name = $request->bank_account_name;
        $bank->bank_account_number = $request->bank_account_number;
        $bank->bank_branch = $request->bank_branch;
        $bank->save();
        $data = [
            'status' => 'success',
            'data' => $bank,
            'error' => false
        ];
        return $data;
    }

    public function list_pengambilan_simpanan_member($id){
        $selected = PencairanSimpanan::getPencairanSimpananMember($id);
		if($selected){
			$data        = array(
				'error'    => 0,
				'msg'      => 'Berhasil.',
				'json'     => $selected,
			);
		} else{
			$data        = array(
				'error' => 1,
				'msg'   => 'Data pencairan tidak ditemukan.',
			);
		}
		return response()->json($data);
	}

    public function getListPencairan(Request $request){
        $users = User::find(auth()->user()->id);
		$input           = Input::all();
		$member          = Member::where('user_id', $user->user_id);
		$selected        = PencairanSimpanan::where('member_id', $member->id);
		$bank = $member->bank;
//		return $sukarela = collect($member->depositSukarela);
		if($selected){
			$data        = array(
				'error'    => 0,
				'msg'      => 'Berhasil.',
				'json'     => $selected,
				'transfer' => $bank[0]['bank_name'] .' - an/ '. $bank[0]['bank_account_name'] .' ('.$bank[0]['bank_account_number'] .')'
			);
		} else{
			$data        = array(
				'error' => 1,
				'msg'   => 'Data pencairan tidak ditemukan.',
			);
		}
		return response()->json($data);
	}

    public function post_change_deposit(Request $request)
	{
        $offTrans = false;
        if ($offTrans) {
            $data = [
                'status' => 'failed',
                'message' => 'Transaksi ini sedang tidak dapat dilakukan.',
                'error' => true
            ];

            return $data;
        }

		$member = auth()->user()->member;

		if($request->wajib == 0 || $request->wajib == ''){
			$data = [
                'status' => 'failed',
                'message' => 'dana yang diubah tidak boleh nol 0',
            ];
		}

		$perubahan = new ChangeDeposit();
		$perubahan->member_id = $member->id;
		$perubahan->date = now()->format('Y-m-d');
		$perubahan->last_wajib = $request->last_wajib;
		$perubahan->last_sukarela = $request->last_sukarela;
		$perubahan->new_wajib = $request->wajib;
		$perubahan->new_sukarela = $request->sukarela;
		$perubahan->approval = false;
		$perubahan->save();

		// $users = User::where('id', 1)->whereNotNull('os_token')->get();
		// $perubahan->blastTo($users,['mail',OneSignalChannel::class]);

        if(isset(auth()->user()->region['id'])){
            $getApproveUser = User::where('region_id', auth()->user()->region['id'])
            ->where('position_id', 9)->get();
        }else{
            $getApproveUser = User::where('position_id', 9)->get();
        }

        if(empty($getApproveUser)){
            $getApproveUser = User::where('position_id', 9)->get();
        }

        // var_dump($getApproveUser);
        foreach($getApproveUser as $value){
            $value->notify(new WaitChangeDepositApplication($perubahan)); 
        }

        $data = [
            'status' => 'success',
            'message' => 'pengajuan perubahan simpanan berhasil',
        ];

		return $data;
	}

    public function changeStatusLoan(Request $request)
    {
        $detail_id     = $request->detail_id;
        $getDetail     = TsLoansDetail::find($detail_id);
        $loan = TsLoans::where('id', $getDetail->loan_id)->first();
        if($request->status == 'belum lunas' || $request->status == 'lunas') {
            if ($getDetail) {
                $getDetail->approval = $request->status;
                if($request->status == 'lunas'){
                    $loan->in_period = $getDetail->in_period;
                    $loan->save();
                }
                if($request->status == 'belum lunas'){
                    $loan->in_period = $getDetail->in_period - 1;
                    $loan->save();
                }
                $getDetail->save();  
            } else {
                $data = [
                    'status' => 'failed',
                    'error' => true,
                    'message' => 'Gagal memperbaharui status data',
                ];

                return $data;
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
                    $getDetail->approval = $request->status;
                    $getDetail->value  = 0;
                    $getDetail->service  = 0;
                    // add more value
                    $findTenor->value  = $value + $findTenor->value;
                    // save data
                    $findTenor->save();
                    $getDetail->save();
                } else {
                    $data = [
                        'status' => 'failed',
                        'error' => true,
                        'message' => 'Maaf, cicilan untuk bulan depan tidak ditemukan. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                    ];
                    return $data;

                }
         
            } else {
                $data = [
                    'status' => 'failed',
                    'error' => true,
                    'message' => 'Gagal memperbaharui status data',
                ];
                return $data;

            }
        }
        // get all data again
        $findDetail    = TsLoansDetail::where('loan_id',$getDetail->loan_id)->orderBy('in_period')->get();
        if($findDetail){
            $data = [
                'status' => 'success',
                'error' => false,
                'message' => 'Berhasil memperbaharui status data',
            ];
        } else{
            $data = [
                'status' => 'failed',
                'error' => true,
                'message' => 'Gagal memperbaharui status data',
            ];
        }
        return $data;
    }

    public function updateStatusLoan(Request $request)
    {
        $loan_id       = $request->loan_id;
        $getLoan       = TsLoans::findOrFail($loan_id);
        if ($getLoan) {
            if($request->status == 'belum lunas'){
                $getLoan->status = $request->status;
                $getLoan->in_period = 0;
                $getLoan->save();  

                TsLoansDetail::where(['loan_id' => $loan_id])->where('approval', '!=', 'lunas')->update(['approval' => $request->status]);
            } else if($request->status == 'lunas'){
                $getLoan->status = $request->status;
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
            $data = [
                'status' => 'success',
                'error' => false,
                'message' => 'Berhasil memperbaharui status data',
            ];
        } else {
            $data = [
                'status' => 'failed',
                'error' => true,
                'message' => 'Gagal memperbaharui status data',
            ];

        }
        return $data;
    }

    protected function sendResetLinkResponse(Request $request)
    {
        $input = $request->only('email');
        $validator = Validator2::make($input, [
            'email' => "required|email"
        ]);
        if ($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $response =  Password::sendResetLink($input);
        if($response == Password::RESET_LINK_SENT){
            $message = "Mail send successfully";
        }else{
            $message = "Email could not be sent to this email address";
        }

        $data = [
            'status' => 'success',
            'error' => false,
            'message' => $message,
        ];
        return $data;
    }
}
