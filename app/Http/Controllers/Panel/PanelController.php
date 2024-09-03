<?php

namespace App\Http\Controllers\Panel;

use App\Helpers\ApprovalUser;
use App\Helpers\cutOff;
use App\Notifications\LoanApplicationStatusUpdated;
use App\Notifications\WaitLoanApplication;
use App\TsLoansDetail;
use DB;
use Auth;
use App\Bank;
use App\Approvals;
use App\TotalDepositMember;
use App\Loan;
use App\User;
use App\Member;
use App\Project;
use App\Policy;
use App\TsLoans;
use App\Position;
use Carbon\Carbon;
use App\MemberPlafon;
use App\DepositTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\LoadController;
use App\Http\Controllers\GlobalController;
use NotificationChannels\OneSignal\OneSignalChannel;
use function foo\func;

class PanelController extends LoadController
{
    function __construct()
    {
        $this->globalFunc        = new GlobalController();
    }

    public function profile()
    {
        if(!auth()->user()->isMember()){
            return abort(403);
        }
    	$bank_member = Bank::where('member_id', Auth::user()->member->id)->first();
      $spcMember   = Member::where('email', Auth::user()->member->email)->first();
    	return view('dashboards.profile', compact('bank_member', 'spcMember'));
    }
    public function updateStaff(Request $request)
    {
      $input             = Input::all();
      $getID             = Member::whereEmail($input['email'])->first();
      $getID->region_id  = $input['region_id'];
      $getID->branch_id  = $input['branch_id'];
      $getID->project_id = $input['project_id'];
      $getID->save();
      $data              =  array(
                             'error' => 0,
                             'msg'   => 'Pembaharuan data pribadi berhasil.',
                             );
      return response()->json($data);

    }
    public function updateProfile(Request $request)
    {
        $input                   = Input::all();
        // dd($input);
        if(isset($input['update_data'])){
        $getID                   = Member::whereEmail($input['update_data'])->first();
        $getID                   = $getID->id;
        } else {
        $getID                   = Auth::User()->member->id;
        }
        // get keys with value not null
        $getMember               = Member::findOrFail($getID);
        if(isset($input['change_image'])) {
            $file                = $request->file('attach');
            $fileName            = $file->getClientOriginalName();
            $request->file('attach')->move('images/', $fileName);
            $getMember->picture  = $fileName;
            $getMember->save();
            $data                =  array(
                                     'error' => 0,
                                     'msg'   => 'Pembaharuan foto profile berhasil.',
                                     );
            return response()->json($data);
        }
        $getKey                  = $this->globalFunc->getKeysArr($input);
        if(isset($input['first_name'])){
          $first_name            = $input['first_name'];
          $last_name             = $input['last_name'];
          // update user data
          if(isset($input['update_data'])){
          $getUser               = User::whereEmail($input['update_data'])->first();
          } else {
          $getUser               = User::find(Auth::User()->id);
          }
          $getUser->name         = $first_name.' '. $last_name;
          if(isset($input['position_id'])){
          $getUser->position_id  = $input['position_id'];
          }
          $getUser->save();
        }
        // update member data
        foreach ($getKey as $key) {
          if(Schema::hasColumn($this->globalFunc->tableName(new Member()), $key)) {
            // if($key == 'is_active' && $input[$key] == 'zero') {
            //     // check change status loan
            //     $cekLoan      = TsLoans::where(['member_id' => $getID, 'approval' => 'disetujui'])->where('status', '!=', 'lunas')->first();
            //     if($cekLoan){
            //       $data       =  array(
            //                     'error' => 1,
            //                     'msg'   => 'Maaf member masih terdata dalam tagihan pinjaman belum lunas. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
            //                       );
            //                     return response()->json($data);
            //     } else{
            //       $input[$key] = 0;                  
            //     }
            // } elseif($key == 'is_active' && $input[$key] != 'zero'){
            //   // send email for activated member by admin
            //   $statBe4     = $getUser->member->is_active;
            //   if (!$statBe4) {
            //   $this->sendEmailActiv($getUser->name, $getUser->email, 'activated');
            //   }
            // }
            if($key == 'status' && $input[$key] == 'aktif'){
              
              $statBe4     = $getUser->member->status;
              if ($statBe4 != 'aktif') {
                $this->sendEmailActiv($getUser->name, $getUser->email, 'activated');
              }
            }else if($key == 'status' && $input[$key] != 'aktif'){
              
              //check change status loan
                $cekLoan      = TsLoans::where(['member_id' => $getID, 'status' => 'disetujui'])->where('status', '!=', 'lunas')->first();
                if($cekLoan){
                  $data       =  array(
                                'error' => 1,
                                'msg'   => 'Maaf member masih terdata dalam tagihan pinjaman belum lunas. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                                  );
                                return response()->json($data);
                }
            }
            // dd($input);
            if ($key == 'position_id') {
            }
            if ($key == 'salary') {
              $getMember->$key              = $this->globalFunc->revive($input['salary']);
              continue;
            }
            if ($key == 'is_permanent') {
              $getMember->$key              = (int) $input[$key];
              continue;
              // dd($input[$key]);
            }
            // if ($key == 'is_permanent') {
            //   dd($input);
            // }

            $getMember->$key              = $input[$key];
          }
        }
        // $getMember->salary                = $this->globalFunc->revive($input['salary']);
        $getMember->phone_number          = $input['phone_number'];
        $getMember->save();
        // update bank data
        $getBank                          = Bank::where('member_id', $getID)->first();
        $insertBank                       = new Bank();
        if(!$getBank) {
         $insertBank->member_id           = $getID;
          foreach ($getKey as $key) {
           if(Schema::hasColumn($this->globalFunc->tableName($insertBank), $key)) {
             $insertBank->$key            = $input[$key];
           }
          }
          $insertBank->save();
        } else {
            foreach ($getKey as $key) {
              if(Schema::hasColumn($this->globalFunc->tableName($insertBank), $key)) {
                $getBank->$key            = $input[$key];
              }
            }
            $getBank->save();
        }
    	$data         =  array(
						'error' => 0,
						'msg'   => 'Pembaharuan data pribadi berhasil.',
				    	);
      return response()->json($data);
    }
    public function loanAggrement()
    {
      $getUser = User::findOrFail(Auth::user()->id);
      $position = Position::find($getUser->position_id);
      // if($position->level_id == 18 || $position->level_id == 14){
      //   $getLoans     = Loan::Publish()->whereNotIn('id', array(15, 16))->get();
      // }else if($position->level_id == 18 || $position->level_id == 14){
      //   $getLoans     = Loan::Publish()->get();
      // }else{
      //   $getLoans     = Loan::Publish()->whereNotIn('id', array(15, 16, 17))->get();
      // }
      if($position->level_id == 14){
        $getLoans     = Loan::Publish()->whereNotIn('id', array(17))->get();
      }else{
        $getLoans     = Loan::Publish()->get();
      }
      return view('dashboards.loan-aggrement', compact('getLoans'));
    }
    public function pickAggrement($el='')
    {
        $decypt_el    = \Crypt::decrypt($el);
        $penjamin = ApprovalUser::getPenjamin(auth()->user());
        $getMember    = Member::findOrFail(Auth::user()->member->id);
        $project      = Project::findOrFail($getMember->project_id);
        $policy       = Policy::where('id', 2)->first();
        $dayBungaBerjalan = cutOff::getDayBungaBerjalan(now()->format('Y-m-d'));
        $getUser = User::findOrFail(Auth::user()->id);
        $getPosition = Position::find($getUser->position_id);
        // var_dump($dayBungaBerjalan);
        if(($decypt_el == 15 || $decypt_el == 16) && ($getPosition->level_id != 8 && $getPosition->level_id != 9)){
          session()->flash('errors',collect(['Untuk mengajukan pinjaman ini, silahkan hubungi Admin Koperasi atau Supervisor Koperasi']));
          return redirect()->back();
        }

        if($decypt_el == 17){
          $checkCount = TsLoans::where('member_id', auth()->user()->member->id)->where('reschedule', 1)->whereYear('created_at', date('Y'))->count();
          if($checkCount <= 3){
            $selected = TsLoans::with('detail')->where('member_id', auth()->user()->member->id)->whereIn('loan_id', array(1, 2))->whereIn('status', array('menunggu', 'disetujui', 'belum lunas'))->first();
            if(isset($selected->value)){
              $findLoan = Loan::findOrFail($selected->loan_id);
              $tenors = [];
              $tenor = 0;
              for ($a=0; $a<$findLoan['tenor']; $a++){
                  $tenor += 1;
                  array_push($tenors, $tenor);
              }
              $halfValue = ceil($selected->value / 2);
              $sisa = collect($selected->detail);
              $dataSisa = $sisa->filter(function ($item)
              {
                  return $item->approval == 'menunggu' || $item->approval == 'belum lunas';
              });
              $sisaPinjaman = $dataSisa->sum('value') + $dataSisa->sum('service');
              if($sisaPinjaman < $halfValue){
                return view('dashboards.loan-reschedule', compact('selected', 'sisaPinjaman', 'findLoan', 'getMember', 'tenors', 'dayBungaBerjalan', 'penjamin', 'project', 'policy'));
              }else{
                  session()->flash('errors',collect(['Sisa Pinjaman Masih Melebihi 50% dari Total Pinjaman']));
                  return redirect()->back();
              }
            }else{
              session()->flash('errors',collect(['Anda belum memiliki pinjaman tunai yang sedang berjalan']));
              return redirect()->back();
            }
          }else{
            session()->flash('errors',collect(['Anda sudah mengajukan reschedule sebanyak 3 kali tahun ini']));
            return redirect()->back();
          }
        }else{
          $findLoan     = Loan::findOrFail($decypt_el);
          $tenors = [];
          $tenor = 0;
          for ($a=0; $a<$findLoan['tenor']; $a++){
              $tenor += 1;
              array_push($tenors, $tenor);
          }
          return view('dashboards.loan-specific', compact('findLoan', 'getMember', 'tenors', 'dayBungaBerjalan', 'penjamin', 'project', 'policy'));
        }
    }
    public function saveLoan(Request $request)
    {
      $offTrans = false;
      if($offTrans){
          $data       =  array(
              'error' => 1,
              'msg'   => 'Fitur Transaksi ini sedang tidak dapat digunakan',
          );
          return response()->json($data);
      }
      $input        = Input::all();
      $period       = $input['period'];
      $value        = $input['value'];
      $toDay        = Carbon::now()->format('Y-m-d');
      $loan_id      = $this->globalFunc->decrypter($input['loan_id']);
      $member_id    = $this->globalFunc->decrypter($input['member_id']);

      $userSubmision = auth()->user();

      if($loan_id == 15){
        $penjamin = User::where('id', auth()->user()->id)->get();
        $adminApproval = User::BusinessApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
        $approvemans = collect($adminApproval);
      }else if($loan_id == 16){
        $penjamin = User::where('id', auth()->user()->id)->get();
        $adminApproval = User::PerseroanApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
        $approvemans = collect($adminApproval);
      }else{
        // $penjamin = User::where('id', $input['penjamin'])->get();
        $adminApproval = User::AdminApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
        // $approvals = ApprovalUser::getApproval($userSubmision);
        // $approvemans = collect($penjamin)->merge($approvals)->merge($adminApproval);
        $approvemans = collect($adminApproval);
      }

      // dd($approvemans);
      $totalDepositWajib = TotalDepositMember::where('member_id', $member_id)->where('ms_deposit_id', 2)->first();
      $totalWajib = TotalDepositMember::totalDepositWajib(auth()->user()->member->id);
      // $totalPokok = TotalDepositMember::totalDepositPokok(auth()->user()->member->id);
      $totalSukarela = TotalDepositMember::totalDepositSukarela(auth()->user()->member->id);

      if(!isset($input['keterangan'])){
          $data       =  array(
              'error' => 1,
              'msg'   => 'Alasan / Keperluan Pinjaman wajib diisi',
          );
          return response()->json($data);
      }

      if(($loan_id == 3 || $loan_id == 9 || $loan_id == 13) && (!isset($input['jenis_barang']) || !isset($input['merk_barang']) || !isset($input['type_barang']))){
          $data       =  array(
              'error' => 1,
              'msg'   => 'Jenis, Merk, Type Barang / Kendaraan wajib diisi',
          );
          return response()->json($data);
      }

      if($loan_id == 2 && $value > ceil($totalDepositWajib->value)){
        $data       =  array(
            'error' => 1,
            'msg'   => 'Nominal Pinjaman Melebihi 90% dari Simpanan Wajib Anda',
        );
        return response()->json($data);
      }

      if($value > ceil($totalWajib + $totalSukarela)){
        $data       =  array(
            'error' => 1,
            'msg'   => 'Nominal Pinjaman Melebihi Total Simpanan Anda',
        );
        return response()->json($data);
      }

      // checking tsloan
      $checkLoan    = TsLoans::where([
                        ['member_id', $member_id],
                        ['status', 'belum lunas'],
                        ['status', 'disetujui'],
                        ['loan_id', $loan_id]
                      ])->first();
      $checkApply   = TsLoans::where('member_id', $member_id)
                      ->where('status', 'menunggu')
                      ->where('loan_id', $loan_id)
                      ->first();
      $checkActive  = Member::where([
                      'id' => Auth::user()->member->id,
                      'status' => 'aktif'
                       ]);
      $checkLoanTunai  = TsLoans::where([
                        ['member_id', $member_id],
                        ['status', '!=', 'lunas'],
                        ['loan_id', 1]
                      ])->first();
      if(($checkLoanTunai != null || !empty($checkLoanTunai)) && $loan_id == 2) {
        $data       =  array(
            'error' => 1,
            'msg'   => 'Anda memiliki pinjaman tunai yang masih berjalan.',
              );
        return response()->json($data);
      }
      if(!$checkActive) {
        $data       =  array(
            'error' => 1,
            'msg'   => 'Keanggotaan anda belum aktif. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
              );
            return response()->json($data);
      }
      $checkTsDep   = DepositTransaction::where(['member_id' => Auth::user()->member->id, 'status' => 'paid']);
      if($checkTsDep->count() < 2) {
        $data       =  array(
            'error' => 1,
            'msg'   => 'Deposit keanggotaan anda masih kurang dari 2 bulan atau belum lunas. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
              );
            return response()->json($data);
      } 
      $checkPlafon  = MemberPlafon::where('member_id', Auth::user()->member->id)->first();
      if(!$checkPlafon) {
        $data       =  array(
            'error' => 1,
            'msg'   => 'Batas nominal cicilan anda belum tersedia. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
              );
            return response()->json($data);
      }else {
        $nominal    = $checkPlafon->nominal;
        if($value > $nominal){
          $data       =  array(
            'error' => 1,
            'msg'   => 'Batas nominal cicilan yang anda masukkan melebihi batas. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
              );
          return response()->json($data);
        } else {
          if($checkLoan) {
            $data       =  array(
                'error' => 1,
                'msg'   => 'Anda masih memiliki pinjaman yang belum lunas.',
                  );
            return response()->json($data);
          } elseif ($checkApply) {
            $data       =  array(
                'error' => 1,
                'msg'   => 'Anda telah melakukan pengajuan pinjaman sebelumnya. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                  );
            return response()->json($data);
           }
        }
      }

    $findLoan = Loan::findOrFail($loan_id);
    if($findLoan->attachment && !$request->hasfile('images')){
        $data       =  array(
            'error' => 1,
            'msg'   => 'Lampiran pinjaman wajib diisi',
        );
        return response()->json($data);
    }

    $name = '';
    if ($request->hasfile('images')) {
        $file = $request->file('images');
        $name = time() . $file->getClientOriginalName();
        $file->move(public_path() . '/images/pinjaman/', $name);
    }

    $payDate = cutOff::getCutoff();
    $loanNumber = new GlobalController();
      // getting master data loan
      $biayaProvisi = $value * ($findLoan->provisi / 100);
      $biayaJasa = ceil($value * ($findLoan->rate_of_interest/100));

      $biayaBungaBerjalan = cutOff::getBungaBerjalan($value, $findLoan->rate_of_interest, now()->format('Y-m-d'));
      // insert tsloan
      $tsLoan                   = new TsLoans();
      $tsLoan->id               = $tsLoan::max('id')+1;
      $tsLoan->member_id        = $member_id;
      $tsLoan->loan_number      = $loanNumber->getLoanNumber();
      $tsLoan->loan_id          = $loan_id;
      // $tsLoan->penjamin         = ($loan_id == 15 || $loan_id == 16) ? auth()->user()->id : $input['penjamin'];
      $tsLoan->penjamin         = auth()->user()->id;
      $tsLoan->value            = $value;
      $tsLoan->period           = $period;
      $tsLoan->start_date       = $payDate;
      $tsLoan->biaya_admin      = ($loan_id == 3 || $loan_id == 9) ? $findLoan->biaya_admin : 0;
      $tsLoan->biaya_jasa       = $biayaJasa;
      $tsLoan->biaya_transfer   = ($input['metode_pencairan'] == "Transfer") ? $findLoan->biaya_transfer : 0;
      $tsLoan->biaya_provisi    = ($loan_id == 3 || $loan_id == 10) ? 0 : (($period > 2) ? $biayaProvisi : 0);
      $tsLoan->biaya_bunga_berjalan = ($loan_id == 3 || $loan_id == 13 || $loan_id == 9) ? 0 : $biayaBungaBerjalan;
      $tsLoan->end_date         = Carbon::parse($payDate)->addMonth($period);
      $tsLoan->in_period        = 0;
      $tsLoan->rate_of_interest = $findLoan->rate_of_interest;
      $tsLoan->plafon           = $findLoan->plafon;
      $tsLoan->attachment       = $name;
      $tsLoan->metode_pencairan = $input['metode_pencairan'];
      $tsLoan->keterangan       = ($input['keterangan'] == null) ? null : $input['keterangan'];
      $tsLoan->jenis_barang     = ($input['jenis_barang'] == 'null') ? null : $input['jenis_barang'];
      $tsLoan->merk_barang      = ($input['merk_barang'] == 'null') ? null : $input['merk_barang'];
      $tsLoan->type_barang      = ($input['type_barang'] == 'null') ? null : $input['type_barang'];
      $tsLoan->status         = 'menunggu';
      $tsLoan->save();
      $tsLoan->generateApprovalsLoan($approvemans);


        $b1 = 1;
        for ($a1 = 0; $a1 < $period; $a1++) {
          // $service = $val * ($findLoan->rate_of_interest / 100);
          $service = ceil($value * ($findLoan->rate_of_interest/100));
          if($input['pembayaran_angsuran'] != null){
            if($a1 == ($period - 1)){
              if($input['pembayaran_angsuran'] == 'Jasa'){
                $val = $value;
              }else{
                $val = $value / $period;
              }
            }else{
              if($input['pembayaran_angsuran'] == 'Jasa'){
                $val = 0;
              }else{
                $val = $value / $period;
              } 
            }
          }else{
            $val = $value / $period;
          }

          $loan_detail = new TsLoansDetail();
          $loan_detail->loan_id = $tsLoan->id;
          $loan_detail->loan_number = $tsLoan->loan_number;
          $loan_detail->value = $val;
          $loan_detail->service = $service;
          $loan_detail->pay_date = Carbon::parse($payDate)->addMonth($a1);
          $loan_detail->in_period = $b1 + $a1;
          $loan_detail->approval = 'menunggu';
          $loan_detail->save();
      }
        $getNextApproval = Approvals::where([
          'fk' => $tsLoan->id,
          'layer' => $tsLoan->approval_level + 1
        ])->first();

        $getNextUser = User::where('position_id', $getNextApproval->position_id)->where('region_id', $userSubmision->region_id)->get();
        // var_dump($getNextUser);
        foreach($getNextUser as $val){
            $val->notify(new WaitLoanApplication($tsLoan)); 
        }

        // $penjamin[0]->notify(new WaitLoanApplication($tsLoan)); 

        // $approvals = collect($penjamin)->merge($approvals)->merge($adminApproval)->merge([$userSubmision]);
        // // $approvals = User::FUserApproval()->get();
        // $tsLoan->newLoanBlastTo($approvals);
      $data         =  array(
            'error' => 0,
            'msg'   => 'Pengajuan pinjaman berhasil. Silahkan menunggu informasi persetujuan lebih lanjut.',
              );
            return response()->json($data);
    }

    public function saveReschedule(Request $request)
    {
      $offTrans = false;
      if($offTrans){
          $data       =  array(
              'error' => 1,
              'msg'   => 'Fitur Transaksi ini sedang tidak dapat digunakan',
          );
          return response()->json($data);
      }
      $input        = Input::all();
      $period       = $input['period'];
      $value        = $input['value'];
      $sisaPinjaman = $input['sisa_pinjaman'];
      $toDay        = Carbon::now()->format('Y-m-d');
      $loan_id      = $this->globalFunc->decrypter($input['loan_id']);
      $member_id    = $this->globalFunc->decrypter($input['member_id']);
      $loan_number_old    = $this->globalFunc->decrypter($input['loan_number_old']);

      $userSubmision = auth()->user();
      // $penjamin = User::where('id', $input['penjamin'])->get();
      $adminApproval = User::AdminApproval()->orderBy('position_id', 'desc')->groupBy('position_id')->get();
      // $approvals = ApprovalUser::getApproval($userSubmision);
      // $approvemans = collect($penjamin)->merge($approvals)->merge($adminApproval);
      $approvemans = collect($adminApproval);

      // checking tsloan
      $checkLoan    = TsLoans::where([
                        ['member_id', $member_id],
                        ['status', 'belum lunas'],
                        ['status', 'disetujui'],
                        ['loan_id', $loan_id],
                        ['loan_number', '!=', $loan_number_old]
                      ])->first();
      $checkApply   = TsLoans::where('member_id', $member_id)
                      ->where('status', 'menunggu')
                      ->where('loan_id', $loan_id)
                      ->where('loan_number', '!=', $loan_number_old)
                      ->first();
      $checkActive  = Member::where([
                      'id' => Auth::user()->member->id,
                      'status' => 'aktif'
                       ]);
      if(!$checkActive) {
        $data       =  array(
            'error' => 1,
            'msg'   => 'Keanggotaan anda belum aktif. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
              );
            return response()->json($data);
      }
      $checkTsDep   = DepositTransaction::where(['member_id' => Auth::user()->member->id, 'status' => 'paid']);
      if($checkTsDep->count() < 2) {
        $data       =  array(
            'error' => 1,
            'msg'   => 'Deposit keanggotaan anda masih kurang dari 2 bulan atau belum lunas. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
              );
            return response()->json($data);
      } 
      $totalWajib = TotalDepositMember::totalDepositWajib(auth()->user()->member->id);
      // $totalPokok = TotalDepositMember::totalDepositPokok(auth()->user()->member->id);
      $totalSukarela = TotalDepositMember::totalDepositSukarela(auth()->user()->member->id);

      if($value > ceil($totalWajib + $totalSukarela)){
        $data       =  array(
            'error' => 1,
            'msg'   => 'Nominal Pinjaman Melebihi Total Simpanan Anda',
        );
        return response()->json($data);
      }
      $checkPlafon  = MemberPlafon::where('member_id', Auth::user()->member->id)->first();
      if(!$checkPlafon) {
        $data       =  array(
            'error' => 1,
            'msg'   => 'Batas nominal cicilan anda belum tersedia. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
              );
            return response()->json($data);
      }else {
        $nominal    = $checkPlafon->nominal;
        if($value > $nominal){
          $data       =  array(
            'error' => 1,
            'msg'   => 'Batas nominal cicilan yang anda masukkan melebihi batas. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
              );
          return response()->json($data);
        } else {
          if($checkLoan) {
            $data       =  array(
                'error' => 1,
                'msg'   => 'Anda masih memiliki pinjaman yang belum lunas.',
                  );
            return response()->json($data);
          } elseif ($checkApply) {
            $data       =  array(
                'error' => 1,
                'msg'   => 'Anda telah melakukan pengajuan pinjaman sebelumnya. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
                  );
            return response()->json($data);
           }
        }
      }

    $getLoanOld = TsLoans::where('loan_number', $loan_number_old)->first();

    if ($getLoanOld) {
      $getLoanOld->in_period = $getLoanOld->period;
      $getLoanOld->status    = 'lunas';
      $getLoanOld->save();  
      // update detail loan
      TsLoansDetail::where(['loan_number' => $loan_number_old])->update(['approval' => 'lunas']);
    }

    $findLoan = Loan::findOrFail($loan_id);

    $payDate = cutOff::getCutoff();
    $loanNumber = new GlobalController();
      // getting master data loan
      $totalVal = $value - $sisaPinjaman;
      $biayaProvisi = $value * ($findLoan->provisi / 100);
      $biayaJasa = ceil($value * ($findLoan->rate_of_interest/100));

      $biayaBungaBerjalan = cutOff::getBungaBerjalan($value, $findLoan->rate_of_interest, now()->format('Y-m-d'));
      // insert tsloan
      $tsLoan                   = new TsLoans();
      $tsLoan->id               = $tsLoan::max('id')+1;
      $tsLoan->member_id        = $member_id;
      $tsLoan->loan_number      = $loanNumber->getLoanNumber();
      $tsLoan->loan_id          = $loan_id;
      // $tsLoan->penjamin         = $input['penjamin'];
      $tsLoan->penjamin         = auth()->user()->id;
      $tsLoan->value            = $totalVal;
      $tsLoan->period           = $period;
      $tsLoan->start_date       = $payDate;
      $tsLoan->biaya_admin      = 0;
      $tsLoan->biaya_jasa       = $biayaJasa;
      $tsLoan->biaya_transfer   = ($input['metode_pencairan'] == "Transfer") ? $findLoan->biaya_transfer : 0;
      $tsLoan->biaya_provisi    = ($period > 2) ? $biayaProvisi : 0;
      $tsLoan->biaya_bunga_berjalan = $biayaBungaBerjalan;
      $tsLoan->end_date         = Carbon::parse($payDate)->addMonth($period);
      $tsLoan->in_period        = 0;
      $tsLoan->rate_of_interest = $findLoan->rate_of_interest;
      $tsLoan->plafon           = $findLoan->plafon;
      $tsLoan->metode_pencairan = $input['metode_pencairan'];
      $tsLoan->keterangan       = ($input['keterangan'] == null) ? null : $input['keterangan'];
      $tsLoan->jenis_barang     = null;
      $tsLoan->merk_barang      = null;
      $tsLoan->type_barang      = null;
      $tsLoan->status           = 'menunggu';
      $tsLoan->reschedule       = 1;
      $tsLoan->save();
      $tsLoan->generateApprovalsLoan($approvemans);


        $b1 = 1;
        for ($a1 = 0; $a1 < $period; $a1++) {
          $val = $value / $period;
          // $service = $val * ($findLoan->rate_of_interest / 100);
          $service = ceil($value * ($findLoan->rate_of_interest/100));

          $loan_detail = new TsLoansDetail();
          $loan_detail->loan_id = $tsLoan->id;
          $loan_detail->loan_number = $tsLoan->loan_number;
          $loan_detail->value = $val;
          $loan_detail->service = $service;
          $loan_detail->pay_date = Carbon::parse($payDate)->addMonth($a1);
          $loan_detail->in_period = $b1 + $a1;
          $loan_detail->approval = 'menunggu';
          $loan_detail->save();
      }

      $getNextApproval = Approvals::where([
        'fk' => $tsLoan->id,
        'layer' => $tsLoan->approval_level + 1
      ])->first();

      $getNextUser = User::where('position_id', $getNextApproval->position_id)->where('region_id', $userSubmision->region_id)->get();
      // var_dump($getNextUser);
      foreach($getNextUser as $val){
          $val->notify(new WaitLoanApplication($tsLoan)); 
      }

      // $getNextUser = User::findOrFail($getNextApproval->user_id);
      // $getNextUser->notify(new WaitLoanApplication($tsLoan)); 
      // $penjamin[0]->notify(new WaitLoanApplication($tsLoan));

      $approvals = User::FUserApproval()->get();
      $tsLoan->newLoanBlastTo($approvals);

      $data         =  array(
            'error' => 0,
            'msg'   => 'Pengajuan pinjaman berhasil. Silahkan menunggu informasi persetujuan lebih lanjut.',
              );
            return response()->json($data);
    }

    public function updateProfilePhoto(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'file' => 'mimes:jpg,jpeg,png,bmp,tiff |max:4096',
            ],
            $messages = [
                'required' => 'The :attribute field is required.',
                'mimes' => 'Only jpg, jpeg, png, bmp,tiff are allowed.'
            ]
        );

        if ($validator->fails()) {
            $fieldsWithErrorMessagesArray = $validator->messages()->get('*');
            $data                =  array(
                'error' => 1,
                'msg'   => $fieldsWithErrorMessagesArray['file'],
                'img' => null
            );
            return response()->json($data);
        }
        $input = Input::all();
        $id = auth()->user()->member->id;
        $getMember = Member::findOrFail($id);
        if(isset($input['file'])) {
            if($getMember->picture !== null){
                $path = public_path('images/'.$getMember->picture);
                unlink($path);
            }
            $file = $request->file('file');
            $fileName = $getMember->nik_koperasi.'.'.$file->getClientOriginalExtension();
            $request->file('file')->move('images/', $fileName);
            $getMember->picture = $fileName;
            $getMember->save();
            $data                =  array(
                'error' => 0,
                'msg'   => 'Pembaharuan foto profile berhasil.',
                'img' => public_path('images/'.$fileName)
            );
            return response()->json($data);
        }

        $data         =  array(
            'error' => 0,
            'msg'   => 'Pembaharuan data pribadi berhasil.',
        );
        return response()->json($data);
    }

}
