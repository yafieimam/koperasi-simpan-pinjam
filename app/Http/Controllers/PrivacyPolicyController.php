<?php

namespace App\Http\Controllers;

use App\Policy;
use App\Qna;
use App\Loan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PrivacyPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function privacy()
    {
        $privacy = Policy::find(6);
        return view('privacy-policy', compact('privacy'));
    }

    public function getQna()
    {
        $DataQna = Qna::where('id', '!=', 3)->get();
		return view('setting.tanya-jawab', compact('DataQna'));
    }

    public function getListProdukPinjamanQna()
    {
        $this->i  = 1;
        $selected = Loan::whereIn('id', array(1, 2, 3, 4, 10, 13, 14, 15, 16));
        return \DataTables::of($selected)
            ->editColumn('no', function ($selected) {
                return $this->i++;
            })
            ->editColumn('produk_pinjaman', function ($selected) {
                return $selected->loan_name;
            })
            ->editColumn('jasa', function ($selected) {
                return $selected->rate_of_interest;
            })
            ->make(true);
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
        //
    }
}
