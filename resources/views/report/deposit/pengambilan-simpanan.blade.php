@extends('adminlte::page')
@section('title', 'List Pengambilan Simpanan Anggota')

@section('content_header')
	<h1>List Pengambilan Simpanan Anggota</h1>
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
					<table id="list-retrieve-deposit" class="table table-bordered table-hover table-condensed">
						<thead>
						<tr>
							<th width="17%">Nik BSP</th>
							<th>Nama</th>
							<th>Proyek</th>
							<th>Jumlah Pencairan</th>
							<th>Jumlah Sukarela</th>
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
							<td>Tanggal <br><small>Pengajuan pengambilan simpanan</small></td>
							<td id="date"></td>
						</tr>
						<tr>
							<td>Status <br><small>Status Pengajuan</small></td>
							<td id="approval"></td>
						</tr>
						<tr>
							<td>Jumlah <br><small>Jumlah yang diajukan</small></td>
							<td id="jumlah"></td>
						</tr>
						<tr>
							<td>Transfer <br><small>Jumlah sukarela yang dimiliki</small></td>
							<td id="bank"></td>
						</tr>
						<tr>
							<td>Memo <br><small>Catatan saat ada perubahan data</small></td>
							<td id="note"></td>
						</tr>
						<tr id="div_note">
							<td>Approval Note <br><small>Catatan approval pencairan dana</small><br/>
								<textarea name="note" id="note" style="width:100%; height: 100px; margin-top:20px"></textarea></td>
						</tr>
					</table>

				</div>
				<div class="modal-footer">
					<span id="button"></span>
					<span id="print"></span>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
@stop

@section('appjs')
	<script>
		var listApprovalPosition = [1, 2, 6, 7, 8, 9];
		$('#list-retrieve-deposit').DataTable({
			fixedHeader: true,
			stateSave: true,
			responsive: true,
			processing: true,
			serverSide : false,
			scrollX: true,
			autoWidth :false,
			ordering: false,
			ajax :  '{{url('list-pengambilan-simpanan/all')}}',
			columns: [
				{data: 'nik' , name : 'nik' },
				{data: 'name' , name : 'name' },
				{data: 'proyek' , name : 'proyek' },
				{data: 'jumlah' , name : 'jumlah' },
				{data: 'jumlah_sukarela' , name : 'jumlah_sukarela' },
				{data: 'status' , name : 'status' },
				{data: 'action' , name : 'action' },
			]
		});

		function showRecord(e) {
			$('#myModal').modal('show');
			$.ajax({
				type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
				url         : '{{url("list-pengambilan-simpanan/get-status")}}', // the url where we want to POST
				data        : {
					'_token': '{{ csrf_token() }}',
					'id': e
				},
				// using the done promise callback
				success:function(data) {
					// console.log(data);
					if (data.error == 0) {
						if(data.json.status == 'waiting'){
							status = 'Menunggu';
						} else if(data.json.status == 'rejected'){
							status = 'Ditolak by ' + data.json.position_approval.name;
						} else if(data.json.status == 'approved'){
							status = 'Disetujui by ' + data.json.position_approval.name;
						}
						$('#approval').html(status);
						$('#date').html(data.json.date);
						$('#jumlah').html(data.json.jumlah);
						$('#note').html(data.json.reason);
						$('#bank').html(data.transfer);
						$("textarea#note").val(data.json.reason);
						if(data.json.status == 'waiting' || data.json.status == 'approved'){
							if(listApprovalPosition.includes(data.json.position.level_id)){
								if(data.json.position.level_id == 9 && data.json.approval_level == 0){
									$('#button').show();
									$('#print').hide();
									$('#div_note').show();
								}else if(data.json.position.level_id == 8 && data.json.approval_level == 1){
									$('#button').show();
									$('#print').hide();
									$('#div_note').show();
								}else if(data.json.position.level_id == 7 && data.json.approval_level == 2){
									$('#button').show();
									$('#print').hide();
									$('#div_note').show();
								}else if(data.json.position.level_id == 6 && data.json.approval_level == 3){
									$('#button').show();
									$('#print').hide();
									$('#div_note').show();
								}else if(data.json.approval_level == 4){
									$('#button').hide();
									$('#print').show();
									$('#div_note').hide();
								}else if(data.json.position.level_id == 1 || data.json.position.level_id == 2){
									$('#button').show();
									$('#print').hide();
									$('#div_note').show();
								}else{
									$('#button').hide();
									$('#print').hide();
									$('#div_note').hide();
								}
							}else{
								$('#button').hide();
								$('#print').hide();
								$('#div_note').hide();
							}
						}else{
							$('#button').hide();
							$('#print').hide();
							$('#div_note').hide();
						}

						$('#button').html('' +
							'<button class="btn btn-primary" onclick="updateRecord(\'' + data.json.id + '\', \'approved \')">Approve</button>' +
							'<button class="btn btn-danger" onclick="updateRecord(\'' + data.json.id + '\', \'rejected \')">Reject</button>');

						$('#print').html('' +
							'<button class="btn btn-primary" onclick="print(\'' + data.json.member.id + '\')">Print</button>');


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
		function print(id) {
			window.open("{{ url('generate/form-pencairan') . '/' }}" + id + "/download", '_blank')
		}
		function updateRecord(id,status) {
			var note = $('textarea#note').val();
			var notice = PNotify.notice({
				title: 'Confirmation Needed',
				text: 'Apakah kamu yakin untuk ' + (status == 'approved ' ? 'approve' : 'reject') + ' pencairan dana yang anda pilih ?',
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
					url         : '{{url('list-pengambilan-simpanan/approval')}}', // the url where we want to POST\
					data        : {
						'_token': '{{ csrf_token() }}',
						'id': id,
						'status': status,
						'note': note
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
							$("textarea#note").val('');

						} else {
							$('#myModal').modal('hide');
							PNotify.success({
								title: 'Success!',
								text: data.msg,
							});
							$("textarea#note").val('');
							table = $('#list-retrieve-deposit').DataTable();
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
