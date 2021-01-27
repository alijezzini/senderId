@extends('layouts.admin')
@section('title', 'Vendor Notes')
@section('content')
<!-- Adding a script for dropzone -->
<style>

#overlay,#overlayDelete,#overlayEdit {
  background: #ffffff;
  color: #666666;
  position: fixed;
  height: 100%;
  width: 100%;
  z-index: 5000;
  top: 0;
  left: 0;
  float: left;
  text-align: center;
  padding-top: 25%;
  opacity: .80;
}
.spinner {
    margin: 0 auto;
    height: 64px;
    width: 64px;
    animation: rotate 0.8s infinite linear;
    border: 5px solid #0055FF;
    border-right-color: transparent;
    border-radius: 50%;
}
@keyframes rotate {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
.bootstrap-select>.dropdown-toggle.bs-placeholder, .bootstrap-select>.dropdown-toggle.bs-placeholder:active, .bootstrap-select>.dropdown-toggle.bs-placeholder:focus, .bootstrap-select>.dropdown-toggle.bs-placeholder:hover {
    color: #495057 !important;
}

.btn-light {
    color: black !important;
    background-color: white !important;
    border-color: lightgrey !important;
}
    </style>
<div id="ModalEdit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalAddLabel" aria-hidden="true">
  <form id="AddForm" method="post" action="add">
  @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="myModalLabel">Edit</h3>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
            <input type="hidden" id="noteID">
            <div class="form-group"><label for="note"><b>Note</b></label><textarea class="form-control" type="text" id="editnote" name="editnote" rows="15"></textarea></div>                    
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                <button id="myFormSubmit" class="btn btn-success" type="submit">Save Changes</button>
            </div>
        </div>
    </div>
 </form>
</div>
<div id="ModalAdd" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalAddLabel" aria-hidden="true">
  <form id="AddForm" method="post" action="submitNote" enctype="multipart/form-data">
  @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="myModalLabel">Add</h3>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
            <input type="hidden" id="hiddenvendor" name="vendor">
            <input type="hidden" id="hiddenoperator" name="operator">
            <input type="hidden" id="hiddencountry" name="country">
            <div class="form-group"><label for="note"><b>Note</b></label><textarea class="form-control" type="text" id="note" name="note" rows="15"></textarea></div>
            <div class="form-group"><label for="content"><b>Attach Files</b></label><br><input type="file" name="attachment[]" multiple></div>
                    
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                <button id="myFormSubmit" class="btn btn-success" type="submit">Add</button>
            </div>
        </div>
    </div>
 </form>
</div>
<div style="margin-left:2rem;margin-right:2rem">
<h3 style="margin-bottom:2rem">Vendor Notes</h3>
<div id="overlay" style="display:none;">
    <div class="spinner"></div>
    <br/>
    Loading Operators...
</div>
<div id="overlayDelete" style="display:none;">
    <div class="spinner"></div>
    <br/>
    Deleting...
</div>
<div id="overlayEdit" style="display:none;">
    <div class="spinner"></div>
    <br/>
    Saving Changes...
</div>
<form action="searchnote" method="POST">
@csrf
<div style="padding:1rem; background-color:whitesmoke;border-radius:10px;">
            <label><b>Select Country, Operator and Vendor</b></label>
            <div class="row">

                <div class="col-md-4 py-1">
                    <select data-live-search="true" name ="country" class="form-control"  id="countryselect" required>
                        <option value="" disabled selected>Select Country</option>
                        @foreach($countries as $country)
                        <option value="{{$country['country']}}" 
                        @if(Session::has('noteselectedOptions'))
                        {{(Session::get('noteselectedOptions')[0] == $country['country']) ? 'selected' : ''}} 
                        @endif 
                        >{{$country['country']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 py-1">
                    <select data-live-search="true" class="form-control" name="operator" id="operatorselect" required>
                        <option value="" disabled selected>Select Operator</option>
                        @if(Session::has('noteoperators'))
                        @foreach(Session::get('noteoperators') as $operator)
                        <option value="{{$operator->op_id}}" {{Session::get('noteselectedOptions')[1] == $operator->op_id ? 'selected' : ''}}>{{$operator->operator}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-4 py-1">
                    <select data-live-search="true" class="form-control selectpicker" name="vendor" id="vendorselect" required>
                        <option value="" disabled selected>Select Vendor</option>
                        @foreach($vendors as $vendor)
                        <option style="font-size:7pt" value="{{$vendor['vn_id']}}"
                        @if(Session()->has('noteselectedOptions')) 
                        {{Session::get('noteselectedOptions')[2] == $vendor['vn_id'] ? 'selected' : ''}}
                        @endif
                        >{{$vendor['vendor']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row" style="margin-bottom:1rem">
        <div class="col-md-10 py-2">
        <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))

        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
        @endforeach
    </div> 
        </div>
            <div class="col-md-2 py-2">
                <button id="submitbutton" style="float:right" type="submit" class="btn btn-info btn-lg">Search</button>
            </div>
        </div>
    </form>

<div id="content">
    @if(!empty($notes))
    <div class="row">
    <div class="col-md-12" style="color:green"><span id="addspan" style="cursor:pointer"><b><i class="fas fa-plus"></i> Add</b></span></div>    
    </div>
    <hr>
    <h4>Notes</h4>
    
		@if ($notes->count() == 0)
        <div style="padding:1rem; background-color:#ffebb5;border: 1px solid #ffba00;border-radius:10px;margin-bottom:1rem;color:">
    <div class="row">
        <div class="col-md-12" style="text-align:center"><span >No Notes to Display</span></div>
    </div>
    </div>
        @endif
        @foreach ($notes as $note)
        <div id="note_row_{{$note->nt_id}}" style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-bottom:0.5rem">
        <div class="row">
        <div class="col-md-10"><span ><pre id="notecontent_{{$note->nt_id}}">{{ $note->note }}</pre></span></div>
        <div class="col-md-2"><div class="btn-group" style="float:right">
                    <i class="fas fa-edit icon-edit"  data-val="{{$note->nt_id}}" style="margin-right:5px;color:green;cursor:pointer;font-size:18pt"></i>
                    <i class="fas fa-trash-alt note icon-delete icon-delete-note"  data-val="{{$note->nt_id}}" style="margin-left:5px;color:#ef3535;cursor:pointer;font-size:18pt"></i>
    </div></div>  
    </div>
    </div>
        @endforeach    
    @endif 

    @if(!empty($files))
    <hr>
    <h4>Files</h4>
    
		@if ($files->count() == 0)
        <div style="padding:1rem; background-color:#ffebb5;border: 1px solid #ffba00;border-radius:10px;margin-bottom:1rem">
    <div class="row">
        <div class="col-md-12" style="text-align:center"><span>No Files to Display</span></div>
    </div>
    </div>
        @endif
        @foreach ($files as $file)
        <div id="file_row_{{$file->fl_id}}" style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-bottom:0.5rem">
        <div class="row">
        <div class="col-md-10"><a href="{{$file->file_url}}">{{ $file->file_name }}</a></span></div>
        <div class="col-md-2"><div class="btn-group" style="float:right">
                    <i class="fas fa-trash-alt icon-delete icon-delete-file"  data-val="{{$file->fl_id}}" style="margin-left:5px;color:#ef3535;cursor:pointer;font-size:18pt"></i>
    </div></div>  
    </div>
    </div>
        @endforeach
    @endif 
</div>
<script>

$(document).ready(function() {
        $('button').click(function(){
		
	});
    $("select").on('change', function() {
        $('#content').remove();
        });
        $("#countryselect").on('change', function() {
            $('#overlay').fadeIn();
            var country = $('#countryselect').find(":selected").text();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                /* the route pointing to the post function */
                url: 'getOperators',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "country": country,
                    "page":"searchnote"
                },
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function(data) {
                    $('#overlay').fadeOut();
                    $('#operatorselect').empty();
                    $('#operatorselect').prepend('<option value="" disabled="disabled" selected>Select Operator</option>');
                    $.each(data, function(key, val) {
                        $('#operatorselect')
                            .append($("<option></option>")
                                .attr("value", val.op_id)
                                .text(val.operator));
                        console.log(val.operator);
                    })
                }
            });
        });

        $('.icon-delete-note').on( 'click', function () {
    
    var r = confirm("Are You Sure you want to delete this Note?");
   
        if (r == true) {
            $('#overlayDelete').fadeIn();
            var nt_id=$(this).data('val');
            var row_id = "note_row_"+nt_id;
            $.ajax({
                /* the route pointing to the post function */
                url: 'deleteNote',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "nt_id": nt_id,
                },
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function(data) {
                    $("#"+row_id).remove();
                    $('#overlayDelete').fadeOut();
                }
            });
            
        }
    
} );
$('.icon-delete-file').on( 'click', function () {
    
    var r = confirm("Are You Sure you want to delete this File?");
   
        if (r == true) {
            $('#overlayDelete').fadeIn();
            var fl_id=$(this).data('val');
            var row_id = "file_row_"+fl_id;
            $.ajax({
                /* the route pointing to the post function */
                url: 'deleteFile',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "fl_id": fl_id,
                },
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function(data) {
                    $("#"+row_id).remove();
                    $('#overlayDelete').fadeOut();
                }
            });
            
        }
    
} );
        $('#addspan').on( 'click', function () {
            $("#hiddencountry").val($( "#countryselect" ).val()) ;
           $("#hiddenoperator").val($( "#operatorselect" ).val()) ;
           $("#hiddenvendor").val($( "#vendorselect" ).val()) ;
        $('#ModalAdd').modal('show');
    });

    $('.icon-edit').on( 'click', function () {
    
    var nt_id=$(this).data('val');
    var note = document.getElementById("notecontent_"+nt_id).innerText;
    $('#editnote').val(note);
    $('#noteID').val(nt_id);
    $('#ModalEdit').modal('show');
    
} );

$("#ModalEdit").submit(function(e){
    e.preventDefault();
    $('#ModalEdit').modal('hide');
    $('#overlayEdit').fadeIn();
    var form = $(this);
    var url = form.attr('action');
    $.ajax({
                /* the route pointing to the post function */
                url: 'editNote',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "nt_id":$("#noteID").val(),
                    "note": $("#editnote").val(),
                },
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function(data) {
                    var nt_id = data[0];
                    var value = data[1];
                    $('#notecontent_'+nt_id).text(value);
                    $('#overlayEdit').fadeOut();
                }
            });
  });
    });
</script>
@endsection