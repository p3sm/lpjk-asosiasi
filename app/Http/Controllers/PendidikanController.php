<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pendidikan;

class PendidikanController extends Controller
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
      $pendidikan = Pendidikan::all();

    	return response()->json($pendidikan, 200);
    }
}
