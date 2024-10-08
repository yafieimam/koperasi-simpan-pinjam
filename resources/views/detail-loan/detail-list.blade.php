@extends('adminlte::page')
@section('title', 'List Simpanan')

@section('content_header')
    <h1>List Pinjaman</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="listDetailLoan" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Debit</th>
                            <th>Kredit</th>
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
    {{-- modal data view end--}}
@stop
@section('appjs')
    <script>
        initDatatable('#listDetailLoan', 'get-detail-loan', {{$id}});
    </script>
@stop
