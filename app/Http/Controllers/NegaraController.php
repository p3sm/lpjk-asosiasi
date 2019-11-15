<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Negara;

class NegaraController extends Controller
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
      $negara = Negara::all();

    	return response()->json($negara, 200);
    }
}
