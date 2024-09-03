<!DOCTYPE html>
<html>
<head>
	<title>Form Pengambilan Simpanan</title>
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
			margin-left: 30px;
			margin-right: 30px;
			margin-bottom: 25px;
			margin-top: 25px;
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
    <table style="width:100%; font-weight: 100; font-size:20px;">
        <tr>
            <td rowspan="3" width="25%"><img src="{{ url('/images/bsp.png') }}" style="width: 100px; height: 100px"></td>
            <td></td>
            <td width="15%"></td>
        </tr>
        <tr>
            <td style="border:1px solid #000; background-color:#CCFFCC; padding-left:10px;">RINCIAN SIMPANAN SUKARELA<br>KOPERASI SECURITY "BSP"</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
    </table>
    <table style="width:100%; font-size:13px;">
        <tr>
            <td width="25%">Nama Anggota</td>
            <td width="2%">:</td>
            <td>{{ $data['member']['fullname'] }}</td>
        </tr>
        <tr>
            <td width="25%">Nama Proyek</td>
            <td>:</td>
            <td>{{ $data['member']['project']['project_name'] }}</td>
        </tr>
        <tr>
            <td width="25%">No Anggota Koperasi</td>
            <td>:</td>
            <td>{{ $data['member']['nik_koperasi'] }}</td>
        </tr>
        <tr>
            <td width="25%">Mulai Bergabung</td>
            <td>:</td>
            <td>{{ date('d F Y', strtotime($data['member']['join_date'])) }}</td>
        </tr>
    </table>
    <br>
	<table style="width:100%; font-size:13px;" class="table table-bordered table-hover responsive table_detail">
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>MASUK</th>
                <th>KELUAR</th>
                <th>SALDO</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $saldo = 0;
            $totalDebit = 0;
            $totalCredit = 0;
            ?>
            @foreach($data['sukarela'] as $key => $value)
            @if($value['debit'] > 0)
            <?php
            $saldo = $saldo + $value['debit'];
            $totalDebit = $totalDebit + $value['debit'];
            ?>
            <tr>
                <td>{{ $value['tanggal'] }}</td>
                <td>{{ number_format($value['debit']) }}</td>
                <td>0</td>
                <td>{{ number_format($saldo) }}</td>
            </tr>
            
            @endif
            @if($value['credit'] > 0)
            <?php
            $saldo = $saldo - $value['credit'];
            $totalCredit = $totalCredit + $value['credit'];
            ?>
            <tr>
                <td>Pengeluaran {{ $value['tanggal'] }}</td>
                <td>0</td>
                <td>{{ number_format($value['credit']) }}</td>
                <td>{{ number_format($saldo) }}</td>
            </tr>
            @endif
            @endforeach
            <tr>
                <th>Total</th>
                <th>{{ number_format($totalDebit) }}</th>
                <th>{{ number_format($totalCredit) }}</th>
                <th>{{ number_format($totalDebit + $totalCredit) }}</th>
            </tr>
        </tbody>
    </table>
</body>
</html>