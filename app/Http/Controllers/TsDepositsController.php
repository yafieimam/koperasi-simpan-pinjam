<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', '3600');
ini_set("memory_limit", "20056M");

use App\TotalDepositMember;
use App\User;
use Auth;
use Carbon\Carbon;
use Redirect;
use Validator;
use App\Member;
use App\TsDeposits;
use App\Deposit;
use App\TsDepositsDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\WaitPenambahanSimpananApplication;
use App\Helpers\cutOff;

class TsDepositsController extends GlobalController
{
    public function index(){

        $selected = TsDeposits::getDepositArea(auth()->user()->region);
        $totalWajib = TotalDepositMember::getDepositAreaWajib()->sum('value');
        $totalPokok = TotalDepositMember::getDepositAreaPokok()->sum('value');
        $totalSukarela = TotalDepositMember::getDepositAreaSukarela()->sum('value');
        $totalShu = TotalDepositMember::getDepositAreaShu()->sum('value');
        $totalLainnya = TotalDepositMember::getDepositAreaLainnya()->sum('value');
//        if(auth()->user()->isSu() || auth()->user()->isPow()){
//            $selected = TsDeposits::get();
//        }else{
//            $selected = TsDeposits::where('member_id', auth()->user()->member->id)->get();
//        }
        if (request()->ajax()) {
            return DataTables::of($selected)
            ->editColumn('member', function ($selected) {
                return $selected->member->first_name . ' ' . $selected->member->last_name;
			})
			->editColumn('total_deposit', function ($selected){
				return 'Rp. '. number_format($selected->total_deposit);
			})
            ->editColumn('type', function ($selected){
                return $selected->ms_deposit->deposit_name;
            })
            ->editColumn('transaction', function ($selected){
                return ucwords($selected->type);
            })
            ->editColumn('date', function ($selected){
                return Carbon::parse($selected->post_date)->format('Y-m-d');
            })
            ->editColumn('status', function ($selected) {
                $status = $selected->status;
                if($status == 'paid') {
                   $status         = 'Lunas';
                } else if($status == 'pending') {
                   $status         = 'Masih pending';
                } else if($status == 'unpaid' ) {
                   $status             = 'Belum Lunas';
                } else if($status == 'approved' ) {
                    $status             = 'Disetujui';
                } else if($status == 'rejected' ) {
                    $status             = 'Ditolak';
                } 
                return ucwords($status);
            })
            ->addColumn('action',function($selected){
                $idRecord = \Crypt::encrypt($selected->id);
                return
                '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Perbaharui </a>
                </center>';
            })
            ->make(true);
		}
		// return $selected[0]->member['first_name'];
        return view('transaction.deposit.ts_deposit_list', compact('totalPokok', 'totalWajib', 'totalLainnya', 'totalSukarela', 'totalShu'));
	}

    public function pokok(){

        $selected = TsDeposits::getDepositTypeArea(auth()->user()->region, 1);
//        if(auth()->user()->isSu() || auth()->user()->isPow()){
//            $selected = TsDeposits::get();
//        }else{
//            $selected = TsDeposits::where('member_id', auth()->user()->member->id)->get();
//        }
        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected) {
                    return $selected->member['first_name'] .' '. $selected->member['last_name'];
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('transaction', function ($selected){
                    return ucwords($selected->type);
                })
                ->editColumn('date', function ($selected){
                    return Carbon::parse($selected->post_date)->format('Y-m-d');
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Perbaharui </a>
                </center>';
                })
                ->make(true);
        }
        // return $selected[0]->member['first_name'];
        return view('transaction.deposit.ts-deposit-list-pokok');
    }

    public function wajib(){

        $selected = TsDeposits::getDepositTypeArea(auth()->user()->region, 2);
//        if(auth()->user()->isSu() || auth()->user()->isPow()){
//            $selected = TsDeposits::get();
//        }else{
//            $selected = TsDeposits::where('member_id', auth()->user()->member->id)->get();
//        }
        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected) {
                    return $selected->member['first_name'] .' '. $selected->member['last_name'];
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('transaction', function ($selected){
                    return ucwords($selected->type);
                })
                ->editColumn('date', function ($selected){
                    return Carbon::parse($selected->post_date)->format('Y-m-d');
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Perbaharui </a>
                </center>';
                })
                ->make(true);
        }
        // return $selected[0]->member['first_name'];
        return view('transaction.deposit.ts-deposit-list-wajib');
    }

    public function sukarela(){

        $selected = TsDeposits::getDepositTypeArea(auth()->user()->region, 3);
//        if(auth()->user()->isSu() || auth()->user()->isPow()){
//            $selected = TsDeposits::get();
//        }else{
//            $selected = TsDeposits::where('member_id', auth()->user()->member->id)->get();
//        }
        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected) {
                    return $selected->member['first_name'] .' '. $selected->member['last_name'];
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('transaction', function ($selected){
                    return ucwords($selected->type);
                })
                ->editColumn('date', function ($selected){
                    return Carbon::parse($selected->post_date)->format('Y-m-d');
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Perbaharui </a>
                </center>';
                })
                ->make(true);
        }
        // return $selected[0]->member['first_name'];
        return view('transaction.deposit.ts-deposit-list-sukarela');
    }

    public function berjangka(){

        $selected = TsDeposits::getDepositTypeArea(auth()->user()->region, 4);
//        if(auth()->user()->isSu() || auth()->user()->isPow()){
//            $selected = TsDeposits::get();
//        }else{
//            $selected = TsDeposits::where('member_id', auth()->user()->member->id)->get();
//        }
        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected) {
                    return $selected->member['first_name'] .' '. $selected->member['last_name'];
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('transaction', function ($selected){
                    return ucwords($selected->type);
                })
                ->editColumn('date', function ($selected){
                    return Carbon::parse($selected->post_date)->format('Y-m-d');
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Perbaharui </a>
                </center>';
                })
                ->make(true);
        }
        // return $selected[0]->member['first_name'];
        return view('transaction.deposit.ts-deposit-list-berjangka');
    }

    public function shu(){

        $selected = TsDeposits::getDepositTypeArea(auth()->user()->region, 5);
//        if(auth()->user()->isSu() || auth()->user()->isPow()){
//            $selected = TsDeposits::get();
//        }else{
//            $selected = TsDeposits::where('member_id', auth()->user()->member->id)->get();
//        }
        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected) {
                    return $selected->member['first_name'] .' '. $selected->member['last_name'];
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('transaction', function ($selected){
                    return ucwords($selected->type);
                })
                ->editColumn('date', function ($selected){
                    return Carbon::parse($selected->post_date)->format('Y-m-d');
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Perbaharui </a>
                </center>';
                })
                ->make(true);
        }
        // return $selected[0]->member['first_name'];
        return view('transaction.deposit.ts-deposit-list-shu');
    }

    public function lainnya(){

        $selected = TsDeposits::getDepositTypeArea(auth()->user()->region, 6);
//        if(auth()->user()->isSu() || auth()->user()->isPow()){
//            $selected = TsDeposits::get();
//        }else{
//            $selected = TsDeposits::where('member_id', auth()->user()->member->id)->get();
//        }
        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected) {
                    return $selected->member['first_name'] .' '. $selected->member['last_name'];
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('date', function ($selected){
                    return Carbon::parse($selected->post_date)->format('Y-m-d');
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Perbaharui </a>
                </center>';
                })
                ->make(true);
        }
        // return $selected[0]->member['first_name'];
        return view('transaction.deposit.ts-deposit-list-lainnya');
    }

	public function ts_deposit_members(){
        if(auth()->user()->isSu() || auth()->user()->isPow()){
            $selected = TsDeposits::where('type', 'debit')->get();
        }else{
            $selected = TsDeposits::where('member_id', auth()->user()->member->id)->where('type', 'debit')->get();
            $totalWajib = TotalDepositMember::totalDepositWajib(auth()->user()->member->id);
            $totalPokok = TotalDepositMember::totalDepositPokok(auth()->user()->member->id);
            $totalSukarela = TotalDepositMember::totalDepositSukarela(auth()->user()->member->id);
            $totalShu = TotalDepositMember::totalDepositShu(auth()->user()->member->id);
            $totalLainnya = TotalDepositMember::totalDepositLainnya(auth()->user()->member->id);
        }

        if (request()->ajax()) {
            return DataTables::of($selected)
            ->editColumn('member', function ($selected){
                return $selected->member->first_name . ' ' . $selected->member->last_name;
            })
            ->editColumn('type', function ($selected){
                return $selected->ms_deposit->deposit_name;
            })
            ->editColumn('deposit_number', function ($selected) {
                return $selected->deposit_number;
            })
			->editColumn('total_deposit', function ($selected){
				return 'Rp. '. number_format($selected->total_deposit);
			})
            ->editColumn('status', function ($selected) {
                $status = $selected->status;
                if($status == 'paid') {
                   $status         = 'Lunas';
                } else if($status == 'pending') {
                   $status         = 'Masih pending';
                } else if($status == 'unpaid' ) {
                   $status             = 'Belum Lunas';
                } 
                return ucwords($status);
            })
            ->addColumn('action',function($selected){
                $idRecord              = \Crypt::encrypt($selected->id);
                return
                '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Detail </a>
                </center>';
            })
            ->make(true);
		}
        return view('members.deposit.deposit', compact('totalWajib', 'totalPokok', 'totalSukarela', 'totalShu', 'totalLainnya'));
	}

	public function ts_deposit_members_detail($el){
        $depDetail = $this->decrypter($el);
        $selected  = TsDepositsDetail::where('transaction_id', $depDetail)->get();
        if (request()->ajax()) {
            return DataTables::of($selected)
            ->editColumn('transaction_id', function ($selected) {
                return $selected->deposit->deposit_number;
            })
            ->editColumn('total', function ($selected){
                return 'Rp. '. number_format($selected->total);
            })
            ->editColumn('status', function ($selected) {
                $status = $selected->status;
                if($status == 'paid') {
                   $status           = 'Lunas';
                } else if($status   == 'pending') {
                   $status           = 'Masih pending';
                } else if($status == 'unpaid' || $selected->status == '') {
                   $status             = 'Belum Lunas';
                } 
                return ucwords($status);
            })
            ->make(true);
        }
     return view('members.deposit.deposit-detail', compact('el'));
    }
    public function depositDetail($el){
        $depDetail = $this->decrypter($el);
		$selected  = TsDepositsDetail::where('transaction_id', $depDetail)->get();
        if (request()->ajax()) {
            return DataTables::of($selected)
            ->editColumn('transaction_id', function ($selected) {
                return $selected->deposit->deposit_number;
            })
            ->editColumn('total', function ($selected){
                return 'Rp. '. number_format($selected->total);
            })
            ->editColumn('status', function ($selected) {
                $status = $selected->status;
                if($status == 'paid') {
                   $status           = 'Lunas';
                } else if($status   == 'pending') {
                   $status           = 'Masih pending';
                } else if($status == 'unpaid' || $selected->status == '') {
                   $status             = 'Belum Lunas';
                } 
                return ucwords($status);
            })
            ->addColumn('action',function($selected){
                $idRecord              = \Crypt::encrypt($selected->id);
                return
                '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Perbaharui </a>
                </center>';
            })
            ->make(true);
        }
     return view('members.deposit.view-detail', compact('el'));
	}
    public function viewDeposit()
    {
        $input           = Input::all();
        $idRecord        = $this->decrypter($input['id']);
        $selected        = TsDeposits::findOrFail($idRecord);
        if($selected){
            $detail      = TsDepositsDetail::where('transaction_id', $idRecord)->get();
            $data        = array(
                                'error'    => 0,
                                'msg'      => 'Berhasil.',
                                'name_dps' => $selected->ms_deposit->deposit_name,
                                'json'     => $selected,
                                'detail'   => $detail,
                            );
            } else{
            $data        = array(
                                'error' => 1,
                                'msg'   => 'Data cicilan tidak ditemukan.',
                            );
         }
        return response()->json($data);
    }

    public function createDeposit(Request $request)
    {
        $validatedData      = $request->validate([
            'anggota'=> 'required',
            'tanggal'=> 'required',
            'nominal'=> 'required',
            'jenis_simpanan'=> 'required',
            'keterangan'=> 'required'
        ]);

        $input           = Input::all();
        $depositNumber = new GlobalController();
        $deposit = Deposit::findOrFail($input['jenis_simpanan']);

        $tsDeposit                   = new TsDeposits();
        $tsDeposit->id               = $tsDeposit::max('id')+1;
        $tsDeposit->member_id        = $input['anggota'];
        $tsDeposit->deposit_number   = $depositNumber->getDepositNumber();
        $tsDeposit->ms_deposit_id    = $input['jenis_simpanan'];
        $tsDeposit->type             = 'debit';
        $tsDeposit->deposits_type    = $deposit->initial;
        $tsDeposit->total_deposit    = $this->revive($input['nominal']);
        $tsDeposit->status           = 'pending';
        $tsDeposit->post_date        = $input['tanggal'];
        $tsDeposit->desc             = $input['keterangan'];
        $tsDeposit->save();

        $pokok_detail = new TsDepositsDetail();
        $pokok_detail->transaction_id = $tsDeposit->id;
        $pokok_detail->deposits_type = $deposit->initial;
        $pokok_detail->debit = $this->revive($input['nominal']);
        $pokok_detail->credit = 0;
        $pokok_detail->total = $this->revive($input['nominal']);
        $pokok_detail->status = 'pending';
        $pokok_detail->payment_date = cutOff::getCutoff();
        $pokok_detail->save();

        if(isset(auth()->user()->region['id'])){
            $getApproveUser = User::where('region_id', auth()->user()->region['id'])
            ->where('position_id', 8)->get();
        }else{
            $getApproveUser = User::where('position_id', 8)->get();
        }

        if(empty($getApproveUser)){
            $getApproveUser = User::where('position_id', 8)->get();
        }

        // var_dump($getApproveUser);
        foreach($getApproveUser as $value){
            $value->notify(new WaitPenambahanSimpananApplication($tsDeposit)); 
        }

        \Session::flash('message', 'Data simpanan baru berhasil ditambahkan.');
        return redirect('tambah-simpanan');
    }

    public function updateDeposit()
    {
        $input           = Input::all();
        $selected        = TsDeposits::findOrFail($input['id']);
        $selected_detail = TsDepositsDetail::where('transaction_id', $input['id'])->first();

        if($selected){
            if($selected['status'] === 'paid'){
                $data     = array(
                    'error'    => 1,
                    'msg'      => 'Mohon maaf data simpanan ini sudah terbayar.',
                );
            }else{
                if($input['status'] == "approved"){
                    $selected->status = 'paid';
                    $selected_detail->status = 'paid';
                }else{
                    $selected->status = $input['status'];
                    $selected_detail->status = $input['status'];
                }
                $selected->save();
                $selected_detail->save();

                if($input['status'] === 'paid'){
                    $totalDepositMember = TotalDepositMember::where([
                        'member_id' => $selected['member_id'],
                        'ms_deposit_id' => $selected['ms_deposit_id']
                    ])->first();

                    if(isset($totalDepositMember)){
                        $value = $totalDepositMember['value'] + $selected['total_deposit'];
                        $totalDepositMember->value = $value;
                        $totalDepositMember->save();
                    }else{
                        $totalDepositMember = new TotalDepositMember();
                        $totalDepositMember->member_id = $selected['member_id'];
                        $totalDepositMember->ms_deposit_id = $selected['ms_deposit_id'];
                        $totalDepositMember->value = $selected['total_deposit'];
                        $totalDepositMember->save();
                    }
                }

                $data     = array(
                                    'error'    => 0,
                                    'msg'      => 'Data Berhasil diperbaharui.',
                                    'json'     => $selected,
                                );
            }
            } else{
            $data     = array(
                                'error' => 1,
                                'msg'   => 'Data simpanan tidak ditemukan.',
                            );
         }
        return response()->json($data);
    }

    public function ts_deposit_members_wajib(){
        if(auth()->user()->isSu() || auth()->user()->isPow()){
            $selected = TsDeposits::get();
        }else{
            $selected = TsDeposits::where('member_id', auth()->user()->member->id )->fTypeDeposit(2)->get();
        }

        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected){
                    return $selected->member->first_name . ' ' . $selected->member->last_name;
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('deposit_number', function ($selected) {
                    return $selected->deposit_number;
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Detail </a>
                </center>';
                })
                ->make(true);
        }
        return view('members.deposit.deposit-wajib');
    }

    public function ts_deposit_members_pokok(){
        if(auth()->user()->isSu() || auth()->user()->isPow()){
            $selected = TsDeposits::get();
        }else{
            $selected = TsDeposits::where('member_id', auth()->user()->member->id )->fTypeDeposit(1)->get();
        }

        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected){
                    return $selected->member->first_name . ' ' . $selected->member->last_name;
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('deposit_number', function ($selected) {
                    return $selected->deposit_number;
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Detail </a>
                </center>';
                })
                ->make(true);
        }
        return view('members.deposit.deposit-pokok');
    }

    public function ts_deposit_members_sukarela(){
        if(auth()->user()->isSu() || auth()->user()->isPow()){
            $selected = TsDeposits::get();
        }else{
            $selected = TsDeposits::where('member_id', auth()->user()->member->id )->fTypeDeposit(3)->get();
        }

        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected){
                    return $selected->member->first_name . ' ' . $selected->member->last_name;
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('deposit_number', function ($selected) {
                    return $selected->deposit_number;
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Detail </a>
                </center>';
                })
                ->make(true);
        }
        return view('members.deposit.deposit-sukarela');
    }

    public function ts_deposit_members_shu(){
        if(auth()->user()->isSu() || auth()->user()->isPow()){
            $selected = TsDeposits::get();
        }else{
            $selected = TsDeposits::where('member_id', auth()->user()->member->id )->fTypeDeposit(5)->get();
        }

        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected){
                    return $selected->member->first_name . ' ' . $selected->member->last_name;
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('deposit_number', function ($selected) {
                    return $selected->deposit_number;
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Detail </a>
                </center>';
                })
                ->make(true);
        }
        return view('members.deposit.deposit-shu');
    }

    public function ts_deposit_members_lainnya(){
        if(auth()->user()->isSu() || auth()->user()->isPow()){
            $selected = TsDeposits::get();
        }else{
            $selected = TsDeposits::where('member_id', auth()->user()->member->id )->fTypeDeposit(6)->get();
        }

        if (request()->ajax()) {
            return DataTables::of($selected)
                ->editColumn('member', function ($selected){
                    return $selected->member->first_name . ' ' . $selected->member->last_name;
                })
                ->editColumn('type', function ($selected){
                    return $selected->ms_deposit->deposit_name;
                })
                ->editColumn('deposit_number', function ($selected) {
                    return $selected->deposit_number;
                })
                ->editColumn('total_deposit', function ($selected){
                    return 'Rp. '. number_format($selected->total_deposit);
                })
                ->editColumn('status', function ($selected) {
                    $status = $selected->status;
                    if($status == 'paid') {
                        $status         = 'Lunas';
                    } else if($status == 'pending') {
                        $status         = 'Masih pending';
                    } else if($status == 'unpaid' ) {
                        $status             = 'Belum Lunas';
                    }
                    return ucwords($status);
                })
                ->addColumn('action',function($selected){
                    $idRecord              = \Crypt::encrypt($selected->id);
                    return
                        '<center>
                <a  class="btn btn-info btn-sm btnEdit" onclick="showRecord('."'".$idRecord."'".','."'". csrf_token() ."'".','."'". $selected->status ."'".')"><i class="glyphicon glyphicon-list-alt"></i>  Detail </a>
                </center>';
                })
                ->make(true);
        }
        return view('members.deposit.deposit-lainnya');
    }

    public function updateDepositMember(){
        ini_set("memory_limit", "10056M");
        $members = Member::select('id', 'first_name')->get();

        foreach ($members as $member){
//            TotalDepositMember::where('member_id', $member->id)->delete();
            #pokok
            $totalPokok = TsDeposits::totalDepositPokok($member->id);
            if($totalPokok < 0){
                $totalPokok = 0;
            }
            $totalDepositMember = new TotalDepositMember();
            $totalDepositMember->member_id = $member->id;
            $totalDepositMember->ms_deposit_id = 1;
            $totalDepositMember->value = $totalPokok;
            $totalDepositMember->save();

            #sukarela
            $totalSukarela = TsDeposits::totalDepositSukarela($member->id);
            if($totalSukarela < 0){
                $totalSukarela = 0;
            }
            $totalDepositMember = new TotalDepositMember();
            $totalDepositMember->member_id = $member->id;
            $totalDepositMember->ms_deposit_id = 3;
            $totalDepositMember->value = $totalSukarela;
            $totalDepositMember->save();

            #wajib
            $totalWajib = TsDeposits::totalDepositWajib($member->id);
            if($totalWajib < 0){
                $totalWajib = 0;
            }
            $totalDepositMember = new TotalDepositMember();
            $totalDepositMember->member_id = $member->id;
            $totalDepositMember->ms_deposit_id = 2;
            $totalDepositMember->value = $totalWajib;
            $totalDepositMember->save();

            #lainnya
            $totalLainnya = TsDeposits::totalDepositLainnya($member->id);
            if($totalLainnya < 0){
                $totalLainnya = 0;
            }
            $totalDepositMember = new TotalDepositMember();
            $totalDepositMember->member_id = $member->id;
            $totalDepositMember->ms_deposit_id = 6;
            $totalDepositMember->value = $totalLainnya;
            $totalDepositMember->save();

            #shu
            $totalShu = TsDeposits::totalDepositShu($member->id);
            if($totalShu < 0){
                $totalShu = 0;
            }
            $totalDepositMember = new TotalDepositMember();
            $totalDepositMember->member_id = $member->id;
            $totalDepositMember->ms_deposit_id = 5;
            $totalDepositMember->value = $totalShu;
            $totalDepositMember->save();

            #berjangka
            $totalBerjangka = TsDeposits::totalDepositBerjangka($member->id);
            if($totalBerjangka < 0){
                $totalBerjangka = 0;
            }
            $totalDepositMember = new TotalDepositMember();
            $totalDepositMember->member_id = $member->id;
            $totalDepositMember->ms_deposit_id = 4;
            $totalDepositMember->value = $totalBerjangka;
            $totalDepositMember->save();
        }

        return 'updated';
    }

    public function updateProjectMember(){
        ini_set("memory_limit", "10056M");
        $Csv = new \App\Helpers\CsvToArray();
        $file = base_path() . '/database/seeds/csv/update_project_member.csv';
        $csv = utf8_encode(file_get_contents($file));
        $array = explode("\r", $csv);
        $data = array_map('str_getcsv', $array);

        $csv_data = array_slice($data, 0, 5000);
        foreach ($csv_data as $key => $val) {
            $iProject = str_replace("ï»¿", '', $val);
            $user = User::where('email', $iProject[2].'@gmail.com')->first();
            $member = $user->member;
            $member->project_id = $iProject[0];
            $member->save();
        }


        return 'updated';
    }

    public function tambah_simpanan()
    {
        $data['form'] = array(
                        'id'          => '',
                        'anggota'     => '',
                        'nominal'     => '',
                        'jenis_simpanan' => '',
                        'tanggal' => '',
                        'keterangan' => '',
                    );
        $data['member'] = Member::get();
        $data['simpanan'] = Deposit::get();
        return view('deposits.add-deposit-form', $data);
    }
}
