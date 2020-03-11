<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ApiKey;
use App\User;

class PengajuanNaikStatusController extends Controller
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
    public function ska()
    {
        return view('pengajuan_status/indexSKA');
    }
    public function skt()
    {
        return view('pengajuan_status/indexSKT');
    }
}
