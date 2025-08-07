<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class AdminProductData extends Controller
{
  public function getAdminProductData(){
   
    $datas=Product::where('added_by','admin')->get();
    if(!$datas){
        return response()->json([
            'status'=>'false',
            'message'=>'Data not Found'
        ]);
    }else{
        return response()->json([
            'status'=>'true',
            'data'=>$datas
        ]);
    }
  }























}
