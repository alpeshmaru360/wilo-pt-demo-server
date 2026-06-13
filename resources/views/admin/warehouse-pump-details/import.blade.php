@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Upload File Selection 
    </div>

    {{-- Success Message --}}
    @if (Session::has('message'))
        <div class="alert alert-success mx-3 mt-3 mb-0">
            {{ Session::get('message') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if (Session::has('error'))
        <div class="alert alert-danger mx-3 mt-3 mb-0">
            {{ Session::get('error') }}
        </div>
    @endif

    <div class="card-body">
        <form action="{{ route('admin.warehouse_pump.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group {{ $errors->has('file_import') ? 'has-error' : '' }}">
                <label for="file_import">File Upload</label>

                <input type="file" id="file_import" name="file_import" class="form-control h-100">

                {{-- Validation Error --}}
                @if($errors->has('file_import'))
                    <em class="invalid-feedback d-block">
                        {{ $errors->first('file_import') }}
                    </em>
                @endif

                <p class="helper-block">
                    Upload only xlsx, xls, csv file
                </p>
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>

</div>

@endsection