<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Provinsi;

class ProvinsiController extends Controller
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
      $provinsi = Provinsi::all();

    	return response()->json($provinsi, 200);
    }
}
