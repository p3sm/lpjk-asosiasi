<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ApiKey;
use App\User;
use App\Asosiasi;

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
        $asosiasi = Asosiasi::find(Auth::user()->asosiasi->asosiasi_id);
        $verifikatorSigns = $asosiasi->verifikatorSign->where("provinsi_id", Auth::user()->asosiasi->provinsi_id);
        $databaseSigns = $asosiasi->databaseSign->where("provinsi_id", Auth::user()->asosiasi->provinsi_id);
        // $verifikatorSign = $verifikatorSigns[array_rand($verifikatorSigns->toArray())]->path;

        dd($verifikatorSigns);

        return view('pengajuan_status/indexSKA');
    }
    public function skt()
    {
        return view('pengajuan_status/indexSKT');
    }
}
