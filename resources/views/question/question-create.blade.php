@extends('adminlte::page')
@section('title', 'Tambah QnA Baru')

@section('content_header')
    <a href="{{url('qna-data')}}" class="btn btn-default"><i class="fa fa-arrow-left"></i></a>
    <h1>Tambah QnA Baru</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['url' => 'qna-data', 'method' => 'post']) !!}
                    @include('question.question-form')
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
    </script>
@stop
@section('ckeditor')
<script>
    CKEDITOR.replace( 'description' );
</script>
@stop
