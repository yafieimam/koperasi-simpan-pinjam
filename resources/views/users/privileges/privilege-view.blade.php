@extends('adminlte::page')
@section('title', 'Privilege')

@section('content_header')
    <h1>{{$level->name}} Privileges</h1>
@stop
@section('content')
    <div class="row">
        <div class="col-xs-12">
            {!! Form::open(['url' => url('privilege/'.$level->id.'/update'), 'method' => 'post']) !!}
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"></h3>
                    <div class="box-tools">
                        <div class="input-group input-group-sm" style="width: 150px; margin-bottom: 30px;">
                            {!! Form::submit('Save', ['class' => 'btn btn-success']) !!}
                        </div>
                    </div>
                </div>
            <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover" id="permissionTable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Permission</th>
                            <th style="text-align: center; vertical-align: middle;">View <input type="checkbox" id="chkAllView" /></th>
                            <th style="text-align: center; vertical-align: middle;">Create <input type="checkbox" id="chkAllCreate" /></th>
                            <th style="text-align: center; vertical-align: middle;">Update <input type="checkbox" id="chkAllUpdate" /></th>
                            <th style="text-align: center; vertical-align: middle;">Delete <input type="checkbox" id="chkAllDelete" /></th>
                        </tr>
                        </thead>
                        @foreach($permissions as $row => $permit)
                            <tr>
                                <td>{{$row + 1}}</td>
                                <td>{{$permit->name}}</td>
                                @if($level->hasPermissionTo("view." . $permit->name))
                                <td style="text-align: center;"><span style="visibility: hidden">1</span><input type="checkbox" class="checkbox_view" name='view.{{$permit->name}}' value="view.{{$permit->name}}" checked></td>
                                @else
                                <td style="text-align: center;"><span style="visibility: hidden">0</span><input type="checkbox" class="checkbox_view" name='view.{{$permit->name}}' value="view.{{$permit->name}}"></td>
                                @endif
                                @if($level->hasPermissionTo("create." . $permit->name))
                                <td style="text-align: center;"><span style="visibility: hidden">1</span><input type="checkbox" class="checkbox_create" name='create.{{$permit->name}}' value="create.{{$permit->name}}" checked></td>
                                @else
                                <td style="text-align: center;"><span style="visibility: hidden">0</span><input type="checkbox" class="checkbox_create" name='create.{{$permit->name}}' value="create.{{$permit->name}}"></td>
                                @endif
                                @if($level->hasPermissionTo("update." . $permit->name))
                                <td style="text-align: center;"><span style="visibility: hidden">1</span><input type="checkbox" class="checkbox_update" name='update.{{$permit->name}}' value="update.{{$permit->name}}" checked></td>
                                @else
                                <td style="text-align: center;"><span style="visibility: hidden">0</span><input type="checkbox" class="checkbox_update" name='update.{{$permit->name}}' value="update.{{$permit->name}}"></td>
                                @endif
                                @if($level->hasPermissionTo("delete." . $permit->name))
                                <td style="text-align: center;"><span style="visibility: hidden">1</span><input type="checkbox" class="checkbox_delete" name='delete.{{$permit->name}}' value="delete.{{$permit->name}}" checked></td>
                                @else
                                <td style="text-align: center;"><span style="visibility: hidden">0</span><input type="checkbox" class="checkbox_delete" name='delete.{{$permit->name}}' value="delete.{{$permit->name}}"></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>

                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('appjs')
    <script>

            $("#chkAllView").click(function () {
                $('#permissionTable tbody .checkbox_view').prop('checked', this.checked);
            });

            $("#chkAllCreate").click(function () {
                $('#permissionTable tbody .checkbox_create').prop('checked', this.checked);
            });

            $("#chkAllUpdate").click(function () {
                $('#permissionTable tbody .checkbox_update').prop('checked', this.checked);
            });

            $("#chkAllDelete").click(function () {
                $('#permissionTable tbody .checkbox_delete').prop('checked', this.checked);
            });

        const table = $("#permissionTable").DataTable({
            'columnDefs': [ {
                'targets': [2], /* column index */
                'orderable': false, /* true or false */
            }],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });

        $('form').on('submit', function(e){
            var $form = $(this);

            // Iterate over all checkboxes in the table
            table.$('input[type="checkbox"]').each(function(){
                // If checkbox doesn't exist in DOM
                if(!$.contains(document, this)){
                    // If checkbox is checked
                    if(this.checked){
                        // Create a hidden element
                        $form.append(
                            $('<input>')
                                .attr('type', 'hidden')
                                .attr('name', this.name)
                                .val(this.value)
                        );
                    }
                }
            });
        });
    </script>
@stop
