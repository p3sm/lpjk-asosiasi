<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kabupaten;

class KabupatenController extends Controller
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

    public function apiGetList($provinsi_id)
    {
      $kabupaten = Kabupaten::where('provinsi_id', $provinsi_id)->get();

    	return response()->json($kabupaten, 200);
    }
}
