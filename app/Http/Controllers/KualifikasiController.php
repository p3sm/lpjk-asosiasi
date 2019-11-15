<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kualifikasi;

class KualifikasiController extends Controller
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

    public function apiGetList()
    {
      $kualifikasi = Kualifikasi::all();

    	return response()->json($kualifikasi, 200);
    }
}
