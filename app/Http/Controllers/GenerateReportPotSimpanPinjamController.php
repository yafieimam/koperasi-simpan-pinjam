<?php

namespace App\Http\Controllers;

use App\Helpers\DownloadReport;
use App\Helpers\reverseDataHelper;
use App\Project;
use App\Region;
use App\Member;
use Illuminate\Http\Request;

class GenerateReportPotSimpanPinjamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $indents = [];
        return view('report.generate.pot-anggota.new', compact('indents'));
    }

    public function getMember(Request $request)
    {
        $allData[] = (object) ["id" => "ALL", "first_name" => "ALL", 'last_name' => ''];
        if($request->area == "ALL"){
            if(isset($request->proyek)){
                if ($request->search != '') {
                    $cari = $request->search;
                    $member = Member::FActive()->select('id','first_name', 'last_name')
                        ->where('project_id', $request->proyek)
                        ->where('first_name', 'LIKE', '%'.$cari.'%')
                        ->orWhere('nik_koperasi', $cari)
                        ->orWhere('nik_bsp', $cari)
                        ->get()->toArray();

                    return response()->json($member);
                }else{
                    $member = Member::FActive()->select('id','first_name', 'last_name')
                        ->where('project_id', $request->proyek) 
                        ->get()->toArray();

                    $member = array_merge($allData, $member);
                    return response()->json($member);
                }
            }else{
                $member = Member::FActive()->select('id','first_name', 'last_name')
                    ->get()->toArray();

                $member = array_merge($allData, $member);
                return response()->json($member);
            }
        }else{
            if(isset($request->proyek)){
                if ($request->search != '') {
                    $cari = $request->search;
                    $member = Member::FActive()->select('id','first_name', 'last_name')
                        ->where('region_id', $request->area)
                        ->where('project_id', $request->proyek)
                        ->where('first_name', 'LIKE', '%'.$cari.'%')
                        ->orWhere('nik_koperasi', $cari)
                        ->orWhere('nik_bsp', $cari)
                        ->get()->toArray();

                    return response()->json($member);
                }else{
                    $member = Member::FActive()->select('id','first_name', 'last_name')
                        ->where('region_id', $request->area)
                        ->where('project_id', $request->proyek) 
                        ->get()->toArray();

                    $member = array_merge($allData, $member);
                    return response()->json($member);
                }
            }else{
                $member = Member::FActive()->select('id','first_name', 'last_name')
                    ->where('region_id', $request->area)
                    ->get()->toArray();

                $member = array_merge($allData, $member);
                return response()->json($member);
            }
        }

        // return [];
    }

    public function getArea(Request $request)
    {
        $allData[] = (object) ["id" => "ALL", "name_area" => "ALL", 'code' => 'ALL'];
        if ($request->has('q')) {
            $cari = $request->get('q');
            $area = Region::select('id','name_area', 'code')
                ->where('name_area', 'LIKE', '%'.$cari.'%')
                ->orWhere('code', $cari)
                ->get()->toArray();

            return response()->json($area);
        }else{
            $area = Region::select('id','name_area','code')
                ->get()->toArray();

            $area = array_merge($allData, $area);
            return response()->json($area);
        }

        // return [];
    }

    public function getProyek(Request $request)
    {
        $allData[] = (object) ["id" => "ALL", "project_name" => "ALL", 'project_code' => 'ALL'];
        if(isset($request->area)){
            if ($request->search != '') {
                $cari = $request->search;
                $proyek = Project::select('id','project_code', 'project_name')
                    ->where('region_id', $request->area)
                    ->where('project_name', 'LIKE', '%'.$cari.'%')
                    ->orWhere('project_code', $cari)
                    ->get()->unique('project_name')->toArray();

                // $proyek = array_merge($allData, $proyek);
                return response()->json($proyek);
            }else{
                $proyek = Project::select('id','project_code', 'project_name')
                    ->where('region_id', $request->area)
                    ->get()->unique('project_name')->toArray();

                $proyek = array_merge($allData, $proyek);   
                return response()->json($proyek);
            }
        }else{
            $proyek = Project::select('id','project_code', 'project_name')
                ->get()->unique('project_name')->toArray();

            $proyek = array_merge($allData, $proyek);   
            return response()->json($proyek);
        }

        // return [];
    }

    public function getData(Request $request)
    {
        $periode = $request->get('periode');
        // $end = $request->get('end');
        // if(!empty($request->get('end'))){
        //     $end = $request->get('end');
        // }else{
        //     $end = $start;
        // }
        $area = $request->get('area');
        $proyek = $request->get('proyek');
        // $member_id = $request->get('member_id');
        $dataPot = reverseDataHelper::generatePotAnggota($periode, $area, $proyek);
        $output = '';
        foreach ($dataPot as $key => $potongan) {
            $output .= '<tr>' .
                '<td style="text-align: center;">' . $potongan['proyek'] . '</td>' .
                '<td style="text-align: center;">' . $potongan['total_anggota'] . '</td>' .
                '<td style="text-align: center;">' . number_format($potongan['pokok']) . '</td>' .
                '<td style="text-align: center;">' . number_format($potongan['sukarela']) . '</td>' .
                '<td style="text-align: center;">' . number_format($potongan['wajib']) . '</td>' .
                '<td style="text-align: center;">' . number_format($potongan['pinj_tunai_total']) . '</td>' .
                '<td style="text-align: center;">' . number_format($potongan['pinj_barang_total']) . '</td>' .
                '<td style="text-align: center;">' . number_format($potongan['pinj_pendidikan_total']) . '</td>' .
                '<td style="text-align: center;">' . number_format($potongan['pinj_softloan_total']) . '</td>' .
                '<td style="text-align: center;">' . number_format($potongan['pinj_kendaraan_total']) . '</td>' .
                '<td style="text-align: center;">' . number_format($potongan['total']) . '</td>' .
                '</tr>';
        }
        
        return Response($output);
    }

    public function download(Request $request){

        $start = $request->get('start');
        // $end = $request->get('end');
        if(!empty($request->get('end'))){
            $end = $request->get('end');
        }else{
            $end = $start;
        }
        $area = $request->get('area');
        $proyek = $request->get('proyek');
        $dataPotongan = reverseDataHelper::generatePotAnggota($start, $end, $area, $proyek);
//        return $memberResign;
        $spreadsheet = DownloadReport::downloadPotAnggota($dataPotongan, $start, $end);
        $filename = 'Laporan Potongan Simpan Pinjam.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $path = \Storage::disk('deposit')->path($filename);
        $writer->save($path);
        if(\Storage::disk('deposit')->exists($filename)){
            return response()->download($path, $filename)->deleteFileAfterSend(true);
        }
        return redirect()->back();
    }
}
