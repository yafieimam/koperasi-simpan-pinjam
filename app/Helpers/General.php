<?php
namespace App\Helpers;

ini_set('max_execution_time', '0');
set_time_limit(0);

use App\Exceptions\ChangeConnectionException;

use App\GeneralSetting;
use App\Member;
use App\Position;
use App\Bank;
use App\Loan;
use App\Region;
use App\Project;
use App\Resign;
use App\TsDeposits;
use App\PencairanSimpanan;
use App\TsLoans;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use function Psy\sh;

class ResponseHelper
{
	/**
	 * Formatted Json Response to FrontEnd
	 * @param int $code
	 * @param $data
	 * @param String $message
	 * @param array $header
	 * @return \Illuminate\Http\JsonResponse
	 */
	public static function json($data ,int $code,  $message = '', $header = []){
	    return response()->json(['result'=>$data,'message'=>$message], $code, $header);
	}
}

class CsvToArray
{
    function csv_to_array($filename = '', $header)
    {
        $delimiter = ',';
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

}

class ReverseData
{
    public static function buildAdminData($admin){
        $admin->map(function($a){
            $region = Region::where('name_area', 'like', $a[1])->first();
            $position = Position::where('description', 'like', $a[5])->first();
            return [
                'user' => [
                    'email' => $a[3],
                    'password' => \Hash::make($a[4]),
                    'name' => $a[0],
                    'username' => $a[0],
                    'position' => $position['id']
                ],
                'member' => [
                    'email' => $a[3],
                    'first_name' => $a[0],
                    'region_id' => $region['id'],
                    'position_id' => $position['id'],
                    'is_active' => 1,
                    'status' => 'aktif'
                ]
            ];
        });
    }

    public static function genAlphabet($colPosition, $param, $value){
        if($param === '-'){
            return chr(ord(strtoupper($colPosition)) - $value);
        }
        return chr(ord(strtoupper($colPosition)) + $value);

    }

    public static function getNameFromNumber($num) {
        $numeric = $num % 26;
        $letter = chr(66 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return self::getNameFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }
}

class cutOff
{
    public static function getCutoff()
    {
        $carbon = now()->format('Y-m');
        $cutOff = GeneralSetting::where('name', 'cut-off')->first();
        $from = Carbon::parse($carbon.'-'.$cutOff->content);

        if($from->lte(now())){
            $from = Carbon::parse($carbon.'-'.$cutOff->content)->addMonth(1)->format('Y-m-d');
        }else{
            $from = Carbon::parse($carbon.'-'.$cutOff->content)->format('Y-m-d');
        }

        return $from;
    }

    public static function getCutoffBungaBerjalan()
    {
        $carbon = now()->format('Y-m');
        $cutOff = GeneralSetting::where('name', 'cut-off')->first();
        $from = Carbon::parse($carbon.'-'.$cutOff->content)->format('Y-m-d');

        return $from;
    }

    public static function getPemotongan()
    {
        $carbon = now()->format('Y-m');
        $cutOff = GeneralSetting::where('name', 'generate-potongan')->first();
        $from = Carbon::parse($carbon.'-'.$cutOff->content);

        if($from->lte(now())){
            $from = Carbon::parse($carbon.'-'.$cutOff->content)->addMonth(1)->format('Y-m-d');
        }else{
            $from = Carbon::parse($carbon.'-'.$cutOff->content)->format('Y-m-d');
        }

        return $from;
    }

    public static function getBungaBerjalan($value, $bunga_berjalan, $tanggal_pengajuan){
        $carbon = now()->format('Y-m');
        $cutOff = GeneralSetting::where('name', 'generate-potongan')->first();
        $tglLebih = GeneralSetting::where('name', 'cut-off')->first();
        $from = Carbon::parse($carbon.'-'.$cutOff->content);
        $tgl_lebih = Carbon::parse($carbon.'-'.$tglLebih->content);
        $tanggalPengajuan = Carbon::parse($tanggal_pengajuan);

        if($from->lte($tanggalPengajuan)){
            if($tanggalPengajuan->gt($tgl_lebih)){
                $diffDays = 0;
            }else{
                $cutOff = self::getCutoffBungaBerjalan();
                $diffDays = $tanggalPengajuan->diffInDays($cutOff);
            }
        }else{
            $to = Carbon::parse($carbon.'-'.$cutOff->content);
            $diffDays = $to->diffInDays($from);
        }
        $bungaBerjalan = $value * ($bunga_berjalan/100);
        $bungaBerjalan = ($bungaBerjalan * $diffDays) / 30;

        return $bungaBerjalan;
    }

    public static function getDayBungaBerjalan($tanggal_pengajuan){
        $carbon = now()->format('Y-m');
        $tglPotong = GeneralSetting::where('name', 'generate-potongan')->first();
        $tglLebih = GeneralSetting::where('name', 'cut-off')->first();
        $from = Carbon::parse($carbon.'-'.$tglPotong->content);
        $tgl_lebih = Carbon::parse($carbon.'-'.$tglLebih->content);
        $tanggalPengajuan = Carbon::parse($tanggal_pengajuan);
        // var_dump($tanggalPengajuan->gt($tgl_lebih));
//        dd($from->lte($tanggalPengajuan));
        if($from->lte($tanggalPengajuan)){
            if($tanggalPengajuan->gt($tgl_lebih)){
                $diffDays = 0;
            }else{
                $cutOff = self::getCutoffBungaBerjalan();
                $diffDays = $tanggalPengajuan->diffInDays($cutOff);
            }
        }else{
            $to = Carbon::parse($carbon.'-'.$tglPotong->content);
            $diffDays = $to->diffInDays($from);
        }

        return $diffDays;
    }
}

class dateConvert
{
    public static function getAllMonth()
    {
        $firstMonth = now()->firstOfYear()->format('Y-m-d');
        $lastMonth = now()->lastOfYear()->format('Y-m-d');
        $months = [];
        foreach (CarbonPeriod::create($firstMonth, '1 month', $lastMonth) as $month) {
            $months[] = $month->format('F Y');
        }
        return collect($months);
    }
}

class grafikData
{
    public static function simpananYearly($start, $end, $region)
    {

        $months = [];
        $values = [];
        $value = 0;
        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {

            $simpanan = TsDeposits::getYearlyDeposit($month, $region);


            if(isset($simpanan->total)){
                $value = $simpanan->total;
            }
            $months[] = $month->format('F Y');
            $values[] = $value;
        }
        $data = [
            'bulan' => $months,
            'value' => $values
        ];
        return collect($data);
    }
}

class DownloadReport{

    public static function generateDeposit($start, $end, $area, $proyek, $member_id)
    {
        $pokok = [];
        $wajib = [];
        $sukarela = [];

        $debitPokok = 0;
        $creditPokok = 0;

        $debitWajib = 0;
        $creditWajib = 0;

        $debitSukarela = 0;
        $creditSukarela = 0;

        // if($status == 'pending'){
        //     $status = 'pending';
        // }else if($status == 'complete'){
        //     $status = 'paid';
        // }else{
        //     $status = 'all';
        // }
            
        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {

            $debitDataPokok = TsDeposits::getYearlyDepositType($month, 'debit', 1, $area, $proyek, $member_id);
            $creditDataPokok = TsDeposits::getYearlyDepositType($month, 'credit', 1, $area, $proyek, $member_id);

            $debitDataWajib = TsDeposits::getYearlyDepositType($month, 'debit', 2, $area, $proyek, $member_id);
            $creditDataWajib = TsDeposits::getYearlyDepositType($month, 'credit', 2, $area, $proyek, $member_id);

            $debitDataSukarela = TsDeposits::getYearlyDepositType($month, 'debit', 3, $area, $proyek, $member_id);
            $creditDataSukarela = TsDeposits::getYearlyDepositType($month, 'credit', 3, $area, $proyek, $member_id);

            if(isset($debitDataPokok->total)){
                $debitPokok = $debitDataPokok->total;
            }

            if(isset($creditDataPokok->total)){
                $creditPokok = $creditDataPokok->total;
            }

            if(isset($debitDataWajib->total)){
                $debitWajib = $debitDataWajib->total;
            }

            if(isset($creditDataWajib->total)){
                $creditWajib = $creditDataWajib->total;
            }

            if(isset($debitDataSukarela->total)){
                $debitSukarela = $debitDataSukarela->total;
            }

            if(isset($creditDataSukarela->total)){
                $creditSukarela = $creditDataSukarela->total;
            }

            $pokok[] = [
                'bulan' => $month->format('F Y'),
                'debit' => $debitPokok,
                'credit' => $creditPokok
            ];

            $wajib[] = [
                'bulan' => $month->format('F Y'),
                'debit' => $debitWajib,
                'credit' => $creditWajib
            ];

            $sukarela[] = [
                'bulan' => $month->format('F Y'),
                'debit' => $debitSukarela,
                'credit' => $creditSukarela
            ];
        }
        $data = [
            'pokok' => $pokok,
            'wajib' => $wajib,
            'sukarela' => $sukarela
        ];
        return collect($data);
    }

    public static function generateLoans($start, $end, $area, $proyek, $member_id){
        $start_join = Carbon::parse($start);
        $end_join = Carbon::parse($end);
        // if($status == 'waiting'){
        //     $status = 'waiting';
        // }else if($status == 'disetujui'){
        //     $status = 'disetujui';
        // }else if($status == 'lunas'){
        //     $status = 'lunas';
        // }else if($status == 'belum lunas'){
        //     $status = 'belum lunas';
        // }else{
        //     $status = 'all';
        // }
        $loan = TsLoans::join('ms_members', 'ms_members.id', '=', 'ts_loans.member_id')
            ->join('ms_loans', 'ms_loans.id', '=', 'ts_loans.loan_id')
            ->whereBetween('ts_loans.start_date', [$start_join, $end_join])
            ->when($area != 'ALL', function ($query) use ($area) {
                return $query->where('region_id', $area);
            })->when($proyek != 'ALL', function ($query) use ($proyek) {
                return $query->where('project_id', $proyek);
            })->when($member_id != 'ALL', function ($query) use ($member_id){
                return $query->where('ms_members.id', $member_id);
            })
            ->select('ts_loans.*', 'ts_loans.loan_number', 'ms_members.first_name', 'ms_members.last_name', 'ms_loans.loan_name', 
            'ts_loans.value', 'ts_loans.period', 'ts_loans.status as status_loan')
            ->get();
        return $loan;
    }

    public static function generatePencairanPinjaman($start, $end, $area, $proyek, $member_id)
    {
        $data = [];
        $totalTunaiA = 0;
        $totalTunaiB = 0;
        $totalBarang = 0;
        $totalPendidikan = 0;
        $totalDarurat = 0;
        $totalSoftloan = 0;
        $totalKendaraan = 0;
        $totalBisnis = 0;

        // if($status == 'disetujui'){
        //     $status = 'disetujui';
        // }else if($status == 'lunas'){
        //     $status = 'lunas';
        // }else if($status == 'belum lunas'){
        //     $status = 'belum lunas';
        // }else{
        //     $status = 'all';
        // }

        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {

            $dataTunaiA = TsLoans::totalPencairanPinjaman($month, 1, $area, $proyek, $member_id);
            $dataTunaiB = TsLoans::totalPencairanPinjaman($month, 2, $area, $proyek, $member_id);
            $dataBarang = TsLoans::totalPencairanPinjaman($month, 3, $area, $proyek, $member_id);
            $dataPendidikan = TsLoans::totalPencairanPinjaman($month, 4, $area, $proyek, $member_id);
            $dataDarurat = 0;
            $dataSoftloan = TsLoans::totalPencairanPinjaman($month, 10, $area, $proyek, $member_id);
            $dataKendaraan = TsLoans::totalPencairanPinjaman($month, 13, $area, $proyek, $member_id);
            $dataBisnis = TsLoans::totalPencairanPinjaman($month, 15, $area, $proyek, $member_id);

            if(isset($dataTunaiA->total)){
                $totalTunaiA = $dataTunaiA->total;
            }

            if(isset($dataTunaiB->total)){
                $totalTunaiB = $dataTunaiB->total;
            }

            if(isset($dataBarang->total)){
                $totalBarang = $dataBarang->total;
            }

            if(isset($dataPendidikan->total)){
                $totalPendidikan = $dataPendidikan->total;
            }

            // if(isset($dataDarurat->total)){
            //     $totalDarurat = $dataDarurat->total;
            // }

            if(isset($dataSoftloan->total)){
                $totalSoftloan = $dataSoftloan->total;
            }

            if(isset($dataKendaraan->total)){
                $totalKendaraan = $dataKendaraan->total;
            }

            if(isset($dataBisnis->total)){
                $totalBisnis = $dataBisnis->total;
            }

            $data[] = [
                'bulan' => $month->format('F Y'),
                'tunai' => $totalTunaiA + $totalTunaiB,
                'barang' => $totalBarang,
                'pendidikan' => $totalPendidikan,
                'darurat' => $totalDarurat,
                'softloan' => $totalSoftloan,
                'kendaraan' => $totalKendaraan,
                'bisnis' => $totalBisnis,
                'total' => $totalTunaiA + $totalTunaiB + $totalBarang + $totalPendidikan + $totalDarurat + $totalSoftloan + $totalKendaraan + $totalBisnis
            ];
        }
        return collect($data);
    }

    public static function generatePiutangPinjaman($start, $end, $area, $proyek, $member_id)
    {
        $data = [];
        $totalTunaiA = 0;
        $totalTunaiB = 0;
        $totalBarang = 0;
        $totalPendidikan = 0;
        $totalDarurat = 0;
        $totalSoftloan = 0;
        $totalKendaraan = 0;
        $totalBisnis = 0;

        // if($status == 'disetujui'){
        //     $status = 'disetujui';
        // }else if($status == 'lunas'){
        //     $status = 'lunas';
        // }else if($status == 'belum lunas'){
        //     $status = 'belum lunas';
        // }else{
        //     $status = 'all';
        // }

        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {

            $dataTunaiA = TsLoans::totalPiutangPinjaman($month, 1, $area, $proyek, $member_id);
            $dataTunaiB = TsLoans::totalPiutangPinjaman($month, 2, $area, $proyek, $member_id);
            $dataBarang = TsLoans::totalPiutangPinjaman($month, 3, $area, $proyek, $member_id);
            $dataPendidikan = TsLoans::totalPiutangPinjaman($month, 4, $area, $proyek, $member_id);
            $dataDarurat = 0;
            $dataSoftloan = TsLoans::totalPiutangPinjaman($month, 10, $area, $proyek, $member_id);
            $dataKendaraan = TsLoans::totalPiutangPinjaman($month, 13, $area, $proyek, $member_id);
            $dataBisnis = TsLoans::totalPiutangPinjaman($month, 15, $area, $proyek, $member_id);

            $data[] = [
                'bulan' => $month->format('F Y'),
                'tunai' => $dataTunaiA + $dataTunaiB,
                'barang' => $dataBarang,
                'pendidikan' => $dataPendidikan,
                'darurat' => $dataDarurat,
                'softloan' => $dataSoftloan,
                'kendaraan' => $dataKendaraan,
                'bisnis' => $dataBisnis,
                'total' => $dataTunaiA + $dataTunaiB + $dataBarang + $dataPendidikan + $dataDarurat + $dataSoftloan + $dataKendaraan + $dataBisnis
            ];
        }
        return collect($data);
    }

    public static function generateFormPencairan($id)
    {
        $sukarela = [];

        $debitSukarela = 0;
        $creditSukarela = 0;

        $getMember = Member::where('id', $id)->with('project')->first();

        foreach (CarbonPeriod::create($getMember->join_date, now()->format('Y-m-d')) as $value) {

            $debitDataSukarela = TsDeposits::getDateDepositType($value, 'debit', 3, 'all');
            $creditDataSukarela = TsDeposits::getDateDepositType($value, 'credit', 3, 'all');

            if(isset($debitDataSukarela->total)){
                $debitSukarela = $debitDataSukarela->total;
            }

            if(isset($creditDataSukarela->total)){
                $creditSukarela = $creditDataSukarela->total;
            }

            if(isset($debitDataSukarela->post_date)){
                $sukarela[] = [
                    'tanggal' => date('d-M-Y', strtotime($debitDataSukarela->post_date)),
                    'debit' => $debitSukarela,
                    'credit' => $creditSukarela
                ];
            }

            if(isset($creditDataSukarela->post_date)){
                $sukarela[] = [
                    'tanggal' => date('d-M-Y', strtotime($creditDataSukarela->post_date)),
                    'debit' => $debitSukarela,
                    'credit' => $creditSukarela
                ];
            }
        }

        $data = [
            'member' => $getMember,
            'sukarela' => $sukarela
        ];
        // var_dump($data);
        return collect($data);
    }

    public static function generateFormResign($id)
    {
        $sukarela = [];

        $debitSukarela = 0;
        $creditSukarela = 0;

        $getMember = Member::where('id', $id)->with('project', 'bank')->first();

        $totalPokok = TsDeposits::totalDepositPokok($id);
        $totalWajib = TsDeposits::totalDepositWajib($id);
        $totalSukarela = TsDeposits::totalDepositSukarela($id);
        $totalShu = TsDeposits::totalDepositShu($id);
        $administrasi = 50000;
        $jumlahHak = ($totalPokok + $totalWajib + $totalSukarela) - $administrasi;

        $loanDataTunai = TsLoans::where('member_id', $id)->whereIn('loan_id', array(1, 2))
        ->where(function($q) {
            $q->where('status', 'disetujui')
            ->orWhere('status', 'belum lunas');
        })->with('detail')->first();
        $loanDataBarang = TsLoans::where('member_id', $id)->where('loan_id', 3)
        ->where(function($q) {
            $q->where('status', 'disetujui')
            ->orWhere('status', 'belum lunas');
        })->with('detail')->first();
        $loanDataPendidikan = TsLoans::where('member_id', $id)->where('loan_id', 4)
        ->where(function($q) {
            $q->where('status', 'disetujui')
            ->orWhere('status', 'belum lunas');
        })->with('detail')->first();
        $loanDataSoftloan = TsLoans::where('member_id', $id)->where('loan_id', 10)
        ->where(function($q) {
            $q->where('status', 'disetujui')
            ->orWhere('status', 'belum lunas');
        })->with('detail')->first();
        $loanDataMotorloan = TsLoans::where('member_id', $id)->where('loan_id', 9)
        ->where(function($q) {
            $q->where('status', 'disetujui')
            ->orWhere('status', 'belum lunas');
        })->with('detail')->first();
        $loanDataLain = TsLoans::where('member_id', $id)->whereNotIn('loan_id', array(1, 2, 3, 4, 9, 10))
        ->where(function($q) {
            $q->where('status', 'disetujui')
            ->orWhere('status', 'belum lunas');
        })->with('detail')->get();

        $valueTunai = (isset($loanDataTunai->id)) ? $loanDataTunai->value: 0;
        $jasaTunai = (isset($loanDataTunai->id)) ? $loanDataTunai->biaya_jasa: 0;
        $valueBarang = (isset($loanDataBarang->id)) ? $loanDataBarang->value: 0;
        $jasaBarang = (isset($loanDataBarang->id)) ? $loanDataBarang->biaya_jasa: 0;
        $valuePendidikan = (isset($loanDataPendidikan->id)) ? $loanDataPendidikan->value: 0;
        $jasaPendidikan = (isset($loanDataPendidikan->id)) ? $loanDataPendidikan->biaya_jasa: 0;
        $valueSoftloan = (isset($loanDataSoftloan->id)) ? $loanDataSoftloan->value: 0;
        $jasaSoftloan = (isset($loanDataSoftloan->id)) ? $loanDataSoftloan->biaya_jasa: 0;
        $valueMotorloan = (isset($loanDataMotorloan->id)) ? $loanDataMotorloan->value: 0;
        $jasaMotorloan = (isset($loanDataMotorloan->id)) ? $loanDataMotorloan->biaya_jasa: 0;
        $valueLain = (isset($loanDataLain)) ? $loanDataLain->sum('value'): 0;
        $jasaLain = (isset($loanDataLain)) ? $loanDataLain->sum('biaya_jasa'): 0;

        $totalJasa = $jasaTunai + $jasaBarang + $jasaPendidikan + $jasaSoftloan + $jasaMotorloan + $jasaLain;

        $countAngsTunai = (isset($loanDataTunai->id)) ? $loanDataTunai->detail->count() : 0;
        $countAngsBarang = (isset($loanDataBarang->id)) ? $loanDataBarang->detail->count() : 0;
        $countAngsPendidikan = (isset($loanDataPendidikan->id)) ? $loanDataPendidikan->detail->count() : 0;
        $countAngsSoftloan = (isset($loanDataSoftloan->id)) ? $loanDataSoftloan->detail->count() : 0;
        $countAngsMotorloan = (isset($loanDataMotorloan->id)) ? $loanDataMotorloan->detail->count() : 0;
        // $countAngsLain = (isset($loanDataLain->id)) ? $loanDataLain->detail->count() : 0;
        $countAngsLain = 0;
        foreach($loanDataLain as $dataDetail){
            $countAngsLain = $countAngsLain + $dataDetail->count();
        }

        $angsTunai = (isset($loanDataTunai->id)) ? $loanDataTunai->detail[0]->value : 0;
        $angsBarang = (isset($loanDataBarang->id)) ? $loanDataBarang->detail[0]->value : 0;
        $angsPendidikan = (isset($loanDataPendidikan->id)) ? $loanDataPendidikan->detail[0]->value : 0;
        $angsSoftloan = (isset($loanDataSoftloan->id)) ? $loanDataSoftloan->detail[0]->value : 0;
        $angsMotorloan = (isset($loanDataMotorloan->id)) ? $loanDataMotorloan->detail[0]->value : 0;
        $angsLain = 0;
        foreach($loanDataLain as $dataDetail){
            $angsLain = $angsLain + (isset($dataDetail[0]->value)) ? $dataDetail[0]->value : 0;
        }

        $jumlahKewajiban = $valueTunai + $valueBarang + $valuePendidikan + $valueSoftloan + $valueMotorloan + $valueLain + $totalJasa;
        $sisaHak = $jumlahHak - $jumlahKewajiban;

        $data = [
            'nama' => $getMember->first_name,
            'no_koperasi' => $getMember->nik_koperasi,
            'no_register' => $getMember->nik_bsp,
            'nama_proyek' => $getMember->project['project_name'],
            'no_rekening' => $getMember->bank[0]['bank_account_number'],
            'bank' => $getMember->bank[0]['bank_name'],
            'simpanan_pokok' => $totalPokok,
            'simpanan_wajib' => $totalWajib,
            'simpanan_sukarela' => $totalSukarela,
            'simpanan_shu' => $totalShu,
            'administrasi' => $administrasi,
            'jumlah_hak' => $jumlahHak,
            'pinjaman_tunai' => $valueTunai,
            'count_tunai' => $countAngsTunai,
            'angs_tunai' => $angsTunai,
            'pinjaman_barang' => $valueBarang,
            'count_barang' => $countAngsBarang,
            'angs_barang' => $angsBarang,
            'pinjaman_pendidikan' => $valuePendidikan,
            'count_pendidikan' => $countAngsPendidikan,
            'angs_pendidikan' => $angsPendidikan,
            'pinjaman_softloan' => $valueSoftloan,
            'count_softloan' => $countAngsSoftloan,
            'angs_softloan' => $angsSoftloan,
            'pinjaman_motorloan' => $valueMotorloan,
            'count_motorloan' => $countAngsMotorloan,
            'angs_motorloan' => $angsMotorloan,
            'pinjaman_lain' => $valueLain,
            'count_lain' => $countAngsLain,
            'angs_lain' => $angsLain,
            'jasa_tunai' => $jasaTunai,
            'jasa_barang' => $jasaBarang,
            'jasa_pendidikan' => $jasaPendidikan,
            'jasa_softloan' => $jasaSoftloan,
            'jasa_motorloan' => $jasaMotorloan,
            'jasa_lain' => $jasaLain,
            'jumlah_jasa' => $totalJasa,
            'jumlah_kewajiban' => $jumlahKewajiban,
            'sisa_hak' => $sisaHak,
        ];
        return collect($data);
    }

    public static function generateMember($start, $end, $area, $proyek, $member_id){
        $start_join = Carbon::parse($start);
        $end_join = Carbon::parse($end);
        // if($status == 'all'){
        //     $status = 'all';
        // }else{
        //     $status = 'not all';
        // }
        $member = Member::with('position', 'region')
            ->whereBetween('join_date', [$start_join, $end_join])
            ->when($area != 'ALL', function ($query) use ($area) {
                return $query->where('region_id', $area);
            })->when($proyek != 'ALL', function ($query) use ($proyek) {
                return $query->where('project_id', $proyek);
            })->when($member_id != 'ALL', function ($query) use ($member_id){
                return $query->where('ms_members.id', $member_id);
            })
            ->get();
        return $member;
    }

    public static function generateRekapAnggota($start, $end, $area){
        $start_join = Carbon::parse($start)->format('Y-m-d');
        $end_join = Carbon::parse($end)->format('Y-m-d');
        $months = self::generateMonth($start_join, $end_join);

        // if($status == 'all'){
        //     $status = 'all';
        // }else{
        //     $status = 'not all';
        // }

        $data = [];
        if($area == 'ALL'){
            $regions = Region::get();
            foreach ($regions as $region){
                foreach ($months as $month){
                    $member_in = Member::where('region_id', $region['id'])
                        ->whereMonth('join_date','>=', $month->format('m'))
                        ->whereYear('join_date','>=', $month->format('Y'))
                        ->whereMonth('join_date','<=',$month->format('m'))
                        ->whereYear('join_date','<=',$month->format('Y'))
                        ->count();

                    $member_out = Member::where('region_id', $region['id'])
                        ->whereIn('status', array('resign', 'tidak aktif'))
                        ->whereMonth('updated_at','>=', $month->format('m'))
                        ->whereYear('updated_at','>=', $month->format('Y'))
                        ->whereMonth('updated_at','<=',$month->format('m'))
                        ->whereYear('updated_at','<=', $month->format('Y'))
                        ->count();
                    $data[] = [
                        'region' => $region['name_area'],
                        'total_masuk' => $member_in,
                        'total_keluar' => $member_out,
                        'bulan' =>  $month->format('F Y')
                    ];
                }
            }
        }else{
            $region = Region::where('id', $area)->first();
            foreach ($months as $month){
                $member_in = Member::where('region_id', $area)
                    ->whereMonth('join_date','>=', $month->format('m'))
                    ->whereYear('join_date','>=', $month->format('Y'))
                    ->whereMonth('join_date','<=',$month->format('m'))
                    ->whereYear('join_date','<=', $month->format('Y'))
                    ->count();

                $member_out = Member::where('region_id', $area)
                        ->whereIn('status', array('resign', 'tidak aktif'))
                        ->whereMonth('updated_at','>=', $month->format('m'))
                        ->whereYear('updated_at','>=', $month->format('Y'))
                        ->whereMonth('updated_at','<=',$month->format('m'))
                        ->whereYear('updated_at','<=', $month->format('Y'))
                        ->count();
                $data[] = [
                    'region' => $region->name_area,
                    'total_masuk' => $member_in,
                    'total_keluar' => $member_out,
                    'bulan' =>  $month->format('F Y')
                ];
            }
        }

        $arr = [];

        foreach ($data as $key => $item) {
            $arr[$item['bulan']][$key] = $item;
        }


        $a = collect($arr)->map(function ($q){
           return array_values($q);
        });

        return $a->toArray();
    }

    public static function generateMonth($start,$end ){
        $months = [];
        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {
            $months[] = $month;
        }
        return $months;
    }

    public static function downloadMemberDeposit($data, $member){
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayData = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ];

        $styleTitleArray = [
            'font' => [
                'bold' => true,
                'color' => array('rgb' => '30453a'),
                'size'  => 15,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet(0)->setTitle('Data Simpanan');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('C2');
        $drawing->setWidthAndHeight(80, 80);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        $sheet->getCell('D3')->setValue('RINCIAN SIMPANAN KOPERASI SECURITY "BSP"');
        $sheet->getCell('C6')->setValue('Nama Anggota');
        $sheet->getCell('C7')->setValue('Nama Proyek');
        $sheet->getCell('C8')->setValue('No. Anggota Koperasi');
        $sheet->getCell('C9')->setValue('Mulai Bergabung');

        $sheet->getCell('D6')->setValue(': ' . $member->full_name);
        $sheet->getCell('D7')->setValue(': ' . $member->project->project_name);
        $sheet->getCell('D8')->setValue(': ' . $member->nik_koperasi);
        $sheet->getCell('D9')->setValue(': ' . date('d F Y', strtotime($member->join_date)));

        $sheet->getCell('C12')->setValue('Tahun');
        $sheet->getCell('D12')->setValue('Simpanan Pokok');
        $sheet->getCell('E12')->setValue('Simpanan Wajib');
        $sheet->getCell('F12')->setValue('Simpanan Sukarela');
        $sheet->getCell('G12')->setValue('SHU Ditahan');

        $sheet->mergeCells('D3:F3');
        $sheet->getStyle('D3:F3')->applyFromArray($styleTitleArray);
        $sheet->getStyle('C12:G12')->applyFromArray($styleArray);

        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $b = 12;
        foreach ($data as $d){
            ++$b;
            $sheet->getCell('C'.$b)->setValue($d['tahun']);
            $sheet->getCell('D'.$b)->setValue($d['pokok']);
            $sheet->getCell('E'.$b)->setValue($d['wajib']);
            $sheet->getCell('F'.$b)->setValue($d['sukarela']);
            $sheet->getCell('G'.$b)->setValue($d['shu']);

            $sheet->getStyle('D'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('E'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('F'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('G'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('C'.$b.':G'.$b)->applyFromArray($styleArrayData);
        }
        $endCol = $b+1;
        $sheet->getCell('C'.$endCol)->setValue('TOTAL');
        $sheet->getCell('D'.$endCol)->setValue('=SUM(D8:D'.$b.')');
        $sheet->getCell('E'.$endCol)->setValue('=SUM(E8:E'.$b.')');
        $sheet->getCell('F'.$endCol)->setValue('=SUM(F8:F'.$b.')');
        $sheet->getCell('G'.$endCol)->setValue('=SUM(G8:G'.$b.')');
        $sheet->getStyle('D'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('E'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('F'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('G'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');

        $sheet->getStyle('C'.$endCol.':G'.$endCol)->applyFromArray($styleArrayTotal);



        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;

    }

    public static function downloadMemberSukarelaDeposit($data, $member){
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayData = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ];

        $styleTitleArray = [
            'font' => [
                'bold' => true,
                'color' => array('rgb' => '30453a'),
                'size'  => 15,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet(0)->setTitle('Pengambilan Simpanan Sukarela');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('C2');
        $drawing->setWidthAndHeight(80, 80);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        $sheet->getCell('D3')->setValue('RINCIAN PENGAMBILAN SIMPANAN SUKARELA KOPERASI SECURITY "BSP"');
        $sheet->getCell('C6')->setValue('Nama Anggota');
        $sheet->getCell('C7')->setValue('Nama Proyek');
        $sheet->getCell('C8')->setValue('No. Anggota Koperasi');
        $sheet->getCell('C9')->setValue('Mulai Bergabung');

        $sheet->getCell('D6')->setValue(': ' . $member->full_name);
        $sheet->getCell('D7')->setValue(': ' . $member->project->project_name);
        $sheet->getCell('D8')->setValue(': ' . $member->nik_koperasi);
        $sheet->getCell('D9')->setValue(': ' . date('d F Y', strtotime($member->join_date)));

        $sheet->getCell('C12')->setValue('Tahun');
        $sheet->getCell('D12')->setValue('Masuk');
        $sheet->getCell('E12')->setValue('Keluar');
        $sheet->getCell('F12')->setValue('Saldo');

        $sheet->mergeCells('D3:J3');
        $sheet->getStyle('D3:J3')->applyFromArray($styleTitleArray);
        $sheet->getStyle('C12:F12')->applyFromArray($styleArray);

        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $b = 12;
        foreach ($data as $d){
            ++$b;
            $sheet->getCell('C'.$b)->setValue($d['tahun']);
            $sheet->getCell('D'.$b)->setValue($d['masuk']);
            $sheet->getCell('E'.$b)->setValue($d['keluar']);
            $sheet->getCell('F'.$b)->setValue($d['saldo']);

            $sheet->getStyle('D'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('E'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('F'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('C'.$b.':F'.$b)->applyFromArray($styleArrayData);
        }
        $endCol = $b+1;
        $sheet->getCell('C'.$endCol)->setValue('TOTAL');
        $sheet->getCell('D'.$endCol)->setValue('=SUM(D8:D'.$b.')');
        $sheet->getCell('E'.$endCol)->setValue('=SUM(E8:E'.$b.')');
        $sheet->getCell('F'.$endCol)->setValue('=SUM(F8:F'.$b.')');
        $sheet->getStyle('D'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('E'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('F'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');

        $sheet->getStyle('C'.$endCol.':F'.$endCol)->applyFromArray($styleArrayTotal);



        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;

    }

    public static function downloadMemberAreaProyek($dataMember, $startDate, $endDate)
    {

        $maks = 100;
        $b = 1;
        $start_date = Carbon::parse($startDate)->format('Y-m-d');
        $end_date = Carbon::parse($endDate)->format('Y-m-d');

        //style header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayBody = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleTitleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 15,
            ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $dataSimpananSheet = $spreadsheet->getActiveSheet(0)->setTitle('Anggota Proyek Area');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('B1');
        $drawing->setWidthAndHeight(110, 110);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        $dataSimpananSheet->getCell('C2')->setValue('LAPORAN REKAPITULASI PENDAFTARAN ANGGOTA');
        $dataSimpananSheet->getCell('C3')->setValue('KOPERASI SECURITY "BSP"');
        $dataSimpananSheet->getCell('C5')->setValue('Periode : ' . $start_date . ' - ' . $end_date);

        $dataSimpananSheet->getCell('B7')->setValue('Wilayah');
        $dataSimpananSheet->getCell('C7')->setValue('Nama Proyek');
        $dataSimpananSheet->getCell('D7')->setValue('Anggota Masuk');
        $dataSimpananSheet->getStyle('B7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $dataSimpananSheet->getColumnDimension('B')->setWidth(40);
        $dataSimpananSheet->getColumnDimension('C')->setWidth(70);
        $dataSimpananSheet->getColumnDimension('D')->setWidth(30);

        $dataSimpananSheet->getStyle('C2:C5')->applyFromArray($styleTitleArray);
        $dataSimpananSheet->mergeCells('C2:E2');
        $dataSimpananSheet->mergeCells('C3:E3');
        $dataSimpananSheet->mergeCells('C5:E5');

        $dataSimpananSheet->getStyle('B7:D7')->applyFromArray($styleArray);


        $iArea = 8;
        foreach($dataMember as $value) {
            $fArea = $iArea;
            $dataSimpananSheet->setCellValue('B'.$iArea, $value['region']);
            foreach($value['project'] as $value2) {
                $dataSimpananSheet->setCellValue('C'.$iArea, $value2['name']);
                $dataSimpananSheet->setCellValue('D'.$iArea, $value2['count']);
                $dataSimpananSheet->getStyle('B'.$iArea.':D'.$iArea)->applyFromArray($styleArrayBody);
                ++$iArea;
            }
            $dataSimpananSheet->setCellValue('C'.$iArea, 'TOTAL');
            $dataSimpananSheet->setCellValue('D'.$iArea, '=SUM(D'.$fArea.':D'.($iArea-1).')');
            $dataSimpananSheet->getStyle('C'.$iArea.':D'.$iArea)->applyFromArray($styleArrayTotal);
            $dataSimpananSheet->getStyle('B'.$iArea)->applyFromArray($styleArrayBody);
            $dataSimpananSheet->mergeCells('B'.$fArea.':B'.$iArea);
            ++$iArea;
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public static function downloadPendapatanProvisiJasaAdmin($data, $start, $end){
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayData = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ];

        $styleTitleArray = [
            'font' => [
                'bold' => true,
                'color' => array('rgb' => '30453a'),
                'size'  => 15,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet(0)->setTitle('Data Pendapatan');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('C2');
        $drawing->setWidthAndHeight(80, 80);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        $sheet->getCell('D3')->setValue('RINCIAN PENDAPATAN PROVISI, JASA, ADMIN');
        $sheet->getCell('D4')->setValue('KOPERASI SECURITY "BSP"');

        $sheet->getCell('C7')->setValue('Tahun');
        $sheet->getCell('D7')->setValue('Pendapatan Provisi');
        $sheet->getCell('E7')->setValue('Pendapatan Jasa');
        $sheet->getCell('F7')->setValue('Pendapatan Admin');

        $sheet->mergeCells('D3:G3');
        $sheet->mergeCells('D4:G4');
        $sheet->getStyle('D3:G4')->applyFromArray($styleTitleArray);
        $sheet->getStyle('C7:F7')->applyFromArray($styleArray);

        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $b = 7;
        foreach ($data as $d){
            ++$b;
            $sheet->getCell('C'.$b)->setValue($d['tahun']);
            $sheet->getCell('D'.$b)->setValue($d['provisi']);
            $sheet->getCell('E'.$b)->setValue($d['jasa']);
            $sheet->getCell('F'.$b)->setValue($d['admin']);

            $sheet->getStyle('D'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('E'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('F'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('C'.$b.':F'.$b)->applyFromArray($styleArrayData);
        }
        $endCol = $b+1;
        $sheet->getCell('C'.$endCol)->setValue('TOTAL');
        $sheet->getCell('D'.$endCol)->setValue('=SUM(D8:D'.$b.')');
        $sheet->getCell('E'.$endCol)->setValue('=SUM(E8:E'.$b.')');
        $sheet->getCell('F'.$endCol)->setValue('=SUM(F8:F'.$b.')');
        $sheet->getStyle('D'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('E'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('F'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');

        $sheet->getStyle('C'.$endCol.':F'.$endCol)->applyFromArray($styleArrayTotal);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;

    }

    public static function downloadMemberResign($data, $start, $end){
        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleTitleArray = array(
        'font'  => array(
            'bold'  => true,
            'size'  => 15,
        ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet(0)->setTitle('Data Anggota Resign');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('D1');
        $drawing->setWidthAndHeight(180, 110);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        $sheet->getCell('E3')->setValue('Laporan Anggota Resign');
        $sheet->getCell('E4')->setValue('Periode : ' . $start_date .' s/d '. $end_date);

        $sheet->getStyle('E3:E4')->applyFromArray($styleTitleArray);

        $sheet->getCell('C7')->setValue('No');
        $sheet->getCell('D7')->setValue('Nama Proyek');
        $sheet->getCell('E7')->setValue('Nama Anggota');
        $sheet->getCell('F7')->setValue('No Anggota');
        $sheet->getCell('G7')->setValue('No Register');
        $sheet->getCell('H7')->setValue('Bank');
        $sheet->getCell('I7')->setValue('Nama Bank');
        $sheet->getCell('J7')->setValue('Total Simpanan');
        $sheet->getCell('J8')->setValue('Pokok');
        $sheet->getCell('K8')->setValue('Wajib');
        $sheet->getCell('L8')->setValue('Sukarela');
        $sheet->getCell('M8')->setValue('SHU Ditahan');
        $sheet->getCell('N8')->setValue('Lainnya');
        $sheet->getCell('O7')->setValue('Biaya Admin');
        $sheet->getCell('P7')->setValue('Pinjaman Tunai');
        $sheet->getCell('P8')->setValue('Angsuran');
        $sheet->getCell('Q8')->setValue('Bunga');
        $sheet->getCell('R7')->setValue('Sisa Potongan');
        $sheet->getCell('S7')->setValue('Pinjaman Barang');
        $sheet->getCell('S8')->setValue('Angsuran');
        $sheet->getCell('T8')->setValue('Bunga');
        $sheet->getCell('U7')->setValue('Sisa Potongan');
        $sheet->getCell('V7')->setValue('Pinjaman Pendidikan');
        $sheet->getCell('V8')->setValue('Angsuran');
        $sheet->getCell('W8')->setValue('Bunga');
        $sheet->getCell('X7')->setValue('Sisa Potongan');
        $sheet->getCell('Y7')->setValue('Pinjaman Softloan');
        $sheet->getCell('Y8')->setValue('Angsuran');
        $sheet->getCell('Z8')->setValue('Bunga');
        $sheet->getCell('AA7')->setValue('Sisa Potongan');
        $sheet->getCell('AB7')->setValue('Pinjaman Kendaraan');
        $sheet->getCell('AB8')->setValue('Angsuran');
        $sheet->getCell('AC8')->setValue('Bunga');
        $sheet->getCell('AD7')->setValue('Sisa Potongan');
        $sheet->getCell('AE7')->setValue('Total Hak / Kewajiban');
        $sheet->getCell('AF7')->setValue('Nama Proyek / Jabatan');
        $sheet->getCell('AG7')->setValue('Mulai Proses');
        $sheet->getCell('AH7')->setValue('Area Wilayah');

        $sheet->mergeCells('C7:C8');
        $sheet->mergeCells('D7:D8');
        $sheet->mergeCells('E7:E8');
        $sheet->mergeCells('F7:F8');
        $sheet->mergeCells('G7:G8');
        $sheet->mergeCells('H7:H8');
        $sheet->mergeCells('I7:I8');
        $sheet->mergeCells('J7:N7');
        $sheet->mergeCells('O7:O8');
        $sheet->mergeCells('P7:Q7');
        $sheet->mergeCells('R7:R8');
        $sheet->mergeCells('S7:T7');
        $sheet->mergeCells('U7:U8');
        $sheet->mergeCells('V7:W7');
        $sheet->mergeCells('X7:X8');
        $sheet->mergeCells('Y7:Z7');
        $sheet->mergeCells('AA7:AA8');
        $sheet->mergeCells('AB7:AC7');
        $sheet->mergeCells('AD7:AD8');
        $sheet->mergeCells('AE7:AE8');
        $sheet->mergeCells('AF7:AF8');
        $sheet->mergeCells('AG7:AG8');
        $sheet->mergeCells('AH7:AH8');

        $sheet->getStyle('C7:AH8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C7:AH8')->applyFromArray($styleArray);


        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->getColumnDimension('V')->setAutoSize(true);
        $sheet->getColumnDimension('W')->setAutoSize(true);
        $sheet->getColumnDimension('X')->setAutoSize(true);
        $sheet->getColumnDimension('Y')->setAutoSize(true);
        $sheet->getColumnDimension('Z')->setAutoSize(true);
        $sheet->getColumnDimension('AA')->setAutoSize(true);
        $sheet->getColumnDimension('AB')->setAutoSize(true);
        $sheet->getColumnDimension('AC')->setAutoSize(true);
        $sheet->getColumnDimension('AD')->setAutoSize(true);
        $sheet->getColumnDimension('AE')->setAutoSize(true);
        $sheet->getColumnDimension('AF')->setAutoSize(true);
        $sheet->getColumnDimension('AG')->setAutoSize(true);
        $sheet->getColumnDimension('AH')->setAutoSize(true);

        $no = 0;
        $b = 8;
        foreach ($data as $d){
            ++$b;

            $sheet->getCell('C'.$b)->setValue(++$no);
            $sheet->getCell('D'.$b)->setValue($d['proyek']);
            $sheet->getCell('E'.$b)->setValue($d['nama']);
            $sheet->getCell('F'.$b)->setValue($d['no_anggota']);
            $sheet->getCell('G'.$b)->setValue($d['no_register']);
            $sheet->getCell('H'.$b)->setValue($d['bank']);
            $sheet->getCell('I'.$b)->setValue($d['bank_name']);
            $sheet->getCell('J'.$b)->setValue($d['pokok']);
            $sheet->getCell('K'.$b)->setValue($d['wajib']);
            $sheet->getCell('L'.$b)->setValue($d['sukarela']);
            $sheet->getCell('M'.$b)->setValue($d['shu']);
            $sheet->getCell('N'.$b)->setValue($d['lainnya']);
            $sheet->getCell('O'.$b)->setValue($d['biaya_admin']);
            $sheet->getCell('P'.$b)->setValue($d['pinj_tunai_angsuran']);
            $sheet->getCell('Q'.$b)->setValue($d['pinj_tunai_bunga']);
            $sheet->getCell('R'.$b)->setValue($d['pinj_tunai_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('S'.$b)->setValue($d['pinj_barang_angsuran']);
            $sheet->getCell('T'.$b)->setValue($d['pinj_barang_bunga']);
            $sheet->getCell('U'.$b)->setValue($d['pinj_barang_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('V'.$b)->setValue($d['pinj_pendidikan_angsuran']);
            $sheet->getCell('W'.$b)->setValue($d['pinj_pendidikan_bunga']);
            $sheet->getCell('X'.$b)->setValue($d['pinj_pendidikan_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('Y'.$b)->setValue($d['pinj_softloan_angsuran']);
            $sheet->getCell('Z'.$b)->setValue($d['pinj_softloan_bunga']);
            $sheet->getCell('AA'.$b)->setValue($d['pinj_softloan_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('AB'.$b)->setValue($d['pinj_kendaraan_angsuran']);
            $sheet->getCell('AC'.$b)->setValue($d['pinj_kendaraan_bunga']);
            $sheet->getCell('AD'.$b)->setValue($d['pinj_kendaraan_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('AE'.$b)->setValue($d['total_hak']);
            $sheet->getCell('AF'.$b)->setValue($d['jabatan']);
            $sheet->getCell('AG'.$b)->setValue($d['mulai_proses']);
            $sheet->getCell('AH'.$b)->setValue($d['area']);

            $sheet->getStyle('J'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('K'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('L'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('M'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('N'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('O'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('P'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('Q'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('S'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('T'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('V'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('W'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('Y'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('Z'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AB'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AC'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AE'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
        }
        $endCol = $b + 1;
        $sheet->getCell('D'.$endCol)->setValue('TOTAL');
        $sheet->getCell('J'.$endCol)->setValue('=SUM(J9:J'.$b.')');
        $sheet->getCell('K'.$endCol)->setValue('=SUM(K9:K'.$b.')');
        $sheet->getCell('L'.$endCol)->setValue('=SUM(L9:L'.$b.')');
        $sheet->getCell('M'.$endCol)->setValue('=SUM(M9:M'.$b.')');
        $sheet->getCell('N'.$endCol)->setValue('=SUM(N9:N'.$b.')');
        $sheet->getCell('O'.$endCol)->setValue('=SUM(O9:O'.$b.')');
        $sheet->getCell('P'.$endCol)->setValue('=SUM(P9:P'.$b.')');
        $sheet->getCell('Q'.$endCol)->setValue('=SUM(Q9:Q'.$b.')');
        $sheet->getCell('R'.$endCol)->setValue('-');
        $sheet->getCell('S'.$endCol)->setValue('=SUM(S9:S'.$b.')');
        $sheet->getCell('T'.$endCol)->setValue('=SUM(T9:T'.$b.')');
        $sheet->getCell('U'.$endCol)->setValue('-');
        $sheet->getCell('V'.$endCol)->setValue('=SUM(V9:V'.$b.')');
        $sheet->getCell('W'.$endCol)->setValue('=SUM(W9:W'.$b.')');
        $sheet->getCell('X'.$endCol)->setValue('-');
        $sheet->getCell('Y'.$endCol)->setValue('=SUM(Y9:Y'.$b.')');
        $sheet->getCell('Z'.$endCol)->setValue('=SUM(Z9:Z'.$b.')');
        $sheet->getCell('AA'.$endCol)->setValue('-');
        $sheet->getCell('AB'.$endCol)->setValue('=SUM(AB9:AB'.$b.')');
        $sheet->getCell('AC'.$endCol)->setValue('=SUM(AC9:AC'.$b.')');
        $sheet->getCell('AD'.$endCol)->setValue('-');
        $sheet->getCell('AE'.$endCol)->setValue('=SUM(AE9:AE'.$b.')');
        $sheet->mergeCells('D'.$endCol.':I'.$endCol);
        $sheet->getStyle('J'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('K'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('L'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('M'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('N'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('O'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('P'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('Q'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('S'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('T'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('V'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('W'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('Y'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('Z'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AB'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AC'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AE'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('D'.$endCol.':AE'.$endCol)->applyFromArray($styleArrayTotal);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;

    }

    public static function downloadSisaHak($data, $start, $end){
        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleTitleArray = array(
        'font'  => array(
            'bold'  => true,
            'size'  => 15,
        ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet(0)->setTitle('Data Sisa Hak Anggota');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('D1');
        $drawing->setWidthAndHeight(180, 110);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        $sheet->getCell('E3')->setValue('Laporan Sisa Hak Anggota');
        $sheet->getCell('E4')->setValue('Periode : ' . $start_date .' s/d '. $end_date);

        $sheet->getStyle('E3:E4')->applyFromArray($styleTitleArray);

        $sheet->getCell('C7')->setValue('No');
        $sheet->getCell('D7')->setValue('Nama Proyek');
        $sheet->getCell('E7')->setValue('Nama Anggota');
        $sheet->getCell('F7')->setValue('No Anggota');
        $sheet->getCell('G7')->setValue('No Register');
        $sheet->getCell('H7')->setValue('Bank');
        $sheet->getCell('I7')->setValue('Nama Bank');
        $sheet->getCell('J7')->setValue('Total Simpanan');
        $sheet->getCell('J8')->setValue('Pokok');
        $sheet->getCell('K8')->setValue('Wajib');
        $sheet->getCell('L8')->setValue('Sukarela');
        $sheet->getCell('M8')->setValue('SHU Ditahan');
        $sheet->getCell('N8')->setValue('Lainnya');
        $sheet->getCell('O7')->setValue('Biaya Admin');
        $sheet->getCell('P7')->setValue('Pinjaman Tunai');
        $sheet->getCell('P8')->setValue('Angsuran');
        $sheet->getCell('Q8')->setValue('Bunga');
        $sheet->getCell('R7')->setValue('Sisa Potongan');
        $sheet->getCell('S7')->setValue('Pinjaman Barang');
        $sheet->getCell('S8')->setValue('Angsuran');
        $sheet->getCell('T8')->setValue('Bunga');
        $sheet->getCell('U7')->setValue('Sisa Potongan');
        $sheet->getCell('V7')->setValue('Pinjaman Pendidikan');
        $sheet->getCell('V8')->setValue('Angsuran');
        $sheet->getCell('W8')->setValue('Bunga');
        $sheet->getCell('X7')->setValue('Sisa Potongan');
        $sheet->getCell('Y7')->setValue('Pinjaman Softloan');
        $sheet->getCell('Y8')->setValue('Angsuran');
        $sheet->getCell('Z8')->setValue('Bunga');
        $sheet->getCell('AA7')->setValue('Sisa Potongan');
        $sheet->getCell('AB7')->setValue('Pinjaman Kendaraan');
        $sheet->getCell('AB8')->setValue('Angsuran');
        $sheet->getCell('AC8')->setValue('Bunga');
        $sheet->getCell('AD7')->setValue('Sisa Potongan');
        $sheet->getCell('AE7')->setValue('Total Hak / Kewajiban');
        $sheet->getCell('AF7')->setValue('Diambil');
        $sheet->getCell('AG7')->setValue('Sisa Hak Yang Belum Diambil');
        $sheet->getCell('AH7')->setValue('Tanggal Pengambilan');
        $sheet->getCell('AI7')->setValue('Nama Proyek / Jabatan');
        $sheet->getCell('AJ7')->setValue('Mulai Proses');
        $sheet->getCell('AK7')->setValue('Area Wilayah');

        $sheet->mergeCells('C7:C8');
        $sheet->mergeCells('D7:D8');
        $sheet->mergeCells('E7:E8');
        $sheet->mergeCells('F7:F8');
        $sheet->mergeCells('G7:G8');
        $sheet->mergeCells('H7:H8');
        $sheet->mergeCells('I7:I8');
        $sheet->mergeCells('J7:N7');
        $sheet->mergeCells('O7:O8');
        $sheet->mergeCells('P7:Q7');
        $sheet->mergeCells('R7:R8');
        $sheet->mergeCells('S7:T7');
        $sheet->mergeCells('U7:U8');
        $sheet->mergeCells('V7:W7');
        $sheet->mergeCells('X7:X8');
        $sheet->mergeCells('Y7:Z7');
        $sheet->mergeCells('AA7:AA8');
        $sheet->mergeCells('AB7:AC7');
        $sheet->mergeCells('AD7:AD8');
        $sheet->mergeCells('AE7:AE8');
        $sheet->mergeCells('AF7:AF8');
        $sheet->mergeCells('AG7:AG8');
        $sheet->mergeCells('AH7:AH8');
        $sheet->mergeCells('AI7:AI8');
        $sheet->mergeCells('AJ7:AJ8');
        $sheet->mergeCells('AK7:AK8');

        $sheet->getStyle('C7:AK8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C7:AK8')->applyFromArray($styleArray);


        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->getColumnDimension('V')->setAutoSize(true);
        $sheet->getColumnDimension('W')->setAutoSize(true);
        $sheet->getColumnDimension('X')->setAutoSize(true);
        $sheet->getColumnDimension('Y')->setAutoSize(true);
        $sheet->getColumnDimension('Z')->setAutoSize(true);
        $sheet->getColumnDimension('AA')->setAutoSize(true);
        $sheet->getColumnDimension('AB')->setAutoSize(true);
        $sheet->getColumnDimension('AC')->setAutoSize(true);
        $sheet->getColumnDimension('AD')->setAutoSize(true);
        $sheet->getColumnDimension('AE')->setAutoSize(true);
        $sheet->getColumnDimension('AF')->setAutoSize(true);
        $sheet->getColumnDimension('AG')->setAutoSize(true);
        $sheet->getColumnDimension('AH')->setAutoSize(true);
        $sheet->getColumnDimension('AI')->setAutoSize(true);
        $sheet->getColumnDimension('AJ')->setAutoSize(true);
        $sheet->getColumnDimension('AK')->setAutoSize(true);

        $no = 0;
        $b = 8;
        foreach ($data as $d){
            ++$b;

            $sheet->getCell('C'.$b)->setValue(++$no);
            $sheet->getCell('D'.$b)->setValue($d['proyek']);
            $sheet->getCell('E'.$b)->setValue($d['nama']);
            $sheet->getCell('F'.$b)->setValue($d['no_anggota']);
            $sheet->getCell('G'.$b)->setValue($d['no_register']);
            $sheet->getCell('H'.$b)->setValue($d['bank']);
            $sheet->getCell('I'.$b)->setValue($d['bank_name']);
            $sheet->getCell('J'.$b)->setValue($d['pokok']);
            $sheet->getCell('K'.$b)->setValue($d['wajib']);
            $sheet->getCell('L'.$b)->setValue($d['sukarela']);
            $sheet->getCell('M'.$b)->setValue($d['shu']);
            $sheet->getCell('N'.$b)->setValue($d['lainnya']);
            $sheet->getCell('O'.$b)->setValue($d['biaya_admin']);
            $sheet->getCell('P'.$b)->setValue($d['pinj_tunai_angsuran']);
            $sheet->getCell('Q'.$b)->setValue($d['pinj_tunai_bunga']);
            $sheet->getCell('R'.$b)->setValue($d['pinj_tunai_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('S'.$b)->setValue($d['pinj_barang_angsuran']);
            $sheet->getCell('T'.$b)->setValue($d['pinj_barang_bunga']);
            $sheet->getCell('U'.$b)->setValue($d['pinj_barang_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('V'.$b)->setValue($d['pinj_pendidikan_angsuran']);
            $sheet->getCell('W'.$b)->setValue($d['pinj_pendidikan_bunga']);
            $sheet->getCell('X'.$b)->setValue($d['pinj_pendidikan_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('Y'.$b)->setValue($d['pinj_softloan_angsuran']);
            $sheet->getCell('Z'.$b)->setValue($d['pinj_softloan_bunga']);
            $sheet->getCell('AA'.$b)->setValue($d['pinj_softloan_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('AB'.$b)->setValue($d['pinj_kendaraan_angsuran']);
            $sheet->getCell('AC'.$b)->setValue($d['pinj_kendaraan_bunga']);
            $sheet->getCell('AD'.$b)->setValue($d['pinj_kendaraan_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('AE'.$b)->setValue($d['total_hak']);
            $sheet->getCell('AF'.$b)->setValue($d['diambil']);
            $sheet->getCell('AG'.$b)->setValue($d['total_blm_diambil']);
            $sheet->getCell('AH'.$b)->setValue($d['tanggal_pengambilan']);
            $sheet->getCell('AI'.$b)->setValue($d['jabatan']);
            $sheet->getCell('AJ'.$b)->setValue($d['mulai_proses']);
            $sheet->getCell('AK'.$b)->setValue($d['area']);

            $sheet->getStyle('J'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('K'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('L'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('M'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('N'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('O'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('P'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('Q'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('S'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('T'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('V'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('W'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('Y'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('Z'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AB'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AC'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AE'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AF'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AG'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
        }
        $endCol = $b + 1;
        $sheet->getCell('D'.$endCol)->setValue('TOTAL');
        $sheet->getCell('J'.$endCol)->setValue('=SUM(J9:J'.$b.')');
        $sheet->getCell('K'.$endCol)->setValue('=SUM(K9:K'.$b.')');
        $sheet->getCell('L'.$endCol)->setValue('=SUM(L9:L'.$b.')');
        $sheet->getCell('M'.$endCol)->setValue('=SUM(M9:M'.$b.')');
        $sheet->getCell('N'.$endCol)->setValue('=SUM(N9:N'.$b.')');
        $sheet->getCell('O'.$endCol)->setValue('=SUM(O9:O'.$b.')');
        $sheet->getCell('P'.$endCol)->setValue('=SUM(P9:P'.$b.')');
        $sheet->getCell('Q'.$endCol)->setValue('=SUM(Q9:Q'.$b.')');
        $sheet->getCell('R'.$endCol)->setValue('-');
        $sheet->getCell('S'.$endCol)->setValue('=SUM(S9:S'.$b.')');
        $sheet->getCell('T'.$endCol)->setValue('=SUM(T9:T'.$b.')');
        $sheet->getCell('U'.$endCol)->setValue('-');
        $sheet->getCell('V'.$endCol)->setValue('=SUM(V9:V'.$b.')');
        $sheet->getCell('W'.$endCol)->setValue('=SUM(W9:W'.$b.')');
        $sheet->getCell('X'.$endCol)->setValue('-');
        $sheet->getCell('Y'.$endCol)->setValue('=SUM(Y9:Y'.$b.')');
        $sheet->getCell('Z'.$endCol)->setValue('=SUM(Z9:Z'.$b.')');
        $sheet->getCell('AA'.$endCol)->setValue('-');
        $sheet->getCell('AB'.$endCol)->setValue('=SUM(AB9:AB'.$b.')');
        $sheet->getCell('AC'.$endCol)->setValue('=SUM(AC9:AC'.$b.')');
        $sheet->getCell('AD'.$endCol)->setValue('-');
        $sheet->getCell('AE'.$endCol)->setValue('=SUM(AE9:AE'.$b.')');
        $sheet->getCell('AF'.$endCol)->setValue('=SUM(AF9:AF'.$b.')');
        $sheet->getCell('AG'.$endCol)->setValue('=SUM(AG9:AG'.$b.')');
        $sheet->mergeCells('D'.$endCol.':I'.$endCol);
        $sheet->getStyle('J'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('K'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('L'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('M'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('N'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('O'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('P'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('Q'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('S'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('T'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('V'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('W'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('Y'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('Z'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AB'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AC'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AE'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AF'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AG'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('D'.$endCol.':AG'.$endCol)->applyFromArray($styleArrayTotal);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;

    }

    public static function downloadKreditBermasalah($data, $start, $end){
        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleTitleArray = array(
        'font'  => array(
            'bold'  => true,
            'size'  => 15,
        ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet(0)->setTitle('Data Kredit Bermasalah');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('D1');
        $drawing->setWidthAndHeight(180, 110);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        $sheet->getCell('E3')->setValue('Laporan Kredit Bermasalah');
        $sheet->getCell('E4')->setValue('Periode : ' . $start_date .' s/d '. $end_date);

        $sheet->getStyle('E3:E4')->applyFromArray($styleTitleArray);

        $sheet->getCell('C7')->setValue('No');
        $sheet->getCell('D7')->setValue('Nama Proyek');
        $sheet->getCell('E7')->setValue('Nama Anggota');
        $sheet->getCell('F7')->setValue('No Anggota');
        $sheet->getCell('G7')->setValue('No Register');
        $sheet->getCell('H7')->setValue('Bank');
        $sheet->getCell('I7')->setValue('Nama Bank');
        $sheet->getCell('J7')->setValue('Total Simpanan');
        $sheet->getCell('J8')->setValue('Pokok');
        $sheet->getCell('K8')->setValue('Wajib');
        $sheet->getCell('L8')->setValue('Sukarela');
        $sheet->getCell('M8')->setValue('SHU Ditahan');
        $sheet->getCell('N8')->setValue('Lainnya');
        $sheet->getCell('O7')->setValue('Biaya Admin');
        $sheet->getCell('P7')->setValue('Pinjaman Tunai');
        $sheet->getCell('P8')->setValue('Angsuran');
        $sheet->getCell('Q8')->setValue('Bunga');
        $sheet->getCell('R7')->setValue('Sisa Potongan');
        $sheet->getCell('S7')->setValue('Pinjaman Barang');
        $sheet->getCell('S8')->setValue('Angsuran');
        $sheet->getCell('T8')->setValue('Bunga');
        $sheet->getCell('U7')->setValue('Sisa Potongan');
        $sheet->getCell('V7')->setValue('Pinjaman Pendidikan');
        $sheet->getCell('V8')->setValue('Angsuran');
        $sheet->getCell('W8')->setValue('Bunga');
        $sheet->getCell('X7')->setValue('Sisa Potongan');
        $sheet->getCell('Y7')->setValue('Pinjaman Softloan');
        $sheet->getCell('Y8')->setValue('Angsuran');
        $sheet->getCell('Z8')->setValue('Bunga');
        $sheet->getCell('AA7')->setValue('Sisa Potongan');
        $sheet->getCell('AB7')->setValue('Pinjaman Kendaraan');
        $sheet->getCell('AB8')->setValue('Angsuran');
        $sheet->getCell('AC8')->setValue('Bunga');
        $sheet->getCell('AD7')->setValue('Sisa Potongan');
        $sheet->getCell('AE7')->setValue('Total Pokok Pinjaman');
        $sheet->getCell('AF7')->setValue('Total Jasa Pinjaman');
        $sheet->getCell('AG7')->setValue('Total Pinjaman Pokok+Jasa');
        $sheet->getCell('AH7')->setValue('Total Kewajiban');
        $sheet->getCell('AI7')->setValue('Pelunasan');
        $sheet->getCell('AJ7')->setValue('Sisa Kewajiban');
        $sheet->getCell('AK7')->setValue('Keterangan NPL');
        $sheet->getCell('AL7')->setValue('Keterangan Pelunasan');
        $sheet->getCell('AM7')->setValue('Nama Proyek / Jabatan');
        $sheet->getCell('AN7')->setValue('Mulai Proses');
        $sheet->getCell('AO7')->setValue('Area Wilayah');

        $sheet->mergeCells('C7:C8');
        $sheet->mergeCells('D7:D8');
        $sheet->mergeCells('E7:E8');
        $sheet->mergeCells('F7:F8');
        $sheet->mergeCells('G7:G8');
        $sheet->mergeCells('H7:H8');
        $sheet->mergeCells('I7:I8');
        $sheet->mergeCells('J7:N7');
        $sheet->mergeCells('O7:O8');
        $sheet->mergeCells('P7:Q7');
        $sheet->mergeCells('R7:R8');
        $sheet->mergeCells('S7:T7');
        $sheet->mergeCells('U7:U8');
        $sheet->mergeCells('V7:W7');
        $sheet->mergeCells('X7:X8');
        $sheet->mergeCells('Y7:Z7');
        $sheet->mergeCells('AA7:AA8');
        $sheet->mergeCells('AB7:AC7');
        $sheet->mergeCells('AD7:AD8');
        $sheet->mergeCells('AE7:AE8');
        $sheet->mergeCells('AF7:AF8');
        $sheet->mergeCells('AG7:AG8');
        $sheet->mergeCells('AH7:AH8');
        $sheet->mergeCells('AI7:AI8');
        $sheet->mergeCells('AJ7:AJ8');
        $sheet->mergeCells('AK7:AK8');
        $sheet->mergeCells('AL7:AL8');
        $sheet->mergeCells('AM7:AM8');
        $sheet->mergeCells('AN7:AN8');
        $sheet->mergeCells('AO7:AO8');

        $sheet->getStyle('C7:AO8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C7:AO8')->applyFromArray($styleArray);


        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->getColumnDimension('V')->setAutoSize(true);
        $sheet->getColumnDimension('W')->setAutoSize(true);
        $sheet->getColumnDimension('X')->setAutoSize(true);
        $sheet->getColumnDimension('Y')->setAutoSize(true);
        $sheet->getColumnDimension('Z')->setAutoSize(true);
        $sheet->getColumnDimension('AA')->setAutoSize(true);
        $sheet->getColumnDimension('AB')->setAutoSize(true);
        $sheet->getColumnDimension('AC')->setAutoSize(true);
        $sheet->getColumnDimension('AD')->setAutoSize(true);
        $sheet->getColumnDimension('AE')->setAutoSize(true);
        $sheet->getColumnDimension('AF')->setAutoSize(true);
        $sheet->getColumnDimension('AG')->setAutoSize(true);
        $sheet->getColumnDimension('AH')->setAutoSize(true);
        $sheet->getColumnDimension('AI')->setAutoSize(true);
        $sheet->getColumnDimension('AJ')->setAutoSize(true);
        $sheet->getColumnDimension('AK')->setAutoSize(true);
        $sheet->getColumnDimension('AL')->setAutoSize(true);
        $sheet->getColumnDimension('AM')->setAutoSize(true);
        $sheet->getColumnDimension('AN')->setAutoSize(true);
        $sheet->getColumnDimension('AO')->setAutoSize(true);

        $no = 0;
        $b = 8;
        foreach ($data as $d){
            ++$b;

            $sheet->getCell('C'.$b)->setValue(++$no);
            $sheet->getCell('D'.$b)->setValue($d['proyek']);
            $sheet->getCell('E'.$b)->setValue($d['nama']);
            $sheet->getCell('F'.$b)->setValue($d['no_anggota']);
            $sheet->getCell('G'.$b)->setValue($d['no_register']);
            $sheet->getCell('H'.$b)->setValue($d['bank']);
            $sheet->getCell('I'.$b)->setValue($d['bank_name']);
            $sheet->getCell('J'.$b)->setValue($d['pokok']);
            $sheet->getCell('K'.$b)->setValue($d['wajib']);
            $sheet->getCell('L'.$b)->setValue($d['sukarela']);
            $sheet->getCell('M'.$b)->setValue($d['shu']);
            $sheet->getCell('N'.$b)->setValue($d['lainnya']);
            $sheet->getCell('O'.$b)->setValue($d['biaya_admin']);
            $sheet->getCell('P'.$b)->setValue($d['pinj_tunai_angsuran']);
            $sheet->getCell('Q'.$b)->setValue($d['pinj_tunai_bunga']);
            $sheet->getCell('R'.$b)->setValue($d['pinj_tunai_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('S'.$b)->setValue($d['pinj_barang_angsuran']);
            $sheet->getCell('T'.$b)->setValue($d['pinj_barang_bunga']);
            $sheet->getCell('U'.$b)->setValue($d['pinj_barang_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('V'.$b)->setValue($d['pinj_pendidikan_angsuran']);
            $sheet->getCell('W'.$b)->setValue($d['pinj_pendidikan_bunga']);
            $sheet->getCell('X'.$b)->setValue($d['pinj_pendidikan_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('Y'.$b)->setValue($d['pinj_softloan_angsuran']);
            $sheet->getCell('Z'.$b)->setValue($d['pinj_softloan_bunga']);
            $sheet->getCell('AA'.$b)->setValue($d['pinj_softloan_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('AB'.$b)->setValue($d['pinj_kendaraan_angsuran']);
            $sheet->getCell('AC'.$b)->setValue($d['pinj_kendaraan_bunga']);
            $sheet->getCell('AD'.$b)->setValue($d['pinj_kendaraan_sisa_ptgn'] . ' Bulan');
            $sheet->getCell('AE'.$b)->setValue($d['total_pokok_pinjaman']);
            $sheet->getCell('AF'.$b)->setValue($d['total_jasa_pinjaman']);
            $sheet->getCell('AG'.$b)->setValue($d['total_pokok_jasa']);
            $sheet->getCell('AH'.$b)->setValue($d['total_kewajiban']);
            $sheet->getCell('AI'.$b)->setValue($d['pelunasan']);
            $sheet->getCell('AJ'.$b)->setValue($d['sisa_kewajiban']);
            $sheet->getCell('AK'.$b)->setValue($d['keterangan_npl']);
            $sheet->getCell('AL'.$b)->setValue($d['keterangan_pelunasan']);
            $sheet->getCell('AM'.$b)->setValue($d['jabatan']);
            $sheet->getCell('AN'.$b)->setValue($d['mulai_proses']);
            $sheet->getCell('AO'.$b)->setValue($d['area']);

            $sheet->getStyle('J'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('K'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('L'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('M'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('N'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('O'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('P'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('Q'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('S'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('T'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('V'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('W'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('Y'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('Z'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AB'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AC'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AE'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AF'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AG'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AH'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AI'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('AJ'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
        }
        $endCol = $b + 1;
        $sheet->getCell('I'.$endCol)->setValue('TOTAL');
        $sheet->getCell('J'.$endCol)->setValue('=SUM(J9:J'.$b.')');
        $sheet->getCell('K'.$endCol)->setValue('=SUM(K9:K'.$b.')');
        $sheet->getCell('L'.$endCol)->setValue('=SUM(L9:L'.$b.')');
        $sheet->getCell('M'.$endCol)->setValue('=SUM(M9:M'.$b.')');
        $sheet->getCell('N'.$endCol)->setValue('=SUM(N9:N'.$b.')');
        $sheet->getCell('O'.$endCol)->setValue('=SUM(O9:O'.$b.')');
        $sheet->getCell('P'.$endCol)->setValue('=SUM(P9:P'.$b.')');
        $sheet->getCell('Q'.$endCol)->setValue('=SUM(Q9:Q'.$b.')');
        $sheet->getCell('R'.$endCol)->setValue('-');
        $sheet->getCell('S'.$endCol)->setValue('=SUM(S9:S'.$b.')');
        $sheet->getCell('T'.$endCol)->setValue('=SUM(T9:T'.$b.')');
        $sheet->getCell('U'.$endCol)->setValue('-');
        $sheet->getCell('V'.$endCol)->setValue('=SUM(V9:V'.$b.')');
        $sheet->getCell('W'.$endCol)->setValue('=SUM(W9:W'.$b.')');
        $sheet->getCell('X'.$endCol)->setValue('-');
        $sheet->getCell('Y'.$endCol)->setValue('=SUM(Y9:Y'.$b.')');
        $sheet->getCell('Z'.$endCol)->setValue('=SUM(Z9:Z'.$b.')');
        $sheet->getCell('AA'.$endCol)->setValue('-');
        $sheet->getCell('AB'.$endCol)->setValue('=SUM(AB9:AB'.$b.')');
        $sheet->getCell('AC'.$endCol)->setValue('=SUM(AC9:AC'.$b.')');
        $sheet->getCell('AD'.$endCol)->setValue('-');
        $sheet->getCell('AE'.$endCol)->setValue('=SUM(AE9:AE'.$b.')');
        $sheet->getCell('AF'.$endCol)->setValue('=SUM(AF9:AF'.$b.')');
        $sheet->getCell('AG'.$endCol)->setValue('=SUM(AG9:AG'.$b.')');
        $sheet->getCell('AH'.$endCol)->setValue('=SUM(AH9:AH'.$b.')');
        $sheet->getCell('AI'.$endCol)->setValue('=SUM(AI9:AI'.$b.')');
        $sheet->getCell('AJ'.$endCol)->setValue('=SUM(AJ9:AJ'.$b.')');
        $sheet->getStyle('J'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('K'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('L'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('M'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('N'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('O'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('P'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('Q'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('S'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('T'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('V'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('W'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('Y'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('Z'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AB'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AC'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AE'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AF'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AG'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AH'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AI'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('AJ'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $sheet->getStyle('I'.$endCol.':AJ'.$endCol)->applyFromArray($styleArrayTotal);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;

    }

    public static function downloadPotAnggota($data, $start, $end){
        $tglStart = Carbon::parse($start);
        $tglEnd = Carbon::parse($end);

        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleUnderline = [
            'font' => [
                'underline' => true,
            ]
        ];

        $styleTitleArray = array(
        'font'  => array(
            'bold'  => true,
            'size'  => 15,
        ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet(0)->setTitle('Rekap');
        $sheet2 = $spreadsheet->createSheet()->setTitle('Pivot');
        $sheet3 = $spreadsheet->createSheet()->setTitle('lamp_pivot rumus');
        $sheet4 = $spreadsheet->createSheet()->setTitle('copy dari lamp rumus');
        $sheet5 = $spreadsheet->createSheet()->setTitle('invoice');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        // $drawing->setName('Logo');
        // $drawing->setDescription('Logo');
        // $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        // $drawing->setCoordinates('D1');
        // $drawing->setWidthAndHeight(180, 110);
        // $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        $sheet->getCell('A1')->setValue('Rekapitulasi Potongan Anggota Koperasi Security BSP');
        $sheet->getCell('A2')->setValue('Periode : ' . $start_date .' s/d '. $end_date);

        $sheet->getStyle('A1:A2')->applyFromArray($styleTitleArray);
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');

        $sheet->getCell('A4')->setValue('NO');
        $sheet->getCell('B4')->setValue('PROYEK');

        $currentColumn = 'C';
        while ($tglStart->lte($tglEnd)) {
            $sheet->getCell($currentColumn.'4')->setValue($tglStart->format('F'));
            $tglStart->addMonth();
            $currentColumn++;
        }
        $sheet->getCell($currentColumn . '4')->setValue('POKOK');
        $sheet->getCell(++$currentColumn . '4')->setValue('WAJIB');
        $sheet->getCell(++$currentColumn . '4')->setValue('S.RELA');
        $sheet->getCell(++$currentColumn . '4')->setValue('ANGSR_UANG');
        $sheet->getCell(++$currentColumn . '4')->setValue('BUNGA');
        $sheet->getCell(++$currentColumn . '4')->setValue('ANGSR_BARANG');
        $sheet->getCell(++$currentColumn . '4')->setValue('BUNGA');
        $sheet->getCell(++$currentColumn . '4')->setValue('ANGSR_PENDIDIKAN');
        $sheet->getCell(++$currentColumn . '4')->setValue('BUNGA');
        $sheet->getCell(++$currentColumn . '4')->setValue('ANGSR_PINJ.DARURAT');
        $sheet->getCell(++$currentColumn . '4')->setValue('BUNGA');
        $sheet->getCell(++$currentColumn . '4')->setValue('SOFT LOAN');
        $sheet->getCell(++$currentColumn . '4')->setValue('BUNGA');
        $sheet->getCell(++$currentColumn . '4')->setValue('ANGSR_PINJ.KENDARAAN');
        $sheet->getCell(++$currentColumn . '4')->setValue('BUNGA');
        $sheet->getCell(++$currentColumn . '4')->setValue('TOTAL');

        $sheet->getStyle('A4:'.$currentColumn.'4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A4:'.$currentColumn.'4')->applyFromArray($styleArray);

        for ($col = 'A'; $col <= $currentColumn; $col++) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $no = 0;
        $b = 4;
        foreach ($data as $d){
            ++$b;

            $sheet->getCell('C'.$b)->setValue(++$no);
            $sheet->getCell('D'.$b)->setValue($d['proyek']);
            $sheet->getCell('E'.$b)->setValue($d['total_anggota']);
            $sheet->getCell('F'.$b)->setValue($d['pokok']);
            $sheet->getCell('G'.$b)->setValue($d['wajib']);
            $sheet->getCell('H'.$b)->setValue($d['sukarela']);
            $sheet->getCell('I'.$b)->setValue($d['pinj_tunai_angsuran']);
            $sheet->getCell('J'.$b)->setValue($d['pinj_tunai_bunga']);
            $sheet->getCell('K'.$b)->setValue($d['pinj_barang_angsuran']);
            $sheet->getCell('L'.$b)->setValue($d['pinj_barang_bunga']);
            $sheet->getCell('M'.$b)->setValue($d['pinj_pendidikan_angsuran']);
            $sheet->getCell('N'.$b)->setValue($d['pinj_pendidikan_bunga']);
            // $sheet->getCell('N'.$b)->setValue($d['pinj_darurat_angsuran']);
            // $sheet->getCell('O'.$b)->setValue($d['pinj_darurat_bunga']);
            $sheet->getCell('O'.$b)->setValue($d['pinj_softloan_angsuran']);
            $sheet->getCell('P'.$b)->setValue($d['pinj_softloan_bunga']);
            $sheet->getCell('Q'.$b)->setValue($d['pinj_kendaraan_angsuran']);
            $sheet->getCell('R'.$b)->setValue($d['pinj_kendaraan_bunga']);
            $sheet->getCell('S'.$b)->setValue('=SUM(D9:Q'.$b.')');
            

            $sheet->getStyle('F'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('G'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('H'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('I'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('J'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('K'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('L'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('M'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('N'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('O'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('P'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('Q'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('R'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $sheet->getStyle('S'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            // $sheet->getStyle('T'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
        }
        $endCol = $b + 1;

        $sheet->getCell('B'.$endCol)->setValue('TOTAL');
        for ($col = 'C'; $col <= $currentColumn; $col++) {
            $sheet->getCell($col.$endCol)->setValue('=SUM('.$col.'5:'.$col.$b.')');
            $sheet->getStyle($col.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        }
        $sheet->getStyle('B'.$endCol.':'.$currentColumn.$endCol)->applyFromArray($styleArrayTotal);

        $sheet->getCell('B' . $endCol+=2)->setValue('JAKARTA, ' . date('d F Y'));
        $sheet->getCell('B' . ++$endCol)->setValue('Dibuat Oleh, ');
        $sheet->getCell('H' . $endCol)->setValue('Mengetahui, ');
        $sheet->getCell('B' . $endCol+=5)->setValue('Fitri Yuliana');
        $sheet->getCell('H' . $endCol)->setValue('Dinova Palmerini');
        $sheet->getStyle('B'.$endCol.':H'.$endCol)->applyFromArray($styleUnderline);
        $sheet->getCell('B' . ++$endCol)->setValue('Spv Simpan Pinjam');
        $sheet->getCell('H' . $endCol)->setValue('Bendahara');

        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet(1);

        return $spreadsheet;

    }

}

class reverseDataHelper{
    public static function generateMemberDeposit($start, $end, $member_id){

        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');
        $months = DownloadReport::generateMonth($start_date, $end_date);
        $m =[];
        foreach ($months as $month){
            $memberDepositPokok = TsDeposits::totalDepositPokokDate($member_id, $month);
            $memberDepositWajib = TsDeposits::totalDepositWajibDate($member_id, $month);
            $memberDepositSukarela = TsDeposits::totalDepositSukarelaDate($member_id, $month);
            $memberDepositShu = TsDeposits::totalDepositShuDate($member_id, $month);
            $m[] = [
                'tahun' => $month->format('F Y'),
                'pokok' => $memberDepositPokok,
                'wajib' => $memberDepositWajib,
                'sukarela' => $memberDepositSukarela,
                'shu' => $memberDepositShu
            ];
        }

        return $m;
    }

    public static function generateMemberSukarelaDeposit($start, $end, $member_id){

        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');
        $months = DownloadReport::generateMonth($start_date, $end_date);
        $m =[];
        $totalSukarela = 0;
        foreach ($months as $month){
            $debitDepositSukarela = TsDeposits::totalDepositSukarelaTypeDate($member_id, $month, 'debit');
            $creditDepositSukarela = TsDeposits::totalDepositSukarelaTypeDate($member_id, $month, 'credit');
            $totalSukarela = $totalSukarela + ($debitDepositSukarela - $creditDepositSukarela);
            $m[] = [
                'tahun' => $month->format('F Y'),
                'masuk' => $debitDepositSukarela,
                'keluar' => $creditDepositSukarela,
                'saldo' => $totalSukarela
            ];
        }

        return $m;
    }

    public static function generatePendapatanProvisiJasaAdmin($start, $end, $area, $proyek, $member_id)
    {
        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');
        $months = DownloadReport::generateMonth($start_date, $end_date);
        $m =[];

        foreach ($months as $month){
            $dataProvisi = TsLoans::totalPendapatanProvisiDate($month, $area, $proyek, $member_id);
            $dataJasa = TsLoans::totalPendapatanJasaDate($month, $area, $proyek, $member_id);
            $dataAdmin = TsLoans::totalPendapatanAdminDate($month, $area, $proyek, $member_id);

            $m[] = [
                'tahun' => $month->format('F Y'),
                'provisi' => $dataProvisi,
                'jasa' => $dataJasa,
                'admin' => $dataAdmin
            ];

        }
        
        return $m;
    }

    public static function generateMemberAreaProyek($start, $end, $area, $proyek){

        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');
        $months = DownloadReport::generateMonth($start_date, $end_date);
        $m =[];

        if($area == 'ALL'){
            $regions = Region::all();
            foreach ($regions as $region){
                $p=[];
                $member = Member::where('region_id', $region->id)
                    ->whereBetween('join_date', [$start_date, $end_date])
                    ->get();

                $countMember = $member->count();
                
                if($countMember > 0){
                    if($proyek == 'ALL'){
                        foreach($region->project as $project){
                            if($member->where('project_id', $project->id)->count() > 0){
                                $p[] = [
                                    'name' => $project->project_name,
                                    'count' => $member->where('project_id', $project->id)->count()
                                ];
                            }
                        }
                    }else{
                        $project = Project::where('id', $proyek)->first();
                        if($member->where('project_id', $proyek)->count() > 0){
                            $p[] = [
                                'name' => $project->project_name,
                                'count' => $member->where('project_id', $proyek)->count()
                            ];
                        }
                    }
                    $m[] = [
                        'region' => $region->name_area,
                        'project' => $p,
                        'total' => $countMember
                    ];
                }
            }
        }else{
            $p=[];
            $region = Region::where('id', $area)->first();
            $member = Member::where('region_id', $area)
                ->whereBetween('join_date', [$start_date, $end_date])
                ->get();
            
            $countMember = $member->count();

            if($countMember > 0){
                if($proyek == 'ALL'){
                    foreach($region->project as $project){
                        if($member->where('project_id', $project->id)->count() > 0){
                            $p[] = [
                                'name' => $project->project_name,
                                'count' => $member->where('project_id', $project->id)->count()
                            ];
                        }
                    }
                }else{
                    $project = Project::where('id', $proyek)->first();
                    if($member->where('project_id', $proyek)->count() > 0){
                        $p[] = [
                            'name' => $project->project_name,
                            'count' => $member->where('project_id', $proyek)->count()
                        ];
                    }
                }
                $m[] = [
                    'region' => $region->name_area,
                    'project' => $p,
                    'total' => $countMember
                ];
            }
        }

        return $m;
    }

    public static function generateMemberResign($start, $end, $area, $proyek, $member_id){
        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');

        $memberResign = Member::whereDate('updated_at','>=', $start_date)
            ->whereDate('updated_at','<=', $end_date)
            ->where('status', 'resign')
            ->when($area != 'ALL', function ($query) use ($area){
                return $query->where('region_id', $area);
            })
            ->when($proyek != 'ALL', function ($query) use ($proyek){
                return $query->where('project_id', $proyek);
            })
            ->when($member_id != 'ALL', function ($query) use ($member_id){
                return $query->where('id', $member_id);
            })
            ->get();

        $m = [];

        foreach ($memberResign as $resign){
            $member = Member::whereHas('region')->where('id', $resign->id)->first();
            $position = Position::findOrFail($member->position_id);
            $project = Project::findOrFail($member->project_id);
            $bank = Bank::where('member_id', $member->id)->first();
            $loanData = Loan::findOrFail(1);
            $loanDataTunai = TsLoans::where('member_id', $member->id)->whereIn('loan_id', array(1, 2))->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataBarang = TsLoans::where('member_id', $member->id)->where('loan_id', 3)->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataPendidikan = TsLoans::where('member_id', $member->id)->where('loan_id', 4)->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataSoftloan = TsLoans::where('member_id', $member->id)->where('loan_id', 10)->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataKendaraan = TsLoans::where('member_id', $member->id)->where('loan_id', 13)->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $pokok = TsDeposits::totalDepositPokok($member->id);
            $sukarela = TsDeposits::totalDepositSukarela($member->id);
            $wajib = TsDeposits::totalDepositWajib($member->id);
            $shu = TsDeposits::totalDepositShu($member->id);
            $lainnya = TsDeposits::totalDepositLainnya($member->id);
            $angsuranPinjTunai = (isset($loanDataTunai->id)) ? ($loanDataTunai->detail[0]->value + $loanDataTunai->detail[0]->service) : 0;
            $bungaPinjTunai = (isset($loanDataTunai->id)) ? $loanDataTunai->biaya_bunga_berjalan : 0;
            $potPinjTunai = (isset($loanDataTunai->id)) ? ($loanDataTunai->period - $loanDataTunai->in_period) : 0;
            $totalPinjTunai = ($angsuranPinjTunai * $potPinjTunai) + $bungaPinjTunai;
            $angsuranPinjBarang = (isset($loanDataBarang->id)) ? ($loanDataBarang->detail[0]->value + $loanDataBarang->detail[0]->service) : 0;
            $bungaPinjBarang = (isset($loanDataBarang->id)) ? $loanDataBarang->biaya_bunga_berjalan : 0;
            $potPinjBarang = (isset($loanDataBarang->id)) ? ($loanDataBarang->period - $loanDataBarang->in_period) : 0;
            $totalPinjBarang = ($angsuranPinjBarang * $potPinjBarang) + $bungaPinjBarang;
            $angsuranPinjPendidikan = (isset($loanDataPendidikan->id)) ? ($loanDataPendidikan->detail[0]->value + $loanDataPendidikan->detail[0]->service) : 0;
            $bungaPinjPendidikan = (isset($loanDataPendidikan->id)) ? $loanDataPendidikan->biaya_bunga_berjalan : 0;
            $potPinjPendidikan = (isset($loanDataPendidikan->id)) ? ($loanDataPendidikan->period - $loanDataPendidikan->in_period) : 0;
            $totalPinjPendidikan = ($angsuranPinjPendidikan * $potPinjPendidikan) + $bungaPinjPendidikan;
            $angsuranPinjSoftloan = (isset($loanDataSoftloan->id)) ? ($loanDataSoftloan->detail[0]->value + $loanDataSoftloan->detail[0]->service) : 0;
            $bungaPinjSoftloan = (isset($loanDataSoftloan->id)) ? $loanDataSoftloan->biaya_bunga_berjalan : 0;
            $potPinjSoftloan = (isset($loanDataSoftloan->id)) ? ($loanDataSoftloan->period - $loanDataSoftloan->in_period) : 0;
            $totalPinjSoftloan = ($angsuranPinjSoftloan * $potPinjSoftloan) + $bungaPinjSoftloan;
            $angsuranPinjKendaraan = (isset($loanDataKendaraan->id)) ? ($loanDataKendaraan->detail[0]->value + $loanDataKendaraan->detail[0]->service) : 0;
            $bungaPinjKendaraan = (isset($loanDataKendaraan->id)) ? $loanDataKendaraan->biaya_bunga_berjalan : 0;
            $potPinjKendaraan = (isset($loanDataKendaraan->id)) ? ($loanDataKendaraan->period - $loanDataKendaraan->in_period) : 0;
            $totalPinjKendaraan = ($angsuranPinjKendaraan * $potPinjKendaraan) + $bungaPinjKendaraan;
            $total = $pokok + $sukarela + $wajib + $shu + $lainnya;
            $totalHak = $total - ($totalPinjTunai + $totalPinjBarang + $totalPinjPendidikan + $totalPinjSoftloan + $totalPinjKendaraan);
            $m[] = [
                'nama' => $member->full_name,
                'tgl_resign' => date('d M Y', strtotime($resign->updated_at)),
                'proyek' => $project->project_name,
                'area' => $member->region->name_area,
                'no_anggota' => $member->nik_koperasi,
                'no_register' => $member->nik_bsp,
                'bank' => (isset($bank->bank_name)) ? $bank->bank_name : "",
                'bank_name' => (isset($bank->bank_account_name)) ? $bank->bank_account_name : "",
                'pokok' => $pokok,
                'sukarela' => $sukarela,
                'wajib' => $wajib,
                'shu' => $shu,
                'lainnya' => $lainnya,
                'total' => $total,
                'biaya_admin' => $loanData->biaya_admin,
                'pinj_tunai_angsuran' =>  $angsuranPinjTunai,
                'pinj_tunai_bunga' => $bungaPinjTunai,
                'pinj_tunai_sisa_ptgn' => $potPinjTunai,
                'pinj_tunai_total' => $totalPinjTunai,
                'pinj_barang_angsuran' =>  $angsuranPinjBarang,
                'pinj_barang_bunga' => $bungaPinjBarang,
                'pinj_barang_sisa_ptgn' => $potPinjBarang,
                'pinj_barang_total' => $totalPinjBarang,
                'pinj_pendidikan_angsuran' =>  $angsuranPinjPendidikan,
                'pinj_pendidikan_bunga' => $bungaPinjPendidikan,
                'pinj_pendidikan_sisa_ptgn' => $potPinjPendidikan,
                'pinj_pendidikan_total' => $totalPinjPendidikan,
                'pinj_softloan_angsuran' =>  $angsuranPinjSoftloan,
                'pinj_softloan_bunga' => $bungaPinjSoftloan,
                'pinj_softloan_sisa_ptgn' => $potPinjSoftloan,
                'pinj_softloan_total' => $totalPinjSoftloan,
                'pinj_kendaraan_angsuran' =>  $angsuranPinjKendaraan,
                'pinj_kendaraan_bunga' => $bungaPinjKendaraan,
                'pinj_kendaraan_sisa_ptgn' => $potPinjKendaraan,
                'pinj_kendaraan_total' => $totalPinjKendaraan,
                'total_hak' => ($totalHak < 0) ? 0 : $totalHak,
                'mulai_proses' => $project->start_date,
                'jabatan' => $position->name,
            ];
        }
        return $m;
    }

    public static function generateSisaHak($start, $end, $area, $proyek, $member_id){
        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');

        $dataMember = Member::when($area != 'ALL', function ($query) use ($area){
            return $query->where('region_id', $area);
        })
        ->when($area != 'ALL', function ($query) use ($area){
            return $query->whereHas('region');
        })
        ->when($proyek != 'ALL', function ($query) use ($proyek){
            return $query->where('project_id', $proyek);
        })
        ->when($member_id != 'ALL', function ($query) use ($member_id){
            return $query->where('id', $member_id);
        })
        ->where('status', 'titipan')->get();

        $m = [];

        foreach ($dataMember as $member){
            $position = Position::findOrFail($member->position_id);
            $project = Project::findOrFail($member->project_id);
            $bank = Bank::where('member_id', $member->id)->first();
            $loanData = Loan::findOrFail(1);
            $loanDataTunai = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                ->whereDate('start_date','<=', $end_date)->whereIn('loan_id', array(1, 2))->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataBarang = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
            ->whereDate('start_date','<=', $end_date)->where('loan_id', 3)->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataPendidikan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
            ->whereDate('start_date','<=', $end_date)->where('loan_id', 4)->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataSoftloan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
            ->whereDate('start_date','<=', $end_date)->where('loan_id', 10)->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataKendaraan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
            ->whereDate('start_date','<=', $end_date)->where('loan_id', 13)->where(function($q) {
                $q->where('status', 'menunggu')
                    ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $totalPencairan = TsDeposits::where('member_id', $member->id)->whereDate('post_date','>=', $start_date)
            ->whereDate('post_date','<=', $end_date)->where('status', 'paid')->sum('total_deposit');
            $dataPencairan = TsDeposits::where('member_id', $member->id)->whereDate('post_date','>=', $start_date)
            ->whereDate('post_date','<=', $end_date)->where('status', 'paid')->orderBy('post_date', 'desc')->first();
            $pokok = TsDeposits::totalDepositPokokRangeDate($member->id, $start_date, $end_date);
            $sukarela = TsDeposits::totalDepositSukarelaRangeDate($member->id, $start_date, $end_date);
            $wajib = TsDeposits::totalDepositWajibRangeDate($member->id, $start_date, $end_date);
            $shu = TsDeposits::totalDepositShuRangeDate($member->id, $start_date, $end_date);
            $lainnya = TsDeposits::totalDepositLainnyaRangeDate($member->id, $start_date, $end_date);
            $angsuranPinjTunai = (isset($loanDataTunai->id)) ? ($loanDataTunai->detail[0]->value + $loanDataTunai->detail[0]->service) : 0;
            $bungaPinjTunai = (isset($loanDataTunai->id)) ? $loanDataTunai->biaya_bunga_berjalan : 0;
            $potPinjTunai = (isset($loanDataTunai->id)) ? ($loanDataTunai->period - $loanDataTunai->in_period) : 0;
            $totalPinjTunai = ($angsuranPinjTunai * $potPinjTunai) + $bungaPinjTunai;
            $angsuranPinjBarang = (isset($loanDataBarang->id)) ? ($loanDataBarang->detail[0]->value + $loanDataBarang->detail[0]->service) : 0;
            $bungaPinjBarang = (isset($loanDataBarang->id)) ? $loanDataBarang->biaya_bunga_berjalan : 0;
            $potPinjBarang = (isset($loanDataBarang->id)) ? ($loanDataBarang->period - $loanDataBarang->in_period) : 0;
            $totalPinjBarang = ($angsuranPinjBarang * $potPinjBarang) + $bungaPinjBarang;
            $angsuranPinjPendidikan = (isset($loanDataPendidikan->id)) ? ($loanDataPendidikan->detail[0]->value + $loanDataPendidikan->detail[0]->service) : 0;
            $bungaPinjPendidikan = (isset($loanDataPendidikan->id)) ? $loanDataPendidikan->biaya_bunga_berjalan : 0;
            $potPinjPendidikan = (isset($loanDataPendidikan->id)) ? ($loanDataPendidikan->period - $loanDataPendidikan->in_period) : 0;
            $totalPinjPendidikan = ($angsuranPinjPendidikan * $potPinjPendidikan) + $bungaPinjPendidikan;
            $angsuranPinjSoftloan = (isset($loanDataSoftloan->id)) ? ($loanDataSoftloan->detail[0]->value + $loanDataSoftloan->detail[0]->service) : 0;
            $bungaPinjSoftloan = (isset($loanDataSoftloan->id)) ? $loanDataSoftloan->biaya_bunga_berjalan : 0;
            $potPinjSoftloan = (isset($loanDataSoftloan->id)) ? ($loanDataSoftloan->period - $loanDataSoftloan->in_period) : 0;
            $totalPinjSoftloan = ($angsuranPinjSoftloan * $potPinjSoftloan) + $bungaPinjSoftloan;
            $angsuranPinjKendaraan = (isset($loanDataKendaraan->id)) ? ($loanDataKendaraan->detail[0]->value + $loanDataKendaraan->detail[0]->service) : 0;
            $bungaPinjKendaraan = (isset($loanDataKendaraan->id)) ? $loanDataKendaraan->biaya_bunga_berjalan : 0;
            $potPinjKendaraan = (isset($loanDataKendaraan->id)) ? ($loanDataKendaraan->period - $loanDataKendaraan->in_period) : 0;
            $totalPinjKendaraan = ($angsuranPinjKendaraan * $potPinjKendaraan) + $bungaPinjKendaraan;
            $total = $pokok + $sukarela + $wajib + $shu + $lainnya;
            $totalHak = $total - ($totalPinjTunai + $totalPinjBarang + $totalPinjPendidikan + $totalPinjSoftloan + $totalPinjKendaraan);
            $totalHakAnggota = ($totalHak < 0) ? 0 : $totalHak;
            $totalMinusSisa = $totalHakAnggota - $totalPencairan;
            $totalSisaAnggota = ($totalMinusSisa < 0) ? 0 : $totalMinusSisa;
            $m[] = [
                'nama' => $member->full_name,
                'proyek' => $project->project_name,
                'area' => $member->region->name_area,
                'no_anggota' => $member->nik_koperasi,
                'no_register' => $member->nik_bsp,
                'bank' => (isset($bank->bank_name)) ? $bank->bank_name : '',
                'bank_name' => (isset($bank->bank_account_name)) ? $bank->bank_account_name : '',
                'pokok' => $pokok,
                'sukarela' => $sukarela,
                'wajib' => $wajib,
                'shu' => $shu,
                'lainnya' => $lainnya,
                'total' => $total,
                'biaya_admin' => $loanData->biaya_admin,
                'pinj_tunai_angsuran' =>  $angsuranPinjTunai,
                'pinj_tunai_bunga' => $bungaPinjTunai,
                'pinj_tunai_sisa_ptgn' => $potPinjTunai,
                'pinj_tunai_total' => $potPinjTunai,
                'pinj_barang_angsuran' =>  $angsuranPinjBarang,
                'pinj_barang_bunga' => $bungaPinjBarang,
                'pinj_barang_sisa_ptgn' => $potPinjBarang,
                'pinj_barang_total' => $potPinjBarang,
                'pinj_pendidikan_angsuran' =>  $angsuranPinjPendidikan,
                'pinj_pendidikan_bunga' => $bungaPinjPendidikan,
                'pinj_pendidikan_sisa_ptgn' => $potPinjPendidikan,
                'pinj_pendidikan_total' => $potPinjPendidikan,
                'pinj_softloan_angsuran' =>  $angsuranPinjSoftloan,
                'pinj_softloan_bunga' => $bungaPinjSoftloan,
                'pinj_softloan_sisa_ptgn' => $potPinjSoftloan,
                'pinj_softloan_total' => $potPinjSoftloan,
                'pinj_kendaraan_angsuran' =>  $angsuranPinjKendaraan,
                'pinj_kendaraan_bunga' => $bungaPinjKendaraan,
                'pinj_kendaraan_sisa_ptgn' => $potPinjKendaraan,
                'pinj_kendaraan_total' => $potPinjKendaraan,
                'total_hak' => $totalHakAnggota,
                'diambil' => $totalPencairan,
                'total_blm_diambil' => $totalSisaAnggota,
                'tanggal_pengambilan' => (isset($dataPencairan->post_date)) ? date('Y-m-d', strtotime($dataPencairan->post_date)) : '',
                'mulai_proses' => $project->start_date,
                'jabatan' => $position->name,
            ];
        }
        return $m;
    }

    public static function generateKreditBermasalah($start, $end, $area, $proyek, $member_id){
        $start_date = Carbon::parse($start)->format('Y-m-d');
        $end_date = Carbon::parse($end)->format('Y-m-d');

        $dataMember = Member::when($area != 'ALL', function ($query) use ($area){
            return $query->where('region_id', $area);
        })
        ->when($area != 'ALL', function ($query) use ($area){
            return $query->whereHas('region');
        })
        ->when($proyek != 'ALL', function ($query) use ($proyek){
            return $query->where('project_id', $proyek);
        })
        ->when($member_id != 'ALL', function ($query) use ($member_id){
            return $query->where('id', $member_id);
        })
        ->where('status', 'npl')->get();

        $m = [];

        foreach ($dataMember as $member){
            $position = Position::findOrFail($member->position_id);
            $project = Project::findOrFail($member->project_id);
            $bank = Bank::where('member_id', $member->id)->first();
            $loanData = Loan::findOrFail(1);
            $loanDataTunai = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                ->whereDate('start_date','<=', $end_date)->whereIn('loan_id', array(1, 2))->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataBarang = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
            ->whereDate('start_date','<=', $end_date)->where('loan_id', 3)->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataPendidikan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
            ->whereDate('start_date','<=', $end_date)->where('loan_id', 4)->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataSoftloan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
            ->whereDate('start_date','<=', $end_date)->where('loan_id', 10)->where(function($q) {
                $q->where('status', 'menunggu')
                ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataKendaraan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
            ->whereDate('start_date','<=', $end_date)->where('loan_id', 13)->where(function($q) {
                $q->where('status', 'menunggu')
                    ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->with('detail')->first();
            $loanDataPokokPinjaman = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
            ->whereDate('start_date','<=', $end_date)->where(function($q) {
                $q->where('status', 'menunggu')
                    ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->sum('value');
            $loanDataJasaPinjaman = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
            ->whereDate('start_date','<=', $end_date)->where(function($q) {
                $q->where('status', 'menunggu')
                    ->orWhere('status', 'disetujui')
                  ->orWhere('status', 'belum lunas');
            })->sum('biaya_jasa');
            $totalPokokdanJasa = $loanDataPokokPinjaman + $loanDataJasaPinjaman;
            $pokok = TsDeposits::totalDepositPokokRangeDate($member->id, $start_date, $end_date);
            $sukarela = TsDeposits::totalDepositSukarelaRangeDate($member->id, $start_date, $end_date);
            $wajib = TsDeposits::totalDepositWajibRangeDate($member->id, $start_date, $end_date);
            $shu = TsDeposits::totalDepositShuRangeDate($member->id, $start_date, $end_date);
            $lainnya = TsDeposits::totalDepositLainnyaRangeDate($member->id, $start_date, $end_date);
            $angsuranPinjTunai = (isset($loanDataTunai->id)) ? ($loanDataTunai->detail[0]->value + $loanDataTunai->detail[0]->service) : 0;
            $bungaPinjTunai = (isset($loanDataTunai->id)) ? $loanDataTunai->biaya_bunga_berjalan : 0;
            $potPinjTunai = (isset($loanDataTunai->id)) ? ($loanDataTunai->period - $loanDataTunai->in_period) : 0;
            $totalPinjTunai = ($angsuranPinjTunai * $potPinjTunai) + $bungaPinjTunai;
            $angsuranPinjBarang = (isset($loanDataBarang->id)) ? ($loanDataBarang->detail[0]->value + $loanDataBarang->detail[0]->service) : 0;
            $bungaPinjBarang = (isset($loanDataBarang->id)) ? $loanDataBarang->biaya_bunga_berjalan : 0;
            $potPinjBarang = (isset($loanDataBarang->id)) ? ($loanDataBarang->period - $loanDataBarang->in_period) : 0;
            $totalPinjBarang = ($angsuranPinjBarang * $potPinjBarang) + $bungaPinjBarang;
            $angsuranPinjPendidikan = (isset($loanDataPendidikan->id)) ? ($loanDataPendidikan->detail[0]->value + $loanDataPendidikan->detail[0]->service) : 0;
            $bungaPinjPendidikan = (isset($loanDataPendidikan->id)) ? $loanDataPendidikan->biaya_bunga_berjalan : 0;
            $potPinjPendidikan = (isset($loanDataPendidikan->id)) ? ($loanDataPendidikan->period - $loanDataPendidikan->in_period) : 0;
            $totalPinjPendidikan = ($angsuranPinjPendidikan * $potPinjPendidikan) + $bungaPinjPendidikan;
            $angsuranPinjSoftloan = (isset($loanDataSoftloan->id)) ? ($loanDataSoftloan->detail[0]->value + $loanDataSoftloan->detail[0]->service) : 0;
            $bungaPinjSoftloan = (isset($loanDataSoftloan->id)) ? $loanDataSoftloan->biaya_bunga_berjalan : 0;
            $potPinjSoftloan = (isset($loanDataSoftloan->id)) ? ($loanDataSoftloan->period - $loanDataSoftloan->in_period) : 0;
            $totalPinjSoftloan = ($angsuranPinjSoftloan * $potPinjSoftloan) + $bungaPinjSoftloan;
            $angsuranPinjKendaraan = (isset($loanDataKendaraan->id)) ? ($loanDataKendaraan->detail[0]->value + $loanDataKendaraan->detail[0]->service) : 0;
            $bungaPinjKendaraan = (isset($loanDataKendaraan->id)) ? $loanDataKendaraan->biaya_bunga_berjalan : 0;
            $potPinjKendaraan = (isset($loanDataKendaraan->id)) ? ($loanDataKendaraan->period - $loanDataKendaraan->in_period) : 0;
            $totalPinjKendaraan = ($angsuranPinjKendaraan * $potPinjKendaraan) + $bungaPinjKendaraan;
            $total = $pokok + $sukarela + $wajib + $shu + $lainnya;
            $pelunasan = 0;
            $m[] = [
                'nama' => $member->full_name,
                'proyek' => $project->project_name,
                'area' => $member->region->name_area,
                'no_anggota' => $member->nik_koperasi,
                'no_register' => $member->nik_bsp,
                'bank' => (isset($bank->bank_name)) ? $bank->bank_name : '',
                'bank_name' => (isset($bank->bank_account_name)) ? $bank->bank_account_name : '',
                'pokok' => $pokok,
                'sukarela' => $sukarela,
                'wajib' => $wajib,
                'shu' => $shu,
                'lainnya' => $lainnya,
                'total' => $total,
                'biaya_admin' => $loanData->biaya_admin,
                'pinj_tunai_angsuran' =>  $angsuranPinjTunai,
                'pinj_tunai_bunga' => $bungaPinjTunai,
                'pinj_tunai_sisa_ptgn' => $potPinjTunai,
                'pinj_tunai_total' => $totalPinjTunai,
                'pinj_barang_angsuran' =>  $angsuranPinjBarang,
                'pinj_barang_bunga' => $bungaPinjBarang,
                'pinj_barang_sisa_ptgn' => $potPinjBarang,
                'pinj_barang_total' => $totalPinjBarang,
                'pinj_pendidikan_angsuran' =>  $angsuranPinjPendidikan,
                'pinj_pendidikan_bunga' => $bungaPinjPendidikan,
                'pinj_pendidikan_sisa_ptgn' => $potPinjPendidikan,
                'pinj_pendidikan_total' => $totalPinjPendidikan,
                'pinj_softloan_angsuran' =>  $angsuranPinjSoftloan,
                'pinj_softloan_bunga' => $bungaPinjSoftloan,
                'pinj_softloan_sisa_ptgn' => $potPinjSoftloan,
                'pinj_softloan_total' => $totalPinjSoftloan,
                'pinj_kendaraan_angsuran' =>  $angsuranPinjKendaraan,
                'pinj_kendaraan_bunga' => $bungaPinjKendaraan,
                'pinj_kendaraan_sisa_ptgn' => $potPinjKendaraan,
                'pinj_kendaraan_total' => $totalPinjKendaraan,
                'total_pokok_pinjaman' => $loanDataPokokPinjaman,
                'total_jasa_pinjaman' => $loanDataJasaPinjaman,
                'total_pokok_jasa' => $totalPokokdanJasa,
                'total_kewajiban' => $loanDataPokokPinjaman,
                'pelunasan' => $pelunasan,
                'sisa_kewajiban' => $loanDataPokokPinjaman - $pelunasan,
                'keterangan_npl' => '',
                'keterangan_pelunasan' => '',
                'mulai_proses' => $project->start_date,
                'jabatan' => $position->name,
            ];
        }
        return $m;
    }

    public static function generatePotAnggota($periode, $area, $proyek){
        $start_date = Carbon::parse($periode)->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::parse($periode)->endOfMonth()->format('Y-m-d');

        $regions = Region::all();
        $project = Member::select('project_id')
            ->when($area != 'ALL', function ($query) use ($area) {
                return $query->where('region_id', $area);
            })->when($proyek != 'ALL', function ($query) use ($proyek) {
                return $query->where('project_id', $proyek);
            })->when($proyek == 'ALL', function ($query) use ($proyek) {
                return $query->whereHas('project');
            })
            ->groupBy('project_id')->get();

        $m = [];

        $angsuranPinjTunai = 0;
        $bungaPinjTunai = 0;
        $angsuranPinjBarang = 0;
        $bungaPinjBarang = 0;
        $angsuranPinjPendidikan = 0;
        $bungaPinjPendidikan = 0;
        $angsuranPinjDarurat = 0;
        $bungaPinjDarurat = 0;
        $angsuranPinjSoftloan = 0;
        $bungaPinjSoftloan = 0;
        $angsuranPinjKendaraan = 0;
        $bungaPinjKendaraan = 0;
        $totalPokok = 0;
        $totalWajib = 0;
        $totalSukarela = 0;
        $total = 0;
        $totalAll = 0;

        // foreach ($regions as $region){
        //     $dataMemberRegion = Member::where('region_id', $region->id)->get();

        //     $countMemberRegion = $dataMemberRegion->count();

        //     if($countMemberRegion > 0){
                foreach($project as $project){
                    $members = Member::where('project_id', $project->project_id)->with('project')->get();
                    // $countMemberProject = $member->count();

                    // if($countMemberProject > 0){
                        // var_dump($project->id);
                        foreach($members as $member){
                            $loanDataTunai = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                                ->whereDate('start_date','<=', $end_date)->whereIn('loan_id', array(1, 2))->where(function($q) {
                                $q->where('status', 'menunggu')
                                ->orWhere('status', 'disetujui')
                                ->orWhere('status', 'belum lunas');
                            })->with('detail')->first();
                            $loanDataBarang = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                            ->whereDate('start_date','<=', $end_date)->where('loan_id', 3)->where(function($q) {
                                $q->where('status', 'menunggu')
                                ->orWhere('status', 'disetujui')
                                ->orWhere('status', 'belum lunas');
                            })->with('detail')->first();
                            $loanDataPendidikan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                            ->whereDate('start_date','<=', $end_date)->where('loan_id', 4)->where(function($q) {
                                $q->where('status', 'menunggu')
                                ->orWhere('status', 'disetujui')
                                ->orWhere('status', 'belum lunas');
                            })->with('detail')->first();
                            // $loanDataDarurat = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                            // ->whereDate('start_date','<=', $end_date)->where('loan_id', 5)->where(function($q) {
                            //     $q->where('status', 'menunggu')
                            //     ->orWhere('status', 'disetujui')
                            //     ->orWhere('status', 'belum lunas');
                            // })->with('detail')->first();
                            $loanDataSoftloan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                            ->whereDate('start_date','<=', $end_date)->where('loan_id', 10)->where(function($q) {
                                $q->where('status', 'menunggu')
                                ->orWhere('status', 'disetujui')
                                ->orWhere('status', 'belum lunas');
                            })->with('detail')->first();
                            $loanDataKendaraan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                            ->whereDate('start_date','<=', $end_date)->where('loan_id', 13)->where(function($q) {
                                $q->where('status', 'menunggu')
                                    ->orWhere('status', 'disetujui')
                                ->orWhere('status', 'belum lunas');
                            })->with('detail')->first();
                            $pokok = TsDeposits::totalDepositPokokRangeDate($member->id, $start_date, $end_date);
                            $sukarela = TsDeposits::totalDepositSukarelaRangeDate($member->id, $start_date, $end_date);
                            $wajib = TsDeposits::totalDepositWajibRangeDate($member->id, $start_date, $end_date);

                            $angsuranPinjTunai = $angsuranPinjTunai + ((isset($loanDataTunai->id)) ? ($loanDataTunai->detail[0]->value + $loanDataTunai->detail[0]->service) : 0);
                            $bungaPinjTunai = $bungaPinjTunai + ((isset($loanDataTunai->id)) ? $loanDataTunai->biaya_bunga_berjalan : 0);
                            $totalPinjTunai = $angsuranPinjTunai + $bungaPinjTunai;
                            $angsuranPinjBarang = $angsuranPinjBarang + ((isset($loanDataBarang->id)) ? ($loanDataBarang->detail[0]->value + $loanDataBarang->detail[0]->service) : 0);
                            $bungaPinjBarang = $bungaPinjBarang + ((isset($loanDataBarang->id)) ? $loanDataBarang->biaya_bunga_berjalan : 0);
                            $totalPinjBarang = $angsuranPinjBarang + $bungaPinjBarang;
                            $angsuranPinjPendidikan = $angsuranPinjPendidikan + ((isset($loanDataPendidikan->id)) ? ($loanDataPendidikan->detail[0]->value + $loanDataPendidikan->detail[0]->service) : 0);
                            $bungaPinjPendidikan = $bungaPinjPendidikan + ((isset($loanDataPendidikan->id)) ? $loanDataPendidikan->biaya_bunga_berjalan : 0);
                            $totalPinjPendidikan = $angsuranPinjPendidikan + $bungaPinjPendidikan;
                            // $angsuranPinjDarurat = $angsuranPinjDarurat + ((isset($loanDataDarurat->id)) ? ($loanDataDarurat->detail[0]->value + $loanDataPendidikan->detail[0]->service) : 0);
                            // $bungaPinjDarurat = $bungaPinjDarurat + ((isset($loanDataDarurat->id)) ? $loanDataDarurat->biaya_bunga_berjalan : 0);
                            $angsuranPinjSoftloan = $angsuranPinjSoftloan + ((isset($loanDataSoftloan->id)) ? ($loanDataSoftloan->detail[0]->value + $loanDataSoftloan->detail[0]->service) : 0);
                            $bungaPinjSoftloan = $bungaPinjSoftloan + ((isset($loanDataSoftloan->id)) ? $loanDataSoftloan->biaya_bunga_berjalan : 0);
                            $totalPinjSoftloan = $angsuranPinjSoftloan + $bungaPinjSoftloan;
                            $angsuranPinjKendaraan = $angsuranPinjKendaraan + ((isset($loanDataKendaraan->id)) ? ($loanDataKendaraan->detail[0]->value + $loanDataKendaraan->detail[0]->service) : 0);
                            $bungaPinjKendaraan = $bungaPinjKendaraan + ((isset($loanDataKendaraan->id)) ? $loanDataKendaraan->biaya_bunga_berjalan : 0);
                            $totalPinjKendaraan = $angsuranPinjKendaraan + $bungaPinjKendaraan;
                            $totalPokok = $totalPokok + $pokok;
                            $totalWajib = $totalWajib + $wajib;
                            $totalSukarela = $totalSukarela + $sukarela;
                            $total = $totalPokok + $totalWajib + $totalSukarela;
                            $totalAll = $total - ($totalPinjTunai + $totalPinjBarang + $totalPinjPendidikan + $totalPinjSoftloan + $totalPinjKendaraan);
                        }

                        $m[] = [
                            'proyek' => $member->project->project_name,
                            'total_anggota' => count($members),
                            'pokok' => $totalPokok,
                            'sukarela' => $totalSukarela,
                            'wajib' => $totalWajib,
                            'pinj_tunai_angsuran' =>  $angsuranPinjTunai,
                            'pinj_tunai_bunga' => $bungaPinjTunai,
                            'pinj_tunai_total' => $totalPinjTunai,
                            'pinj_barang_angsuran' =>  $angsuranPinjBarang,
                            'pinj_barang_bunga' => $bungaPinjBarang,
                            'pinj_barang_total' => $totalPinjBarang,
                            'pinj_pendidikan_angsuran' =>  $angsuranPinjPendidikan,
                            'pinj_pendidikan_bunga' => $bungaPinjPendidikan,
                            'pinj_pendidikan_total' => $totalPinjPendidikan,
                            // 'pinj_darurat_angsuran' =>  $angsuranPinjDarurat,
                            // 'pinj_darurat_bunga' => $bungaPinjDarurat,
                            'pinj_softloan_angsuran' =>  $angsuranPinjSoftloan,
                            'pinj_softloan_bunga' => $bungaPinjSoftloan,
                            'pinj_softloan_total' => $totalPinjSoftloan,
                            'pinj_kendaraan_angsuran' =>  $angsuranPinjKendaraan,
                            'pinj_kendaraan_bunga' => $bungaPinjKendaraan,
                            'pinj_kendaraan_total' => $totalPinjKendaraan,
                            'total' => $totalAll
                        ];
                    // }
                }
        //     }
        // }
        return $m;
    }

    public static function generatePotAnggotaPivot($periode, $area, $proyek){
        $start_date = Carbon::parse($periode)->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::parse($periode)->endOfMonth()->format('Y-m-d');

        $m = [];

        $angsuranPinjTunai = 0;
        $bungaPinjTunai = 0;
        $angsuranPinjBarang = 0;
        $bungaPinjBarang = 0;
        $angsuranPinjPendidikan = 0;
        $bungaPinjPendidikan = 0;
        $angsuranPinjDarurat = 0;
        $bungaPinjDarurat = 0;
        $angsuranPinjSoftloan = 0;
        $bungaPinjSoftloan = 0;
        $angsuranPinjKendaraan = 0;
        $bungaPinjKendaraan = 0;
        $totalPokok = 0;
        $totalWajib = 0;
        $totalSukarela = 0;
        $total = 0;
        $totalAll = 0;

        foreach (CarbonPeriod::create($start_date, '1 month', $end_date) as $month) {
            $dataPokok = TsDeposits::totalPotonganSimpanan($month, 1, $area, $proyek);
        }

        // foreach ($regions as $region){
        //     $dataMemberRegion = Member::where('region_id', $region->id)->get();

        //     $countMemberRegion = $dataMemberRegion->count();

        //     if($countMemberRegion > 0){
                foreach($project as $project){
                    $members = Member::where('project_id', $project->project_id)->with('project')->get();
                    // $countMemberProject = $member->count();

                    // if($countMemberProject > 0){
                        // var_dump($project->id);
                        foreach($members as $member){
                            $loanDataTunai = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                                ->whereDate('start_date','<=', $end_date)->whereIn('loan_id', array(1, 2))->where(function($q) {
                                $q->where('status', 'menunggu')
                                ->orWhere('status', 'disetujui')
                                ->orWhere('status', 'belum lunas');
                            })->with('detail')->first();
                            $loanDataBarang = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                            ->whereDate('start_date','<=', $end_date)->where('loan_id', 3)->where(function($q) {
                                $q->where('status', 'menunggu')
                                ->orWhere('status', 'disetujui')
                                ->orWhere('status', 'belum lunas');
                            })->with('detail')->first();
                            $loanDataPendidikan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                            ->whereDate('start_date','<=', $end_date)->where('loan_id', 4)->where(function($q) {
                                $q->where('status', 'menunggu')
                                ->orWhere('status', 'disetujui')
                                ->orWhere('status', 'belum lunas');
                            })->with('detail')->first();
                            // $loanDataDarurat = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                            // ->whereDate('start_date','<=', $end_date)->where('loan_id', 5)->where(function($q) {
                            //     $q->where('status', 'menunggu')
                            //     ->orWhere('status', 'disetujui')
                            //     ->orWhere('status', 'belum lunas');
                            // })->with('detail')->first();
                            $loanDataSoftloan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                            ->whereDate('start_date','<=', $end_date)->where('loan_id', 10)->where(function($q) {
                                $q->where('status', 'menunggu')
                                ->orWhere('status', 'disetujui')
                                ->orWhere('status', 'belum lunas');
                            })->with('detail')->first();
                            $loanDataKendaraan = TsLoans::where('member_id', $member->id)->whereDate('start_date','>=', $start_date)
                            ->whereDate('start_date','<=', $end_date)->where('loan_id', 13)->where(function($q) {
                                $q->where('status', 'menunggu')
                                    ->orWhere('status', 'disetujui')
                                ->orWhere('status', 'belum lunas');
                            })->with('detail')->first();
                            $pokok = TsDeposits::totalDepositPokokRangeDate($member->id, $start_date, $end_date);
                            $sukarela = TsDeposits::totalDepositSukarelaRangeDate($member->id, $start_date, $end_date);
                            $wajib = TsDeposits::totalDepositWajibRangeDate($member->id, $start_date, $end_date);

                            $angsuranPinjTunai = $angsuranPinjTunai + ((isset($loanDataTunai->id)) ? ($loanDataTunai->detail[0]->value + $loanDataTunai->detail[0]->service) : 0);
                            $bungaPinjTunai = $bungaPinjTunai + ((isset($loanDataTunai->id)) ? $loanDataTunai->biaya_bunga_berjalan : 0);
                            $totalPinjTunai = $angsuranPinjTunai + $bungaPinjTunai;
                            $angsuranPinjBarang = $angsuranPinjBarang + ((isset($loanDataBarang->id)) ? ($loanDataBarang->detail[0]->value + $loanDataBarang->detail[0]->service) : 0);
                            $bungaPinjBarang = $bungaPinjBarang + ((isset($loanDataBarang->id)) ? $loanDataBarang->biaya_bunga_berjalan : 0);
                            $totalPinjBarang = $angsuranPinjBarang + $bungaPinjBarang;
                            $angsuranPinjPendidikan = $angsuranPinjPendidikan + ((isset($loanDataPendidikan->id)) ? ($loanDataPendidikan->detail[0]->value + $loanDataPendidikan->detail[0]->service) : 0);
                            $bungaPinjPendidikan = $bungaPinjPendidikan + ((isset($loanDataPendidikan->id)) ? $loanDataPendidikan->biaya_bunga_berjalan : 0);
                            $totalPinjPendidikan = $angsuranPinjPendidikan + $bungaPinjPendidikan;
                            // $angsuranPinjDarurat = $angsuranPinjDarurat + ((isset($loanDataDarurat->id)) ? ($loanDataDarurat->detail[0]->value + $loanDataPendidikan->detail[0]->service) : 0);
                            // $bungaPinjDarurat = $bungaPinjDarurat + ((isset($loanDataDarurat->id)) ? $loanDataDarurat->biaya_bunga_berjalan : 0);
                            $angsuranPinjSoftloan = $angsuranPinjSoftloan + ((isset($loanDataSoftloan->id)) ? ($loanDataSoftloan->detail[0]->value + $loanDataSoftloan->detail[0]->service) : 0);
                            $bungaPinjSoftloan = $bungaPinjSoftloan + ((isset($loanDataSoftloan->id)) ? $loanDataSoftloan->biaya_bunga_berjalan : 0);
                            $totalPinjSoftloan = $angsuranPinjSoftloan + $bungaPinjSoftloan;
                            $angsuranPinjKendaraan = $angsuranPinjKendaraan + ((isset($loanDataKendaraan->id)) ? ($loanDataKendaraan->detail[0]->value + $loanDataKendaraan->detail[0]->service) : 0);
                            $bungaPinjKendaraan = $bungaPinjKendaraan + ((isset($loanDataKendaraan->id)) ? $loanDataKendaraan->biaya_bunga_berjalan : 0);
                            $totalPinjKendaraan = $angsuranPinjKendaraan + $bungaPinjKendaraan;
                            $totalPokok = $totalPokok + $pokok;
                            $totalWajib = $totalWajib + $wajib;
                            $totalSukarela = $totalSukarela + $sukarela;
                            $total = $totalPokok + $totalWajib + $totalSukarela;
                            $totalAll = $total - ($totalPinjTunai + $totalPinjBarang + $totalPinjPendidikan + $totalPinjSoftloan + $totalPinjKendaraan);
                        }

                        $m[] = [
                            'proyek' => $member->project->project_name,
                            'total_anggota' => count($members),
                            'pokok' => $totalPokok,
                            'sukarela' => $totalSukarela,
                            'wajib' => $totalWajib,
                            'pinj_tunai_angsuran' =>  $angsuranPinjTunai,
                            'pinj_tunai_bunga' => $bungaPinjTunai,
                            'pinj_tunai_total' => $totalPinjTunai,
                            'pinj_barang_angsuran' =>  $angsuranPinjBarang,
                            'pinj_barang_bunga' => $bungaPinjBarang,
                            'pinj_barang_total' => $totalPinjBarang,
                            'pinj_pendidikan_angsuran' =>  $angsuranPinjPendidikan,
                            'pinj_pendidikan_bunga' => $bungaPinjPendidikan,
                            'pinj_pendidikan_total' => $totalPinjPendidikan,
                            // 'pinj_darurat_angsuran' =>  $angsuranPinjDarurat,
                            // 'pinj_darurat_bunga' => $bungaPinjDarurat,
                            'pinj_softloan_angsuran' =>  $angsuranPinjSoftloan,
                            'pinj_softloan_bunga' => $bungaPinjSoftloan,
                            'pinj_softloan_total' => $totalPinjSoftloan,
                            'pinj_kendaraan_angsuran' =>  $angsuranPinjKendaraan,
                            'pinj_kendaraan_bunga' => $bungaPinjKendaraan,
                            'pinj_kendaraan_total' => $totalPinjKendaraan,
                            'total' => $totalAll
                        ];
                    // }
                }
        //     }
        // }
        return $m;
    }

    public static function downloadDeposit($simpanan, $start, $end)
    {

        $maks = 100;
        $b = 1;

        //style header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ]
            ],
        ];

        $styleTitleArray = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '30453a'),
                'size'  => 15,
            ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $dataSimpananSheet = $spreadsheet->getActiveSheet(0)->setTitle('Simpanan');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('B1');
        $drawing->setWidthAndHeight(180, 110);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        // apply style to header
        $dataSimpananSheet->getStyle('B7:H8')->applyFromArray($styleArray);

        $dataSimpananSheet->getStyle('C2:C5')->applyFromArray($styleTitleArray);

        // set auto width
        $dataSimpananSheet->getColumnDimension('B')->setWidth(30);
        $dataSimpananSheet->getColumnDimension('C')->setWidth(20);
        $dataSimpananSheet->getColumnDimension('D')->setWidth(20);
        $dataSimpananSheet->getColumnDimension('E')->setWidth(15);
        $dataSimpananSheet->getColumnDimension('F')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('G')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('H')->setWidth(20);

        // set header
        $dataSimpananSheet->getCell('C2')->setValue('Laporan Simpanan');
        $dataSimpananSheet->getCell('C4')->setValue('Periode : ' . $start . ' - ' . $end);

        $dataSimpananSheet->mergeCells('C7:D7');
        $dataSimpananSheet->mergeCells('E7:F7');
        $dataSimpananSheet->mergeCells('G7:H7');
        $dataSimpananSheet->mergeCells('B7:B8');


        $dataSimpananSheet->getCell('B7')->setValue('Periode');
        $dataSimpananSheet->getCell('C7')->setValue('Simpanan Pokok');
        $dataSimpananSheet->getCell('C8')->setValue('Saldo Masuk');
        $dataSimpananSheet->getCell('D8')->setValue('Saldo Keluar');

        $dataSimpananSheet->getCell('E7')->setValue('Simpanan Wajib');
        $dataSimpananSheet->getCell('E8')->setValue('Saldo Masuk');
        $dataSimpananSheet->getCell('F8')->setValue('Saldo Keluar');

        $dataSimpananSheet->getCell('G7')->setValue('Simpanan Sukarela');
        $dataSimpananSheet->getCell('G8')->setValue('Saldo Masuk');
        $dataSimpananSheet->getCell('H8')->setValue('Saldo Keluar');

        $b = 8;
        $simpananPokok = $simpanan['pokok'];
        foreach ($simpananPokok as $pokok){
            ++$b;
            $dataSimpananSheet->getCell('B'.$b)->setValue($pokok['bulan']);
            $dataSimpananSheet->getCell('C'.$b)->setValue($pokok['debit']);
            $dataSimpananSheet->getCell('D'.$b)->setValue($pokok['credit']);
            $dataSimpananSheet->getStyle('C'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('D'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
        }
        $endCol = $b + 1;
        $dataSimpananSheet->getCell('B'.$endCol)->setValue('TOTAL');
        $dataSimpananSheet->getCell('C'.$endCol)->setValue('=SUM(C9:C'.$b.')');
        $dataSimpananSheet->getCell('D'.$endCol)->setValue('=SUM(D9:D'.$b.')');
        $dataSimpananSheet->getStyle('C'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $dataSimpananSheet->getStyle('D'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $dataSimpananSheet->getStyle('B'.$endCol.':D'.$endCol)->applyFromArray($styleArrayTotal);

        $b = 8;
        $simpananWajib = $simpanan['wajib'];
        foreach ($simpananWajib as $wajib){
            ++$b;
            $dataSimpananSheet->getCell('E'.$b)->setValue($wajib['debit']);
            $dataSimpananSheet->getCell('F'.$b)->setValue($wajib['credit']);
            $dataSimpananSheet->getStyle('E'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('F'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
        }
        $endCol = $b + 1;
        $dataSimpananSheet->getCell('E'.$endCol)->setValue('=SUM(E9:E'.$b.')');
        $dataSimpananSheet->getCell('F'.$endCol)->setValue('=SUM(F9:F'.$b.')');
        $dataSimpananSheet->getStyle('E'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $dataSimpananSheet->getStyle('F'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $dataSimpananSheet->getStyle('E'.$endCol.':F'.$endCol)->applyFromArray($styleArrayTotal);

        $b = 8;
        $simpananSukarela = $simpanan['sukarela'];
        foreach ($simpananSukarela as $sukarela){
            ++$b;
            $dataSimpananSheet->getCell('G'.$b)->setValue($sukarela['debit']);
            $dataSimpananSheet->getCell('H'.$b)->setValue($sukarela['credit']);
            $dataSimpananSheet->getStyle('G'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('H'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
        }
        $endCol = $b + 1;
        $dataSimpananSheet->getCell('G'.$endCol)->setValue('=SUM(G9:G'.$b.')');
        $dataSimpananSheet->getCell('H'.$endCol)->setValue('=SUM(H9:H'.$b.')');
        $dataSimpananSheet->getStyle('G'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $dataSimpananSheet->getStyle('H'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $dataSimpananSheet->getStyle('G'.$endCol.':H'.$endCol)->applyFromArray($styleArrayTotal);
        
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public static function downloadPencairanPinjaman($simpanan, $start, $end)
    {

        $maks = 100;
        $b = 1;

        //style header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayBody = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleTitleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 15,
            ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $dataSimpananSheet = $spreadsheet->getActiveSheet(0)->setTitle('Pinjaman');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('B1');
        $drawing->setWidthAndHeight(180, 110);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        // apply style to header
        $dataSimpananSheet->getStyle('B7:J8')->applyFromArray($styleArray);

        $dataSimpananSheet->getStyle('C2:C5')->applyFromArray($styleTitleArray);

        // set auto width
        $dataSimpananSheet->getColumnDimension('B')->setWidth(30);
        $dataSimpananSheet->getColumnDimension('C')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('D')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('E')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('F')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('G')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('H')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('I')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('J')->setAutoSize(true);
        // $dataSimpananSheet->getColumnDimension('K')->setAutoSize(true);

        // set header
        $dataSimpananSheet->getCell('C2')->setValue('Laporan Rekap Pencairan Pinjaman Koperasi');
        $dataSimpananSheet->getCell('C4')->setValue('Periode : ' . $start . ' - ' . $end);

        $dataSimpananSheet->mergeCells('C2:J2');
        $dataSimpananSheet->mergeCells('C4:J4');
        $dataSimpananSheet->mergeCells('B7:B8');
        $dataSimpananSheet->mergeCells('C7:J7');

        $dataSimpananSheet->getCell('B7')->setValue('Periode');
        $dataSimpananSheet->getCell('C7')->setValue('Jenis Pinjaman');
        $dataSimpananSheet->getCell('C8')->setValue('Tunai');
        $dataSimpananSheet->getCell('D8')->setValue('Barang');
        $dataSimpananSheet->getCell('E8')->setValue('Pendidikan');
        $dataSimpananSheet->getCell('F8')->setValue('Darurat');
        $dataSimpananSheet->getCell('G8')->setValue('Softloan');
        $dataSimpananSheet->getCell('H8')->setValue('Kendaraan');
        $dataSimpananSheet->getCell('I8')->setValue('Bisnis');
        $dataSimpananSheet->getCell('J8')->setValue('Total Pencairan');

        $b = 8;
        foreach ($simpanan as $data){
            ++$b;
            $dataSimpananSheet->getCell('B'.$b)->setValue($data['bulan']);
            $dataSimpananSheet->getCell('C'.$b)->setValue($data['tunai']);
            $dataSimpananSheet->getCell('D'.$b)->setValue($data['barang']);
            $dataSimpananSheet->getCell('E'.$b)->setValue($data['pendidikan']);
            $dataSimpananSheet->getCell('F'.$b)->setValue($data['darurat']);
            $dataSimpananSheet->getCell('G'.$b)->setValue($data['softloan']);
            $dataSimpananSheet->getCell('H'.$b)->setValue($data['kendaraan']);
            $dataSimpananSheet->getCell('I'.$b)->setValue($data['bisnis']);
            $dataSimpananSheet->getCell('J'.$b)->setValue($data['total']);
            $dataSimpananSheet->getStyle('C'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('D'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('E'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('F'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('G'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('H'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('I'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('J'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('B'.$b.':J'.$b)->applyFromArray($styleArrayBody);
        }
        $endCol = $b + 1;
        $dataSimpananSheet->getCell('H'.$endCol)->setValue('TOTAL');
        $dataSimpananSheet->getCell('J'.$endCol)->setValue('=SUM(J9:J'.$b.')');
        $dataSimpananSheet->getStyle('J'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $dataSimpananSheet->getStyle('H'.$endCol.':J'.$endCol)->applyFromArray($styleArrayTotal);
        

        // $dataSimpananSheet->mergeCells('H'.$endCol.':I'.$endCol);
        
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public static function downloadPiutangPinjaman($simpanan, $start, $end)
    {

        $maks = 100;
        $b = 1;

        //style header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayBody = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleTitleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 15,
            ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $dataSimpananSheet = $spreadsheet->getActiveSheet(0)->setTitle('Pinjaman');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('B1');
        $drawing->setWidthAndHeight(180, 110);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        // apply style to header
        $dataSimpananSheet->getStyle('B7:J8')->applyFromArray($styleArray);

        $dataSimpananSheet->getStyle('C2:C5')->applyFromArray($styleTitleArray);

        // set auto width
        $dataSimpananSheet->getColumnDimension('B')->setWidth(30);
        $dataSimpananSheet->getColumnDimension('C')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('D')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('E')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('F')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('G')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('H')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('I')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('J')->setAutoSize(true);
        // $dataSimpananSheet->getColumnDimension('K')->setAutoSize(true);

        // set header
        $dataSimpananSheet->getCell('C2')->setValue('Laporan Rekap Piutang Pinjaman Koperasi');
        $dataSimpananSheet->getCell('C4')->setValue('Periode : ' . $start . ' - ' . $end);

        $dataSimpananSheet->mergeCells('C2:J2');
        $dataSimpananSheet->mergeCells('C4:J4');
        $dataSimpananSheet->mergeCells('B7:B8');
        $dataSimpananSheet->mergeCells('C7:J7');

        $dataSimpananSheet->getCell('B7')->setValue('Periode');
        $dataSimpananSheet->getCell('C7')->setValue('Jenis Pinjaman');
        $dataSimpananSheet->getCell('C8')->setValue('Tunai');
        $dataSimpananSheet->getCell('D8')->setValue('Barang');
        $dataSimpananSheet->getCell('E8')->setValue('Pendidikan');
        $dataSimpananSheet->getCell('F8')->setValue('Darurat');
        $dataSimpananSheet->getCell('G8')->setValue('Softloan');
        $dataSimpananSheet->getCell('H8')->setValue('Kendaraan');
        $dataSimpananSheet->getCell('I8')->setValue('Bisnis');
        $dataSimpananSheet->getCell('J8')->setValue('Total Pencairan');

        $b = 8;
        foreach ($simpanan as $data){
            ++$b;
            $dataSimpananSheet->getCell('B'.$b)->setValue($data['bulan']);
            $dataSimpananSheet->getCell('C'.$b)->setValue($data['tunai']);
            $dataSimpananSheet->getCell('D'.$b)->setValue($data['barang']);
            $dataSimpananSheet->getCell('E'.$b)->setValue($data['pendidikan']);
            $dataSimpananSheet->getCell('F'.$b)->setValue($data['darurat']);
            $dataSimpananSheet->getCell('G'.$b)->setValue($data['softloan']);
            $dataSimpananSheet->getCell('H'.$b)->setValue($data['kendaraan']);
            $dataSimpananSheet->getCell('I'.$b)->setValue($data['bisnis']);
            $dataSimpananSheet->getCell('J'.$b)->setValue($data['total']);
            $dataSimpananSheet->getStyle('C'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('D'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('E'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('F'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('G'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('H'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('I'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('J'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('B'.$b.':J'.$b)->applyFromArray($styleArrayBody);
        }
        $endCol = $b + 1;
        $dataSimpananSheet->getCell('H'.$endCol)->setValue('TOTAL');
        $dataSimpananSheet->getCell('J'.$endCol)->setValue('=SUM(J9:J'.$b.')');
        $dataSimpananSheet->getStyle('J'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $dataSimpananSheet->getStyle('H'.$endCol.':J'.$endCol)->applyFromArray($styleArrayTotal);
        

        // $dataSimpananSheet->mergeCells('H'.$endCol.':I'.$endCol);
        
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public static function downloadMember($members, $start, $end)
    {

        $maks = 100;
        $b = 1;

        //style header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleTitleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 15,
            ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $dataSimpananSheet = $spreadsheet->getActiveSheet(0)->setTitle('Anggota');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('B1');
        $drawing->setWidthAndHeight(100, 100);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        // apply style to header
        $dataSimpananSheet->getStyle('B6:J6')->applyFromArray($styleArray);

        // set auto width
        $dataSimpananSheet->getColumnDimension('B')->setWidth(20);
        $dataSimpananSheet->getColumnDimension('C')->setWidth(20);
        $dataSimpananSheet->getColumnDimension('D')->setWidth(20);
        $dataSimpananSheet->getColumnDimension('E')->setWidth(15);
        $dataSimpananSheet->getColumnDimension('F')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('G')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('H')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('I')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('J')->setAutoSize(true);

        // set header
        $dataSimpananSheet->getCell('C2')->setValue('Laporan Pendaftaran Karyawan Koperasi BSP');
        $dataSimpananSheet->getCell('C3')->setValue('Period : ' . $start . ' s/d ' . $end);

        $dataSimpananSheet->getStyle('C2:C3')->applyFromArray($styleTitleArray);

        $dataSimpananSheet->getCell('B6')->setValue('NIK Koperasi');
        $dataSimpananSheet->getCell('C6')->setValue('NIK Koperasi Lama');
        $dataSimpananSheet->getCell('D6')->setValue('NIK BSP');
        $dataSimpananSheet->getCell('E6')->setValue('Nama Anggota');
        $dataSimpananSheet->getCell('F6')->setValue('Area');
        $dataSimpananSheet->getCell('G6')->setValue('Tanggal Bergabung');
        $dataSimpananSheet->getCell('H6')->setValue('Email');
        $dataSimpananSheet->getCell('I6')->setValue('Alamat');
        $dataSimpananSheet->getCell('J6')->setValue('No Handphone');

        $b = 6;
        foreach ($members as $member){
            ++$b;
            $dataSimpananSheet->getCell('B'.$b)->setValue($member['nik_koperasi']);
            $dataSimpananSheet->getCell('C'.$b)->setValue($member['nik_koperasi_lama']);
            $dataSimpananSheet->getCell('D'.$b)->setValue($member['nik_bsp']);
            $dataSimpananSheet->getCell('E'.$b)->setValue($member['first_name']);
            $dataSimpananSheet->getCell('F'.$b)->setValue(isset($member->region['name_area']) ? $member->region['name_area'] : "");
            $dataSimpananSheet->getCell('G'.$b)->setValue($member['join_date']->format('Y-m-d'));
            $dataSimpananSheet->getCell('H'.$b)->setValue($member['email']);
            $dataSimpananSheet->getCell('I'.$b)->setValue($member['address']);
            $dataSimpananSheet->getCell('J'.$b)->setValue($member['phone_number']);
        }
        $endCol = $b + 1;
        $dataSimpananSheet->getCell('B'.$endCol)->setValue('TOTAL');
        $dataSimpananSheet->getCell('J'.$endCol)->setValue($members->count());
        $dataSimpananSheet->getStyle('J'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $dataSimpananSheet->getStyle('B'.$endCol.':J'.$endCol)->applyFromArray($styleArrayTotal);

        $dataSimpananSheet->mergeCells('B'.$endCol.':I'.$endCol);

        // initialize an empty array to store the dates
        $xAxisData = [];
        $yAxisData = [100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1100];

        // loop through each month between the start and end dates
        $currentDate = $start;
        while ($currentDate <= $end) {
            // add the current date to the array
            $xAxisData[] =  date('F', strtotime($currentDate));

            // move to the first day of the next month
            $currentDate = date('Y-m-d', strtotime('+1 month', strtotime($currentDate)));
        }

        $chart = new \PhpOffice\PhpSpreadsheet\Chart\Chart(
            'chart1', // chart name
            null, // chart title
            null, // legend
            null, // plot area
            true, // plot visible only
            0, // display blancks as
            null, // x-axis label
            null, // y-axis label
            null
        );

        $dataSeries = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART, // chart type
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::GROUPING_CLUSTERED, // grouping
            [0, 1], // plot order
            [], // plot label
            $xAxisData, // x-axis data source
            $yAxisData // y-axis data source
        );

        $plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea(null, [$dataSeries]);

        $chart->setTopLeftPosition('L7'); // set chart position
        $chart->setPlotArea($plotArea); // set plot area

        $dataSimpananSheet->addChart($chart);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public static function downloadLoans($loans, $start, $end)
    {

        $maks = 100;
        $b = 1;

        //style header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayBody = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleTitleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 15,
            ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $dataSimpananSheet = $spreadsheet->getActiveSheet(0)->setTitle('Loan');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('B1');
        $drawing->setWidthAndHeight(100, 100);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        // apply style to header
        $dataSimpananSheet->getStyle('B6:T6')->applyFromArray($styleArray);

        // set auto width
        $dataSimpananSheet->getColumnDimension('B')->setWidth(20);
        $dataSimpananSheet->getColumnDimension('C')->setWidth(20);
        $dataSimpananSheet->getColumnDimension('D')->setWidth(30);
        $dataSimpananSheet->getColumnDimension('E')->setWidth(15);
        $dataSimpananSheet->getColumnDimension('F')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('G')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('H')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('I')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('J')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('K')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('L')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('M')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('N')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('O')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('P')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('Q')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('R')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('S')->setAutoSize(true);
        $dataSimpananSheet->getColumnDimension('T')->setAutoSize(true);


        // set header
        $dataSimpananSheet->getCell('C2')->setValue('Laporan Pinjaman Karyawan Koperasi BSP');
        $dataSimpananSheet->getCell('C3')->setValue('Period : ' . $start . ' s/d ' . $end);

        $dataSimpananSheet->getStyle('C2:C3')->applyFromArray($styleTitleArray);

        $dataSimpananSheet->getCell('B6')->setValue('Loan Number');
        $dataSimpananSheet->getCell('C6')->setValue('Nama Peminjam');
        $dataSimpananSheet->getCell('D6')->setValue('Jenis Pinjaman');
        $dataSimpananSheet->getCell('E6')->setValue('Value');
        $dataSimpananSheet->getCell('F6')->setValue('Biaya Bunga Berjalan');
        $dataSimpananSheet->getCell('G6')->setValue('Biaya Provisi');
        $dataSimpananSheet->getCell('H6')->setValue('Biaya Admin');
        $dataSimpananSheet->getCell('I6')->setValue('Biaya Transfer');
        $dataSimpananSheet->getCell('J6')->setValue('Biaya Jasa');
        $dataSimpananSheet->getCell('K6')->setValue('Start Date');
        $dataSimpananSheet->getCell('L6')->setValue('End Date');
        $dataSimpananSheet->getCell('M6')->setValue('Period');
        $dataSimpananSheet->getCell('N6')->setValue('In Period');
        $dataSimpananSheet->getCell('O6')->setValue('Status');
        $dataSimpananSheet->getCell('P6')->setValue('Keterangan');
        $dataSimpananSheet->getCell('Q6')->setValue('Metode Pencairan');
        $dataSimpananSheet->getCell('R6')->setValue('Jenis Barang');
        $dataSimpananSheet->getCell('S6')->setValue('Merk Barang');
        $dataSimpananSheet->getCell('T6')->setValue('Tipe Barang');

        $b = 6;
        foreach ($loans as $loan){
            ++$b;
            $dataSimpananSheet->getCell('B'.$b)->setValue($loan['loan_number']);
            $dataSimpananSheet->getCell('C'.$b)->setValue($loan['first_name']);
            $dataSimpananSheet->getCell('D'.$b)->setValue($loan['loan_name']);
            $dataSimpananSheet->getCell('E'.$b)->setValue($loan['value']);
            $dataSimpananSheet->getCell('F'.$b)->setValue($loan['biaya_bunga_berjalan']);
            $dataSimpananSheet->getCell('G'.$b)->setValue($loan['biaya_provisi']);
            $dataSimpananSheet->getCell('H'.$b)->setValue($loan['biaya_admin']);
            $dataSimpananSheet->getCell('I'.$b)->setValue($loan['biaya_transfer']);
            $dataSimpananSheet->getCell('J'.$b)->setValue($loan['biaya_jasa']);
            $dataSimpananSheet->getCell('K'.$b)->setValue(date('Y-m-d', strtotime($loan['start_date'])));
            $dataSimpananSheet->getCell('L'.$b)->setValue(date('Y-m-d', strtotime($loan['end_date'])));
            $dataSimpananSheet->getCell('M'.$b)->setValue($loan['period']);
            $dataSimpananSheet->getCell('N'.$b)->setValue($loan['in_period']);
            $dataSimpananSheet->getCell('O'.$b)->setValue($loan['status']);
            $dataSimpananSheet->getCell('P'.$b)->setValue($loan['keterangan']);
            $dataSimpananSheet->getCell('Q'.$b)->setValue($loan['metode_pencairan']);
            $dataSimpananSheet->getCell('R'.$b)->setValue($loan['jenis_barang']);
            $dataSimpananSheet->getCell('S'.$b)->setValue($loan['merk_barang']);
            $dataSimpananSheet->getCell('T'.$b)->setValue($loan['type_barang']);
            $dataSimpananSheet->getStyle('E'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('F'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('G'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('H'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('I'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('J'.$b)->getNumberFormat()->setFormatCode('Rp* #,##0');
            $dataSimpananSheet->getStyle('B'.$b.':T'.$b)->applyFromArray($styleArrayBody);
        }
        $endCol = $b + 1;
        $dataSimpananSheet->getCell('B'.$endCol)->setValue('TOTAL');
        $dataSimpananSheet->getCell('T'.$endCol)->setValue($loans->count());
        $dataSimpananSheet->getStyle('T'.$endCol)->getNumberFormat()->setFormatCode('Rp* #,##0');
        $dataSimpananSheet->getStyle('B'.$endCol.':T'.$endCol)->applyFromArray($styleArrayTotal);

        $dataSimpananSheet->mergeCells('B'.$endCol.':S'.$endCol);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public static function downloadRekapMember($dataMember, $start, $end, $area)
    {

        $maks = 100;
        $b = 1;

        //style header
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFCCFFCC',
                ]
            ],
        ];

        $styleArrayKeluar = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ]
            ],
        ];

        $styleArrayMasuk = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ]
            ],
        ];

        $styleArrayTotal = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ]
            ],
        ];

        $styleTitleArray = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '30453a'),
                'size'  => 15,
            ));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $dataSimpananSheet = $spreadsheet->getActiveSheet(0)->setTitle('Rekap Anggota');

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(public_path() . '/images/bsp.png'); // put your path and image here
        $drawing->setCoordinates('B1');
        $drawing->setWidthAndHeight(130, 130);
        $drawing->setWorksheet($spreadsheet->getActiveSheet(0));

        $dataSimpananSheet->getCell('C2')->setValue('LAPORAN PENDAFTARAN ANGGOTA');
        $dataSimpananSheet->getCell('C3')->setValue('KOPERASI SECURITY "BSP"');
        $dataSimpananSheet->getCell('C5')->setValue('Periode : ' . $start . ' - ' . $end);

        $dataSimpananSheet->mergeCells('B7:B9');
        $dataSimpananSheet->getCell('B7')->setValue('AREA WILAYAH');
        $dataSimpananSheet->getCell('C7')->setValue('BULAN');
        $dataSimpananSheet->getStyle('B7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $dataSimpananSheet->getColumnDimension('B')->setAutoSize(true);

        $dataSimpananSheet->getStyle('C2:C5')->applyFromArray($styleTitleArray);


            $iArea = 10;
            $colArea = 'B';
            if($area == 'ALL'){
                $areaData = Region::get();
                foreach($areaData as $value) {
                    $dataSimpananSheet->setCellValue($colArea.$iArea++, $value['name_area']);
                }
            }else{
                $areaData = Region::where('id', $area)->first();
                $dataSimpananSheet->setCellValue($colArea.$iArea++, $areaData->name_area);
            }
            $startDataArea = $colArea.'10';
            $endDataArea = $colArea.$iArea++;
            $dataSimpananSheet->getStyle($startDataArea.':'.$endDataArea)->applyFromArray($styleArray);

            $iMonth = 8;
            $colMonth = 'C';
            $colMonthMerge = 'D';
            $iInfo = 9;
            $months = DownloadReport::generateMonth($start, $end);

            $totalColMonth = count($months) * 2;

        $ranges = [];
            foreach($months as $value){
                $dataSimpananSheet->setCellValue($colMonth.$iMonth, Carbon::parse($value)->format('F Y'));
                $dataSimpananSheet->getStyle($colMonth.$iMonth)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $dataSimpananSheet->setCellValue($colMonth.$iInfo, 'Masuk');
                $dataSimpananSheet->mergeCells($colMonth.$iMonth.':'.$colMonthMerge.$iMonth);

                $ranges[]=$colMonth;

                $colMonth++;
                $colMonthMerge++;
                $dataSimpananSheet->setCellValue($colMonth.$iInfo, 'Keluar');

                $ranges[]=$colMonth;

                $colMonth++;
                $colMonthMerge++;
//                $dataSimpananSheet->mergeCells($colMonth.$iMonth.':'.$colMonthMerge.$iMonth);
            }
        $startSumTotal = $iInfo+1;
        if($area == 'ALL'){
            $endSumTotal = $iInfo+count($areaData);
        }else{
            $endSumTotal = $iInfo+1;
        }
        
        $dataSimpananSheet->setCellValue('B'.($endSumTotal+1), 'TOTAL');
        foreach ($ranges as $range){
            $endRangeSumTotal = $endSumTotal+1;
            $dataSimpananSheet->setCellValue($range.$endRangeSumTotal, '=SUM('.$range.$startSumTotal.':'.$range.$endSumTotal.')');
            $dataSimpananSheet->getStyle($range.$endRangeSumTotal)->applyFromArray($styleArrayTotal);
        }

        $dataSimpananSheet->mergeCells('C7:'.ReverseData::getNameFromNumber($totalColMonth).'7');
        $dataSimpananSheet->getStyle('C7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
//        $dataSimpananSheet->mergeCells(ReverseData::getNameFromNumber($totalColMonth+1).'5:'.ReverseData::getNameFromNumber($totalColMonth+1).'7');
//        $dataSimpananSheet->setCellValue(ReverseData::getNameFromNumber($totalColMonth+1).'5', 'TOTAL');
        $dataSimpananSheet->getStyle('B7:'.ReverseData::getNameFromNumber($totalColMonth).'9')->applyFromArray($styleArray);
//        dd(ReverseData::getNameFromNumber($totalColMonth));
        $anggotaMasuk = [];
            if($area == 'ALL'){
                foreach ($areaData as $region){
                    foreach ($months as $month){
                        $member = Member::where('region_id', $region['id'])
                            ->whereMonth('join_date','>=', $month->format('m'))
                            ->whereYear('join_date','>=', $month->format('Y'))
                            ->whereMonth('join_date','<=',$month->format('m'))
                            ->whereYear('join_date','<=',$month->format('Y'))
                            ->count();
                        $anggotaMasuk[] =  $member;
                    }
                }
            }else{
                foreach ($months as $month){
                    $member = Member::where('region_id', $area)
                        ->whereMonth('join_date','>=', $month->format('m'))
                        ->whereYear('join_date','>=', $month->format('Y'))
                        ->whereMonth('join_date','<=',$month->format('m'))
                        ->whereYear('join_date','<=',$month->format('Y'))
                        ->count();
                    $anggotaMasuk[] =  $member;
                }
            }
            $countMonth = count($months);
            $iAnggota = 10;
            $colAnggota = 'C';
            $resetCountMonth = 0;
            foreach($anggotaMasuk as $value){

                if($resetCountMonth === $countMonth){
                    $resetCountMonth = 0;
                    $iAnggota++;
                    $colAnggota = 'C';
                }

                $dataSimpananSheet->setCellValue($colAnggota.$iAnggota, $value);
                $dataSimpananSheet->getStyle($colAnggota.$iAnggota)->applyFromArray($styleArrayMasuk);

                $resetCountMonth++;
                $colAnggota++;
                $colAnggota++;
            }

        $anggotaKeluar = [];
        if($area == 'ALL'){
            foreach ($areaData as $region){
                foreach ($months as $month){
                    $member = Member::where('region_id', $region['id'])
                        ->whereIn('status', array('resign', 'tidak aktif'))
                        ->whereMonth('updated_at','>=', $month->format('m'))
                        ->whereYear('updated_at','>=', $month->format('Y'))
                        ->whereMonth('updated_at','<=',$month->format('m'))
                        ->whereYear('updated_at','<=',$month->format('Y'))
                        ->count();
                    $anggotaKeluar[] =  $member;
                }
            }
        }else{
            foreach ($months as $month){
                $member = Member::where('region_id', $area)
                    ->whereIn('status', array('resign', 'tidak aktif'))
                    ->whereMonth('updated_at','>=', $month->format('m'))
                    ->whereYear('updated_at','>=', $month->format('Y'))
                    ->whereMonth('updated_at','<=',$month->format('m'))
                    ->whereYear('updated_at','<=',$month->format('Y'))
                    ->count();
                $anggotaKeluar[] =  $member;
            }
        }

        $countMonthKeluar = count($months);
        $iAnggotaKeluar = 10;
        $colAnggotaKeluar = 'D';
        $resetCountMonthKeluar = 0;
        foreach($anggotaKeluar as $value){
            if($resetCountMonthKeluar === $countMonthKeluar){
                $resetCountMonthKeluar = 0;
                $iAnggotaKeluar++;
                $colAnggotaKeluar = 'D';
            }

            $dataSimpananSheet->setCellValue($colAnggotaKeluar.$iAnggotaKeluar, $value);
            $dataSimpananSheet->getStyle($colAnggotaKeluar.$iAnggotaKeluar)->applyFromArray($styleArrayKeluar);
            $resetCountMonthKeluar++;
            $colAnggotaKeluar++;
            $colAnggotaKeluar++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }
}
