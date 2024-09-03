@extends('adminlte::page')
@section('title', 'Daftar Simpanan')

@section('content_header')
    <h1>Daftar Simpanan Lainnya</h1>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">


        <div class="box">
            <div class="box-header">
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table id="listTsDepositsMembers" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>Type</th>
                        <th>Nomor Simpanan</th>
						<th>Total</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
 <div class="modal-dialog modal-lg" role="document">
   <div class="modal-content">
    <div class="modal-header">
     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Detail Simpanan Anggota {{-- <span id="bulanKe">1</span> --}} </h4>
      </div>
      <div class="modal-body">
       <table class="table">
           <tr>
               <td>Anggota <br><small>koperasi</small></td>
               <td id="anggota"></td>
           </tr>
          <tr>
            <td>Jenis Simpanan <br><small>yang dipilih anggota</small></td>
            <td id="name_of_deposit"></td>
          </tr>
          <tr>
            <td>Nominal yang perlu dibayarkan <br><small></small></td>
            <td>Rp. <span id="nominalValue"></span></td>
          </tr>
          <tr>
            <td>Tanggal <br><small></small></td>
            <td><span id="tanggal"></span></td>
          </tr>
          <tr>
            <td>Status <br><small>Simpanan bulanan</small></td>
            <td id="status"></td>
          </tr>
        </table>
        <table class="table" id="detail_deposit">
            <thead>
            <tr>
                <td>#</td>
                <td>Tipe Simpanan</td>
                <td>Total</td>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
     </div>
    </div>
   </div>
  </div>
{{-- modal data view end--}}
@stop
@section('appjs')
    <script>
    initDatatable('#listTsDepositsMembers', 'lainnya');
    // show data record
    function showRecord(e) {
        $('#myModal').modal('show');
        $.ajax({
              type        : 'post', // define the type of HTTP verb we want to use (POST for our form)
              url         : '{{url("view-deposit")}}', // the url where we want to POST
              data        : {
                            '_token': '{{ csrf_token() }}',
                            'id': e,
                            },
          // using the done promise callback
        success:function(data) {
        if (data.error == 0) {
             $('#name_of_deposit').html(data.name_dps);
             $('#nominalValue').html(idr(data.json.total_deposit));
             $('#tanggal').html(dateTime('dd-mm-yy', data.json.post_date));
             if(data.json.status == 'pending' || data.json.status == null){
                status = 'Masih Pending';
             } else if(data.json.status == 'paid'){
                status = 'Lunas';
             } else if(data.json.status == 'unpaid'){
                status = 'Belum Lunas';
             }
             $('#status').html(status);
             var i = 1;
             $('table#detail_deposit tbody >tr').remove();
             $.each(data.detail, function(key, value){
                $('table#detail_deposit').append(
                '<tr>'+
                '<td>'+i+'</td>'+
                '<td>'+value.deposits_type+'</td>'+
                '<td>Rp. '+idr(value.total)+'</td>'+
                '</tr>'
                );
                i++;
             });
            } else {
              // handling anomali rule
            PNotify.error({
                title: 'Gagal.',
                text: data.msg,
                type: 'error'
            });
          }        
        },
        // handling error code
        error: function (data) {
          loading('hide', 1000);
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
