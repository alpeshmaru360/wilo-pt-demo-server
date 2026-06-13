@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Import Excel Sheet
    </div>
    @if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
    @endif
    <div class="card-body">
        <form action="{{ route("admin.upload") }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">Range*</label>
                <select name="range" id="range" class="form-control">
                    @foreach($ranges as $range)
                    <option value="{{ $range->id }}">{{ $range->value }}</option>
                    @endforeach
                </select>


            </div>
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">Folder Name*</label>
                <select name="folder_name" id="folder-name" class="form-control">
                    <option value=''>Select Folder Name</option>
                    @foreach($folderNames as $folderName)
                    <option value="{{ $folderName->folder_name }}">{{ $folderName->folder_name }}</option>
                    @endforeach
                </select>


            </div>

            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">File Name*</label>
                <select name="file_name" id="file-name" class="form-control">
                    <option value=''>Select</option>


                </select>


            </div>
            <div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                <label for="file_import">Import File</label>
                <input type="file" id="file_import" name="file_import" class="form-control">
                @if($errors->has('file-import'))
                <em class="invalid-feedback">
                    {{ $errors->first('file-import') }}
                </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.product.fields.price_helper') }}
                </p>
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('change', '#folder-name', function () {
//        $("#school-2").change(function () {
            var folderName = $(this).val();


            $.ajax({
                url: "{{ URL::to('admin/ajax-fileName') }}",
                type: 'get',
                data: {folderName: folderName},
                dataType: 'json',
                success: function (response) {

                    var len = response.filenames.length;
                    $('#file-name').empty();
                    $('#file-name').prop('disabled', false);
                    $('#file-name').append("<option value=''>Select</option>");
                    for (var i = 0; i < len; i++) {

                        var filename = response.filenames[i]['file_name_under_folder'];

                        $('#file-name').append("<option value='" + filename + "'>" + filename + "</option>");

                    }
                }
            });
        });
    });
</script>
@endsection