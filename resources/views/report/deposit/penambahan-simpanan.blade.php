@extends('adminlte::page')
@section('title', 'List Penambahan Simpanan Anggota')

@section('content_header')
	<h1>List Penambahan Simpanan Anggota</h1>
@stop

@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<div class="col-xs-3">
						<div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
							<input class="date form-control" type="text" id="start">
						</div>
					</div>
					<div class="col-xs-3">
						<div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
							<input class="date form-control" type="text" id="end">
						</div>
					</div>
					<!-- <div class="col-xs-3">
						<button class="btn btn-primary">Search</button>
					</div> -->
				</div>
			<!-- <div class="box-header">
                    <h3 class="box-title"></h3>
                    <div class="box-tools">
                        {{--<div class="input-group input-group-sm" style="width: 150px;">--}}
			{{--<a href="{{url('members/create')}}" class="btn btn-default"><i class="fa fa-plus"></i></a>--}}
			{{--</div>--}}
				</div>
            </div> -->
				<!-- /.box-header -->
				<div class="box-body">
					<table id="list-add-deposit" class="table table-bordered table-hover table-condensed">
						<thead>
						<tr>
							<th width="17%">Nik BSP</th>
							<th>Nama</th>
							<th>Proyek</th>
                            <th>Simpanan</th>
							<th>Jumlah</th>
							<th>Status Terakhir</th>
							<th class="text-center" colspan="2">Aksi</th>
						</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Approval</h4>
				</div>
				<div class="modal-body">
					<table class="table">
						<tr>
							<td>Tanggal <br><small>Pengajuan penambahan simpanan</small></td>
							<td id="date"></td>
						</tr>
						<tr>
							<td>Status <br><small>Status Pengajuan</small></td>
							<td id="status"></td>
						</tr>
                        <tr>
							<td>Jenis <br><small>Simpanan yang diajukan</small></td>
							<td id="jenis"></td>
						</tr>
						<tr>
							<td>Jumlah <br><small>Simpanan yang diajukan</small></td>
							<td id="jumlah"></td>
						</tr>
						<tr>
							<td>Keterangan <br><small>Simpanan yang diajukan</small></td>
							<td id="note"></td>
						</tr>
					</table>

				</div>
				<div class="modal-footer">
					<span id="button"></span>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
@stop

@section('appjs')
	<script>
		$('#list-add-deposit').DataTable({
			fixedHeader: true,
			stateSave: true,
			responsive: true,
			processing: true,
			serverSide : false,
			scrollX: true,
			autoWidth :false,
			ordering: false,
			ajax :  '{{url('list-penambahan-simpanan/all')}}',
			columns: [
				{data: 'nik' , name : 'nik' },
				{data: 'name' , name : 'name' },
				{data: 'proyek' , name : 'proyek' },
                {data: 'deposit_type' , name : 'deposit_type' },
				{data: 'jumlah' , name : 'jumlah' },
				{data: 'status' , name : 'status' },
				{data: 'action' , name : 'action' },
			]
		});

		function showRecord(e) {
			$('#myModal').modal('show');
			$.ajax({
				type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
				url         : '{{url("list-penambahan-simpanan/get-status")}}', // the url where we want to POST
				data        : {
					'_token': '{{ csrf_token() }}',
					'id': e
				},
				// using the done promise callback
				success:function(data) {
					// console.log(data);
					if (data.error == 0) {
						if(data.json.status == 'pending'){
							status = 'Menunggu';
						} else if(data.json.status == 'rejected'){
							status = 'Ditolak';
						} else if(data.json.status == 'approved'){
							status = 'Disetujui';
						}
						$('#status').html(status);
						$('#date').html(data.json.post_date);
						$('#jumlah').html(data.json.total_deposit);
						$('#note').html(data.json.desc);
						$('#jenis').html(data.json.jenis.deposit_name);
						if(data.json.position.level_id == 8){
							$('#button').show();
						}else{
							$('#button').hide();
						}

						$('#button').html('' +
							'<button class="btn btn-primary" onclick="updateRecord(\'' + data.json.id + '\', \'approved \')">Approve</button>' +
							'<button class="btn btn-danger" onclick="updateRecord(\'' + data.json.id + '\', \'rejected \')">Reject</button>');

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
		function updateRecord(id,status) {
			var notice = PNotify.notice({
				title: 'Confirmation Needed',
				text: 'Apakah kamu yakin untuk ' + (status == 'approved ' ? 'approve' : 'reject') + ' penambahan simpanan yang anda pilih ?',
				// icon: 'fas fa-question-circle',
				hide: false,
				stack: {
					'dir1': 'down',
					'modal': true,
					'firstpos1': 25
				},
				modules: {
					Confirm: {
						confirm: true
					},
					Buttons: {
						closer: false,
						sticker: false
					},
					History: {
						history: false
					},
				}
			});
			notice.on('pnotify.confirm', function() {
				// process the form
				$.ajax({
					type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
					url         : '{{url('list-penambahan-simpanan/approval')}}', // the url where we want to POST\
					data        : {
						'_token': '{{ csrf_token() }}',
						'id': id,
						'status': status
					},

					dataType    : 'json', // what type of data do we expect back from the server
					encode          : true,
					// using the done promise callback
					success:function(data) {

						if(data.error == 1){
							PNotify.error({
								title: 'Oh No!',
								text: data.msg
							});

						} else {
							$('#myModal').modal('hide');
							PNotify.success({
								title: 'Success!',
								text: data.msg,
							});
							table = $('#list-add-deposit').DataTable();
							table.ajax.reload(null,false); //reload datatable ajax
						}
					},
					// handling error code
					error: function (data) {
						PNotify.error({
							title: 'Terjadi anomali.',
							text: 'Mohon hubungi pengembang aplikasi untuk mengatasi masalah ini.',
							type: 'error'
						});
					}
				});
				// stop the form from submitting the normal way and refreshing the page
			});
		}

	</script>
	{{--	<script>--}}
	{{--		var start = $('#start').val();--}}
	{{--		var end = $('#end').val();--}}
	{{--		$( "#start" ).change(function() {--}}
	{{--			// alert( "Handler for .change() called." );--}}
	{{--			initDatatable('#list-retrieve-deposit', 'list-retrieve-deposit', '', start, end);--}}

	{{--		});--}}

	{{--		initDatatable('#list-retrieve-deposit', 'list-retrieve-deposit', '', start, end);--}}
	{{--	</script>--}}
@stop
