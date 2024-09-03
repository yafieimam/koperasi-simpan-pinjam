<!DOCTYPE html>
<html>
<head>
	<title>Form Resign</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.0/css/bootstrap.min.css">
	<link rel="icon" href="{{ asset('pictures/favicon.png') }}" type="image/x-icon"/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<style type="text/css">
		* { box-sizing: border-box; }
		body{
			margin-left: 20px;
			margin-right: 20px;
			margin-bottom: 10px;
			margin-top: 10px;
		}
		.table_detail td{
			border:1px solid #000;
			text-align:center;
		}
		.table_detail th{
			border:1px solid #000;
            padding:4px;
            background-color:#CCFFCC;
            text-align:center;
		}
		.table_detail{
			border-collapse:collapse;
		}

		.page_break { page-break-before: always; }
	</style>
</head>
<body>
    <table style="width:100%; font-weight: 100; font-size:24px;">
        <tr>
            <td style="text-align:center; padding-bottom: 10px;">CLEARANCE SHEET<br>KOPERASI SECURITY "BSP"</td>
        </tr>
        <tr style="line-height: 15px;">
            <td style="border:1px solid #000; background-color:#000000;">&nbsp;</td>
        </tr>
    </table>
    <br />
    <table style="width:100%; font-size:15px;">
        <tr>
            <td width="25%">Nama</td>
            <td width="2%">:</td>
            <td>{{ $data['nama'] }}</td>
        </tr>
        <tr>
            <td width="25%">No. Koperasi</td>
            <td>:</td>
            <td>{{ $data['no_koperasi'] }}</td>
        </tr>
        <tr>
            <td width="25%">No. Register</td>
            <td>:</td>
            <td>{{ $data['no_register'] }}</td>
        </tr>
        <tr>
            <td width="25%">Nama Proyek</td>
            <td>:</td>
            <td>{{ $data['nama_proyek'] }}</td>
        </tr>
        <tr>
            <td width="25%">No. Rekening</td>
            <td>:</td>
            <td>{{ $data['no_rekening'] }}</td>
        </tr>
        <tr>
            <td width="25%">Bank</td>
            <td>:</td>
            <td>{{ $data['bank'] }}</td>
        </tr>
    </table>
    <br />
	<table style="width:100%; font-size:15px;">
        <tr>
            <td colspan="6" style="font-weight:100; font-size: 20px; text-decoration: underline;">Hak</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">Simpanan Pokok</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right;">{{ number_format($data['simpanan_pokok']) }}</td>
            <td width="15%">&nbsp;</td>
            <td width="30%">&nbsp;</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">Simpanan Wajib</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right;">{{ number_format($data['simpanan_wajib']) }}</td>
            <td width="15%">&nbsp;</td>
            <td width="30%">&nbsp;</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">Simpanan Sukarela</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right;">{{ number_format($data['simpanan_sukarela']) }}</td>
            <td width="15%">&nbsp;</td>
            <td width="30%">&nbsp;</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">SHU Ditahan</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right;">{{ number_format($data['simpanan_shu']) }}</td>
            <td width="15%">&nbsp;</td>
            <td width="30%">&nbsp;</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">Administrasi</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right; border-bottom: 1px solid #000;">({{ number_format($data['administrasi']) }})</td>
            <td width="15%">(-)</td>
            <td width="30%">&nbsp;</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%" style="font-weight:100; text-align:right;">Jumlah Hak</td>
            <td width="2%">&nbsp;</td>
            <td width="25%">&nbsp;</td>
            <td width="15%" style="text-align: right;">{{ number_format($data['jumlah_hak']) }}</td>
            <td width="30%">&nbsp;</td>
        </tr>
    </table>
    <br/>
    <?php $data_count = 0; ?>
    <table style="width:100%; font-size:15px;">
        <tr>
            <td colspan="8" style="font-weight:100; font-size: 20px; text-decoration: underline;">Kewajiban</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">Pinjaman Tunai</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right;">{{ number_format($data['pinjaman_tunai']) }}</td>
            <td width="15%">&nbsp;</td>
            <td width="13%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['count_tunai']) }}</td>
            <td width="2%" style="background-color: #BFBFBF;">x</td>
            <td width="15%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['angs_tunai']) }}</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">Pinjaman Barang</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right;">{{ number_format($data['pinjaman_barang']) }}</td>
            <td width="15%">&nbsp;</td>
            <td width="13%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['count_barang']) }}</td>
            <td width="2%" style="background-color: #BFBFBF;">x</td>
            <td width="15%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['angs_barang']) }}</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">Pinjaman Pendidikan</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right;">{{ number_format($data['pinjaman_pendidikan']) }}</td>
            <td width="15%">&nbsp;</td>
            <td width="13%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['count_pendidikan']) }}</td>
            <td width="2%" style="background-color: #BFBFBF;">x</td>
            <td width="15%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['angs_pendidikan']) }}</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">Pinjaman Softloan</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right;">{{ number_format($data['pinjaman_softloan']) }}</td>
            <td width="15%">&nbsp;</td>
            <td width="13%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['count_softloan']) }}</td>
            <td width="2%" style="background-color: #BFBFBF;">x</td>
            <td width="15%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['angs_softloan']) }}</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">Motorloan</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right;">{{ number_format($data['pinjaman_motorloan']) }}</td>
            <td width="15%">&nbsp;</td>
            <td width="13%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['count_motorloan']) }}</td>
            <td width="2%" style="background-color: #BFBFBF;">x</td>
            <td width="15%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['angs_motorloan']) }}</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">Dll</td>
            <td width="2%">:</td>
            <td width="25%" style="text-align: right;">{{ number_format($data['pinjaman_lain']) }}</td>
            <td width="15%">&nbsp;</td>
            <td width="13%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['count_lain']) }}</td>
            <td width="2%" style="background-color: #BFBFBF;">x</td>
            <td width="15%" style="text-align: right; background-color: #BFBFBF;">{{ number_format($data['angs_lain']) }}</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%" style="vertical-align:top;">Jasa Pinjaman</td>
            <td width="2%" style="vertical-align:top;">:</td>
            <td width="25%" style="vertical-align:top; text-align: right;">{{ number_format($data['jumlah_jasa']) }}</td>
            <td width="15%">&nbsp;</td>
            <td colspan="2" width="13%" style="font-size:12px; text-align: right; background-color: #BFBFBF;">
            @if($data['jasa_tunai'] > 0)
            <?php $data_count++; ?>
            Pinj Tunai
            <br>
            @endif
            @if($data['jasa_barang'] > 0)
            <?php $data_count++; ?>
            Pinj Barang
            <br>
            @endif
            @if($data['jasa_pendidikan'] > 0)
            <?php $data_count++; ?>
            Pinj Pendidikan
            <br>
            @endif
            @if($data['jasa_softloan'] > 0)
            <?php $data_count++; ?>
            Pinj Softloan
            <br>
            @endif
            @if($data['jasa_motorloan'] > 0)
            <?php $data_count++; ?>
            Pinj Motorloan
            <br>
            @endif
            @if($data['jasa_lain'] > 0)
            <?php $data_count++; ?>
            Pinj Lain
            <br>
            @endif
            </td>
            <td width="15%" style="font-size:12px; text-align: right; background-color: #BFBFBF;">
            @if($data['jasa_tunai'] > 0)
            {{ number_format($data['jasa_tunai']) }}
            <br>
            @endif
            @if($data['jasa_barang'] > 0)
            {{ number_format($data['jasa_barang']) }}
            <br>
            @endif
            @if($data['jasa_pendidikan'] > 0)
            {{ number_format($data['jasa_pendidikan']) }}
            <br>
            @endif
            @if($data['jasa_softloan'] > 0)
            {{ number_format($data['jasa_softloan']) }}
            <br>
            @endif
            @if($data['jasa_motorloan'] > 0)
            {{ number_format($data['jasa_motorloan']) }}
            <br>
            @endif
            @if($data['jasa_lain'] > 0)
            {{ number_format($data['jasa_lain']) }}
            <br>
            @endif
            </td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%" style="font-weight:100; text-align:right;">Jumlah Kewajiban</td>
            <td width="2%">&nbsp;</td>
            <td width="25%">&nbsp;</td>
            <td width="15%">&nbsp;</td>
            <td width="13%">&nbsp;</td>
            <td width="2%">&nbsp;</td>
            <td width="15%">&nbsp;</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%">&nbsp;</td>
            <td width="2%">&nbsp;</td>
            <td width="25%">&nbsp;</td>
            <td width="15%" style="text-align: right; border-bottom: 1px solid #000;">{{ number_format($data['jumlah_kewajiban']) }}</td>
            <td width="13%">(-)</td>
            <td width="2%">&nbsp;</td>
            <td width="15%">&nbsp;</td>
        </tr>
        <tr>
            <td width="5%">&nbsp;</td>
            <td width="20%" style="font-weight:100; text-align:right;">Sisa Hak</td>
            <td width="2%">:</td>
            <td width="25%">&nbsp;</td>
            <td width="15%" style="text-align: right; border-bottom: 1px double #000;">{{ number_format($data['sisa_hak']) }}</td>
            <td width="13%">&nbsp;</td>
            <td width="2%">&nbsp;</td>
            <td width="15%">&nbsp;</td>
        </tr>
    </table>
    <br />
    @if($data_count > 2)
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    @endif
    <table style="width:100%; font-size:15px;">
        <tr style="text-align: center;">
            <td width="25%">Jakarta, {{ date('d F Y') }}</td>
            <td width="25%">&nbsp;</td>
            <td width="25%">&nbsp;</td>
            <td width="25%">&nbsp;</td>
        </tr>
        <tr style="text-align: center;">
            <td width="25%">Dibuat Oleh,</td>
            <td width="25%">Diperiksa,</td>
            <td width="25%">Disetujui,</td>
            <td width="25%">Disetujui,</td>
        </tr>
        <tr style="text-align: center; line-height: 50px;">
            <td width="25%">&nbsp;</td>
            <td width="25%">&nbsp;</td>
            <td width="25%">&nbsp;</td>
            <td width="25%">&nbsp;</td>
        </tr>
        <tr style="text-align: center; font-weight: 100; text-decoration: underline;">
            <td width="25%">{{ $data['nama'] }}</td>
            <td width="25%">Fitri Yuliana P</td>
            <td width="25%">Dinova Palmerini</td>
            <td width="25%">Julia Widiawati</td>
        </tr>
        <tr style="text-align: center;">
            <td width="25%">Simpan Pinjam</td>
            <td width="25%">Spv. Simpan Pinjam</td>
            <td width="25%">Bendahara</td>
            <td width="25%">Ketua</td>
        </tr>
    </table>
</body>
</html>