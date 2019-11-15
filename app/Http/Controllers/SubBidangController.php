<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SubBidang;

class SubBidangController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return view('personal/index');
    }

    public function apiGetList($bidang_id)
    {
      $sub_bidang = SubBidang::where("bidang_id", $bidang_id)->get();

    	return response()->json($sub_bidang, 200);
    }
}
