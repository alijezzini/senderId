<?php

namespace App\Http\Controllers;
use App\Sender;
use App\Vendor;
use App\Operator;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class SearchController extends Controller
{
    public function searchall()
{
    $senders = DB::table('senders')
    ->join('operators', 'operators.op_id', '=', 'senders.operator')
    ->join('vendors', 'vendors.vn_id', '=', 'senders.vendor')
    ->get();
    $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
    $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
    return view('searchall',['countries'=>$countries,'vendors'=>$vendors,'senders'=>$senders]);
}

public function searchsender()
{

    $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
    $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
    return view('searchsender',['countries'=>$countries,'vendors'=>$vendors]);
}
public function searchallnotes()
{
    $notes = DB::table('notes')
    ->select('vendors.vendor','operators.operator','country','vendors.vn_id','operators.op_id')
    ->join('operators', 'operators.op_id', '=', 'notes.operator')
    ->join('vendors', 'vendors.vn_id', '=', 'notes.vendor')
    ->get()->toArray();

    $files = DB::table('files')
    ->select('vendors.vendor','operators.operator','country','vendors.vn_id','operators.op_id')
    ->join('operators', 'operators.op_id', '=', 'files.operator')
    ->join('vendors', 'vendors.vn_id', '=', 'files.vendor')
    ->get()->toArray();

    $merge = array_merge($notes,$files);
    $output = array_map("unserialize", array_unique(array_map("serialize", $merge)));
    $final = array();
    foreach($output as $o){
        
        $row=array();
        array_push($row,$o->vn_id);
        array_push($row,$o->op_id);
        array_push($row,$o->vendor);
        array_push($row,$o->operator);
        array_push($row,$o->country);
        $temp_note = "false";
        $temp_file = "false";
        foreach($notes as $n){
            if($o==$n){
                $temp_note = "true";
                break;
            }
        }
        foreach($files as $f){
            if($o==$f){
                $temp_file = "true";
                break;
            }
        }
        array_push($row,$temp_note);
        array_push($row,$temp_file);
        array_push($final,$row);
    }
    return view('searchallnotes',['data'=>$final]);
}
function getsenderTable(Request $req){

    $senders = DB::table('senders')
    ->join('operators', 'operators.op_id', '=', 'senders.operator')
    ->join('vendors', 'vendors.vn_id', '=', 'senders.vendor')
    ->where('operators.op_id', '=', $req->operator)
    ->where('vendors.vn_id', '=', $req->vendor)
    ->get();
    $selectedOptions = [$req->country,$req->operator,$req->vendor];
    $req->session()->flash('selectedOptions',$selectedOptions);
    $req->session()->flash('operators',Session::get('tempoperators'));
    $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
    $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
    return view('searchsender',['countries'=>$countries,'vendors'=>$vendors,'senders'=>$senders]);

}

function getnote(Request $req){

    $notes = DB::table('notes')
    ->where('operator', '=', $req->operator)
    ->where('vendor', '=', $req->vendor)
    ->get();
    $files = DB::table('files')
    ->where('operator', '=', $req->operator)
    ->where('vendor', '=', $req->vendor)
    ->get();
    $selectedOptions = [$req->country,$req->operator,$req->vendor];
    $req->session()->flash('noteselectedOptions',$selectedOptions);
    $req->session()->flash('noteoperators',Session::get('notetempoperators'));
    $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
    $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
    return view('addNote',['countries'=>$countries,'vendors'=>$vendors,'notes'=>$notes,'files'=>$files]);
}

}