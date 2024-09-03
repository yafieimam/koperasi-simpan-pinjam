@extends('adminlte::page')
@section('title', 'List Resign Anggota')

@section('content_header')
	<h1>List Resign Anggota</h1>
@stop

@section('content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
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
					<table id="list-resign" class="table table-bordered table-hover table-condensed">
						<thead>
						<tr>
							<th width="10%">Nik BSP</th>
							<th>Nama</th>
							<th>Proyek</th>
							<th>Tanggal Pengajuan</th>
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
							<td>Tanggal <br><small>Pengajuan pengunduran diri</small></td>
							<td id="date"></td>
						</tr>
						<tr>
							<td>Alasan <br><small></small></td>
							<td><span id="reason"></span></td>
						</tr>
						<tr>
							<td>Status <br><small>Status Pengajuan</small></td>
							<td id="approval"></td>
						</tr>
						<tr id="div_lampiran">
							<td>Lampiran <br><small>Lampiran Pengajuan</small></td>
							<td id="lampiran"></td>
						</tr>
						<tr>
							<td>Memo <br><small>Catatan saat ada perubahan data</small></td>
							<td id="note"></td>
						</tr>
						<tr id="div_note">
							<td>Approval Note <br><small>Catatan approval resign anggota</small><br/>
								<textarea name="note" id="note_txt" style="width:100%; height: 100px; margin-top:20px"></textarea></td>
						</tr>
					</table>

				</div>
				<div class="modal-footer">
					<span id="button"></span>
					<span id="button_area"></span>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModalUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Approval</h4>
				</div>
				<div class="modal-body">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="col-md-12">
							<span class="labelTop">Lampiran</span>
							<br>
							<div class="" style="margin-top: 10px; margin-bottom: 10px;">
									<input type="file" id="upload_file" name="upload_file" class="form-control"/>
									<small>Format file yang diperbolehkan : .docx, .pdf</small>
							</div>
						</div>
						<div class="col-md-12">
							<span class="labelTop">Notes</span>
							<textarea name="note" id="note_txt" style="width:100%; height: 100px; margin-top:10px"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<span id="button_file"></span>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
@stop

@section('appjs')
	<script>
		var listApprovalPositionAnggota = [13, 12, 9, 8, 7, 6];
		var listApprovalPositionPengelola = [22, 9, 8, 7, 6];

		$('#list-resign').DataTable({
			orderCellsTop: true,
			fixedHeader: true,
			stateSave: true,
			responsive: true,
			processing: true,
			serverSide : false,
			scrollX: true,
			autoWidth :false,
			ajax :  '{{url('list-resign/all')}}',
			columns: [
				{data: 'nik' , name : 'nik' },
				{data: 'name' , name : 'name' },
				{data: 'proyek' , name : 'proyek' },
				{data: 'tanggal' , name : 'tanggal' },
				{data: 'status' , name : 'status' },
				{data: 'action' , name : 'action' },
			]
		});

		function showRecord(e) {
			$('#myModal').modal('show');
			$.ajax({
				type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
				url         : '{{url("list-resign/get-status-resign")}}', // the url where we want to POST
				data        : {
					'_token': '{{ csrf_token() }}',
					'id': e,
				},
				// using the done promise callback
				success:function(data) {
					if (data.error == 0) {
						if(data.json.status == 'canceled'){
							status = 'Menunggu';
						} else if(data.json.status == 'rejected'){
							status = 'Ditolak by ' + data.json.position_approval.name;
						} else if(data.json.status == 'waiting'){
							status = 'Menunggu persetujuan';
						} else if(data.json.status == 'approved'){
							status = 'Disetujui by ' + data.json.position_approval.name;
						}
						$('#approval').html(status);
						$('#date').html(data.json.date);
						$('#reason').html(data.json.reason);
						$('#note').html(data.json.note);
						if(data.json.attachment == null || data.json.attachment == ""){
							$('#div_lampiran').hide();
						}else{
							$('#div_lampiran').show();
						}
						$('#lampiran').html('<a class="btn btn-primary btn-sm" href="../file/resign/' + data.json.attachment + '" target="_blank">Preview</a>')
						$("#note_txt").val(data.json.note);
						if(data.json.status == 'waiting' || data.json.status == 'approved'){
							if(data.json.member.position_id == 14){
								if(listApprovalPositionAnggota.includes(data.json.position.level_id)){
									if(data.json.position.level_id == 13 && data.json.approval_level == 0){
										$('#button').show();
										$('#button_area').hide();
										$('#div_note').show();
									}else if(data.json.position.level_id == 12 && data.json.approval_level == 1){
										$('#button').hide();
										$('#button_area').show();
										$('#div_note').show();
									}else if(data.json.position.level_id == 9 && data.json.approval_level == 2){
										$('#button').show();
										$('#button_area').hide();
										$('#div_note').show();
									}else if(data.json.position.level_id == 8 && data.json.approval_level == 3){
										$('#button').show();
										$('#button_area').hide();
										$('#div_note').show();
									}else if(data.json.position.level_id == 7 && data.json.approval_level == 4){
										$('#button').show();
										$('#button_area').hide();
										$('#div_note').show();
									}else if(data.json.position.level_id == 6 && data.json.approval_level == 5){
										$('#button').show();
										$('#button_area').hide();
										$('#div_note').show();
									}else if(data.json.approval_level == 6){
										$('#button').hide();
										$('#button_area').hide();
										$('#div_note').hide();
									}else{
										$('#button').hide();
										$('#button_area').hide();
										$('#div_note').hide();
									}
								}else{
									$('#button').hide();
									$('#button_area').hide();
									$('#div_note').hide();
								}
							}else if(data.json.member.position_id == 20){
								if(listApprovalPositionPengelola.includes(data.json.position.level_id)){
									if(data.json.position.level_id == 22 && data.json.approval_level == 0){
										$('#button').hide();
										$('#button_area').show();
										$('#div_note').show();
									}else if(data.json.position.level_id == 9 && data.json.approval_level == 1){
										$('#button').show();
										$('#button_area').hide();
										$('#div_note').show();
									}else if(data.json.position.level_id == 8 && data.json.approval_level == 2){
										$('#button').show();
										$('#button_area').hide();
										$('#div_note').show();
									}else if(data.json.position.level_id == 7 && data.json.approval_level == 3){
										$('#button').show();
										$('#button_area').hide();
										$('#div_note').show();
									}else if(data.json.position.level_id == 6 && data.json.approval_level == 4){
										$('#button').show();
										$('#button_area').hide();
										$('#div_note').show();
									}else if(data.json.approval_level == 5){
										$('#button').hide();
										$('#button_area').hide();
										$('#div_note').hide();
									}else{
										$('#button').hide();
										$('#button_area').hide();
										$('#div_note').hide();
									}
								}else{
									$('#button').hide();
									$('#button_area').hide();
									$('#div_note').hide();
								}
							}else{
								$('#button').hide();
								$('#button_area').hide();
								$('#div_note').hide();
							}
						}else{
							$('#button').hide();
							$('#button_area').hide();
							$('#div_note').hide();
						}
						$('#button_area').html('' +
							'<button class="btn btn-primary" onclick="showUpdateRecord(\'' + data.json.id + '\')">Approve</button>' +
							'<button class="btn btn-warning" onclick="updateRecord(\'' + data.json.id + '\', \'canceled \')">Cancel</button>' +
							'<button class="btn btn-danger" onclick="updateRecord(\'' + data.json.id + '\', \'rejected \')">Reject</button>' +
							'');
						$('#button').html('' +
							'<button class="btn btn-primary" onclick="updateRecord(\'' + data.json.id + '\', \'approved \')">Approve</button>' +
							'<button class="btn btn-warning" onclick="updateRecord(\'' + data.json.id + '\', \'canceled \')">Cancel</button>' +
							'<button class="btn btn-danger" onclick="updateRecord(\'' + data.json.id + '\', \'rejected \')">Reject</button>' +
							'');


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

		function showUpdateRecord(e) {
			// console.log(e);
			$('#myModalUpdate').modal('show');
			$('#button_file').html('' +
				'<button class="btn btn-primary" onclick="updateFileRecord(\'' + e + '\')">Submit</button>' +
				'');
		}

		function updateRecord(id,status) {
			var note = $('textarea#note').val();
			var notice = PNotify.notice({
				title: 'Confirmation Needed',
				text: 'Apakah kamu yakin untuk ' + (status == 'approved ' ? 'approve' : status == 'canceled' ? 'canceled' : 'reject') + ' status resign yang anda pilih ?',
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
					url         : '{{url('list-resign/approval')}}', // the url where we want to POST\
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
							$("#note_txt").val('');

						} else {
							$('#myModal').modal('hide');
							PNotify.success({
								title: 'Success!',
								text: data.msg,
							});
							$("#note_txt").val('');
							table = $('#list-resign').DataTable();
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

		function updateFileRecord(id) {
			var note = $('textarea#note_txt').val();
			var formData = new FormData();
    		formData.append('_token', '{{ csrf_token() }}');
			formData.append('id', id);
			formData.append('status', 'approved');
			formData.append('note', note);
			formData.append('upload_file', $('#upload_file').prop('files')[0]);
			$.ajax({
				type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
				url         : '{{url('list-resign/approval')}}', // the url where we want to POST\
				data      : formData,
				processData: false,  // tell jQuery not to process the data
				contentType: false,

				dataType    : 'json', // what type of data do we expect back from the server
				encode          : true,
				// using the done promise callback
				success:function(data) {

					if(data.error == 1){
						PNotify.error({
							title: 'Oh No!',
							text: data.msg
						});
						$("#note_txt").val('');

					} else {
						$('#myModal').modal('hide');
						$('#myModalUpdate').modal('hide');
						PNotify.success({
							title: 'Success!',
							text: data.msg,
						});
						$("#note_txt").val('');
						table = $('#list-resign').DataTable();
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
