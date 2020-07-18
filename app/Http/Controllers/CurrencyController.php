<?php

namespace App\Http\Controllers;
use App\Currency;
class CurrencyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    public function getCurrencies($id = null)
    {
        if($id){
            $currs = app('db')->select("SELECT * FROM currencies where curr_id = ".(int)$id);
        }else{
            $currs = app('db')->select("SELECT * FROM currencies");
        }
        
        return response()->json($currs);
    }
    

    //
}
