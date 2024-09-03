@extends('adminlte::page')
@section('title', 'Tanya Jawab')

@section('content_header')
    <h1>Tanya Jawab</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    @foreach($DataQna as $key => $data)
                        {!! $data->description !!}
                        @if($key == 1)
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-8">
                                <table id="produkPinjaman" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Produk Pinjaman</th>
                                        <th>Jasa</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@stop

@section('appjs')
    <script>
        // initDatatable('#produkPinjaman', 'list-produk-pinjaman');
        $('#produkPinjaman').DataTable({
			orderCellsTop: false,
			fixedHeader: true,
			stateSave: true,
			responsive: true,
			processing: true,
			serverSide : false,
			autoWidth :false,
            lengthChange: false,
            dom: 'lrt',
			ajax : '{{url("list-produk-pinjaman")}}',
			columns: [
				{data: 'no' , name : 'no' },
				{data: 'produk_pinjaman' , name : 'produk_pinjaman' },
				{data: 'jasa' , name : 'jasa',
                    render: function (data, type, row, meta) {
                        // console.log(row.id);
                        if(row.id == 14 || row.id == 16){
                            return 'Mengikuti S&K yang berlaku';
                        }else{
                            return data;
                        }
                    },
                },
			]
		});
    </script>
@stop
