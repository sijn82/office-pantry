<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AdditionalInfo;

class AdditionalInfoController extends Controller
{
    //
    public function addAdditionalInfo(Request $request)
    {
        // dd($request);
        $new_info = new AdditionalInfo();
        $new_info->company_id = $request['additional_info']['company'];
        $new_info->additional_info = $request['additional_info']['info'];
        $new_info->save();
        
        return AdditionalInfo::where('company_id', $request['additional_info']['company'])->where('additional_info', $request['additional_info']['info'])->get();
    }
    
    public function destroy($id) {
        
        AdditionalInfo::destroy($id);
        
    }
}
