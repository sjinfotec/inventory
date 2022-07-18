<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatManageController extends Controller
{


    public function index()
    {
    	//Log::debug('ViewInventoryController index in GET[order_info] = '.$_GET["order_info"]);
        // リダイレクト
        // return redirect()->route('edit_work_order.edithome', [
        //     'order_no' => $_GET["order_no"],
        //     'seq' => $_GET["seq"]
        // ]);
    	if (isset($_GET["product_name"]) || isset($_GET["mdate"]) || isset($_GET["charge"])) {
            $rv_product_name = !empty($_GET["product_name"]) ? $_GET['product_name'] : "";
            $rv_mdate = !empty($_GET["mdate"]) ? $_GET['mdate'] : "";
            $rv_charge = !empty($_GET["charge"]) ? $_GET['charge'] : "";
            $rv_orderfr = !empty($_GET["orderfr"]) ? $_GET['orderfr'] : "";

	        return view('matmanage', [
            	'product_name' => $rv_product_name,
            	'mdate' => $rv_mdate,
            	'charge' => $rv_charge,
            	'orderfr' => $rv_orderfr
	        ]);
	    }
        else {
	        //$authusers = Auth::user();
            return view('matmanage'
            );

    	} 

    }

    public function dust()
    {
        return view('mm_dust'
        );
    }


    public function search()
    {
    
        return view('matmanage'
        );
    }







}
