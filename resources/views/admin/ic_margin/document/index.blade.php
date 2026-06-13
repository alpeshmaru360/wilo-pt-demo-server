@extends('layouts.admin')
@section('content')
<style type="text/css">
    .btn-success {
        background: #169e88 !important;
    }
    .text-success {
        color: #169e88 !important;
    }
</style>
    <div class="pt-4"></div>
    @if (Session::has('message'))
        <div class="alert alert-success mx-4">{{ Session::get('message') }}</div>
    @endif
    @if (Session::has('error'))
        <div class="alert alert-danger mx-4">{{ Session::get('error') }}</div>
    @endif
    <div class="row p-0 m-0 px-4 mb-2">
        <div class="col-12 d-flex align-items-center">
            @php
                $dir_path = str_replace('assets/fire-fighting/documents', '', $main_path);
                if ($dir_path == '') {
                    $all_dir_path[url('admin/fire-fighting-documents')] = 'Home';
                } else {
                    $dir_path = explode('/', $dir_path);
                    $all_dir_path = [];
                    $main_origin_path = 'assets/fire-fighting/documents';
                    foreach ($dir_path as $dk => $dv) {
                        if ($dv == '') {
                            $all_dir_path[url('admin/fire-fighting-documents')] = 'Home';
                        } else {
                            $main_origin_path .= '/'.$dv;
                            $all_dir_path[url('admin/fire-fighting-documents?s='.base64_encode($main_origin_path))] = $dv;
                        }
                    }
                }
                // dd($all_dir_path);
            @endphp
            @foreach($all_dir_path as $dir_path => $dir_folder)
                @if(array_key_last($all_dir_path) == $dir_path)
                    <span>{{ $dir_folder }}</span>
                @else
                    <a href="{{ $dir_path }}">{{ $dir_folder }}</a> <span class="text-sm text-gray"><i class="fa fa-angle-right fa-1x px-1"></i></span>
                @endif
            @endforeach
        </div>
    </div>
    <div class="card mx-4">
        <div class="card-header d-flex justify-content-between">
            <span>All Documents</span>
            <div class="ml-auto">
                <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#addFolderModal">Create Folder</button>
                <button class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#addFileModal">Add Files</button>
            </div>
        </div>
        <div class="card-body">
            @foreach($files as $fk => $fv)
                @php
                    $file = public_path($main_path.'/'.$fv);
                    $file_path = $main_path.'/'.$fv;
                @endphp
                <div class="row py-1 d-flex justify-content-between align-items-center">
                    @if(pathinfo($file, PATHINFO_EXTENSION))
                        <a href="{{ url(''.$main_path.'/'.$fv) }}" target="_blank"><span>{{ $fv }}</span></a>
                    @else
                        <a href="{{ url('admin/fire-fighting-documents?s='.base64_encode($file_path)) }}"><span>{{ $fv }}</span></a>
                    @endif
                    <div class="d-flex align-items-center">
                        @if(pathinfo($file, PATHINFO_EXTENSION))
                            <a href="{{ url(''.$main_path.'/'.$fv) }}" target="_blank"><i class="text-success fa fa-eye"></i></a>
                        @else
                            {{-- <button class="btn btn-link p-0 m-0"><i class="text-success fa fa-edit"></i></button> --}}
                        @endif
                        <button class="btn btn-link delete-file" data-title="Are you sure to delete {{ $fv }} {{ pathinfo($file, PATHINFO_EXTENSION) ? 'file' : 'folder' }} ?" data-path="{{ base64_encode($main_path.'/'.$fv) }}"><i class="text-danger fa fa-trash"></i></button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <form action="{{ route('admin.fire-fighting-documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="folder">
        <input type="hidden" name="path" value="{{ $main_path }}">
        <div id="addFolderModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Folder</h5>
                        <button type="button" class="close w-auto" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <label for="file_import">Folder Name</label>
                        <input type="text" id="file_import" name="data" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form action="{{ route('admin.fire-fighting-documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="files">
        <input type="hidden" name="path" value="{{ $main_path }}">
        <div id="addFileModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Files</h5>
                        <button type="button" class="close w-auto" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <label for="file_import">Upload Files</label>
                        <input type="file" id="file_import" name="data[]" class="form-control border-0" multiple required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form method="POST" id="deleteFile" action="{{ route('admin.fire-fighting-documents.store') }}">
        @csrf
        <input type="hidden" name="delete_file" class="delete_file" value="">
    </form>
@endsection

@section('scripts')
<script>
    $(document).on('click', '.delete-file', function () {
        if (confirm($(this).data('title'))) {
            $('#deleteFile').find('.delete_file').val($(this).data('path'));
            $('#deleteFile').submit();
        }
    });
</script>
@endsection