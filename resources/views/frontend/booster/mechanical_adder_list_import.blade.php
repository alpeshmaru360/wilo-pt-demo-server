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
        <form action="{{ route("admin.mechanical.adder.optional.upload") }}" method="POST" enctype="multipart/form-data">
              @csrf
              <!--              <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                              <label for="name">Adder Name*</label>
                              <select name="adder" id="range" class="form-control" required>
                                  <option value="">Select </option>
                                  <option value="electrical">Electrical</option>
                                  <option value="mechanical">Mechanical</option>
                                  <option value="motor">Motor</option>
                              </select>


            </div>-->

              <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">Adder Type*</label>
                <select name="adderType" id="range" class="form-control" required>
                    <option value="">Select </option>
                    <option value="common">Common</option>
                    <option value="common_strainer">Common Strainer</option>
                    <option value="common_flexible">Common Flexible Connectors</option>
                    <option value="common_pressure_vessel">Pressure Vessel</option>
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
