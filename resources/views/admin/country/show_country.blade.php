@extends('layouts.admin')
@section('content')
<style>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
</style>
<style>
#add_country{
background-color: #169e88;
color: white;
padding: 5px 10px;
text-align: center;
text-decoration: none;
display: inline-block;
margin-top: 20px;
margin-left: 980px;
}
#edit_country{
background-color: #169e88;
color: white;
padding: 5px 10px;
text-align: center;
text-decoration: none;
display: inline-block;
margin-top: 20px;
}
#table{
    width:50%;
    height:50%;
    margin-left: auto;
    margin-right: auto;
}
/*** 27 Feb Alpesh - Country Edit/Delete - Start ***/
.addCountryBtn,.editCountryBtn{border-color:#169e88;background-color:#169e88;}
.addCountryBtn:hover,.editCountryBtn:hover{border-color:#169e88;background-color:#169e88;}
.cntry_msg{border-radius: .2rem;}
/*** 27 Feb Alpesh - Country Edit/Delete - End ***/
</style>

<div class="container-fluid">
<div class="row">
<!-- /*** 27 Feb Alpesh - Country Edit/Delete - Start ***/ -->
    <!-- @if (\Session::has('success'))
        <div class="col-xl-12">
            <div class="alert alert-success mt-2 px-0 py-2 cntry_msg">
                <ul class="mb-0">
                    <li>{!! \Session::get('success') !!}</li>
                </ul>
            </div>
        </div>
    @endif -->
    @if (session('success'))
        <div class="col-12">
            <div class="alert alert-success mt-2 cntry_msg alert-dismissible fade show d-flex align-items-center" role="alert">
                <span class="me-2 mr-2">
                    <i class="fas fa-check-circle"></i>
                </span>
                <span>{{ session('success') }}</span>
                <button type="button" class="close ms-auto bg-transparent border-0" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="col-12">
            <div class="alert alert-danger mt-2 cntry_msg alert-dismissible fade show d-flex align-items-center" role="alert">
                <ul class="m-0 px-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close ms-auto bg-transparent border-0" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif
<!-- /*** 27 Feb Alpesh - Country Edit/Delete - End ***/ -->

    <div class="col-xl-12 float-right">
        <a href="" id="add_country" data-toggle="modal" data-target="#mediumModal" title="Create a Country">Add Country</a>
        <br>
        <br>
    </div>
    <div class="row"></div>
</div>

<table class="table cell-border table-striped" id="table">
    <thead>
        <tr>
            <th class="text-center">Sr.no.</th>
            <th class="text-center">Name</th>
            <!-- /*** 27 Feb Alpesh - Country Edit/Delete - Start ***/ -->
            <td class="text-center">Action</td>
            <!-- /*** 27 Feb Alpesh - Country Edit/Delete - End ***/ -->
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; ?>
        @foreach($country as $value)
        <tr>
            <td class="text-center">{{$i}}</td>
            <td class="text-center">{{$value->country}}</td>
            <!-- /*** 27 Feb Alpesh - Country Edit/Delete - Start ***/ -->
            <td class="text-center">
                <button class="btn btn-success btn-sm editCountryBtn" 
                        data-toggle="modal" 
                        data-target="#editCountryModal" 
                        title="Edit Country" 
                        data-id="{{ $value->id }}" 
                        data-name="{{ $value->country }}" 
                        onclick="fetchCountry({{ $value->id }},'{{ $value->country }}')">
                    Edit
                </button>
                <button type="button" class="btn btn-danger btn-sm" 
                    data-toggle="modal" 
                    data-target="#deleteCountryModal" 
                    title="Delete Country" 
                    data-id="{{ $value->id }}" 
                    onclick="ConfirmDeleteCountry({{ $value->id }})">
                    Delete
                </button>
            </td>
            <!-- /*** 27 Feb Alpesh - Country Edit/Delete - End ***/ -->
        </tr>
        <?php $i++; ?>
        @endforeach
    </tbody>   
</table>
</div>


<!-- /*** 27 Feb Alpesh - Country Edit/Delete - Start ***/ -->
 <!-- medium modal -->
 <!-- <form action="add_country" method="post">
     @csrf
 <div class="modal fade" id="mediumModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">Add Country
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="mediumBody">
                    <div>
                        <input type="text" name="country_name" required>
                        <input type="submit" value="add">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form> -->

<form action="add_country" method="post">
    @csrf
    <div class="modal fade" id="mediumModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediumModalLabel">Add Country</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="country_name">Country Name</label>
                        <input type="text" name="country_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary addCountryBtn">Add</button>
                </div>
            </div>
        </div>
    </div>
</form>

<form action="edit_country" method="post">
    @csrf
    <div class="modal fade" id="editCountryModal" tabindex="-1" role="dialog" aria-labelledby="editCountryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCountryModalLabel">Edit Country</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                
                    <input type="hidden" name="country_id" id="country_id">
                    <div class="form-group">
                        <label for="country_name">Country Name</label>
                        <input type="text" name="country_name" class="form-control" id="country_name" required>
                    </div>                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary editCountryBtn">Update</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="deleteCountryModal" tabindex="-1" role="dialog" aria-labelledby="deleteCountryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMemberModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this country?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel_button" data-dismiss="modal">Cancel</button>
                <form action="delete_country" method="post">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="cntry_id" id="cntry_id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /*** 27 Feb Alpesh - Country Edit/Delete - End ***/ -->
@endsection

<script src="//code.jquery.com/jquery-1.12.3.js"></script>
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script
    src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('#table').DataTable();
});
/*** 27 Feb Alpesh - Country Edit/Delete - Start ***/
function fetchCountry(id,name){
    $("#country_id").val(id);
    $("#country_name").val(name);
    $("#editCountryModal").modal("show"); 
}
function ConfirmDeleteCountry(id){
    $("#cntry_id").val(id);
    $("#deleteCountryModal").modal("show");
}
/*** 27 Feb Alpesh - Country Edit/Delete - End ***/
</script>


