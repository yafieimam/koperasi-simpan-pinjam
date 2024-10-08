@extends('adminlte::page')
@section('title', 'List QnA')
@section('content_header')
<h1>QnA</h1>
@stop
@section('content')
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<h3 class="box-title"></h3>
				<div class="box-tools">
					<div class="input-group input-group-sm" style="width: 150px;">
						@can('create.master.qna')
							<a href="{{url('qna-data/create')}}" class="btn btn-default"><i class="fa fa-plus"></i>    Tambah QnA</a>
						@endcan
					</div>
				</div>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				@can('view.master.qna')
				<table id="dtQuestion" class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>Nama</th>
							<th width="15%"></th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
				@endcan
			</div>
		</div>
	</div>
</div>
@endsection

@section('appjs')
<script>
    $('#dtQuestion').DataTable({
		orderCellsTop: true,
		fixedHeader: true,
        stateSave: true,
        responsive: true,
        processing: true,
        serverSide : false,
		scrollX: true,
		autoWidth :false,
        ajax :  '{{url('qna-data/datatable/all')}}',
        columns: [
            {data: 'name' , name : 'name' },
            {data: 'action' , name : 'action' },
        ]
    });
</script>
@stop
