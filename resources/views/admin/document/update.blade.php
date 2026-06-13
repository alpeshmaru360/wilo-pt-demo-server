@extends('layouts.admin')
@section('content')


<div class="card">
    <div class="card-header">
        Document
    </div>
    @if (Session::has('message'))
    <div class="alert alert-success mx-3 mt-3 mb-0">{{ Session::get('message') }}</div>
    @endif

    @if (Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ Session::get('error') }}</div>
    @endif
    <form action="{{ route('admin.document_upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">

            <div class="form-group ">
                <label for="name">Module*</label>
                <select name="module" id="module" class="form-control" required="">
                    <option value="">Select </option>
                    <option value="booster_set">Booster Set</option>
                    <option value="control_panel">Control Panel</option>
                    <option value="scp_pump_assembly">SCP Pump Assembly</option>

                    {{-- A Code: 06-11-2025 Start --}}
                    <option value="scpv_pump_assembly">SCPV Pump Assembly</option>
                    {{-- A Code: 06-11-2025 End --}}

                    <option value="atmos_giga">Atmos GIGA</option>
                    <option value="firefighting">Fire Fighting</option>
                </select>
            </div>

            <div class="form-group ">
                <label for="name">Article*</label>
                <select name="article" id="article"  class="form-control select2 h-100" required=""></select>
            </div>
            
            <div class="form-group ">
                <label for="name">Article*</label>
                <table width="100%" border="1">
                    <thead>
                        <tr>
                        <th>Module Name</th>
                        <th>Article Number</th>
                        <th>File Upload</th>
                        </tr>
                    </thead>
                    <tbody id="article_detail_table">
                    </tbody>                
                </table>
            </div>

        <div>
        <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">      
    </form>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

    $(document).ready(function () {
        $(document).on('change', '#module', function () {
             var module = $(this).val();
             
             $("#article_detail_table").html("");
            $.ajax({
                url: "{{ URL::to('admin/get_artical_by_module') }}",
                type: 'get',
                data: {module: module},
                dataType: 'html',
                success: function (response) {
                    
                    $("#article").html(response);
                   
                }
            });
        });

        $(document).on('change', '#article', function () {
             var article = $(this).val();
             var module = $("#module").val();
            $.ajax({
                url: "{{ URL::to('admin/get_artical_detail') }}",
                type: 'get',
                data: {article: article, module: module},
                dataType: 'html',
                success: function (response) {
                    
                    $("#article_detail_table").html(response);
                   
                }
            });
        });

    });
</script>
@endsection