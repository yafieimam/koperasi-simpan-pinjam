<?php

namespace App\Http\Controllers;

use App\TsLoans;
use DB;
use App\Loan;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\GlobalController;

class LoanController extends Controller
{
    function __construct()
    {
        $this->globalFunc        = new GlobalController();
    }

    public function index()
    {
        $selected = Loan::get();
        if (request()->ajax()) {
            return DataTables::of($selected)
            ->editColumn('plafon', function ($selected) {
                return  'Rp '.number_format($selected->plafon);
            })
            ->editColumn('tenor', function ($selected) {
                return  $selected->tenor .' Bulan';
            })
            ->editColumn('attachment', function ($selected) {
                if($selected->attachment == 1){
                    $attachment = 'Wajib';
                } else {
                    $attachment = 'Tidak Wajib';                    
                }
                return  $attachment;
            })
            ->addColumn('action',function($selected){
                return 
                '<center>
                <a  class="btn btn-info btn-sm btnEdit" href="/loans/'.$selected->id.'/edit"><i class="glyphicon glyphicon-pencil"></i></a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="destroyData('."'loans'".','."'".$selected->id."'".','."'". csrf_token() ."'".','."'listLoans'".')">
                <i class="glyphicon glyphicon-trash" data-token="{{ csrf_token() }}"></i></a>
                </center>';
            })
            ->make(true);
        }
        return view('transaction.loan.loan-list');
    }

    public function create()
    {
        $data = array(
            'title'            => 'create',
            'id'               => '',
            'loan_name'        => '',
            'rate_of_interest' => '',
            'provisi'          => '',
            'plafon'           => '',
            'description'      => '',
            'attachment'       => '',
            'logo'            => null,
            'tenor'           => 0,
            'biaya_admin'     => 0,
            'biaya_bunga_berjalan' => 0,
            'biaya_transfer' => 0,
            'publish'         => 0
        );
        return view('transaction.loan.loan-create', $data);
    }

    public function store(Request $request)
    {
        $findLoan                = Loan::where('loan_name', $request->loan_name)->count();
        if($findLoan   > 0){
            \Session::flash('error', 'Nama Pinjaman sudah dipakai.');
            return redirect('loans/create');
        }else{
            $logo = $request->file('logo');
            $new_name = null;
            if($logo){
                $new_name = rand() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('images'), $new_name);
            }

            $loans                   = new Loan();
            $loans->id               = $loans::max('id')+1;
            $loans->loan_name        = input::get('loan_name');
            $loans->rate_of_interest = input::get('rate_of_interest');
            $loans->provisi          = input::get('provisi');
            $loans->plafon           = $this->globalFunc->revive(input::get('plafon'));
            $loans->description      = input::get('description');
            $loans->attachment       = input::get('attachment');
            $loans->biaya_admin      = $this->globalFunc->revive(input::get('biaya_admin'));
            $loans->biaya_bunga_berjalan = $this->globalFunc->revive(input::get('biaya_bunga_berjalan'));
            $loans->biaya_transfer = $this->globalFunc->revive(input::get('biaya_transfer'));
            $loans->tenor            = $this->globalFunc->revive(input::get('tenor'));
            $loans->publish          = input::get('publish');
            $loans->logo             = $new_name;
            $loans->save();
            \Session::flash('success', 'Data Pinjaman baru berhasil ditambahkan.');
            return redirect('loans');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $loans = Loan::findOrFail($id);
        $data  = array(
                    'title'            => 'edit',
                    'id'               => $id,
                    'loan_name'        => $loans->loan_name,
                    'rate_of_interest' => (int)$loans->rate_of_interest,
                    'provisi'          => (int)$loans->provisi,
                    'plafon'           => (int)$loans->plafon,
                    'description'      => $loans->description,
                    'attachment'       => $loans->attachment,
                    'tenor'            => (int)$loans->tenor,
                    'biaya_admin'      => (int)$loans->biaya_admin,
                    'biaya_bunga_berjalan' => (int)$loans->biaya_bunga_berjalan,
                    'biaya_transfer' => (int)$loans->biaya_transfer,
                    'logo'             => $loans->logo,
                    'publish'          => $loans->publish
                );
        return view('transaction.loan.loan-create', $data);
    }

    public function update(Request $request, $id)
    {
        $logo = $request->file('logo');
        $new_name = null;

        $loans                   = Loan::findOrFail($id);
        $loans->loan_name        = $request->loan_name;
        $loans->rate_of_interest = $request->rate_of_interest;
        $loans->provisi          = $request->provisi;
        $loans->plafon           = $this->globalFunc->revive($request->plafon);
        $loans->attachment       = $request->attachment;
        $loans->description      = $request->description;
        $loans->tenor            = $this->globalFunc->revive($request->tenor);
        $loans->biaya_admin      = $this->globalFunc->revive($request->biaya_admin);
        $loans->biaya_bunga_berjalan = $this->globalFunc->revive($request->biaya_bunga_berjalan);
        $loans->biaya_transfer = $this->globalFunc->revive($request->biaya_transfer);
        $loans->publish          = $request->publish == '' ? 0 : 1;
        if(isset($logo)){
            if($loans->logo !== null){
                $path = public_path('images/'.$loans->logo);
                unlink($path);
            }

            $new_name = rand() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('images'), $new_name);
            $loans->logo             = $new_name;
        }
        $loans->save();
        \Session::flash('message', 'Data pinjaman  berhasil diperbaharui.');
        return redirect('loans');
    }

    public function destroy($id)
    {
        $loans = Loan::findOrFail($id);

        // if($loans->logo !== null){
        //     $path = public_path('images/'.$loans->logo);
        //     unlink($path);
        // }

        $tsLoans = TsLoans::where('loan_id', $id)->get();
        if(count($tsLoans) > 0){
            $data = 'Failed';
        }else{
            if (Loan::destroy($id)) {
                $data = 'Success';
            }else{
                $data = 'Failed';
            }
        }
         return response()->json($data);
    }
}
