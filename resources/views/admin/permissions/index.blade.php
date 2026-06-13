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
            <span class="page-heading">{{ trans('global.list') }}</span>
            
            <div class="pull-right">
                @can('permission_create')
                <a class="btn btn-primary" href="{{ route("admin.permissions.create") }}">
                    <i class="fas fa-plus"></i>
                </a>
                @endcan
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover datatable">
                {{-- <table class=" table table-bordered table-striped table-hover datatable"> --}}
                    <thead>
                        <tr>
                            <th>
                                {{ trans('global.permission.fields.title') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $key => $permission)
                            <tr data-entry-id="{{ $permission->id }}">
                                <td>
                                    {{ $permission->title ?? '' }}
                                </td>
                                <td>
                                    @can('permission_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.permissions.show', $permission->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('permission_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.permissions.edit', $permission->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('permission_delete')
                                        <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                        </form>
                                    @endcan
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@section('scripts')
@parent
<script>
    
$(function() {
    console.log('test');
    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
    $('.datatable').DataTable({ 
        "buttons": dtButtons ,
        "responsive": true,
    })
})
    
</script>
@endsection
@endsection