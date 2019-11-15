<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bidang;

class BidangController extends Controller
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

    public function apiGetList($tipe_profesi)
    {
      $bidang = Bidang::where("id_tipe_profesi", $tipe_profesi)->get();

    	return response()->json($bidang, 200);
    }
}
