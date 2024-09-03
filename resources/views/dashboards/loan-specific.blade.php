@extends('adminlte::page')
@section('title', 'Ajukan Pinjaman')
@section('content')
<style>
  a.loaner {
    color: #000;
    text-decoration: none;
  }
  a.loaner:hover {
    color: #000 !important;
    text-decoration: none;
  }
  .info-box-number {
    display: block;
    font-size: 16px;
    margin:5px 0px;
}
.content-header {
    position: relative;
    padding: 0px 0px 15px 15px; 
}
#minval {
    border:0;
    background: transparent; 
    color:#b9cd6d; 
    font-weight:bold;
}
button {
  padding: 5px;
  margin: 0px 5px;
  background: #fff
}
button.selected{
  border:1px solid #00c0ef;
  background: #fff;
}
#pickLoan {
  margin: 0px 8px; 
}
hr {
  margin-top: 10px;
}
</style>

<div class="col-md-12 col-sm-12 col-xs-12">
<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">Pinjaman Pribadi :</h3><br>
      <span class="labelTop">Dapatkan cicilan pinjaman hingga batas maksimum pinjaman {{ number_format($findLoan->plafon) }} </span>
  </div>
  <div class="box-body">
  <p>
    <label>Nama Proyek </label>
    <div style="margin-bottom: 10px;">
      <span>{{ $project->project_name }}</span>
    </div>
  </p>
  <p>
    <label>PKS Proyek </label>
    <div style="margin-bottom: 10px;">
      <span class="labelTop">{{ $project->start_date }} - {{ $project->end_date }}</span>
    </div>
  </p>
  @if($findLoan->id != 15 || $findLoan->id != 16)
    <!-- <div class="col-md-6 no-padding">
      <span class="labelTop">Pilih Penjamin Pinjaman Anda</span>
      <div class="text-center" style="margin-top: 10px; margin-bottom: 10px;">
          <select id="penjamin" name="penjamin" class="form-control">
              @foreach ($penjamin as $p)
                  <option value="{{ $p->id }}">{{ $p->name }}</option>
              @endforeach
          </select>
      </div>
    </div> -->
    @endif
    <div class="col-md-6">
      <span class="pull-left">Lama cicilan :</span>
      <br>
      @php
        $arr_month = [];
      @endphp
        @php

            $arr_month    = $tenors;

        @endphp

        <div class="text-center" style="margin-top: 10px; margin-bottom: 10px;">
              <select id="tenor" class="form-control">
                <option value="" selected>Pilih Tenor</option>
                  @foreach ($arr_month as $el)
                  <option value="{{ $el }}">{{ $el }} Bulan</option>
                  @endforeach
              </select>
        </div>
      </div>
      <div class="col-md-6 no-padding">
        <span class="pull-left">Metode Pencairan :</span>
        <br>
        <div class="text-center" style="margin-top: 10px; margin-bottom: 10px;">
          <select id="metode_pencairan" class="form-control">
            <option value="" selected>Pilih Metode Pencairan</option>
            <option value="Cash">Cash</option>
            <option value="Transfer">Transfer</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <span class="pull-left"><span style="color:red;">*</span> Alasan / Keperluan Pinjaman :</span> 
        <br>
        <div class="text-center" style="margin-top: 10px; margin-bottom: 10px;">
        <!-- <input type="text" class="form-control" placeholder="Keterangan" name="keterangan" id="keterangan" value="{{ old('keterangan') }}"> -->
        {!! Form::text('keterangan',old('keterangan'), ['id' => 'keterangan', 'class' => 'form-control','placeholder'=>'Alasan / Keperluan Pinjaman', 'required'=>true]) !!}
        </div>
      </div>
      @if($findLoan->id == 15 || $findLoan->id == 16)
      <div class="col-md-12 no-padding">
        <span class="pull-left">Pembayaran Angsuran :</span>
        <br>
        <div class="text-center" style="margin-top: 10px; margin-bottom: 10px;">
          <select id="pembayaran_angsuran" class="form-control">
            <option value="" selected>Pilih Pembayaran Angsuran</option>
            <option value="Jasa">Jasa</option>
            <option value="Pokok">Pokok + Jasa</option>
          </select>
        </div>
      </div>
      @endif
      @if($findLoan->loan_name == 'Pinjaman barang' || $findLoan->loan_name == 'Motorloan' || $findLoan->loan_name == 'Pinjaman Kendaraan')
      <div class="col-md-6 no-padding">
        <span class="pull-left"><span style="color:red;">*</span> Jenis Barang / Kendaraan :</span>
        <br>
        <div class="text-center" style="margin-top: 10px; margin-bottom: 10px;">
        {!! Form::text('jenis_barang',old('jenis_barang'), ['id' => 'jenis_barang', 'class' => 'form-control','placeholder'=>'Jenis Barang / Kendaraan']) !!}
        </div>
      </div>
      <div class="col-md-6">
        <span class="pull-left"><span style="color:red;">*</span> Merk :</span>
        <br>
        <div class="text-center" style="margin-top: 10px; margin-bottom: 10px;">
        {!! Form::text('merk_barang',old('merk_barang'), ['id' => 'merk_barang', 'class' => 'form-control','placeholder'=>'Merk']) !!}
        </div>
      </div>
      <div class="col-md-6 no-padding">
        <span class="pull-left"><span style="color:red;">*</span> Type :</span>
        <br>
        <div class="text-center" style="margin-top: 10px; margin-bottom: 10px;">
        {!! Form::text('type_barang',old('type_barang'), ['id' => 'type_barang', 'class' => 'form-control','placeholder'=>'Type']) !!}
        </div>
      </div>
      @endif
      @if($findLoan->attachment)
      <div class="col-md-6">
        <span class="labelTop">Lampiran</span>
        <br>
        <div class="" style="margin-top: 10px; margin-bottom: 10px;">
                <input type="file" id="images" name="images" class="form-control"/>
                <small>Format file yang diperbolehkan : .jpeg, .jpg, .png</small>
        </div>
      </div>
      @endif
      <div class="col-md-12 no-padding">
        <p>
          <label>Nominal : Rp. </label>
          <input type="text" id="minval" disabled>
          <input type="hidden" id="lockLoan">
          <div id="pickLoan"></div>
        </p>
      </div>
      <div id="showloan" style="display: none">
        <span>Rincian Pinjaman :</span>
        <p></p> 
        <table class="table">
          <tr>
            <td>Jenis Pinjaman <br><small>pinjaman yang dipilih</small></td>
            <td align="right"><b>{{ $findLoan->loan_name }}</b></td>
          </tr>
            <tr>
                <td><b>Keterangan Jenis Pinjaman</b><br>
                    {!! $findLoan->description !!}</td>
                <td align="right"></td>
            </tr>
          <tr>
            <td>Pokok Pinjaman</td>
            <td align="right">Rp. <span id="pokokLoan"></span></td>
          </tr>
          <tr>
            <td>Jasa x Lama Cicilan <br><small>Jasa {{ $findLoan->rate_of_interest }}%</small></td>
            <td align="right">(Rp. <span id="monthlyLoanDetail"></span>) Rp. <span id="monthlyLoan"></span><br><small>Rp. <span id="monthlyLoanJasa"></span></small></td>
          </tr>
          <tr>
            <td>Jumlah Pinjaman</td>
            <td align="right">Rp. <span id="totalNoRate"></span></td>
          </tr>
          <tr>
              <td><b>Total Pinjaman</td>
              <td align="right"><b>Rp. <span id="totalyLoan"></span></b></td>
          </tr>
            <tr>
                <td><b>Cicilan Bulanan <br><small>Jumlah Pokok + Jasa</small></td>
                <td align="right"><b>Rp. <span id="bulananLoan"></span></b></td>
            </tr>
            @if($findLoan->loan_name == 'Pinjaman barang')
            <tr>
                <td>Biaya Admin</td>
                <td align="right">Rp. <span id="biayaAdmin"></span></td>
            </tr>
            @endif
            <tr id="div_biaya_transfer">
                <td>Biaya Transfer</td>
                <td align="right">Rp. <span id="biayaTransfer"></span></td>
            </tr>
            @if($findLoan->loan_name != 'Pinjaman barang')
            <tr id="div_biaya_provisi">
                <td>Biaya Provisi</td>
                <td align="right">Rp. <span id="biayaProvisi"></span></td>
            </tr>
            @endif
            <tr id="div_biaya_berjalan">
                <td>Jasa Berjalan<br><small>Bunga {{ $findLoan->rate_of_interest }}%</small></td>
                <td align="right">Rp. <span id="biayaBungaBerjalan"></span></td>
            </tr>
            <tr>
                <td><b>Pinjaman yang akan diterima <br>
                <small> <span style="color: red;">*</span> dikurangi biaya admin, provisi dan ( jasa berjalan jika persetujuan diatas tanggal 14 tiap bulanya ).</small></b>
                    <br>
                    <small><span style="color: red;">*</span> Mohon periksa kembali nominal yang anda masukkan sebelum klik tombol 'ajukan'.</small>
                </td>
                <td align="right"><b>Rp. <span id="jumlahDiterima"></span></b></td>
            </tr>
            <tr>
                <td>
                <input type="checkbox" id="agree" /> <label data-toggle="modal" data-target="#exampleModal">Persyaratan pengajuan pinjaman, setuju ?</label>
                </td>
            </tr>
          <tr>
            <td> </td>
            <td align="right"><button type="submit" class="btn btn-primary" id="saveLoan" onclick="saveLoan()"> <i class="fa fa-send"></i> Ajukan</button></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
<!-- /.box-body -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{ $policy->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! $policy->description !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" onclick="setuju()" class="btn btn-primary">Setuju</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('appjs')
<!-- Javascript -->
<script>
$(document).ready(function(){
var start_pkwt         = '{{ $getMember->start_date}}';
var end_pkwt           = '{{ $getMember->end_date}}';
{{--if(start_pkwt == '') {--}}
{{--  PNotify.error({--}}
{{--      title: 'Error.',--}}
{{--      text: 'Data perjanjian PKWT anda belum tersedia / salah tanggal PKWT. Mohon hubungi bagian administrasi untuk info lebih lanjut.',--}}
{{--      type: 'error'--}}
{{--  });--}}
{{--  setTimeout(function(){ window.location.href= '{{route("dashboard")}}'; }, 3000);--}}
{{--}--}}
});


$('#showloan').hide();
$('#saveLoan').attr('disabled', 'disabled');
function setuju() {
  document.getElementById("agree").checked = true;
  $('#exampleModal').modal('hide');
    $('#saveLoan').removeAttr('disabled');
}
$('#saveLoan').attr('disabled', 'disabled');
$('#agree').change(function () {
    if(this.checked) {
        $('#saveLoan').removeAttr('disabled');
    }else{
        $('#saveLoan').attr('disabled', 'disabled');
    }
});
$(function() {
const rate_of_interest = '{{ $findLoan->rate_of_interest }}' / 100;
const rate_of_bunga_berjalan = '{{ $findLoan->biaya_bunga_berjalan }}' / 100;
const provisi = '{{ $findLoan->provisi }}' / 100;
var biayaAdmin = 0;
var biayaTransfer = 0;
const dayBungaBerjalan = '{{ $dayBungaBerjalan }}';
// console.log(dayBungaBerjalan);
$('#metode_pencairan').on('change', function(){
  // console.log($(this).val());
  if($(this).val() == "Transfer") {
    $('#div_biaya_transfer').show();
    biayaTransfer = '{{ $findLoan->biaya_transfer }}';
  }else{
    $('#div_biaya_transfer').hide();
    biayaTransfer = 0;
  }
});

if('{{ $findLoan->loan_name }}' == 'Pinjaman barang'){
  biayaAdmin = '{{ $findLoan->biaya_admin }}';
}

$('#metode_pencairan').on('change', function(){
  var calcMonth   = $('#tenor').val();
    if($('#showloan').is(':hidden')) {
      $('#showloan').show('slow');
    } else {
      $('#showloan').hide('slow');
      $('#showloan').show('slow');
    }
    var lockLoan    = parseInt($('#lockLoan').val());
    var rateLoan    = lockLoan * rate_of_interest;
    var rateTenorLoan = parseInt(rateLoan * calcMonth);
    var provisiLoan = 0;
    var totalLoan   = lockLoan + rateLoan;
    var monthLoan   = parseInt((lockLoan / calcMonth) + rateLoan);
    var pokokLoan   = parseInt(lockLoan / calcMonth);
        monthy      = idr(monthLoan);
        totaly      = idr(totalLoan);
    // IDR Currency
    var idrLoan     = idr(lockLoan);
    var rateBungaBerjalan = lockLoan * rate_of_interest;
    var biayaBungaBerjalan = parseInt((rateBungaBerjalan * dayBungaBerjalan) / 30);
    if($(this).val() == "Transfer") {
      biayaTransfer = '{{ $findLoan->biaya_transfer }}';
    }else{
      biayaTransfer = 0;
    }
    // start calculate base on pick loan nominal
    $('#totalNoRate').html(idrLoan);
    $('#monthlyLoan').html(idr(rateTenorLoan));
    $('#monthlyLoanDetail').html(idr(rateLoan) + ' x ' + calcMonth);
    $('#monthlyLoanJasa').html(idr(rateLoan));
    // $('#totalyLoan').html(totaly);
    $('#totalyLoan').html(idrLoan);
    $('#pokokLoan').html(idr(pokokLoan));
    $('#biayaAdmin').html(idr(biayaAdmin));
    $('#biayaTransfer').html(idr(biayaTransfer));
    if('{{ $findLoan->loan_name }}' == 'Pinjaman barang' || '{{ $findLoan->loan_name }}' == 'Softloan'){
      provisiLoan = 0;
      $('#div_biaya_provisi').hide();
    }else{
      if(calcMonth <= 2){
        provisiLoan = 0;
        $('#div_biaya_provisi').hide();
      }else{
        provisiLoan = lockLoan * provisi;
        $('#div_biaya_provisi').show();
      }
    }
    $('#biayaProvisi').html(idr(provisiLoan));
    $('#jumlahDiterima').html(idr(0));
    $('#bulananLoan').html(idr(monthLoan));
    if('{{ $findLoan->id }}' == 3 || '{{ $findLoan->id }}' == 9 || '{{ $findLoan->id }}' == 13){
      biayaBungaBerjalan = 0;
      $('#biayaBungaBerjalan').html(0);
      $('#div_biaya_berjalan').hide();
    }else{
      $('#div_biaya_berjalan').show();
    }
    if(dayBungaBerjalan == 0 && lockLoan == 0){
        $('#biayaBungaBerjalan').html(0);
    }else{
        $('#biayaBungaBerjalan').html(idr(biayaBungaBerjalan));
    }

    if(lockLoan == 0){
        $('#jumlahDiterima').html(idr(0));
    }else{
        $('#jumlahDiterima').html(idr(lockLoan - biayaAdmin - provisiLoan - biayaBungaBerjalan - biayaTransfer));
    }

        // add rule for button submit
    // if(lockLoan > 0) {
    //   $('#saveLoan').removeAttr('disabled');
    //  } else {
    //   $('#saveLoan').attr('disabled', 'disabled');
    //  }
});

$('#tenor').on('change', function(){
    // $('#tenor').removeClass('selected');
    // $(this).addClass('selected');

    var calcMonth   = $(this).val();
    if(calcMonth < 1) {
      PNotify.error({
          title: 'Error.',
          text: 'Anda tidak bisa mengajukan pinjaman karna pktw anda segera berakhir. Mohon hubungi bagian administrasi untuk info lebih lanjut.',
          type: 'error'
      });
      $('#pickLoan').slider({disabled: true});
      $('#monthlyLoan').html(0);
      $('#monthlyLoanJasa').html(0);
      $('#monthlyLoanDetail').html(0 + ' x ' + 0);
    } else{
    if($('#showloan').is(':hidden')) {
      $('#showloan').show('slow');
    } else {
      $('#showloan').hide('slow');
      $('#showloan').show('slow');
    }
    var lockLoan    = parseInt($('#lockLoan').val());
    var rateLoan    = lockLoan * rate_of_interest;
    var rateTenorLoan = parseInt(rateLoan * calcMonth);
    var provisiLoan = 0;
    var totalLoan   = lockLoan + rateLoan;
    var monthLoan   = parseInt((lockLoan / calcMonth) + rateLoan);
    var pokokLoan   = parseInt(lockLoan / calcMonth);
        monthy      = idr(monthLoan);
        totaly      = idr(totalLoan);
    // IDR Currency
    var idrLoan     = idr(lockLoan);
    var rateBungaBerjalan = lockLoan * rate_of_interest;
    var biayaBungaBerjalan = parseInt((rateBungaBerjalan * dayBungaBerjalan) / 30);
    if($('#metode_pencairan').val() == "Transfer") {
      biayaTransfer = '{{ $findLoan->biaya_transfer }}';
    }else{
      biayaTransfer = 0;
    }
    // start calculate base on pick loan nominal
    $('#totalNoRate').html(idrLoan);
    $('#monthlyLoan').html(idr(rateTenorLoan));
    $('#monthlyLoanDetail').html(idr(rateLoan) + ' x ' + calcMonth);
    $('#monthlyLoanJasa').html(idr(rateLoan));
    // $('#totalyLoan').html(totaly);
    $('#totalyLoan').html(idrLoan);
    $('#pokokLoan').html(idr(pokokLoan));
    $('#biayaAdmin').html(idr(biayaAdmin));
    $('#biayaTransfer').html(idr(biayaTransfer));
    if('{{ $findLoan->loan_name }}' == 'Pinjaman barang' || '{{ $findLoan->loan_name }}' == 'Softloan'){
      provisiLoan = 0;
      $('#div_biaya_provisi').hide();
    }else{
      if(calcMonth <= 2){
        provisiLoan = 0;
        $('#div_biaya_provisi').hide();
      }else{
        provisiLoan = lockLoan * provisi;
        $('#div_biaya_provisi').show();
      }
    }
    $('#biayaProvisi').html(idr(provisiLoan));
    $('#jumlahDiterima').html(idr(0));
    $('#bulananLoan').html(idr(monthLoan));
    if('{{ $findLoan->id }}' == 3 || '{{ $findLoan->id }}' == 9 || '{{ $findLoan->id }}' == 13){
      biayaBungaBerjalan = 0;
      $('#biayaBungaBerjalan').html(0);
      $('#div_biaya_berjalan').hide();
    }else{
      $('#div_biaya_berjalan').show();
    }
    if(dayBungaBerjalan == 0 && lockLoan == 0){
        $('#biayaBungaBerjalan').html(0);
    }else{
        $('#biayaBungaBerjalan').html(idr(biayaBungaBerjalan));
    }

    if(lockLoan == 0){
        $('#jumlahDiterima').html(idr(0));
    }else{
        $('#jumlahDiterima').html(idr(lockLoan - biayaAdmin - provisiLoan - biayaBungaBerjalan - biayaTransfer));
    }

        // add rule for button submit
    // if(lockLoan > 0) {
    //   $('#saveLoan').removeAttr('disabled');
    //  } else {
    //   $('#saveLoan').attr('disabled', 'disabled');
    //  }
   }
});

$( "#pickLoan" ).slider({
   min: 0,
   step: 50000,
   max: '{{ $findLoan->plafon }}',

   slide: function( event, ui ) {
    var e = document.getElementById("tenor");
    var tenor = e.options[e.selectedIndex].value;
      var numLoan   = ui.value;
      var period    = tenor;
      var rateLoan  = numLoan * rate_of_interest;
      var rateTenorLoan = parseInt(rateLoan * period);
      var provisiLoan  = 0;
      var totalLoan = numLoan + rateLoan;
      var monthLoan = parseInt((numLoan / tenor) + rateLoan);
      var pokokLoan = parseInt(numLoan / period);
          monthy    = idr(rateLoan);
          totaly    = idr(totalLoan);
      // IDR Currency
      var idrLoan   = idr(numLoan);
       var rateBungaBerjalan = numLoan * rate_of_interest;
       var biayaBungaBerjalan = parseInt((rateBungaBerjalan * dayBungaBerjalan) / 30);
      // start calculate base on pick loan nominal
      $('#totalNoRate').html(idrLoan);
      $('#monthlyLoan').html(idr(rateTenorLoan));
      $('#monthlyLoanDetail').html(idr(rateLoan) + ' x ' + period);
      $('#monthlyLoanJasa').html(idr(rateLoan));
      $('#totalyLoan').html(totaly);
      $('#pokokLoan').html(idr(pokokLoan));
      $('#lockLoan').val(numLoan);
      $('#minval').val(idrLoan);
      if($('#metode_pencairan').val() == "Transfer") {
        biayaTransfer = '{{ $findLoan->biaya_transfer }}';
      }else{
        biayaTransfer = 0;
      }
      $('#biayaTransfer').html(idr(biayaTransfer));
      $('#biayaAdmin').html(idr(biayaAdmin));
      if('{{ $findLoan->loan_name }}' == 'Pinjaman barang' || '{{ $findLoan->loan_name }}' == 'Softloan'){
        provisiLoan = 0;
        $('#div_biaya_provisi').hide();
      }else{
        if(tenor <= 2){
          provisiLoan = 0;
          $('#div_biaya_provisi').hide();
        }else{
          provisiLoan = numLoan * provisi;
          $('#div_biaya_provisi').show();
        }
      }
     $('#biayaProvisi').html(idr(provisiLoan));
       $('#bulananLoan').html(idr(monthLoan));
        if('{{ $findLoan->id }}' == 3 || '{{ $findLoan->id }}' == 9 || '{{ $findLoan->id }}' == 13){
          biayaBungaBerjalan = 0;
          $('#biayaBungaBerjalan').html(0);
          $('#div_biaya_berjalan').hide();
        }else{
          $('#div_biaya_berjalan').show();
        }
       if(dayBungaBerjalan == 0 && numLoan == 0){
           $('#biayaBungaBerjalan').html(0);
       }else{
           $('#biayaBungaBerjalan').html(idr(biayaBungaBerjalan));
       }

       if(numLoan == 0){
          $('#jumlahDiterima').html(idr(0));
      }else{
          $('#jumlahDiterima').html(idr(numLoan - biayaAdmin - provisiLoan - biayaBungaBerjalan - biayaTransfer));
      }

      // add rule for button submit
      // if(numLoan > 0) {
      //   $('#saveLoan').removeAttr('disabled');
      // } else {
      //   $('#saveLoan').attr('disabled', 'disabled');
      // }
    }
});
  var nominal = $('#pickLoan').slider('value');
  $('#monthlyLoan').html(nominal);
  $('#lockLoan').val(nominal);
  $('#minval').val(nominal);
});

// processing data by ajax 
function saveLoan() {
  const member_id = '{{ \crypt::encrypt(Auth::User()->member->id) }}';
    var e = document.getElementById("tenor");
    var tenor = e.options[e.selectedIndex].value;
    // var ep = document.getElementById("penjamin");
    // var penjamin = (ep == null) ? '' : ep.options[ep.selectedIndex].value;
    var em = document.getElementById("metode_pencairan");
    var metode = em.options[em.selectedIndex].value;
    var keterangan = document.getElementById("keterangan").value;
    if('{{ $findLoan->loan_name }}' == 'Pinjaman barang'  || '{{ $findLoan->loan_name }}' == 'Motorloan' || '{{ $findLoan->loan_name }}' == 'Pinjaman Kendaraan'){
      var jenis_barang = document.getElementById("jenis_barang").value;
      var merk_barang = document.getElementById("merk_barang").value;
      var type_barang = document.getElementById("type_barang").value;
    }else{
      var jenis_barang = null;
      var merk_barang = null;
      var type_barang = null;
    }

    if('{{ $findLoan->id }}' == 15  || '{{ $findLoan->id }}' == 16){
      var epa = document.getElementById("pembayaran_angsuran");
      var pembayaran_angsuran = epa.options[epa.selectedIndex].value;
    }else{
      var pembayaran_angsuran = null;
    }
    
  loading('show');
  $('#saveLoan').prop('disabled', true);
   // process the data
   if('{{ $findLoan->attachment }}' == 1){
    var file_data = $('#images').prop('files')[0];
   }else{
    var file_data = null;
   }
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('member_id', member_id);
    formData.append('loan_id', '{{ \crypt::encrypt($findLoan->id) }}');
    formData.append('value', parseInt($('#lockLoan').val()));
    formData.append('period', tenor);
    // formData.append('penjamin', penjamin);
    formData.append('metode_pencairan', metode);
    formData.append('keterangan', keterangan);
    formData.append('jenis_barang', jenis_barang);
    formData.append('merk_barang', merk_barang);
    formData.append('type_barang', type_barang);
    formData.append('pembayaran_angsuran', pembayaran_angsuran);
    formData.append('images', file_data);

    formData.loan_id = '{{ \crypt::encrypt($findLoan->id) }}';
    formData.value = parseInt($('#lockLoan').val());

        $.ajax({
            type      : 'POST', // define the type of HTTP verb we want to use (POST for our form)
            url       : '{{url("save-loan")}}', // the url where we want to POST
            data      : formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,
        // using the done promise callback
        success:function(data) {
          if (data.error == 0) {
           PNotify.success({
                  title: 'Success!',
                  text: data.msg,
          });
          loading('hide', 1000);
          setTimeout(function(){ window.location.href= '{{url("member-loans")}}'; }, 3000);
          } else {
          // handling anomali rule
          loading('hide');
          $('#saveLoan').prop('disabled', false);
          PNotify.error({
              title: 'Gagal.',
              text: data.msg,
              type: 'error'
          });
          }
        },
        // handling error code
        error: function (data) {
          loading('hide');
          $('#saveLoan').prop('disabled', false);
            PNotify.error({
                title: 'Terjadi anomali.',
                text: 'Mohon hubungi pengembang aplikasi untuk mengatasi masalah ini.',
                type: 'error'
            });
          }
        });
}
</script>
@stop
