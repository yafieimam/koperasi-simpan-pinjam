@extends('adminlte::page')
@section('title', 'Tambah Generate Rekap Anggota')

@section('content_header')
    <h1>Tambah Generate Rekap Anggota</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    {!! Form::open(['url' => 'generate/rekap-anggota', 'method' => 'post']) !!}
                    @include('report.generate.rekap-anggota.form')
                    {!! Form::close() !!}

                    <br/>
                    <br/>
                    <br/>
                    <div class="form-group">
                        <!-- <h4>Preview Data</h4> -->
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col" style="text-align: center;">Area Wilayah</th>
                                <th scope="col" style="text-align: center;">Bulan</th>
                                <th scope="col" style="text-align: center;">Total Anggota Masuk</th>
                                <th scope="col" style="text-align: center;">Total Anggota Keluar</th>
                            </tr>
                            </thead>
                            <tbody id="edpinfo">
                            </tbody>
                        </table>
                    </div>
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
    <script type="text/javascript">
        $('.cari_area').select2({
            placeholder: 'Cari...',
            // allowClear: true,
            ajax: {
                url: '/generate/deposit-report/get-area',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results:  $.map(data, function (item) {
                            return {
                                text: item.name_area,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });

        // function getProyek(area = ''){
        //     $('.cari_proyek').select2({
        //         placeholder: 'Cari...',
        //         // allowClear: true,
        //         ajax: {
        //             url: '/generate/deposit-report/get-proyek',
        //             dataType: 'json',
        //             delay: 250,
        //             data: function (params) {
        //                 return {
        //                     // _token: CSRF_TOKEN,
        //                     search: params.term,
        //                     'area': area
        //                 };
        //             },
        //             processResults: function (data) {
        //                 return {
        //                     results:  $.map(data, function (item) {
        //                         return {
        //                             text: item.project_name,
        //                             id: item.id
        //                         }
        //                     })
        //                 };
        //             },
        //             cache: true
        //         }
        //     });
        // }
        
        // function getMember(area = '', proyek = ''){
        //     $('.cari_member').select2({
        //         placeholder: 'Cari...',
        //         // allowClear: true,
        //         ajax: {
        //             url: '/generate/deposit-report/get-member',
        //             dataType: 'json',
        //             delay: 250,
        //             data: function (params) {
        //                 return {
        //                     // _token: CSRF_TOKEN,
        //                     search: params.term,
        //                     'area': area,
        //                     'proyek': proyek
        //                 };
        //             },
        //             processResults: function (data) {
        //                 return {
        //                     results:  $.map(data, function (item) {
        //                         return {
        //                             text: item.first_name + ' ' + item.last_name,
        //                             id: item.id
        //                         }
        //                     })
        //                 };
        //             },
        //             cache: true
        //         }
        //     });
        // }

        // $('#member_id').on('change', function() {
        //     if(this.value == "" || this.value == "ALL"){
        //         $('#div_detail_anggota').hide();
        //     }else{
        //         $.ajax({
        //             url : '../member/get-member/' + this.value,
        //             type: 'get',
        //             data: {},
        //             dataType : 'json',
        //             success:function(data){
        //                 $('#noRegister').html(data.nik_bsp);
        //                 $('#nikKoperasi').html(data.nik_koperasi);
        //                 if(data.project){
        //                     $('#namaProyek').html(data.project.project_name);
        //                 }else{
        //                     $('#namaProyek').html('');
        //                 }
        //                 $('#div_detail_anggota').show();
        //             },
        //             failed: function(err){
        //                 console.error(err);
        //             }
        //         });
        //     }
        // });

        // $('#area').on('change', function() {
        //     if(this.value == ""){
        //         $('#proyek').prop('disabled', true);
        //     }else if(this.value == "ALL"){
        //         $('#proyek').prop('disabled', false);
        //         getProyek();
        //     }else{
        //         $('#proyek').prop('disabled', false);
        //         getProyek(this.value);
        //     }
        // });

        // $('#proyek').on('change', function() {
        //     var area = $('#area').val();
        //     if(this.value == ""){
        //         $('#member_id').prop('disabled', true);
        //     }else if(this.value == "ALL"){
        //         $('#member_id').prop('disabled', false);
        //         getMember(area);
        //     }else{
        //         $('#member_id').prop('disabled', false);
        //         getMember(area, this.value);
        //     }
        // });

        $(document).ready(function () {
            $('#search_data').on('click', function () {
                $value = $(this).val();
                var area = $('#area').val();
                var start = $('#start').val();
                var end = $('#end').val();

                if(area == '' || start == '' || end == '')
                {
                    PNotify.error({
                        title: 'Error',
                        text: 'Pastikan semua form terisi dengan benar.',
                    });
                    return;
                }

                $.ajax({
                    type: 'post',
                    url: '/generate/rekap-anggota/get-data',
                    data: {'_token' : "{{csrf_token()}}",'search': $value, 'area' : area, 'start': start, 'end':end},
                    success: function (data) {
                        $('#edpinfo').html(data);

                    }
                })

            });
        });

    </script>
@stop

