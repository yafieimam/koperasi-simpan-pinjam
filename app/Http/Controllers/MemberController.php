<?php

namespace App\Http\Controllers;

use App\ChangeDeposit;
use App\Level;
use App\PencairanSimpanan;
use App\Policy;
use App\Position;
use App\Resign;
use App\User;
use App\TotalDepositMember;
use NotificationChannels\OneSignal\OneSignalChannel;
use Redirect;
use Validator;
use App\Member;
use App\TsLoans;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\WaitPencairanSimpananApplication;
use App\Notifications\WaitChangeDepositApplication;
use App\Notifications\WaitPenambahanSimpananApplication;

// use Carbon;
class MemberController extends GlobalController
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $isCanViewMemberProfile = auth()->user()->can('view.member.profile');
        $isCanViewDeposit = auth()->user()->can('view.member.deposit');
        $isCaneViewLoan = auth()->user()->can('view.transaction.member.loan');
		$selected = auth()->user()->getMember();
        if (request()->ajax()) {
            return DataTables::of($selected)
            ->editColumn('fullname', function ($selected) {
                return  $selected->first_name .' '.$selected->last_name;
			})
			->editColumn('project', function ($selected) {
                if($selected->project_id != ''){
                return $selected->Project->project_name;
                } else {
                return 'Tidak ditemukan';
                }
            })
            ->editColumn('start_date', function ($selected) {
                $start = $selected->keterangan;
                if($selected->start_date != null && $selected->start_date != ''){
                    $start = $this->dateTime($selected->start_date, 'indo');
                }

                return $start;
            })
            ->editColumn('end_date', function ($selected) {
                $end = $selected->keterangan;
                if($selected->end_date != null && $selected->end_date != ''){
                    $end = $this->dateTime($selected->end_date, 'indo');
                }else{
                    $end = 'TETAP';
                }

                return $end;

			})
            ->editColumn('status', function ($selected){
                // if($selected->is_active == 1){
                //     $is_active = 'Aktif';
                // } else {
                //     $is_active = 'Tidak Aktif';
                // }
                return ucwords($selected->status);
            })
            ->addColumn('action',function($selected) use ($isCanViewMemberProfile) {

                $btnMemberProfile='';
                if($isCanViewMemberProfile){
                    $btnMemberProfile = ' <a  class="btn btn-info btn-sm btnEdit" href="/profile-member/'.Crypt::encrypt($selected->user_id).'"  data-toggle="tooltip" title="Cek data"><i class="ion ion-aperture"></i></a>';
                }
                return '<center>'.$btnMemberProfile.'</center>';

                // return
                // '<center>
                // <a  class="btn btn-info btn-sm btnEdit" href="/members/'.$selected->id.'/edit"><i class="glyphicon glyphicon-pencil"></i></a>
                // <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="destroyData('."'members'".','."'".$selected->id."'".','."'". csrf_token() ."'".','."'listPosition'".')">
                // <i class="glyphicon glyphicon-trash" data-token="{{ csrf_token() }}"></i></a>
                // </center>';
            })
            ->addColumn('deposit',function($selected) use ($isCanViewDeposit) {

                $btnDeposit = '';
                if($isCanViewDeposit){
                    $btnDeposit = '<a  class="btn btn-info btn-sm" href="/member-detail-deposit/'.$selected->id.'"  data-toggle="tooltip" title="Deposit">Deposit</a>';
                }
                return '<center>'.$btnDeposit.'</center>';
            })
            ->addColumn('loan',function($selected) use ($isCaneViewLoan){

                $btnLoan ='';
                if($isCaneViewLoan){
                    $btnLoan = '<a  class="btn btn-info btn-sm" href="/member-detail-loan/'.$selected->id.'"  data-toggle="tooltip" title="Loan">Loan</a>';
                }
                return '<center>'.$btnLoan.'</center>';
            })
            ->make(true);
        }
        return view('members.member-list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    public function viewProfile($id)
    {
        $member = Member::findOrFail($id);
        return view('members.profile.profile-index', compact('member'));
    }

    public function getMemberData($id)
    {
        $member = Member::where('id', $id)->with('project')->first();
        return response()->json($member);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $id;
	}


	public function register(Request $request){
		// dd($request);
	}

	public function retrieve_deposit()
	{

		$member = auth()->user()->member;
        $totalWajib = TotalDepositMember::totalDepositWajib($member->id);
        $totalPokok = TotalDepositMember::totalDepositPokok($member->id);
        $totalSukarela = TotalDepositMember::totalDepositSukarela($member->id);
        $totalShu = TotalDepositMember::totalDepositShu($member->id);
        $totalLainnya = TotalDepositMember::totalDepositLainnya($member->id);
		$swajib = collect($member->depositWajib);
		$sukarela = collect($member->depositSukarela);
		$slain = collect($member->depositLainlain);
        $pokok = collect($member->depositPokok);
        $minSukarela = collect($member->pencairanSukarela);
		// $pokok = $member->pokok->value;
		$data = [
			'wajib' => $totalWajib,
			'sukarela' => $totalSukarela,
			'lainnya' => $totalLainnya,
            'pokok' => $totalPokok,
            // 'wajib' => $swajib->sum('total'),
			// 'sukarela' => $sukarela->sum('total') - $minSukarela->sum('total'),
			// 'lainnya' => $slain->sum('total'),
            // 'pokok' => $pokok->sum('total'),
			// 'pokok' => $pokok,
            'policy' => Policy::where('id', 4)->first(),
		];
		return view('master.retrieve-deposit', compact('data'));
	}

	public function post_retrieve_deposit(Request $request)
	{
        $offTrans = false;
        if($offTrans){
            return redirect('retrieve-member-deposits')->with('error', 'Fitur Transaksi ini sedang tidak dapat digunakan');
        }
		$member = auth()->user()->member;

		if($this->revive($request->jumlah) > $request->sukarela){
			return redirect('retrieve-member-deposits')->with('error', 'Dana yang diajukan lebih besar dari jumlah simpanan');
		}
		$bank =  $member->bank[0];

        if(empty($bank)){
            return redirect('retrieve-member-deposits')->with('error', 'Anda belum memiliki data bank, silahkan tambahkan informasi bank anda.');
        }

        $checkLoan = TsLoans::where([
            ['member_id', $member->id],
            ['status', 'menunggu'],
            ['status', 'belum lunas'],
            ['status', 'disetujui'],
        ])->first();
        
        if($checkLoan) {
            return redirect('retrieve-member-deposits')->with('error', 'Anda masih memiliki pinjaman berjalan.');
        }

		$pencairan = new PencairanSimpanan();
		$pencairan->member_id = $member->id;
		$pencairan->bank_id = $bank->id;
		$pencairan->jumlah = $this->revive($request->jumlah);
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

		return redirect('retrieve-member-deposits')->with('message', 'Pengajuan pencairan berhasil');
	}

	public function change_deposit()
	{
		$dat = [];
		$member = auth()->user()->member;
		$deposit_info = $member->configDeposits;
        $data['wajib'] = 0;
        $data['sukarela'] = 0;
        $data['pokok'] = 0;
        $data['lainnya'] = 0;
		foreach ($deposit_info as $val){
//			dd($val->value);
			if($val->type == 'wajib'){
				$data['wajib'] = (int) $val->value;
			}else if($val->type == 'sukarela'){
				$data['sukarela'] = (int) $val->value;
			}else if($val->type == 'pokok'){
				$data['pokok'] = (int) $val->value;
			}else if($val->type == 'lainnya'){
				$data['lainnya'] = (int) $val->value;
			}

		}
		return view('master.change-deposit', compact('data'));
	}

	public function post_change_deposit(Request $request)
	{
        $offTrans = false;
        if($offTrans){
            return redirect('change-member-deposits')->with('error', 'Fitur Transaksi ini sedang tidak dapat digunakan');
        }
        $getUser = auth()->user();
		$member = auth()->user()->member;

		if($request->wajib == 0 || $request->wajib == ''){
			return redirect('change-member-deposits')->with('error', 'dana yang diubah tidak boleh nol 0');
		}

		$perubahan = new ChangeDeposit();
		$perubahan->member_id = $member->id;
		$perubahan->date = now()->format('Y-m-d');
		$perubahan->last_wajib = $request->last_wajib;
		$perubahan->last_sukarela = $request->last_sukarela;
		$perubahan->new_wajib = $this->revive($request->wajib);
		$perubahan->new_sukarela = $this->revive($request->sukarela);
		$perubahan->status = false;
		$perubahan->save();

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

		// $users = User::where('id', 1)->whereNotNull('os_token')->get();
		// $perubahan->blastTo($users,['mail',OneSignalChannel::class]);
        

		return redirect('change-member-deposits')->with('message', 'pengajuan perubahan simpanan berhasil');
	}

    public function viewKartuAnggota()
    {
        $member = Member::find(auth()->user()->member->id);
        if(isset($member->position->name)){
            return view('member.kartu-anggota.main', compact('member'));
        }else{
            session()->flash('errors',collect(['Anda Belum Mengisi Data Posisi Secara Lengkap']));
            return redirect()->back();
        }
    }

    public function downloadKartuAnggota()
    {
        $member = Member::find(auth()->user()->member->id);
//        $pdf = \PDF::loadView('member.kartu-anggota.download', compact('member'));
//        return $pdf->download('kartu-anggota-ksbsp.pdf');
        return view('member.kartu-anggota.download', compact('member'));
    }

    public function updateMemberAdmin(){
        ini_set("memory_limit", "10056M");
        $Csv = new \App\Helpers\CsvToArray();
        $file = base_path() . '/database/seeds/csv/update_admin_member.csv';
        $csv = utf8_encode(file_get_contents($file));
        $array = explode("\r", $csv);
        $data = array_map('str_getcsv', $array);

        $csv_data = array_slice($data, 0, 5000);
        foreach ($csv_data as $key => $val) {
            $iAdmin = str_replace("ï»¿", '', $val);
            $user = User::where('email', $iAdmin[2].'@gmail.com')->first();
            $position = Position::where('description', $val[1])->first();
            $level = Level::find($position['level_id']);
//            $user->assignRole($level['name']);
            // dd($user);

        }


        return 'updated';
    }

}
