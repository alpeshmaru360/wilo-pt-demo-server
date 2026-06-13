@extends('layouts.admin')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">{{ trans('global.permission.title_singular') }}</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            @include('layouts.breadcrumbs')
        </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
    
<!-- Main content -->
<section class="content">
@include('layouts.message')
<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('global.role.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.roles.update", [$role->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                <label for="title">{{ trans('global.role.fields.title') }}*</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($role) ? $role->title : '') }}">
                @if($errors->has('title'))
                    <em class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.role.fields.title_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('permissions') ? 'has-error' : '' }}">
                <label for="permissions">{{ trans('global.role.fields.permissions') }}*
                    <span class="btn btn-info btn-xs select-all">Select all</span>
                    <span class="btn btn-info btn-xs deselect-all">Deselect all</span></label>
                <select name="permissions[]" id="permissions" class="form-control select2" multiple="multiple">
                    @foreach($permissions as $id => $permissions)
                        <option value="{{ $id }}" {{ (in_array($id, old('permissions', [])) || isset($role) && $role->permissions->contains($id)) ? 'selected' : '' }}>
                            {{ $permissions }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('permissions'))
                    <em class="invalid-feedback">
                        {{ $errors->first('permissions') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('global.role.fields.permissions_helper') }}
                </p>
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>
</section>

@endsection