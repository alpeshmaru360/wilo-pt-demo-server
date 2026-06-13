@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        SCPV Import Assembly Cost, Painting Cost and Packing Cost
    </div>
    @if (Session::has('message'))
    <div class="alert alert-success mx-3 mt-3 mb-0">{{ Session::get('message') }}</div>
    @endif
    <div class="card-body">
        <form action="{{ route('admin.scpv.master.costpaint.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                <label for="file_import">Import File</label>
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
</div>

@endsection
