@extends('adminlte::page')
@section('title', 'Form Pengajuan perubahan simpanan')

@section('content')
	<!-- grafik member -->
	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">Perubahan Simpanan</h3>

			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
				</button>
			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class="col-md-12">
				<form action="{{ url('change-member-deposits/form') }}" method="POST">
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
					<input type="hidden" name="sukarela" value="{{ $data['sukarela']  }}">

					<!-- echo validation error -->
					@if (session('message'))
						<div class="alert alert-success">
							{{ session('message') }}
						</div>
					@endif
					@if (session('error'))
						<div class="alert alert-danger">
							{{ session('error') }}
						</div>
				@endif
				<!--/ echo validation error -->

{{--					<div class="col-md-5 no-padding">--}}
{{--						<div class="form-group">--}}
{{--							<label class="control-label">Tanggal diajukan</label><p></p>--}}
{{--							<input type="text" class="form-control" name="date" id="datepicker" value="{{\Carbon\Carbon::parse(now())->format('Y-m-d')}}" />--}}
{{--						</div>--}}
{{--					</div>--}}

					<div class="col-md-12 no-padding">
						<div class="form-group">
							<label for="">Info Simpanan</label><p></p>
							<table class="table">
								<thead>
								<th>Sukarela</th>
								<th>Pokok</th>
								<th>Wajib</th>
								</thead>
								<tbody>
									<tr>
										<td>{{ number_format($data['sukarela']) }}</td>
										<td>{{ number_format($data['pokok']) }}</td>
										<td>{{ number_format($data['wajib']) }}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-3 no-padding" style="margin-right: 10px;">
						<label for="">Simpanan Wajib</label><p></p>
						<input type="hidden" class="form-control" value="{{ $data['wajib'] }}" name="last_wajib" required/>
						<input type="text" class="form-control" value="{{ $data['wajib'] }}" name="wajib" id="wajib" required/>

					</div>
					<div class="col-md-3 no-padding">
						<label for="">Simpanan Sukarela</label><p></p>
						<input type="hidden" class="form-control" name="last_sukarela" value="{{ $data['sukarela'] }}" required/>
						<input type="text" class="form-control" name="sukarela" id="sukarela" value="{{ $data['sukarela'] }}" required/>

					</div>
					<div class="col-md-12 no-padding" style="margin-top:30px">
						<div class="form-group">
							<button class="btn btn-default" type="reset">Batal</button>
							<button class="btn btn-primary" type="submit">Ajukan Perubahan</button>
							<button class="btn btn-primary pull-right" id="btnHistory" data-toggle="modal" data-target="#historyModal" type="button">History</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /.box-body -->
	<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">History Perubahan Simpanan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
					<table id="list-perubahan-simpanan" class="table table-bordered table-hover table-condensed" style="width:100%;">
						<thead>
						<tr>
							<th width="17%">Nik BSP</th>
							<th>Nama</th>
							<th>Proyek</th>
							<th>Last Wajib</th>
							<th>New Wajib</th>
							<th>Last Sukarela</th>
							<th>New Sukarela</th>
							<th>Status Terakhir</th>
						</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('appjs')
	<script>
		$(document).ready(function(){
            $('#wajib').mask('0.000.000.000.000.000', {reverse:true});
			$('#sukarela').mask('0.000.000.000.000.000', {reverse:true});
        });
		$('#list-perubahan-simpanan').DataTable({
			orderCellsTop: true,
			fixedHeader: true,
			stateSave: true,
			responsive: true,
			processing: true,
			serverSide : false,
			autoWidth :false,
			ajax :  '{{url('list-perubahan-simpanan/member/' . auth()->user()->id)}}',
			columns: [
				{data: 'nik' , name : 'nik' },
				{data: 'name' , name : 'name' },
				{data: 'proyek' , name : 'proyek' },
				{data: 'last_wajib' , name : 'last_wajib' },
				{data: 'new_wajib' , name : 'new_wajib' },
				{data: 'last_sukarela' , name : 'last_sukarela' },
				{data: 'new_sukarela' , name : 'new_sukarela' },
				{data: 'status' , name : 'status' },
			]
		});
		// create DatePicker from input HTML element
		$("#datepicker").kendoDatePicker({
			// display month and year in the input
			format: "yyyy-MM-dd",
			min: new Date(),

			// specifies that DateInput is used for masking the input element
			dateInput: true
		});
	</script>
@endsection
