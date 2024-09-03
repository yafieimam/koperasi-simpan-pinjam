<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-4" for="name">Pilih Area <span class="required">*</span>
    </label>
    <div class="col-md-9 col-sm-9 col-xs-8">
        <select id="area" class="cari_area form-control col-md-7 col-xs-12 select2" name="area" required="required"></select>
    </div>
</div>
<br/>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-4" for="name">Pilih Proyek <span class="required">*</span>
    </label>
    <div class="col-md-9 col-sm-9 col-xs-8">
        <select id="proyek" class="cari_proyek form-control col-md-7 col-xs-12 select2" name="proyek" required="required" disabled></select>
    </div>
</div>
<br/>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-4" for="name">Pilih Anggota <span class="required">*</span>
    </label>
    <div class="col-md-9 col-sm-9 col-xs-8">
        <select id="member_id" class="cari_member form-control col-md-7 col-xs-12 select2" name="member_id" required="required" disabled></select>
    </div>
</div>
<br/>
<!-- <div class="clear-fix"></div> -->
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
<br/>
<div class="clear-fix1"></div>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-4" for="name">Tanggal Awal <span class="required">*</span>
    </label>
    <div class="col-md-9 col-sm-9 col-xs-8">
        {!! Form::date('start',old('start'), ['id' => 'start', 'class' => 'form-control datepicker col-md-7 col-xs-12','placeholder'=>'Tanggal Awal','required'=>true]) !!}
    </div>
</div>
<br/>
<div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-4" for="name">Tanggal Akhir <span class="required">*</span>
    </label>
    <div class="col-md-9 col-sm-9 col-xs-8">
        {!! Form::date('end',old('end'), ['id' => 'end', 'class' => 'form-control datepicker col-md-7 col-xs-12','placeholder'=>'Tanggal Akhir','required'=>false]) !!}
    </div>
</div>
<br/>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        <br>
        @if(request()->segment(3) ==  'edit')
            <button class="btn btn-primary" type="reset">Reset</button>
            {!! Form::submit('Update', ['class' => 'btn btn-success']) !!}
        @else
            <button class="btn btn-primary" type="reset">Reset</button>
            <div class="btn btn-primary" id="search_data">Search</div>
            {!! Form::submit('Download', ['class' => 'btn btn-success']) !!}
            {!! Form::button('Download PDF', ['class' => 'btn btn-success']) !!}
        @endif
    </div>
</div>
