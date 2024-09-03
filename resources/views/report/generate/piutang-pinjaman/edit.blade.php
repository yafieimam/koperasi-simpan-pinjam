@extends('adminlte::page')
@section('title', 'Ubah Generate Report Piutang Pinjaman')

@section('content_header')
    <h1>Ubah Generate Report Piutang Pinjaman</h1>
@stop

@section('content')

    <div class="row">
        <div class="col-md-8">
            <div class="box">
                <div class="box-body">
                    {!! Form::model($genLoan, ['route' => ['generate.piutang-pinjaman.update', $genLoan->id], 'method' => 'post']) !!}
                    {{ method_field('PUT') }}
                    @include('report.generate.piutang-pinjaman.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('appjs')
    <script>
        $(".datepicker").kendoDatePicker({
            format: "yyyy-MM-dd",
        });

        var dateStart = $("#start").data("kendoDatePicker");
        var dateEnd = $("#end").data("kendoDatePicker");

        $("#start").click(function() {
            dateStart.open();
        });

        $("#end").click(function() {
            dateEnd.open();
        });
    </script>
@stop
