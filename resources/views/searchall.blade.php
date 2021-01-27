@extends('layouts.admin')
@section('title', 'Search Senders')
@section('content')
<style>
td { font-size: 12px; }
    #overlay {
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
</style>
<div style="margin-left:2rem;margin-right:2rem">
    <div id="overlay">
    <div class="spinner"></div>
    <br/>
    Loading Table...
</div>
<h3 style="margin-bottom:2rem">Search Senders</h3>

<table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>SenderID</th>
                <th>Content</th>
                <th>Website</th>
                <th>Note</th>
                <th>Operator</th>
                <th>Country</th>
                <th>Vendor</th>
            </tr>
        </thead>
        
        <tbody>
		@if ($senders->count() == 0)
        <tr>
            <td colspan="7" style="text-align:center">No SenderIDs to display.</td>
        </tr>
        @endif
            @foreach ($senders as $sender)
        <tr>
            <td>{{ $sender->senderid }}</td>
            <td>{{ $sender->content }}</td>
            <td>{{ $sender->website }}</td>
            <td>{{ $sender->note }}</td>
            <td>{{ $sender->operator }}</td>
            <td>{{ $sender->country }}</td>
            <td>{{ $sender->vendor }}</td>
        </tr>
        @endforeach
            
        </tbody>
        <tfoot>
            <tr>
            <th>SenderID</th>
                <th>Content</th>
                <th>Website</th>
                <th>Note</th>
                <th>Operator</th>
                <th>Country</th>
                <th>Vendor</th>
            </tr>
        </tfoot>
    </table>
</div>
<script>
$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#example thead tr').clone(true).appendTo( '#example thead' );
    $('#example thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" class="form-control" placeholder="Search" />' );
 
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    } );
 
    var table = $('#example').DataTable( {
        "scrollX": true,
        "initComplete": function(settings, json) {
    $('#overlay').delay(1000).fadeOut();
  },
        "bPaginate": false,
        dom: 'Bfrtip',
        buttons: [
    {
        text: 'Export Excel',
        extend: 'excelHtml5',
        exportOptions: {
            columns: ':visible'
        },
        className:'btn btn-success'
    }
],
        orderCellsTop: true,
        fixedHeader: true
    } );
} );
</script>

@endsection
