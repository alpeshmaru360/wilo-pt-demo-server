@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Import Main Adder List
    </div>
    @if (Session::has('message'))
    <div class="alert alert-success">{{ Session::get('message') }}</div>
    @endif
    <div class="card-body">
        <form action="{{ route("admin.adder.optional.upload") }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">Adder Name*</label>
                <select name="adder" id="range" class="form-control" required>
                   
                    <option value="electrical">Electrical</option>
<!--                    <option value="mechanical">Mechanical</option>
                    <option value="motor">Motor</option>-->
                </select>


            </div>

            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">Adder Type*</label>
                <select name="adderType" id="range" class="form-control" required>
                    <option value="">Select </option>
                    <option value="common_adder">Common adder</option>
                    <option value="common_adder_based_on_ampere">Common adder based on Ampere </option>
                    <option value="adder_per_pump">Adder per pump</option>
                    <option value="adder_per_pump_based_on_ampere">Adder per pump based on Ampere</option>
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
