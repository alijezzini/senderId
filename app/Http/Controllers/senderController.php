<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Sender;
use App\Vendor;
use App\Operator;
use Auth;
use Session;
use App\Imports\SendersImport;
use Maatwebsite\Excel\Facades\Excel;

class senderController extends Controller
{
    function index(){
        $countries= Operator::select('country')->groupBy('country')->orderBy('country')->get()->toArray() ;
        $vendors = Vendor::select('vn_id','vendor')->orderBy('vendor')->get()->toArray() ;
        return view('addsender',['countries'=>$countries,'vendors'=>$vendors]);
    }
    function submit(Request $req){
        if($req->radiocheck == "add"){

        $sender = new sender;
        $sender->senderid =  $req->senderid;
        $sender->content =  $req->content;
        $sender->website =  $req->website;
        $sender->note =  $req->note;
        $sender->operator =  $req->operator;
        $sender->vendor = $req->vendor;
        $sender->created_by = Auth::user()->name;
        $sender->save();
        $selectedOptions = [$req->country,$req->operator,$req->vendor];
        $req->session()->flash('selectedOptions',$selectedOptions);
        $req->session()->flash('operators',Session::get('tempoperators'));
        $req->session()->flash('alert-success', 'Sender Successfully Added!');
        return redirect()->route('searchsender');

        }
        
        else if($req->radiocheck == "import"){
            $message = "Sheet Successfully Imported";
            $color = "alert-success";
            $path1 = $req->file('senderidExcel')->store('temp'); 
            $path=storage_path('app').'/'.$path1;  
            $extension = pathinfo(storage_path($path), PATHINFO_EXTENSION);
            try{
            if($extension=="xlsx"){
            $rows= Excel::toArray(new SendersImport,$path);
            $emptysender = false;
            $rowcounter=0;
            foreach($rows[0] as $row){
                if($row['senderid']==""){
                    $emptysender=true;
                    break;
                }
                $rowcounter++;
            }
            if(!$emptysender){
                 foreach($rows[0] as $row){
                    $sender = new sender;
                    $sender->senderid =  $row['senderid'];
                    $sender->content =  $row['content'];
                    $sender->website =  $row['website'];
                    $sender->note =  $row['note'];
                    $sender->operator =  $req->operator;
                    $sender->vendor = $req->vendor;
                    $sender->created_by = Auth::user()->name;
                    $sender->save();
                 }
             }
             else{
                $message = "Sender ID can't be empty at row ".($rowcounter+2);
                $color = "alert-danger";
             }
        }
        else{
            $message = "Please import .xlsx file";
            $color = "alert-danger";
        }
    }
    catch(\Exception $e){
        $message = "Invalid Sheet Content";
            $color = "alert-danger";
    }
        $selectedOptions = [$req->country,$req->operator,$req->vendor];
        $req->session()->flash('selectedOptions',$selectedOptions);
        $req->session()->flash('operators',Session::get('tempoperators'));
            $req->session()->flash($color, $message);
            return redirect()->route('searchsender');  
        }
    }
    function getOperator(Request $req){
        $operators = DB::table('operators')->where('country', $req->country)->get();
        if($req->page =="searchsender"){
        Session::put('tempoperators', $operators);
        }
        if($req->page =="searchnote"){
            Session::put('notetempoperators', $operators);
        }
        return response()->json($operators);
    }


    function deleteSender(Request $req){
        $ids = explode(",",$req->sn_ids);
        foreach($ids as $sn_id){
             DB::table('senders')->where('sn_id', $sn_id)->delete();
        }
        return response()->json("success");
    }

    function editSender(Request $req){
        DB::table('senders')
            ->where('sn_id', $req->sn_id)
            ->update([
                'senderid' => $req->senderid,
                'content' => $req->content,
                'website' => $req->website,
                'note' => $req->note
                    ]);
        $resp = [$req->senderid,$req->content,$req->website,$req->note,$req->tr_row];
        return response()->json($resp);
    }
}
