<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ustk;

class UstkController extends Controller
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

    public function apiGetList($provinsi_id, $bidang)
    {
      $ustk = Ustk::where("provinsi_id", $provinsi_id)
            ->where(function($q) use ($bidang) {
              $q->where('bidang_ta', "ALL")
              ->orWhere('bidang_tt', "ALL")
              ->orWhere('bidang_ta', $bidang)
              ->orWhere('bidang_tt', $bidang);
            })
            ->get();

    	return response()->json($ustk, 200);
    }
}
