@extends('layouts.admin')
@section('content')

    <div class="card mx-4">
        <div class="card-header">
            Import Diesel Tank Master
        </div>
        @if (Session::has('message'))
            <div class="alert alert-success">{{ Session::get('message') }}</div>
        @endif
        <div class="card-body">
            <form action="{{ route('admin.fire-fighting.diesel-tank-master-import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group {{ $errors->has('import_file') ? 'has-error' : '' }}">

                    <label for="file_import">Import File</label>
                    <input type="file" id="file_import" name="file_import" class="form-control h-100" required>
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
