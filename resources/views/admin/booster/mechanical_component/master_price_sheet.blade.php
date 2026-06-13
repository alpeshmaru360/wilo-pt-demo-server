@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            Import Master Price Sheet
        </div>
        @if (Session::has('message'))
            <div class="alert alert-success">{{ Session::get('message') }}</div>
        @endif
        <div class="card-body">
            <form action="{{ route("admin.booster.mechanical_master_sheet_price.upload") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group {{ $errors->has('import_file') ? 'has-error' : '' }}">

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
