@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Upload File Selection 
    </div>

    @if (Session::has('message'))
        <div class="alert alert-success">{{ Session::get('message') }}</div>
    @endif

    <div class="card-body">
        <form action="{{route('admin.cp.main.upload')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                <label for="file_import">File Upload</label>
                <input type="file" id="file_import" name="file_import" class="form-control h-100">
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

    <!-- <div class="row">
        <a class="btn btn-primary" href="{{URL::to('getCpbasic')}}"/>Click this button file import into database</a>
    </div> -->
</div>

@endsection
