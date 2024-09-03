@extends('adminlte::page')
@section('title', 'Daftar Anggota')

@section('content_header')
    <h1>Daftar Anggota</h1>
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
                    <table id="listmember" class="table table-bordered table-hover table-condensed">
                        <thead>
                        <tr>
                            <th width="10%">Nik Koperasi</th>
							<th>Nama</th>
                            <th>Projek</th>
                            <th>Awal PKWT</th>
                            <th>PKWT Berakhir</th>
							<th width="10%">Status</th>
                            <th class="text-center">Deposit</th>
                            <th class="text-center">Loan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
@section('appjs')
    <script>
		var start = $('#start').val();
		var end = $('#end').val();
		$( "#start" ).change(function() {
            loadTable('#listmember', 'members', start, end);
		});

        loadTable('#listmember', 'members', start, end);

        function loadTable(idTable = '', urlIndex = '', start = '', end = ''){
            $(idTable +' thead tr').clone(true).appendTo( idTable +' thead' );
            $(idTable +' thead tr:eq(1) th').each( function (i) {
                const thLength = $(idTable +' thead tr:eq(1) th').length;
                const title = $(this).text();
                if(i < thLength - 1 && title.toLowerCase() !== "aksi" && title.toLowerCase() !== "action"){
                    $(this).html( '<input type="text" placeholder="Cari '+title+'" />' );

                    $( 'input', this ).on( 'keyup change', function () {
                        if ( table.column(i).search() !== this.value ) {
                            table
                                .column(i)
                                .search( this.value )
                                .draw();
                        }
                    } );
                }

                if(i == thLength-1)
                {
                    $(this).text("");
                }
            } );
            var table = $(idTable).DataTable({
                ajax: ({
                    type: "get",
                    url: APP_URL + '/' +urlIndex,
                    data: {

                    },

                }),
                // orderCellsTop: true,
                fixedHeader: true,
                // stateSave: true,
                responsive: true,
                processing: true,
                serverSide : false,
                ajax :  urlIndex,
                columns: [
                    {data: 'nik_koperasi' , name : 'nik_koperasi' },
                    {data: 'fullname' , name : 'fullname' },
                    {data: 'project' , name : 'project' },
                    {data: 'start_date' , name : 'start_date' },
                    {data: 'end_date' , name : 'end_date' },
                    {data: 'status' , name : 'status' },
                    {data: 'deposit' , name : 'deposit' },
                    {data: 'loan' , name : 'loan' },
                    {data: 'action' , name : 'action' },

                ],
                scrollX: true,
                autoWidth:false,
                select:true,
                order: [[ 5, 'desc' ]],
                dom:
            "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend:    'copyHtml5',
                    text:      '<i class="fa fa-files-o"></i> Copy',
                    titleAttr: 'Copy'
                },
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i> Excel',
                    titleAttr: 'Excel'
                },
                {
                    extend:    'csvHtml5',
                    text:      '<i class="fa fa-file-text-o"></i> CSV',
                    titleAttr: 'CSV'
                },
                {
                    extend:    'pdfHtml5',
                    text:      '<i class="fa fa-file-pdf-o"></i> PDF',
                    titleAttr: 'PDF'
                }
            ],
            // columnDefs: [
            // 	{
            // 		targets: -1,
            // 		visible: false
            // 	}
            // ],
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
            });
        }
    </script>
@stop
