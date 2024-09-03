@extends('adminlte::page')
@section('title', 'Tambah Simpanan')

@section('content_header')
    <h1>Tambah Simpanan</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <form  method="POST" action="{{ URL::to('add-simpanan') }}">
                      <!-- message -->
                      <div class="text-center">
                       <!-- flash message -->
                      </div>
                      <!-- echo validation error -->
                      @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                      @endif
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
                      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-4" for="anggota">Pilih Anggota <span class="required">*</span>
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-8">
                            <select name="anggota" id="anggota" required="required" class="form-control col-md-7 col-xs-12">
                                <option value="">Pilih Anggota</option>
                                @foreach ($member as $member)
                                  <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="id" name="id" value="{{ $form['id'] }}" autocomplete='id'>
                        </div>
                      </div>
                      <br>
                      <div class="clear-fix"></div>
                      <div class="form-group" id="div_detail_anggota" style="display: none;">
                        <label class="control-label col-md-3 col-sm-3 col-xs-4" for="jenis_simpanan">&nbsp;
                        </label>
                        <div class="col-md-7 col-sm-7 col-xs-6">
                        <table class="table">
                                <tr>
                                    <td>No Register</td>
                                    <td align="right"><span id="noRegister"></span></td>
                                </tr>
                                <tr>
                                    <td>NIK Koperasi</td>
                                    <td align="right"><span id="nikKoperasi"></span></td>
                                </tr>
                                <tr>
                                    <td>Nama Proyek</td>
                                    <td align="right"><span id="namaProyek"></span></td>
                                </tr>
                            </table>
                        </div>
                      </div>
                      <div class="clear-fix"></div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-4" for="jenis_simpanan">Jenis Simpanan <span class="required">*</span>
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-8">
                            <select name="jenis_simpanan" id="jenis_simpanan" required="required" class="form-control col-md-7 col-xs-12">
                                <option value="">Pilih Jenis Simpanan</option>
                                @foreach ($simpanan as $item)
                                  <option value="{{ $item->id }}">{{ $item->deposit_name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" id="id" name="id" value="{{ $form['id'] }}" autocomplete='id'>
                        </div>
                      </div>
                      <div class="clear-fix"></div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-4" for="nominal">Nominal <span class="required">*</span>
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-8">
                            <input type="text" id="nominal" name="nominal" value="{{ $form['nominal'] }}" placeholder="Nominal" autocomplete='order' required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="clear-fix"></div>
                      <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-4" for="tanggal">Tanggal <span class="required">*</span>
                            </label>
                            <div class="col-md-9 col-sm-9 col-xs-8">
                                {!! Form::date('tanggal',old('tanggal'), ['id' => 'tanggal', 'class' => 'form-control datepicker col-md-7 col-xs-12','placeholder'=>'Tanggal','required'=>true]) !!}
                            </div>
                        </div>
                      <div class="clear-fix"></div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-4" for="keterangan">Keterangan <span class="required">*</span>
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-8">
                            <textarea name="keterangan" id="keterangan"  autocomplete='Keterangan' required="required" class="form-control col-md-7 col-xs-12" placeholder="Keterangan">{{ $form['keterangan'] }}</textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                            <br>
                            <button class="btn btn-primary" type="reset">Reset</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                       </div>
                    </form>
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

        var tanggal = $("#tanggal").data("kendoDatePicker");

        $("#tanggal").click(function() {
            tanggal.open();
        });

        $(document).ready(function(){
            $('#nominal').mask('0.000.000.000.000.000', {reverse:true});
        });
        initSelect("select");

        $('#anggota').on('change', function() {
            if(this.value == ""){
                $('#div_detail_anggota').hide();
            }else{
                $.ajax({
                    url : 'member/get-member/' + this.value,
                    type: 'get',
                    data: {},
                    dataType : 'json',
                    success:function(data){
                        $('#noRegister').html(data.nik_bsp);
                        $('#nikKoperasi').html(data.nik_koperasi);
                        if(data.project){
                            $('#namaProyek').html(data.project.project_name);
                        }else{
                            $('#namaProyek').html('');
                        }
                        $('#div_detail_anggota').show();
                    },
                    failed: function(err){
                        console.error(err);
                    }
                });
            }
        });
    </script>
@stop
