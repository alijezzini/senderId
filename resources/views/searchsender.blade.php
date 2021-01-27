@extends('layouts.admin')
@section('title', 'Senders')
@section('content')
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
td { font-size: 12px; }
    .bootstrap-select>.dropdown-toggle.bs-placeholder, .bootstrap-select>.dropdown-toggle.bs-placeholder:active, .bootstrap-select>.dropdown-toggle.bs-placeholder:focus, .bootstrap-select>.dropdown-toggle.bs-placeholder:hover {
    color: #495057 !important;
}

.btn-light {
    color: black !important;
    background-color: white !important;
    border-color: lightgrey !important;
}
</style>
<div style="margin-left:2rem;margin-right:2rem">
<h3 style="margin-bottom:2rem">Senders</h3>
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
    Editing...
</div>
<div id="ModalAdd" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalAddLabel" aria-hidden="true">
  <form id="AddForm" method="post" action="submit" enctype="multipart/form-data">
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
            <div style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-top:1rem">
            <label><input type="radio" name="radiocheck" value= "add" checked><b>  Add SenderID</b></label>

            <div class="row">
                <div class="col-md-6 py-1">
                    <input type="text" class="form-control" id="addsenderid" placeholder="Enter SenderID" name="senderid" required>
                </div>
                <div class="col-md-6 py-1">
                    <input type="text" class="form-control" id="addcontent" placeholder="Enter Content" name="content">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 py-1">
                    <input type="text" class="form-control" id="addwebsite" placeholder="Enter Website" name="website">

                </div>
                <div class="col-md-6 py-1">
                    <input type="text" class="form-control" id="addnote" placeholder="Enter Note" name="note">
                </div>
            </div>
        </div>
        <div style="padding:1rem; background-color:whitesmoke;border-radius:10px;margin-top:1rem">
            <label><input type="radio" name="radiocheck" value= "import"><b>  Import Multiple SenderIDs</b></label>
            <div class="row ">
                <div class="col-md-12 py-1">
                    <input type="file" id="importsender" name="senderidExcel"  accept=".xlsx" disabled required>
                </div>
            </div>
        </div>
                    
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                <button class="btn btn-success" type="submit">Add</button>
            </div>
        </div>
    </div>
 </form>
</div>
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form id="myForm" method="post" action="editSender">
  @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="myModalLabel">Edit Sender</h3>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
            <input type="hidden" name="sn_id" id="sn_id">
            <input type="hidden" name="tr_row" id="tr_row">
            <div class="form-group"><label for="senderid"><b>SenderID</b></label><input class="form-control" type="text" id="senderid" name="senderid"></div>
            <div class="form-group"><label for="content"><b>Content</b></label><input class="form-control" type="text" id="content" name="content"></div>
            <div class="form-group"><label for="website"><b>Website</b></label><input class="form-control" type="text" id="website" name="website"></div>
            <div class="form-group"><label for="note"><b>Note</b></label><textarea class="form-control" type="text" id="note" name="note" rows="3"></textarea></div>
                    
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                <button id="myFormSubmit" class="btn btn-success" type="submit">Save Changes</button>
            </div>
        </div>
    </div>
 </form>
</div>
<form action="searchsender" method="POST">
@csrf
<div style="padding:1rem; background-color:whitesmoke;border-radius:10px;">
            <label><b>Select Country, Operator and Vendor</b></label>
            <div class="row">

                <div class="col-md-4 py-1">
                    <select data-live-search="true" name ="country" class="form-control"  id="countryselect" required>
                        <option value="" disabled selected>Select Country</option>
                        @foreach($countries as $country)
                        <option value="{{$country['country']}}" 
                        @if(Session::has('selectedOptions'))
                        {{(Session::get('selectedOptions')[0] == $country['country']) ? 'selected' : ''}} 
                        @endif 
                        >{{$country['country']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 py-1">
                    <select data-live-search="true" class="form-control" name="operator" id="operatorselect" required>
                        <option value="" disabled selected>Select Operator</option>
                        @if(Session::has('operators'))
                        @foreach(Session::get('operators') as $operator)
                        <option value="{{$operator->op_id}}" {{Session::get('selectedOptions')[1] == $operator->op_id ? 'selected' : ''}}>{{$operator->operator}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-4 py-1">
                <select data-live-search="true" class="form-control selectpicker" name="vendor" id="vendorselect" required>
                        <option value="" disabled selected>Select Vendor</option>
                        @foreach($vendors as $vendor)
                        <option  style="font-size:7pt" value="{{$vendor['vn_id']}}"
                        @if(Session()->has('selectedOptions')) 
                        {{Session::get('selectedOptions')[2] == $vendor['vn_id'] ? 'selected' : ''}}
                        @endif
                        >{{$vendor['vendor']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row" style="margin-bottom:2rem">
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
        @if(!empty($senders))
        <hr>
        <div class="row">
    <div class="col-md-12" style="color:green"><span id="addspan" style="cursor:pointer"><b><i class="fas fa-plus"></i> Add</b></span></div>    
    </div>
    
     
<table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <td><input type="checkbox" id="checkAll"></td>
                <th>SenderID</th>
                <th>Content</th>
                <th>Website</th>
                <th>Note</th>
                <td></td>
            </tr>
        </thead>
        
        <tbody id="tablebody"> 

		@if ($senders->count() == 0)
        <tr>
            <td colspan="6" style="text-align:center">No SenderIDs to display.</td>
            <td style="display: none"></td>
             <td style="display: none"></td>
             <td style="display: none"></td>
             <td style="display: none"></td>
            <td style="display: none"></td>
        </tr>
        @endif
        @foreach ($senders as $sender)
        <tr id='tr_{{$sender->sn_id}}'>
            <td><input type='checkbox' class="checkbox" data-id="{{$sender->sn_id}}" ></td>
            <td>{{ $sender->senderid }}</td>
            <td>{{ $sender->content }}</td>
            <td>{{ $sender->website }}</td>
            <td>{{ $sender->note }}</td>
            <td><div class="btn-group">
                    <i class="fas fa-edit icon-edit" data-val="{{$sender->sn_id}}" style="margin-right:0px;color:green;cursor:pointer;font-size:18pt"></i>
                    <!-- <i class="fas fa-trash-alt icon-delete" data-val="{{$sender->sn_id}}" style="margin-left:5px;color:#ef3535;cursor:pointer;font-size:18pt"></i> -->
</div></td>
        </tr>
        @endforeach

        </tbody>
        <tfoot>
            <tr>
            <td></td>
            <th>SenderID</th>
                <th>Content</th>
                <th>Website</th>
                <th>Note</th>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @endif 
</div>
<script>

    $(document).ready(function() {
        $('button').click(function(){
		
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
                    "page":"searchsender"
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
                    })
                }
            });
        });

        var table = $('#example').DataTable({
            "scrollX": true,
            "bPaginate": false,
            dom: 'lfr<"toolbar">tip',
            fnInitComplete: function(){
           $('div.toolbar').html('<span id="delete" style="color:#ef3535;cursor:pointer;font-size:13pt;font-weight:bold"><i class="fas fa-trash-alt icon-delete" ></i> Delete</span>');
         }
        });

$("#checkAll").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
});
$('#delete').click(function() {
            var idsArr = [];  
            $(".checkbox:checked").each(function() {  
                idsArr.push($(this).attr('data-id'));
                console.log(idsArr);
            });  
            if(idsArr.length <=0)  
            {  
                alert("Please select atleast one record to delete.");  
            }  else {  
                if(confirm("Are you sure, you want to delete the selected Senders?")){  
                    $('#overlayDelete').fadeIn();
                    var strIds = idsArr.join(","); 
                    $.ajax({
                        url: 'deleteSender',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "sn_ids": strIds,
                },
                dataType: 'JSON',
                        success: function (data) {
                                $(".checkbox:checked").each(function() {  
                                    var tr=$(this).parents("tr").remove();
                                    table.row(tr).remove().draw();
                                });
                                $("#checkAll").prop("checked", false);
                                $('#overlayDelete').fadeOut();
                        },
                    });
                }  
            }  
        });
$('#example tbody').on( 'click', '.icon-edit', function () {
    
    
    
    var tr = $(this).parents('tr');
    var row = $(this).parents('tr').index();
    var sn_id=$(this).data('val');
    var sender = $(this).closest('tr').find('td:eq(1)').text();
    var content = $(this).closest('tr').find('td:eq(2)').text();
    var website = $(this).closest('tr').find('td:eq(3)').text();
    var note = $(this).closest('tr').find('td:eq(4)').text();
    $("#sn_id").val(sn_id);
    $("#tr_row").val(row);
    $("#senderid").val(sender);
    $("#content").val(content);
    $("#website").val(website);
    $("#note").val(note);
    $('#myModal').modal('show');
    
} );
$('input[type=radio][name=radiocheck]').change(function() {
            if (this.value == 'add') {
                $('#addsenderid').prop('disabled', false);
                $('#addcontent').prop('disabled', false);
                $('#addwebsite').prop('disabled', false);
                $('#addnote').prop('disabled', false);
                $('#importsender').prop('disabled', true);
                $('#importsender').prop('required', true);
            } else if (this.value == 'import') {
                $('#addsenderid').prop('disabled', true);
                $('#addcontent').prop('disabled', true);
                $('#addwebsite').prop('disabled', true);
                $('#addnote').prop('disabled', true);
                $('#importsender').prop('disabled', false);
            }
        });
$('#addspan').on( 'click', function () {
            $("#hiddencountry").val($( "#countryselect" ).val()) ;
           $("#hiddenoperator").val($( "#operatorselect" ).val()) ;
           $("#hiddenvendor").val($( "#vendorselect" ).val()) ;
        $('#ModalAdd').modal('show');
    });
$("#myModal").submit(function(e){
    e.preventDefault();
    $('#myModal').modal('hide');
    $('#overlayEdit').fadeIn();
    var form = $(this);
    var url = form.attr('action');
    $.ajax({
                /* the route pointing to the post function */
                url: 'editSender',
                type: 'POST',
                /* send the csrf-token and the input to the controller */
                data: {
                    "_token": "{{ csrf_token() }}",
                    "sn_id": $("#sn_id").val(),
                    "senderid": $("#senderid").val(),
                    "content": $("#content").val(),
                    "website": $("#website").val(),
                    "note": $("#note").val(),
                    "tr_row": $("#tr_row").val()
                },
                dataType: 'JSON',
                /* remind that 'data' is the response of the AjaxController */
                success: function(dat) {
                    var table = $('#example').DataTable();
                    var temp = $("#example")[0];
                    var Num = parseInt(dat[4])+1;
                    var cell0 = temp.rows[Num].cells[1];
                    var cell1 = temp.rows[Num].cells[2];
                    var cell2 = temp.rows[Num].cells[3];
                    var cell3 = temp.rows[Num].cells[4];
                    table.cell(cell0).data(dat[0]);
                    table.cell(cell1).data(dat[1]);
                    table.cell(cell2).data(dat[2]);
                    table.cell(cell3).data(dat[3]);
                    $('#overlayEdit').fadeOut();
                }
            });
  });
    });
</script>
@endsection
