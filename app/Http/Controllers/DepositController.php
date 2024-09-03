<?php

namespace App\Http\Controllers;

use App\ChangeDeposit;
use App\ConfigDepositMembers;
use App\Deposit;
use App\Helpers\cutOff;
use App\Position;
use App\Project;
use App\Member;
use App\User;
use App\PencairanSimpanan;
use App\Resign;
use App\TsDeposits;
use App\TsDepositsDetail;
use App\TotalDepositMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use App\Http\Controllers\GlobalController;
use App\Notifications\WaitPencairanSimpananApplication;
use App\Notifications\PencairanSimpananApplicationStatusUpdated;
use App\Notifications\PencairanSimpananApplicationStatusRejected;
use App\Notifications\WaitChangeDepositApplication;
use App\Notifications\ChangeDepositApplicationStatusUpdated;
use App\Notifications\ChangeDepositApplicationStatusRejected;
use App\Notifications\WaitPenambahanSimpananApplication;
use App\Notifications\PenambahanSimpananApplicationStatusUpdated;
use App\Notifications\PenambahanSimpananApplicationStatusRejected;

class DepositController extends Controller
{
	function __construct()
    {
        $this->globalFunc = new GlobalController();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deposits = Deposit::get();
        return view('deposits.deposit-type-list', compact('deposits'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('deposits.deposit-type-new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'deposit_name' => 'required|unique:ms_deposits|min:3',
        ]);
        // $newDepositType = Deposit::create($validatedData);
		$deposit = new Deposit();
		$deposit->deposit_name = $request->deposit_name;
		$deposit->deposit_minimal = $this->globalFunc->revive($request->deposit_minimal);
		$deposit->deposit_maximal = $this->globalFunc->revive($request->deposit_maximal);
        $deposit->save();
        session()->flash('success', trans('response-message.success.create', ['object'=>$deposit->deposit_name]));
        return redirect('deposits');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $deposit = Deposit::findOrFail($id);
        return view('deposits.deposit-type-edit', compact('deposit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'deposit_name' => ['required','min:3',  Rule::unique('ms_deposits')->ignore($id)],
        ]);
        $deposit = Deposit::findOrFail($id);
		$deposit->deposit_name = $request->deposit_name;
		$deposit->deposit_minimal = $this->globalFunc->revive($request->deposit_minimal);
		$deposit->deposit_maximal = $this->globalFunc->revive($request->deposit_maximal);
        $deposit->save();
        session()->flash('success', trans('response-message.success.update', ['object'=>$deposit->deposit_name]));
        return redirect('deposits');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deposit = Deposit::where('id', $id)->with('transaction')->first();
        if($deposit->transaction->count() > 0){
            // session()->flash('error', trans('response-message.failed.delete', ['object'=>$deposit->deposit_name]));
			session()->flash('errors', collect(['Simpanan Masih Terdapat Data Transaksi.']));
			return redirect()->back();
        }else{
            // session()->flash('success', trans('response-message.success.delete', ['object'=>$deposit->deposit_name]));
			\Session::flash('success', 'Simpanan Berhasil Dihapus.');
            $deposit->delete();
			return redirect('deposits');
        }
    }

    public function pengambilan_simpanan(){
    	return view('report.deposit.pengambilan-simpanan');
	}

	public function list_pengambilan_simpanan($query){
		if($query == 'all')
		{
//			$query = PencairanSimpanan::with('member')->get();
            $query = PencairanSimpanan::getPencairanSimpananArea(auth()->user()->region);
		}
		return \DataTables::of($query)
			->editColumn('nik', function($pencairan){
				return $pencairan->member['nik_koperasi'];
			})
			->editColumn('name', function($pencairan){
				return $pencairan->member['first_name'] . ' ' . $pencairan->member['last_name'];
			})
			->editColumn('proyek', function($pencairan){
				$project = Project::find($pencairan->member['project_id']);
				if(isset($project->project_name)){
					return $project->project_name;
				}else{
					return '';
				}
			})
			->editColumn('jumlah', function($pencairan){

				return number_format($pencairan->jumlah);
			})
			->editColumn('jumlah_sukarela', function($pencairan){

				$sukarela = TotalDepositMember::totalDepositSukarela($pencairan->member->id);
				return number_format($sukarela);
			})
			->editColumn('status', function($pencairan){
				$position = Position::find($pencairan->status_by);
				if($pencairan->status == 'approved'){
					return 'Approved by ' . $position->name;
				}else{
					return $pencairan->status;
				}
				// return '';
			})
			->addColumn('action', function($pencairan){
				$btnResign = '<a  class="btn btn-primary btn-sm btnEdit" onclick="showRecord('."'".$pencairan->id."'".','."'". csrf_token() ."'".')"><i class="fa fa-edit"></i></a>';

				return $btnResign;

			})->make(true);
	}

	public function list_pengambilan_simpanan_member($id){
        $query = PencairanSimpanan::getPencairanSimpananMember($id);
		return \DataTables::of($query)
			->editColumn('nik', function($pencairan){
				return $pencairan->member['nik_koperasi'];
			})
			->editColumn('name', function($pencairan){
				return $pencairan->member['first_name'] . ' ' . $pencairan->member['last_name'];
			})
			->editColumn('proyek', function($pencairan){
				$project = Project::find($pencairan->member['project_id']);
				if(isset($project->project_name)){
					return $project->project_name;
				}else{
					return '';
				}
			})
			->editColumn('jumlah', function($pencairan){

				return number_format($pencairan->jumlah);
			})
			->editColumn('jumlah_sukarela', function($pencairan){
				$sukarela = TotalDepositMember::totalDepositSukarela($pencairan->member->id);
				return number_format($sukarela);
			})
			->editColumn('status', function($pencairan){

				$position = Position::find($pencairan->status_by);
				if($pencairan->status == 'approved'){
					return 'Approved by ' . $position->name;
				}else{
					return $pencairan->status;
				}
			})->make(true);
	}

	public function getStatus(Request $request){


		$input           = Input::all();
		$idRecord        = $input['id'];
		$selected        = PencairanSimpanan::findOrFail($idRecord);
		$member = $selected->member;
		$member = Member::find($member->id);
		$position = Position::find(auth()->user()->position_id);
		$position_approval = Position::find($selected->status_by);
		$selected->position = $position;
		$selected->position_approval = $position_approval;
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

	public function approve(Request $request){
		$listApprovalPosition = [9, 8, 7, 6];
		$approval = $request->status;
		$note = $request->note;
		$id = $request->id;
        $global = new GlobalController();
		$pencairan = PencairanSimpanan::find($id);
		$user = User::find(auth()->user()->id);
		$position = Position::find($user->position_id);
		// var_dump($position->level_id);
		if(!empty($pencairan)) {
			if(!isset($note) && $approval == 'rejected'){
				$data = array(
					'error' => 1,
					'msg'   => 'Alasan Harus Diisi',
				);

				return response()->json($data);
			}

			$pencairan->approval_level = $pencairan->approval_level + 1;
			$pencairan->status = $approval;
			$pencairan->status_by = $position->level_id;
			$pencairan->reason = $note;
			$pencairan->update();

			if($pencairan->approval_level >= 4 && $approval == 'approved'){

				$pokok = new TsDeposits();
				$pokok->member_id = $pencairan->member['id'];
				$pokok->deposit_number = $global->getDepositNumber();
				$pokok->ms_deposit_id = 3;
				$pokok->type = 'credit';
				$pokok->deposits_type = 'wajib';
				$pokok->total_deposit = $pencairan->jumlah;
				$pokok->status = 'paid';
				$pokok->post_date = now()->format('Y-m-d');
				$pokok->desc = $pencairan->reason;
				$pokok->save();

				$pokok_detail = new TsDepositsDetail();
				$pokok_detail->transaction_id = $pokok->id;
				$pokok_detail->deposits_type = 'sukarela';
				$pokok_detail->debit = 0;
				$pokok_detail->credit = $pencairan->jumlah;
				$pokok_detail->total = $pencairan->jumlah;
				$pokok_detail->status = 'paid';
				$pokok_detail->payment_date = cutOff::getCutoff();
				$pokok_detail->save();

				$totalDepositMember = TotalDepositMember::where([
					'member_id' => $pencairan->member['id'],
					'ms_deposit_id' => 3
				])->first();

				$value = $totalDepositMember['value'] - $pencairan->jumlah;
				$totalDepositMember->value = $value;
				$totalDepositMember->save();
			}else{
				if($approval == 'approved'){
					if(isset(auth()->user()->region['id'])){
						$getApproveUser = User::where('region_id', auth()->user()->region['id'])
						->where('position_id', $listApprovalPosition[$pencairan->approval_level])->get();
					}else{
						$getApproveUser = User::where('position_id', $listApprovalPosition[$pencairan->approval_level])->get();
					}
	
					if(empty($getApproveUser)){
						$getApproveUser = User::where('position_id', $listApprovalPosition[$pencairan->approval_level])->get();
					}
	
					// var_dump($getApproveUser);
					foreach($getApproveUser as $value){
						$value->notify(new WaitPencairanSimpananApplication($pencairan)); 
					}
				}else{
					if(isset(auth()->user()->region['id'])){
						$getApproveUser = User::where('region_id', auth()->user()->region['id'])
						->whereIn('position_id', $listApprovalPosition)->get();
					}else{
						$getApproveUser = User::whereIn('position_id', $listApprovalPosition)->get();
					}
	
					if(empty($getApproveUser)){
						$getApproveUser = User::whereIn('position_id', $listApprovalPosition)->get();
					}

					foreach($getApproveUser as $value){
						$value->notify(new PencairanSimpananApplicationStatusRejected($pencairan)); 
					}

					$pencairan->member->user->notify(new PencairanSimpananApplicationStatusRejected($pencairan));
				}
			}

			if($approval == 'approved'){
				if(isset(auth()->user()->region['id'])){
					$getApprovedUser = User::where('region_id', auth()->user()->region['id'])
					->where('position_id', 9)->get();
				}else{
					$getApprovedUser = User::where('position_id', 9)->get();
				}
				$pencairan->member->user->notify(new PencairanSimpananApplicationStatusUpdated($pencairan));
				foreach($getApprovedUser as $val){
					$val->notify(new PencairanSimpananApplicationStatusUpdated($pencairan)); 
				}
			}

			$data = array(
				'error' => 0,
				'msg'   => 'Berhasil diupdate.',
			);

		}else{
			$data = array(
				'error' => 1,
				'msg'   => 'Gagal diupdate.',
			);
		}
		return response()->json($data);
	}

	public function change_deposit(){
		return view('report.deposit.perubahan-simpanan');
	}

	public function list_change_deposit($query){
		if($query == 'all')
		{
			$query = ChangeDeposit::where('approval_level', '<', 2)->with('member')->orderBy('id', 'asc')->get();
		}

		return \DataTables::of($query)
			->editColumn('nik', function($pencairan){
				return $pencairan->member['nik_koperasi'];
			})
			->editColumn('name', function($pencairan){
				return $pencairan->member['first_name'] . ' ' . $pencairan->member['last_name'];
			})
			->editColumn('proyek', function($pencairan){
				$project = Project::find($pencairan->member['project_id']);
				if(isset($project->project_name)){
					return $project->project_name;
				}else{
					return '';
				}
			})
			->editColumn('last_wajib', function($pencairan){
				return number_format($pencairan->last_wajib);
			})
			->editColumn('new_wajib', function($pencairan){
				return number_format($pencairan->new_wajib);
			})
			->editColumn('last_sukarela', function($pencairan){
				return number_format($pencairan->last_sukarela);
			})
			->editColumn('new_sukarela', function($pencairan){
				return number_format($pencairan->new_sukarela);
			})
			->editColumn('status', function($pencairan){
				$position = Position::find($pencairan->status_by);
				if($pencairan->status == 1){
					return 'Approve by ' . $position->name;
				}else if($pencairan->status == 2){
					return 'Rejected by ' . $position->name;
				}
				return 'Not Approve';
			})
			->addColumn('action', function($pencairan){
				$btnResign = '<a  class="btn btn-primary btn-sm btnEdit" onclick="showRecord('."'".$pencairan->id."'".','."'". csrf_token() ."'".')"><i class="fa fa-edit"></i></a>';
				return $btnResign;

			})->make(true);
	}

	public function list_perubahan_simpanan_member($id){
        $query = ChangeDeposit::where('member_id', $id)->with('member')->get();
		return \DataTables::of($query)
			->editColumn('nik', function($perubahan){
				return $perubahan->member['nik_koperasi'];
			})
			->editColumn('name', function($perubahan){
				return $perubahan->member['first_name'] . ' ' . $perubahan->member['last_name'];
			})
			->editColumn('proyek', function($perubahan){
				$project = Project::find($perubahan->member['project_id']);
				if(isset($project->project_name)){
					return $project->project_name;
				}else{
					return '';
				}
			})
			->editColumn('last_wajib', function($perubahan){
				return number_format($perubahan->last_wajib);
			})
			->editColumn('new_wajib', function($perubahan){
				return number_format($perubahan->new_wajib);
			})
			->editColumn('last_sukarela', function($perubahan){
				return number_format($perubahan->last_sukarela);
			})
			->editColumn('new_sukarela', function($perubahan){
				return number_format($perubahan->new_sukarela);
			})
			->editColumn('status', function($perubahan){
				$position = Position::find($perubahan->status_by);
				if($perubahan->status == 1){
					return 'Approve by ' . $position->name;
				}else if($perubahan->status == 2){
					return 'Rejected by ' . $position->name;
				}
				return 'Not Approve';
			})->make(true);
	}

	public function getPerubahanSimpanan(Request $request){


		$input           = Input::all();
		$idRecord        = $input['id'];
		$selected        = ChangeDeposit::findOrFail($idRecord);
		$member = $selected->member;
		$member = Member::find($member->id);
		$position = Position::find(auth()->user()->position_id);
		$position_approval = Position::find($selected->status_by);
		$selected->position = $position;
		$selected->position_approval = $position_approval;
		$bank = $member->bank;
//		return $sukarela = collect($member->depositSukarela);
		if($selected){
			$data        = array(
				'error'    => 0,
				'msg'      => 'Berhasil.',
				'json'     => $selected,
				'transfer' => (count($bank) > 0) ? $bank[0]['bank_name'] .' - an/ '. $bank[0]['bank_account_name'] .' ('.$bank[0]['bank_account_number'] .')' : ''
			);
		} else{
			$data        = array(
				'error' => 1,
				'msg'   => 'Data pencairan tidak ditemukan.',
			);
		}
		return response()->json($data);

	}

	public function change_deposit_approve(Request $request){
		$listApprovalPosition = [9, 8];
		$status = $request->status;
		$note = $request->note;
		$id = $request->id;
		$user = User::find(auth()->user()->id);
		$position = Position::find($user->position_id);

		$pencairan = ChangeDeposit::find($id);
		if(!empty($pencairan)) {
			if(!isset($note) && $status == 2){
				$data = array(
					'error' => 1,
					'msg'   => 'Alasan Harus Diisi',
				);

				return response()->json($data);
			}

			$pencairan->status = $status;
			$pencairan->approval_level = $pencairan->approval_level + 1;
			$pencairan->status_by = $position->level_id;
			$pencairan->reason = $note;
			$pencairan->update();

			if($pencairan->approval_level >= 2 && $status == 1){
				$configDepositWajib = ConfigDepositMembers::where('member_id', $pencairan->member_id)->where('type', 'wajib')->first();
				$configDepositWajib->value = (int) $pencairan->new_wajib;
				$configDepositWajib->save();

				$configDepositSukarela = ConfigDepositMembers::where('member_id', $pencairan->member_id)->where('type', 'sukarela')->first();
				$configDepositSukarela->value = (int) $pencairan->new_sukarela;
				$configDepositSukarela->save();
			}else{
				if($status == 1){
					if(isset(auth()->user()->region['id'])){
						$getApproveUser = User::where('region_id', auth()->user()->region['id'])
						->where('position_id', $listApprovalPosition[$pencairan->approval_level])->get();
					}else{
						$getApproveUser = User::where('position_id', $listApprovalPosition[$pencairan->approval_level])->get();
					}
	
					if(empty($getApproveUser)){
						$getApproveUser = User::where('position_id', $listApprovalPosition[$pencairan->approval_level])->get();
					}
	
					// var_dump($getApproveUser);
					foreach($getApproveUser as $value){
						$value->notify(new WaitChangeDepositApplication($pencairan)); 
					}
				}else{
					if(isset(auth()->user()->region['id'])){
						$getApproveUser = User::where('region_id', auth()->user()->region['id'])
						->whereIn('position_id', $listApprovalPosition)->get();
					}else{
						$getApproveUser = User::whereIn('position_id', $listApprovalPosition)->get();
					}
	
					if(empty($getApproveUser)){
						$getApproveUser = User::whereIn('position_id', $listApprovalPosition)->get();
					}

					foreach($getApproveUser as $value){
						$value->notify(new ChangeDepositApplicationStatusRejected($pencairan)); 
					}

					$pencairan->member->user->notify(new ChangeDepositApplicationStatusRejected($pencairan));
				}
			}

			if($status == 1){
				$pencairan->member->user->notify(new PencairanSimpananApplicationStatusUpdated($pencairan));
			}

			$data = array(
				'error' => 0,
				'msg'   => 'Berhasil diupdate.',
			);

		}else{
			$data = array(
				'error' => 1,
				'msg'   => 'Gagal diupdate.',
			);
		}
		return response()->json($data);
	}

	public function penambahan_simpanan(){
    	return view('report.deposit.penambahan-simpanan');
	}

	public function list_penambahan_simpanan($query){
		if($query == 'all')
		{
//			$query = PencairanSimpanan::with('member')->get();
            $query = TsDeposits::getDepositAreaPending(auth()->user()->region);
		}
		return \DataTables::of($query)
			->editColumn('nik', function($penambahan){
				return $penambahan->member['nik_koperasi'];
			})
			->editColumn('name', function($penambahan){
				return $penambahan->member['first_name'] . ' ' . $penambahan->member['last_name'];
			})
			->editColumn('proyek', function($penambahan){
				$project = Project::find($penambahan->member['project_id']);
				if(isset($project->project_name)){
					return $project->project_name;
				}else{
					return '';
				}
			})
			->editColumn('deposit_type', function($penambahan){
				$deposit = Deposit::find($penambahan->ms_deposit_id);
				return $deposit->deposit_name;
			})
			->editColumn('jumlah', function($penambahan){

				return number_format($penambahan->total_deposit);
			})
			->editColumn('status', function($penambahan){
				return $penambahan->status;
				// return '';
			})
			->addColumn('action', function($penambahan){
				$btnResign = '<a  class="btn btn-primary btn-sm btnEdit" onclick="showRecord('."'".$penambahan->id."'".','."'". csrf_token() ."'".')"><i class="fa fa-edit"></i></a>';

				return $btnResign;

			})->make(true);
	}

	public function getStatusPenambahan(Request $request){


		$input           = Input::all();
		$idRecord        = $input['id'];
		$selected        = TsDeposits::findOrFail($idRecord);
		$jenis			 = Deposit::find($selected->ms_deposit_id);
		$selected->jenis = $jenis;
		$position = Position::find(auth()->user()->position_id);
		$selected->position = $position;
//		return $sukarela = collect($member->depositSukarela);
		if($selected){
			$data        = array(
				'error'    => 0,
				'msg'      => 'Berhasil.',
				'json'     => $selected
			);
		} else{
			$data        = array(
				'error' => 1,
				'msg'   => 'Data pencairan tidak ditemukan.',
			);
		}

		return response()->json($data);

	}

	public function approvePenambahan(Request $request){

		$approval = $request->status;
		$id = $request->id;
        $global = new GlobalController();
		$penambahan = TsDeposits::find($id);
		$penambahan_detail = TsDepositsDetail::where('transaction_id', $id)->first();
		// var_dump($position->level_id);
		if(!empty($penambahan)) {
			if($approval == "approved"){
				$penambahan->status = 'paid';
				$penambahan->update();

				$penambahan_detail->status = 'paid';
				$penambahan_detail->update();

				$totalDepositMember = TotalDepositMember::where([
					'member_id' => $penambahan['member_id'],
					'ms_deposit_id' => $penambahan['ms_deposit_id']
				])->first();

				if(isset($totalDepositMember)){
					$value = $totalDepositMember['value'] + $penambahan['total_deposit'];
					$totalDepositMember->value = $value;
					$totalDepositMember->save();
				}else{
					$totalDepositMember = new TotalDepositMember();
					$totalDepositMember->member_id = $penambahan['member_id'];
					$totalDepositMember->ms_deposit_id = $penambahan['ms_deposit_id'];
					$totalDepositMember->value = $penambahan['total_deposit'];
					$totalDepositMember->save();
				}
			}else{
				$penambahan->status = $approval;
				$penambahan->update();

				$penambahan_detail->status = $approval;
				$penambahan_detail->update();
			}

			if($approval == 'approved'){
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
					$value->notify(new WaitPenambahanSimpananApplication($penambahan)); 
				}

				$penambahan->member->user->notify(new PenambahanSimpananApplicationStatusUpdated($penambahan));
			}else{
				if(isset(auth()->user()->region['id'])){
					$getApproveUser = User::where('region_id', auth()->user()->region['id'])
					->whereIn('position_id', [9, 8])->get();
				}else{
					$getApproveUser = User::whereIn('position_id', [9, 8])->get();
				}

				if(empty($getApproveUser)){
					$getApproveUser = User::whereIn('position_id', [9, 8])->get();
				}

				foreach($getApproveUser as $value){
					$value->notify(new PenambahanSimpananApplicationStatusRejected($penambahan)); 
				}

				$penambahan->member->user->notify(new PenambahanSimpananApplicationStatusRejected($penambahan));
			}

			$data = array(
				'error' => 0,
				'msg'   => 'Berhasil diupdate.',
			);

		}else{
			$data = array(
				'error' => 1,
				'msg'   => 'Gagal diupdate.',
			);
		}
		return response()->json($data);
	}
}
