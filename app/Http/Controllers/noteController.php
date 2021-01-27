<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Vendor;
use App\Operator;
use App\Note;
use App\File;
use Auth;
use Session;

class noteController extends Controller
{
    function index(){
        $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
        $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
        return view('addNote',['countries'=>$countries,'vendors'=>$vendors]);
    }

    function submit(Request $req){
        $successmsg="";
        $failmsg="";
        
        if($req->note != ""){
            $note = new note;
            $note->vendor = $req->vendor;
            $note->operator = $req->operator;
            $note->note = $req->note;
            $note->save();
        }
        $files = $req->file('attachment');
        if($req->hasFile('attachment'))
        {
            foreach ($files as $file) {
            $uniqueFileName = uniqid() . $file->getClientOriginalName();
            $opname = DB::table('operators')->where('op_id',$req->operator )->first()->operator;
            $vname = DB::table('vendors')->where('vn_id', $req->vendor)->first()->vendor;
            $file->move('files/'.$req->country.'/'.$opname.'/'.$vname.'/',$uniqueFileName);
            $completePath = 'files/'.$req->country.'/'.$opname.'/'.$vname.'/'.$uniqueFileName;
            $fl = new file;
            $fl->file_name = $file->getClientOriginalName();
            $fl->file_url = $completePath;
            $fl->vendor = $req->vendor;
            $fl->operator = $req->operator;
            $fl->save();
            }

              
        }
        $selectedOptions = [$req->country,$req->operator,$req->vendor];
        $req->session()->flash('noteselectedOptions',$selectedOptions);
        $req->session()->flash('noteoperators',Session::get('notetempoperators'));
        $req->session()->flash("alert-success", "Successfully Added");
            return redirect()->route('searchnote');
    }
    function deleteNote(Request $req){
        DB::table('notes')->where('nt_id', $req->nt_id)->delete();
        return response()->json("success");
    }
    function deleteFile(Request $req){
        DB::table('files')->where('fl_id', $req->fl_id)->delete();
        return response()->json("success");
    }
    function editNote(Request $req){
        DB::table('notes')
            ->where('nt_id', $req->nt_id)
            ->update([
                'note' => $req->note
                    ]);
        $resp = [$req->nt_id,$req->note];
        return response()->json($resp);
    }
    function deleteNotesFiles(Request $req){
        $ids = explode(",",$req->nf_ids);
        foreach($ids as $vn_op){
            $arr = explode("_",$vn_op);
             DB::table('notes')->where('vendor', $arr[0])->where('operator', $arr[1])->delete();
             DB::table('files')->where('vendor', $arr[0])->where('operator', $arr[1])->delete();
        }
        return response()->json("success");
    }
}