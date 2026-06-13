@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            Import Bareshaft Pump/Motor Price
        </div>
        @if (Session::has('message'))
            <div class="alert alert-success">{{ Session::get('message') }}</div>
        @endif
        <div class="card-body">
            <form action="{{ route("admin.booster.bareshaft_pump_motor_price.upload") }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group {{ $errors->has('file_type') ? 'has-error' : '' }}">
                    <label for="name">Select File Type*</label>
                    <select name="file_type" id="range" class="form-control" required>
                        <option value="">Select </option>
                        <option value="pump">Bareshaft Pump Price</option>
                        <option value="motor">Motor Price</option>
                    </select>


                </div>

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

