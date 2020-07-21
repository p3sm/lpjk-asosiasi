<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\ApiKey;
use App\User;
use App\Asosiasi;
use App\Personal;
use App\PersonalKursus;
use App\PersonalOrganisasi;
use App\PersonalPendidikan;
use App\PersonalProyek;
use App\PersonalRegTA;
use App\PersonalRegTT;
use App\PengajuanNaikStatus;
use App\PengajuanNaikStatusTT;
use Carbon\Carbon;

class PersonalController extends Controller
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
        return view('personal/index');
    }

    public function apiGetBiodata(Request $request)
    {
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $postData = [
            "id_personal" => $request->id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => config("app.lpjk_endpoint") . "Service/Biodata/Get",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);

        $obj = json_decode($response);
        
        if($obj->message == "Token Anda Sudah Expired ! Silahkan Lakukan Aktivasi Token Untuk Mendapatkan Token Baru." || $obj->message == "Token Anda Tidak Terdaftar ! Silahkan Lakukan Aktivasi Token Untuk Mendapatkan Token Baru."){
            if($this->refreshToken()){
                return $this->apiGetBiodata($request);
            } else {
                $result = new \stdClass();
                $result->message = "Error while refreshing token, please contact Administrator";
                $result->status = 401;

                return response()->json($result, 401);
            }
        }

        $result = new \stdClass();
        $result->message = $obj->message;
        $result->status = $obj->response;
        $result->data = isset($obj->result) ? $obj->result[0] : [];

        $local = Personal::find($request->id_personal);

        if($local && $obj->response > 0){
            $result->data->file = [
                "persyaratan_5" => asset("storage/" . $local->persyaratan_5),
                "persyaratan_8" => asset("storage/" . $local->persyaratan_8),
                "persyaratan_4" => asset("storage/" . $local->persyaratan_4),
                "persyaratan_11" => asset("storage/" . $local->persyaratan_11),
            ];
        }

        if(isset($obj->result))
            $this->cloneBiodata($obj->result[0]);

    	return response()->json($result, $obj->response > 0 ? 200 : 400);
    }

    public function apiCreateBiodata(Request $request)
    {
        $postData = [
            "id_personal"         => $request->id_personal,
            "no_ktp"              => $request->id_personal,
            "nama"                => $request->nama,
            "nama_tanpa_gelar"    => $request->nama_tanpa_gelar,
            "alamat"              => $request->alamat,
            "kodepos"             => $request->pos,
            "id_kabupaten_alamat" => $request->kabupaten,
            "tgl_lahir"           => $request->tgl_lahir,
            "jenis_kelamin"       => $request->jenis_kelamin,
            "tempat_lahir"        => $request->tempat_lahir,
            "id_kabupaten_lahir"  => $request->kabupaten,
            "id_propinsi"         => $request->provinsi,
            "npwp"                => $request->npwp,
            "email"               => $request->email,
            "no_hp"               => $request->telepon,
            "id_negara"           => $request->negara,
            "jenis_tenaga_kerja"  => $request->jenis_tenaga_kerja,
            "url_pdf_ktp"                             => $request->file("file_ktp") ? curl_file_create($request->file("file_ktp")->path()) : "",
            "url_pdf_npwp"                            => $request->jenis_tenaga_kerja == "tenaga_ahli" && $request->file("file_npwp") ? curl_file_create($request->file("file_npwp")->path()) : "",
            "url_pdf_photo"                           => $request->file("file_photo") ? curl_file_create($request->file("file_photo")->path()) : "",
            "url_pdf_surat_pernyataan_kebenaran_data" => $request->file("file_pernyataan") ? curl_file_create($request->file("file_pernyataan")->path()) : "",
            "url_pdf_daftar_riwayat_hidup"            => $request->file("file_cv") ? curl_file_create($request->file("file_cv")->path()) : ""
            ];

        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Biodata/Tambah",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalBiodata($request);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiUpdateBiodata(Request $request, $id)
    {
        $postData = [
            "id_personal"         => $request->id_personal,
            "no_ktp"              => $request->id_personal,
            "nama"                => $request->nama,
            "nama_tanpa_gelar"    => $request->nama_tanpa_gelar,
            "alamat"              => $request->alamat,
            "kodepos"             => $request->pos,
            "id_kabupaten_alamat" => $request->kabupaten,
            "tgl_lahir"           => $request->tgl_lahir,
            "jenis_kelamin"       => $request->jenis_kelamin,
            "tempat_lahir"        => $request->tempat_lahir,
            "id_kabupaten_lahir"  => $request->kabupaten,
            "id_propinsi"         => $request->provinsi,
            "npwp"                => $request->npwp,
            "email"               => $request->email,
            "no_hp"               => $request->telepon,
            "id_negara"           => $request->negara,
            "jenis_tenaga_kerja"  => $request->jenis_tenaga_kerja,
            "url_pdf_ktp"                             => $request->file("file_ktp") ? curl_file_create($request->file("file_ktp")->path()) : "",
            "url_pdf_npwp"                            => $request->file("file_npwp") ? curl_file_create($request->file("file_npwp")->path()) : "",
            "url_pdf_photo"                           => $request->file("file_photo") ? curl_file_create($request->file("file_photo")->path()) : "",
            "url_pdf_surat_pernyataan_kebenaran_data" => $request->file("file_pernyataan") ? curl_file_create($request->file("file_pernyataan")->path()) : "",
            "url_pdf_daftar_riwayat_hidup"            => $request->file("file_cv") ? curl_file_create($request->file("file_cv")->path()) : ""
            ];

        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Biodata/Ubah",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalBiodata($request);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function storeLocalBiodata(Request $request)
    {
        $data = Personal::find($request->id_personal);
        
        if(!$data){
            $data = new Personal();
            $data->ID_Personal = $request->id_personal;
            $data->No_KTP = $request->id_personal;
            $data->created_by = Auth::user()->id;
        }
        
        $data->Nama = $request->nama;
        $data->nama_tanpa_gelar = $request->nama_tanpa_gelar;
        $data->Alamat1 = $request->alamat;
        $data->Kodepos = $request->pos;
        $data->ID_Kabupaten_Alamat = $request->kabupaten;
        $data->Tgl_Lahir = $request->tgl_lahir;
        $data->jenis_kelamin = $request->jenis_kelamin;
        $data->Tempat_Lahir = $request->tempat_lahir;
        $data->ID_Kabupaten_Lahir = $request->kabupaten;
        $data->ID_Propinsi = $request->provinsi;
        $data->npwp = $request->npwp;
        $data->email = $request->email;
        $data->no_hp = $request->telepon;
        $data->ID_Negara = $request->negara;
        $data->Tenaga_Kerja = $request->jenis_tenaga_kerja == "tenaga_ahli" ? "AHLI" : "TRAMPIL";
        $data->updated_by = Auth::user()->id;
        
        $ktp = $request->file("file_ktp") ? $request->file_ktp->store('ktp') : null;
        $npwp = $request->file("file_npwp") ? $request->file_npwp->store('npwp') : null;
        $photo = $request->file("file_photo") ? $request->file_photo->store('photo') : null;
        $pernyataan = $request->file("file_pernyataan") ? $request->file_pernyataan->store('kebenaran_data') : null;
        $cv = $request->file("file_cv") ? $request->file_cv->store('cv') : null;

        if($ktp != null){
            Storage::delete($data->persyaratan_5);
            $data->persyaratan_5 = $ktp;
        }
        if($npwp != null){
            Storage::delete($data->persyaratan_8);
            $data->persyaratan_8 = $npwp;
        }
        if($photo != null){
            Storage::delete($data->persyaratan_12);
            $data->persyaratan_12 = $photo;
        }
        if($pernyataan != null){
            Storage::delete($data->persyaratan_4);
            $data->persyaratan_4 = $pernyataan;
        }
        if($cv != null){
            Storage::delete($data->persyaratan_11);
            $data->persyaratan_11 = $cv;
        }

        $data->save();
    }

    public function cloneBiodata($result)
    {
        $data = Personal::find($result->id_personal);
        
        if(!$data){
            $data = new Personal();
            $data->ID_Personal = $result->id_personal;
            $data->No_KTP = $result->id_personal;
            $data->created_by = Auth::user()->id;
        }

        $data->Nama = $result->Nama;
        $data->nama_tanpa_gelar = $result->nama_tanpa_gelar;
        $data->Alamat1 = $result->Alamat1;
        $data->Kodepos = $result->Kodepos;
        $data->ID_Kabupaten_Alamat = $result->ID_Kabupaten_Alamat;
        $data->Tgl_Lahir = $result->Tgl_Lahir;
        $data->jenis_kelamin = $result->jenis_kelamin;
        $data->Tempat_Lahir = $result->Tempat_Lahir;
        $data->ID_Kabupaten_Lahir = $result->ID_Kabupaten_Lahir;
        $data->ID_Propinsi = $result->ID_Propinsi;
        $data->npwp = $result->npwp;
        $data->email = $result->email;
        $data->no_hp = $result->no_hp;
        $data->ID_Negara = $result->ID_Negara;
        $data->Tenaga_Kerja = $result->Tenaga_Kerja;
        $data->updated_by = Auth::user()->id;

        $data->save();
    }

    public function apiGetPendidikan(Request $request, $id_personal)
    {
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $postData = [
            "id_personal" => $id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => config("app.lpjk_endpoint") . "Service/Pendidikan/Get",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);

        $obj = json_decode($response);

        $result = new \stdClass();
        $result->message = $obj->message;
        $result->status = $obj->response;
        $result->data = $obj->result;

        $this->clonePendidikan($obj->result);

    	return response()->json($result, $obj->response > 0 ? 200 : 400);
    }

    public function apiCreatePendidikan(Request $request)
    {
        $postData = [
            "id_personal"                                => $request->id_personal,
            "nama_sekolah"                               => $request->nama,
            "alamat_sekolah"                             => $request->alamat,
            "id_propinsi_sekolah"                        => $request->provinsi,
            "id_kabupaten_sekolah"                       => $request->kabupaten,
            "id_negara_sekolah"                          => $request->negara,
            "tahun"                                      => $request->tahun,
            "jenjang"                                    => $request->jenjang,
            "jurusan"                                    => $request->jurusan,
            "no_ijazah"                                  => $request->no_ijazah,
            "url_pdf_ijazah"                             => $request->file("file_ijazah") ? curl_file_create($request->file("file_ijazah")->path()) : "",
            "url_pdf_data_pendidikan"                    => $request->file("file_data_pendidikan") ? curl_file_create($request->file("file_data_pendidikan")->path()) : "",
            "url_pdf_data_surat_keterangan_dari_sekolah" => $request->file("file_keterangan_sekolah") ? curl_file_create($request->file("file_keterangan_sekolah")->path()) : "",
        ];
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Pendidikan/Tambah",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalPendidikan($request, $obj->ID_Personal_Pendidikan);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiUpdatePendidikan(Request $request)
    {
        $postData = [
            "id_personal_pendidikan"                     => $request->ID_Personal_Pendidikan,
            "id_personal"                                => $request->id_personal,
            "nama_sekolah"                               => $request->nama,
            "alamat_sekolah"                             => $request->alamat,
            "id_propinsi_sekolah"                        => $request->provinsi,
            "id_kabupaten_sekolah"                       => $request->kabupaten,
            "id_negara_sekolah"                          => $request->negara,
            "tahun"                                      => $request->tahun,
            "jenjang"                                    => $request->jenjang,
            "jurusan"                                    => $request->jurusan,
            "no_ijazah"                                  => $request->no_ijazah,
            "url_pdf_ijazah"                             => $request->file("file_ijazah") ? curl_file_create($request->file("file_ijazah")->path()) : "",
            "url_pdf_data_pendidikan"                    => $request->file("file_data_pendidikan") ? curl_file_create($request->file("file_data_pendidikan")->path()) : "",
            "url_pdf_data_surat_keterangan_dari_sekolah" => $request->file("file_keterangan_sekolah") ? curl_file_create($request->file("file_keterangan_sekolah")->path()) : "",
        ];
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Pendidikan/Ubah",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalPendidikan($request, $request->ID_Personal_Pendidikan);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiDeletePendidikan(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $postData = [
            "id_personal_pendidikan" => $request->id_personal_pendidikan,
          ];

        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Pendidikan/Hapus",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function storeLocalPendidikan(Request $request, $id)
    {
        $data = PersonalPendidikan::find($id);
        
        if(!$data){
            $data = new PersonalPendidikan();
            $data->ID_Personal_Pendidikan = $id;
            $data->ID_Personal = $request->id_personal;
            $data->created_by = Auth::user()->id;
        }
        $data->Nama_Sekolah = $request->nama;
        $data->Alamat1 = $request->alamat;
        $data->ID_Propinsi = $request->provinsi;
        $data->ID_Kabupaten = $request->kabupaten;
        $data->ID_Countries = $request->negara;
        $data->Tahun = $request->tahun;
        $data->Jenjang = $request->jenjang;
        $data->Jurusan = $request->jurusan;
        $data->No_Ijazah = $request->no_ijazah;
        $data->updated_by = Auth::user()->id;
        
        $ijazah = $request->file("file_ijazah") ? $request->file_ijazah->store('ijazah') : null;
        $datapendidikan = $request->file("file_data_pendidikan") ? $request->file_data_pendidikan->store('data_pendidikan') : null;
        $dataketerangan = $request->file("file_keterangan_sekolah") ? $request->file_keterangan_sekolah->store('keterangan_sekolah') : null;

        if($ijazah != null){
            Storage::delete($data->persyaratan_6);
            $data->persyaratan_6 = $ijazah;
        }
        
        if($datapendidikan != null){
            Storage::delete($data->persyaratan_15);
            $data->persyaratan_15 = $datapendidikan;
        }
        
        if($dataketerangan != null){
            Storage::delete($data->persyaratan_7);
            $data->persyaratan_7 = $dataketerangan;
        }

        $data->save();
    }

    public function clonePendidikan($result)
    {
        foreach($result as $pendidikan){
            $data = PersonalPendidikan::find($pendidikan->ID_Personal_Pendidikan);
            
            if(!$data){
                $data = new PersonalPendidikan();
                $data->ID_Personal_Pendidikan = $pendidikan->ID_Personal_Pendidikan;
                $data->ID_Personal = $pendidikan->ID_Personal;
                $data->created_by = Auth::user()->id;
                $data->Nama_Sekolah = $pendidikan->Nama_Sekolah;
                $data->Alamat1 = $pendidikan->Alamat1;
                $data->ID_Propinsi = $pendidikan->ID_Propinsi;
                $data->ID_Kabupaten = $pendidikan->ID_Kabupaten;
                $data->ID_Countries = $pendidikan->ID_Countries;
                $data->Tahun = $pendidikan->Tahun;
                $data->Jenjang = $pendidikan->Jenjang;
                $data->Jurusan = $pendidikan->Jurusan;
                $data->No_Ijazah = $pendidikan->No_Ijazah;
                $data->updated_by = Auth::user()->id;
        
                $data->save();
            }
        }
    }

    public function apiGetKursus(Request $request)
    {
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $postData = [
            "id_personal" => $request->id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => config("app.lpjk_endpoint") . "Service/Kursus/Get",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);

        $obj = json_decode($response);

        $result = new \stdClass();
        $result->message = $obj->message;
        $result->status = $obj->response;
        $result->data = $obj->result;

        $this->cloneKursus($obj->result);

    	return response()->json($result, $obj->response > 0 ? 200 : 400);
    }

    public function apiCreateKursus(Request $request)
    {
        $postData = [
            "id_personal" => $request->id_personal,
            "nama_kursus" => $request->nama_kursus,
            "nama_penyelenggara_Kursus" => $request->penyelenggara,
            "alamat" => $request->alamat,
            "id_propinsi" => $request->provinsi,
            "id_kabupaten" => $request->kabupaten,
            "id_countries" => $request->negara,
            "tahun" => $request->tahun,
            "no_sertifikat" => $request->no_sertifikat,
            "url_pdf_persyaratan_kursus" => $request->file("file_persyaratan") ? curl_file_create($request->file("file_persyaratan")->path()) : "",
        ];
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Kursus/Tambah",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalKursus($request, $obj->ID_Personal_Kursus);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiUpdateKursus(Request $request)
    {
        $postData = [
            "ID_Personal_Kursus" => $request->ID_Personal_Kursus,
            "id_personal" => $request->id_personal,
            "nama_kursus" => $request->nama_kursus,
            "nama_penyelenggara_Kursus" => $request->penyelenggara,
            "alamat" => $request->alamat,
            "id_propinsi" => $request->provinsi,
            "id_kabupaten" => $request->kabupaten,
            "id_countries" => $request->negara,
            "tahun" => $request->tahun,
            "no_sertifikat" => $request->no_sertifikat,
            "url_pdf_persyaratan_kursus" => $request->file("file_persyaratan") ? curl_file_create($request->file("file_persyaratan")->path()) : "",
        ];
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Kursus/Ubah",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalKursus($request, $request->ID_Personal_Kursus);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiDeleteKursus(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $postData = [
            "ID_Personal_Kursus" => $request->id_personal_kursus,
          ];

        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Kursus/Hapus",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function storeLocalKursus(Request $request, $id)
    {
        $data = PersonalKursus::find($id);
        
        if(!$data){
            $data = new PersonalKursus();
            $data->ID_Personal_Kursus = $id;
            $data->ID_Personal = $request->id_personal;
            $data->created_by = Auth::user()->id;
        }
        $data->Nama_Kursus = $request->nama_kursus;
        $data->Nama_Penyelenggara_Kursus = $request->penyelenggara;
        $data->Alamat1 = $request->alamat;
        $data->ID_Propinsi = $request->provinsi;
        $data->ID_Kabupaten = $request->kabupaten;
        $data->ID_Countries = $request->negara;
        $data->Tahun = $request->tahun;
        $data->No_Sertifikat = $request->no_sertifikat;
        $data->updated_by = Auth::user()->id;
        
        $kursus = $request->file("file_persyaratan") ? $request->file_persyaratan->store('kursus') : null;

        if($kursus != null){
            Storage::delete($data->persyaratan_17);
            $data->persyaratan_17 = $kursus;
        }

        $data->save();
    }

    public function cloneKursus($result)
    {
        foreach($result as $kursus){
            $data = PersonalKursus::find($kursus->ID_Personal_Kursus);
            
            if(!$data){
                $data = new PersonalKursus();
                $data->ID_Personal_Kursus = $kursus->ID_Personal_Kursus;
                $data->ID_Personal = $kursus->ID_Personal;
                $data->created_by = Auth::user()->id;
                $data->Nama_Kursus = $kursus->Nama_Kursus;
                $data->Nama_Penyelenggara_Kursus = $kursus->Nama_Penyelenggara_Kursus;
                $data->Alamat1 = $kursus->Alamat1;
                $data->ID_Propinsi = $kursus->ID_Propinsi;
                $data->ID_Kabupaten = $kursus->ID_Kabupaten;
                $data->ID_Countries = $kursus->ID_Countries;
                $data->Tahun = $kursus->Tahun;
                $data->No_Sertifikat = $kursus->No_Sertifikat;
                $data->updated_by = Auth::user()->id;
        
                $data->save();
            }
        }
    }

    public function apiGetOrganisasi(Request $request)
    {
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $postData = [
            "id_personal" => $request->id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => config("app.lpjk_endpoint") . "Service/Organisasi/Get",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);

        $obj = json_decode($response);

        $result = new \stdClass();
        $result->message = $obj->message;
        $result->status = $obj->response;
        $result->data = $obj->result;

        $this->cloneOrganisasi($obj->result);

    	return response()->json($result, $obj->response > 0 ? 200 : 400);
    }

    public function apiCreateOrganisasi(Request $request)
    {
        $postData = [
            "id_personal" => $request->id_personal,
            "nama_badan_usaha" => $request->nama_bu,
            "NRBU" => " ",
            "alamat" => $request->alamat,
            "jenis_bu" => $request->jenis_bu,
            "jabatan" => $request->jabatan,
            "tgl_mulai" => $request->tgl_mulai,
            "tgl_selesai" => $request->tgl_selesai,
            "role_pekerjaan" => $request->role_pekerjaan,
            "url_pdf_persyaratan_pengalaman_organisasi" => $request->file("file_pengalaman") ? curl_file_create($request->file("file_pengalaman")->path()) : "",
        ];
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Organisasi/Tambah",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalOrganisasi($request, $obj->ID_Personal_Pengalaman);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiUpdateOrganisasi(Request $request)
    {
        $postData = [
            "ID_Personal_Pengalaman" => $request->ID_Personal_Pengalaman,
            "id_personal" => $request->id_personal,
            "nama_badan_usaha" => $request->nama_bu,
            "NRBU" => " ",
            "alamat" => $request->alamat,
            "jenis_bu" => $request->jenis_bu,
            "jabatan" => $request->jabatan,
            "tgl_mulai" => $request->tgl_mulai,
            "tgl_selesai" => $request->tgl_selesai,
            "role_pekerjaan" => $request->role_pekerjaan,
            "url_pdf_persyaratan_pengalaman_organisasi" => $request->file("file_pengalaman") ? curl_file_create($request->file("file_pengalaman")->path()) : "",
        ];
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Organisasi/Ubah",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalOrganisasi($request, $request->ID_Personal_Pengalaman);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiDeleteOrganisasi(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $postData = [
            "ID_Personal_Pengalaman" => $request->id_personal_pengalaman,
          ];

        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Organisasi/Hapus",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function storeLocalOrganisasi(Request $request, $id)
    {
        $data = PersonalOrganisasi::find($id);
        
        if(!$data){
            $data = new PersonalOrganisasi();
            $data->ID_Personal_Pengalaman = $id;
            $data->ID_Personal = $request->id_personal;
            $data->created_by = Auth::user()->id;
        }

        $data->Nama_Badan_Usaha = $request->nama_bu;
        $data->Alamat = $request->alamat;
        $data->Jenis_BU = $request->jenis_bu;
        $data->Jabatan = $request->jabatan;
        $data->Tgl_Mulai = $request->tgl_mulai;
        $data->Tgl_Selesai = $request->tgl_selesai;
        $data->Role_Pekerjaan = $request->role_pekerjaan;
        $data->updated_by = Auth::user()->id;
        
        $organisasi = $request->file("file_pengalaman") ? $request->file_pengalaman->store('organisasi') : null;

        if($organisasi != null){
            Storage::delete($data->persyaratan_18);
            $data->persyaratan_18 = $organisasi;
        }

        $data->save();
    }

    public function cloneOrganisasi($result)
    {
        foreach($result as $org){
            $data = PersonalOrganisasi::find($org->ID_Personal_Pengalaman);
            
            if(!$data){
                $data = new PersonalOrganisasi();
                $data->ID_Personal_Pengalaman = $org->ID_Personal_Pengalaman;
                $data->ID_Personal = $org->ID_Personal;
                $data->created_by = Auth::user()->id;
                $data->Nama_Badan_Usaha = $org->Nama_Badan_Usaha;
                $data->Alamat = $org->Alamat;
                $data->Jenis_BU = $org->Jenis_BU;
                $data->Jabatan = $org->Jabatan;
                $data->Tgl_Mulai = $org->Tgl_Mulai;
                $data->Tgl_Selesai = $org->Tgl_Selesai;
                $data->Role_Pekerjaan = $org->Role_Pekerjaan;
                $data->updated_by = Auth::user()->id;
        
                $data->save();
            }
        }
    }

    public function apiGetProyek(Request $request)
    {
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $postData = [
            "id_personal" => $request->id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => config("app.lpjk_endpoint") . "Service/Proyek/Get",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);

        $obj = json_decode($response);

        $result = new \stdClass();
        $result->message = $obj->message;
        $result->status = $obj->response;
        $result->data = $obj->result;

        $this->cloneProyek($obj->result);

    	return response()->json($result, $obj->response > 0 ? 200 : 400);
    }

    public function apiCreateProyek(Request $request)
    {
        $postData = [
            "id_personal" => $request->id_personal,
            "nama_proyek" => $request->nama_proyek,
            "lokasi" => $request->lokasi,
            "tgl_mulai" => $request->tgl_mulai,
            "tgl_selesai" => $request->tgl_selesai,
            "jabatan" => $request->jabatan,
            "nilai_proyek" => $request->nilai_proyek,
            "url_pdf_persyaratan_pengalaman_proyek" => $request->file("file_pengalaman") ? curl_file_create($request->file("file_pengalaman")->path()) : "",
        ];
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Proyek/Tambah",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalProyek($request, $obj->id_personal_proyek);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiUpdateProyek(Request $request)
    {
        $postData = [
            "id_personal_proyek" => $request->id_personal_proyek,
            "id_personal" => $request->id_personal,
            "nama_proyek" => $request->nama_proyek,
            "lokasi" => $request->lokasi,
            "tgl_mulai" => $request->tgl_mulai,
            "tgl_selesai" => $request->tgl_selesai,
            "jabatan" => $request->jabatan,
            "nilai_proyek" => $request->nilai_proyek,
            "url_pdf_persyaratan_pengalaman_proyek" => $request->file("file_pengalaman") ? curl_file_create($request->file("file_pengalaman")->path()) : "",
        ];
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Proyek/Ubah",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalProyek($request, $request->id_personal_proyek);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiDeleteProyek(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $postData = [
            "id_personal_proyek" => $request->id_personal_proyek,
          ];

        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Proyek/Hapus",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function storeLocalProyek(Request $request, $id)
    {
        $data = PersonalProyek::find($id);
        
        if(!$data){
            $data = new PersonalProyek();
            $data->id_personal_proyek = $id;
            $data->id_personal = $request->id_personal;
            $data->created_by = Auth::user()->id;
        }

        $data->Proyek = $request->nama_proyek;
        $data->Lokasi = $request->lokasi;
        $data->Tgl_Mulai = $request->tgl_mulai;
        $data->Tgl_Selesai = $request->tgl_selesai;
        $data->Jabatan = $request->jabatan;
        $data->Nilai = $request->nilai_proyek;
        $data->updated_by = Auth::user()->id;
        
        $proyek = $request->file("file_pengalaman") ? $request->file_pengalaman->store('proyek') : null;

        if($proyek != null){
            Storage::delete($data->persyaratan_16);
            $data->persyaratan_16 = $proyek;
        }

        $data->save();
    }

    public function cloneProyek($result)
    {
        foreach($result as $proyek){
            $data = PersonalProyek::find($proyek->id_personal_proyek);
            
            if(!$data){
                $data = new PersonalProyek();
                $data->id_personal_proyek = $proyek->id_personal_proyek;
                $data->id_personal = $proyek->id_personal;
                $data->created_by = Auth::user()->id;
                $data->Proyek = $proyek->Proyek;
                $data->Lokasi = $proyek->Lokasi;
                $data->Tgl_Mulai = $proyek->Tgl_Mulai;
                $data->Tgl_Selesai = $proyek->Tgl_Selesai;
                $data->Jabatan = $proyek->Jabatan;
                $data->Nilai = $proyek->Nilai;
                $data->updated_by = Auth::user()->id;
        
                $data->save();
            }
        }
    }

    public function apiGetKualifikasiTA(Request $request)
    {
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $postData = [
            "ID_Personal" => $request->id_personal
            // "status_99" => 0
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => config("app.lpjk_endpoint") . "Service/Klasifikasi/Get-TA",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);

        $obj = json_decode($response);

        $result = new \stdClass();
        $result->message = $obj->message;
        $result->status = $obj->response;
        $result->data = $obj->result;

        $this->cloneRegTA($obj->result);

    	return response()->json($result, $obj->response > 0 ? 200 : 400);
    }

    public function apiGetKualifikasiTAStatus99(Request $request)
    {
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $postData = [
            "ID_Personal" => $request->id_personal
            // "status_99" => 0
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => config("app.lpjk_endpoint") . "Service/Klasifikasi/Get-TA",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);

        $obj = json_decode($response);

        $this->cloneRegTA($obj->result);

        try {
            $filtered_result = [];

            foreach($obj->result as $data){
                $exist = PersonalRegTA::find($data->ID_Registrasi_TK_Ahli);
                if($exist){
                    $data->doc_url = \Illuminate\Support\Facades\Crypt::encryptString($exist->ID_Personal . "." . date('Y-m-d', strtotime($exist->Tgl_Registrasi)));
                    $data->diajukan = $exist->diajukan;
                    $filtered_result[] = $data;
                }
            }

            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;
            $result->data = $filtered_result;
    
            return response()->json($result, $obj->response > 0 ? 200 : 400);
        } catch (\Exception $e){
            $result = new \stdClass();
            $result->message = "An error has occurred";
            $result->status = 400;
            $result->data = null;
            return response()->json($result, 400);
        }
    }

    public function apiCreateKualifikasiTA(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $postData = [
            "id_personal"           => $request->id_personal,
            "id_sub_bidang"         => $request->sub_bidang,
            "id_kualifikasi"        => $request->kualifikasi,
            "id_asosiasi"           => $user->asosiasi->asosiasi_id,
            "no_reg_asosiasi"       => $request->no_reg_asosiasi,
            "id_unit_sertifikasi"   => $request->id_unit_sertifikasi,
            "id_permohonan"         => $request->id_permohonan,
            "tgl_registrasi"        => $request->tgl_registrasi,
            "id_propinsi_reg"       => $user->asosiasi->provinsi_id,
            "url_pdf_berita_acara_vva"          => $request->file("file_berita_acara_vva") ? curl_file_create($request->file("file_berita_acara_vva")->path()) : "",
            "url_pdf_surat_permohonan_asosiasi" => $request->file("file_surat_permohonan_asosiasi") ? curl_file_create($request->file("file_surat_permohonan_asosiasi")->path()) : "",
            "url_pdf_surat_permohonan"          => $request->file("file_surat_permohonan") ? curl_file_create($request->file("file_surat_permohonan")->path()) : "",
            "url_pdf_penilaian_mandiri_f19"     => $request->file("file_penilaian_mandiri") ? curl_file_create($request->file("file_penilaian_mandiri")->path()) : "",
          ];

        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Klasifikasi/Tambah-TA",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalRegTA($request, $obj->ID_Registrasi_TK_Ahli);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiDeleteKualifikasiTA(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $postData = [
            "id_personal"              => $request->id_personal,
            "ID_Registrasi_TK_Ahli" => $request->ID_Registrasi_TK_Ahli,
          ];

        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Klasifikasi/Hapus-TA",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function storeLocalRegTA(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        $data = PersonalRegTA::find($id);
        
        if(!$data){
            $data = new PersonalRegTA();
            $data->ID_Registrasi_TK_Ahli = $id;
            $data->ID_Personal = $request->id_personal;
            $data->created_by = Auth::user()->id;
        }

        $data->ID_Sub_Bidang = $request->sub_bidang;
        $data->ID_Kualifikasi = $request->kualifikasi;
        $data->ID_Asosiasi_Profesi = $user->asosiasi->asosiasi_id;
        $data->No_Reg_Asosiasi = $request->no_reg_asosiasi;
        $data->id_unit_sertifikasi = $request->id_unit_sertifikasi;
        $data->id_permohonan = $request->id_permohonan;
        $data->Tgl_Registrasi = $request->tgl_registrasi;
        $data->ID_Propinsi_reg = $user->asosiasi->provinsi_id;
        $data->status_terbaru = $request->status_terbaru;
        $data->updated_by = Auth::user()->id;
        
        $vva = $request->file("file_berita_acara_vva") ? $request->file_berita_acara_vva->store('vva') : null;
        $permohonan_asosiasi = $request->file("file_surat_permohonan_asosiasi") ? $request->file_surat_permohonan_asosiasi->store('permohonan_asosiasi') : null;
        $permohonan = $request->file("file_surat_permohonan") ? $request->file_surat_permohonan->store('permohonan') : null;
        $penilaian = $request->file("file_penilaian_mandiri") ? $request->file_penilaian_mandiri->store('penilaian') : null;

        if($vva != null){
            Storage::delete($data->persyaratan_1);
            $data->persyaratan_1 = $vva;
        }
        if($permohonan_asosiasi != null){
            Storage::delete($data->persyaratan_3);
            $data->persyaratan_3 = $permohonan_asosiasi;
        }
        if($permohonan != null){
            Storage::delete($data->persyaratan_2);
            $data->persyaratan_2 = $permohonan;
        }
        if($penilaian != null){
            Storage::delete($data->persyaratan_13);
            $data->persyaratan_13 = $penilaian;
        }

        $data->save();
    }

    public function cloneRegTA($result)
    {
        foreach($result as $ta){
            $data = PersonalRegTA::find($ta->ID_Registrasi_TK_Ahli);
            
            // if(!$data){
            //     $data = new PersonalRegTA();
            //     $data->ID_Registrasi_TK_Ahli = $ta->ID_Registrasi_TK_Ahli;
            //     $data->ID_Personal = $ta->ID_Personal;
            //     $data->created_by = Auth::user()->id;

            if($data){
                $data->ID_Sub_Bidang = $ta->ID_Sub_Bidang;
                $data->ID_Kualifikasi = $ta->ID_Kualifikasi;
                $data->ID_Asosiasi_Profesi = $ta->ID_Asosiasi_Profesi;
                $data->No_Reg_Asosiasi = $ta->No_Reg_Asosiasi;
                $data->id_unit_sertifikasi = $ta->id_unit_sertifikasi;
                $data->id_permohonan = $ta->id_permohonan;
                $data->Tgl_Registrasi = $ta->Tgl_Registrasi;
                $data->ID_Propinsi_reg = $ta->ID_Propinsi_reg;
                $data->status_terbaru = $ta->status_terbaru;
                $data->updated_by = Auth::user()->id;
        
                $data->save();
            }
        }
    }

    public function apiPengajuanNaikStatus(Request $request)
    {
        $regta = PersonalRegTA::find($request->permohonan_id);

        if(Auth::user()->asosiasi->asosiasi_id != $regta->ID_Asosiasi_Profesi){
            return response()->json('Maaf Anda tidak dapat mengajukan data Asosiasi lain', 400);
        }

        if(Auth::user()->asosiasi->provinsi_id != $regta->ID_Propinsi_reg){
            return response()->json('Maaf Anda tidak dapat mengajukan data provinsi lain', 400);
        }

        $regta->diajukan = 1;
        $regta->diajukan_by = Auth::user()->id;
        $regta->diajukan_at = Carbon::now();
        $regta->save();

        $exist = PengajuanNaikStatus::where("date", $request->tanggal)
                                    ->where("id_personal", $request->id_personal)
                                    ->where("sub_bidang", $regta->ID_Sub_Bidang)
                                    ->where("asosiasi", $regta->ID_Asosiasi_Profesi)->first();
        if(!$exist){
            
            $asosiasi = Asosiasi::find(Auth::user()->asosiasi->asosiasi_id);
            $verifikatorSigns = $asosiasi->verifikatorSign->where("provinsi_id", Auth::user()->asosiasi->provinsi_id);
            $databaseSigns = $asosiasi->databaseSign->where("provinsi_id", Auth::user()->asosiasi->provinsi_id);

            $userVerifikatorName = $asosiasi->detail->where("provinsi_id", Auth::user()->asosiasi->provinsi_id)[0]->user_verifikator;
            $userDatabaseName = $asosiasi->detail->where("provinsi_id", Auth::user()->asosiasi->provinsi_id)[0]->user_database;
            $verifikatorSign = $verifikatorSigns[array_rand($verifikatorSigns->toArray())]->path;
            $databaseSign = $databaseSigns[array_rand($databaseSigns->toArray())]->path;

            $new = new PengajuanNaikStatus();
            $new->date = $request->tanggal;
            $new->id_personal = $request->id_personal;
            $new->nama = $regta->personal->Nama;
            $new->sub_bidang = $regta->ID_Sub_Bidang;
            $new->kualifikasi = $regta->ID_Kualifikasi;
            $new->asosiasi = $regta->ID_Asosiasi_Profesi;
            $new->ustk = $regta->id_unit_sertifikasi;
            $new->user_verifikator = $userVerifikatorName;
            $new->user_database = $userDatabaseName;
            $new->ttd_verifikator = $verifikatorSign;
            $new->ttd_database = $databaseSign;
            $new->created_by = Auth::user()->id;
            $new->updated_by = Auth::user()->id;

            if($new->save()){
                return response()->json('Input Succeeded', 201);
            } else {
                return response()->json('An error has occurred', 400);
            }
        }

        return response()->json(null, 204);
    }

    public function apiGetKualifikasiTT(Request $request)
    {
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $postData = [
            "ID_Personal" => $request->id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => config("app.lpjk_endpoint") . "Service/Klasifikasi/Get-TT",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);

        $obj = json_decode($response);

        $result = new \stdClass();
        $result->message = $obj->message;
        $result->status = $obj->response;
        $result->data = $obj->result;

        $this->cloneRegTT($obj->result);

    	return response()->json($result, $obj->response > 0 ? 200 : 400);
    }

    public function apiGetKualifikasiTTStatus99(Request $request)
    {
        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $postData = [
            "ID_Personal" => $request->id_personal
            // "status_99" => 0
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => config("app.lpjk_endpoint") . "Service/Klasifikasi/Get-TT",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);

        $obj = json_decode($response);

        $this->cloneRegTT($obj->result);

        try {
            $filtered_result = [];

            foreach($obj->result as $data){
                $exist = PersonalRegTT::find($data->ID_Registrasi_TK_Trampil);
                if($exist){
                    $data->doc_url = \Illuminate\Support\Facades\Crypt::encryptString($exist->ID_Personal . "." . date('Y-m-d', strtotime($exist->Tgl_Registrasi)));
                    $data->diajukan = $exist->diajukan;
                    $filtered_result[] = $data;
                }
            }

            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;
            $result->data = $filtered_result;
    
            return response()->json($result, $obj->response > 0 ? 200 : 400);
        } catch (\Exception $e){
            $result = new \stdClass();
            $result->message = "An error has occurred";
            $result->status = 400;
            $result->data = null;
            return response()->json($result, 400);
        }
    }

    public function apiCreateKualifikasiTT(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $postData = [
            "id_personal"           => $request->id_personal,
            "id_sub_bidang"         => $request->sub_bidang,
            "id_kualifikasi"        => $request->kualifikasi,
            "id_asosiasi"           => $user->asosiasi->asosiasi_id,
            "no_reg_asosiasi"       => $request->no_reg_asosiasi,
            "id_unit_sertifikasi"   => $request->id_unit_sertifikasi,
            "id_permohonan"         => $request->id_permohonan,
            "tgl_registrasi"        => $request->tgl_registrasi,
            "id_propinsi_reg"       => $user->asosiasi->provinsi_id,
            "no_sk"                 => "-",
            "url_pdf_berita_acara_vva"          => $request->file("file_berita_acara_vva") ? curl_file_create($request->file("file_berita_acara_vva")->path()) : "",
            "url_pdf_surat_permohonan_asosiasi" => $request->file("file_surat_permohonan_asosiasi") ? curl_file_create($request->file("file_surat_permohonan_asosiasi")->path()) : "",
            "url_pdf_surat_permohonan"          => $request->file("file_surat_permohonan") ? curl_file_create($request->file("file_surat_permohonan")->path()) : "",
          ];

        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Klasifikasi/Tambah-TT",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                $this->storeLocalRegTT($request, $obj->ID_Registrasi_TK_Trampil);
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiDeleteKualifikasiTT(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $postData = [
            "id_personal"              => $request->id_personal,
            "ID_Registrasi_TK_Trampil" => $request->ID_Registrasi_TK_Trampil,
          ];

        $key = ApiKey::where('provinsi_id', Auth::user()->asosiasi->provinsi_id)->first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => config("app.lpjk_endpoint") . "Service/Klasifikasi/Hapus-TT",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        
		if($obj = json_decode($response)){
            $result = new \stdClass();
            $result->message = $obj->message;
            $result->status = $obj->response;

			if($obj->response == 1) {
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function storeLocalRegTT(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        $data = PersonalRegTT::find($id);
        
        if(!$data){
            $data = new PersonalRegTT();
            $data->ID_Registrasi_TK_Trampil = $id;
            $data->ID_Personal = $request->id_personal;
            $data->created_by = Auth::user()->id;
        }

        $data->ID_Sub_Bidang = $request->sub_bidang;
        $data->ID_Kualifikasi = $request->kualifikasi;
        $data->ID_Asosiasi_Profesi = $user->asosiasi->asosiasi_id;
        // $data->No_Reg_Asosiasi = $request->no_reg_asosiasi;
        $data->id_unit_sertifikasi = $request->id_unit_sertifikasi;
        $data->id_permohonan = $request->id_permohonan;
        $data->Tgl_Registrasi = $request->tgl_registrasi;
        $data->ID_propinsi_reg = $user->asosiasi->provinsi_id;
        $data->status_terbaru = $request->status_terbaru;
        $data->updated_by = Auth::user()->id;
        
        $vva = $request->file("file_berita_acara_vva") ? $request->file_berita_acara_vva->store('vva') : null;
        $permohonan_asosiasi = $request->file("file_surat_permohonan_asosiasi") ? $request->file_surat_permohonan_asosiasi->store('permohonan_asosiasi') : null;
        $permohonan = $request->file("file_surat_permohonan") ? $request->file_surat_permohonan->store('permohonan') : null;

        if($vva != null){
            Storage::delete($data->persyaratan_1);
            $data->persyaratan_1 = $vva;
        }
        if($permohonan_asosiasi != null){
            Storage::delete($data->persyaratan_3);
            $data->persyaratan_3 = $permohonan_asosiasi;
        }
        if($permohonan != null){
            Storage::delete($data->persyaratan_2);
            $data->persyaratan_2 = $permohonan;
        }

        $data->save();
    }

    public function cloneRegTT($result)
    {
        foreach($result as $tt){
            $data = PersonalRegTT::find($tt->ID_Registrasi_TK_Trampil);
            
            // if(!$data){
            //     $data = new PersonalRegTT();
            //     $data->ID_Registrasi_TK_Trampil = $tt->ID_Registrasi_TK_Trampil;
            //     $data->ID_Personal = $tt->ID_Personal;
            //     $data->created_by = Auth::user()->id;

            if(!$data){
                $data->ID_Sub_Bidang = $tt->ID_Sub_Bidang;
                $data->ID_Kualifikasi = $tt->ID_Kualifikasi;
                $data->ID_Asosiasi_Profesi = $tt->ID_Asosiasi_Profesi;
                $data->id_unit_sertifikasi = $tt->id_unit_sertifikasi;
                $data->id_permohonan = $tt->id_permohonan;
                $data->Tgl_Registrasi = $tt->Tgl_Registrasi;
                $data->ID_propinsi_reg = $tt->ID_propinsi_reg;
                $data->status_terbaru = $tt->status_terbaru;
                $data->updated_by = Auth::user()->id;
        
                $data->save();
            }
        }
    }

    public function apiPengajuanNaikStatusTT(Request $request)
    {
        $regta = PersonalRegTT::find($request->permohonan_id);

        if(Auth::user()->asosiasi->asosiasi_id != $regta->ID_Asosiasi_Profesi){
            return response()->json('Maaf Anda tidak dapat mengajukan data Asosiasi lain', 400);
        }

        if(Auth::user()->asosiasi->provinsi_id != $regta->ID_propinsi_reg){
            return response()->json('Maaf Anda tidak dapat mengajukan data provinsi lain', 400);
        }

        $regta->diajukan = 1;
        $regta->diajukan_by = Auth::user()->id;
        $regta->diajukan_at = Carbon::now();
        $regta->save();

        $exist = PengajuanNaikStatusTT::where("date", $request->tanggal)
                                        ->where("id_personal", $request->id_personal)
                                        ->where("sub_bidang", $regta->ID_Sub_Bidang)
                                        ->where("asosiasi", $regta->ID_Asosiasi_Profesi)->first();
        if(!$exist){
            
            $asosiasi = Asosiasi::find(Auth::user()->asosiasi->asosiasi_id);
            $verifikatorSigns = $asosiasi->verifikatorSign->where("provinsi_id", Auth::user()->asosiasi->provinsi_id);
            $databaseSigns = $asosiasi->databaseSign->where("provinsi_id", Auth::user()->asosiasi->provinsi_id);

            $userVerifikatorName = $asosiasi->detail->where("provinsi_id", Auth::user()->asosiasi->provinsi_id)[0]->user_verifikator;
            $userDatabaseName = $asosiasi->detail->where("provinsi_id", Auth::user()->asosiasi->provinsi_id)[0]->user_database;
            $verifikatorSign = $verifikatorSigns[array_rand($verifikatorSigns->toArray())]->path;
            $databaseSign = $databaseSigns[array_rand($databaseSigns->toArray())]->path;

            $new = new PengajuanNaikStatusTT();
            $new->date = $request->tanggal;
            $new->id_personal = $request->id_personal;
            $new->nama = $regta->personal->Nama;
            $new->sub_bidang = $regta->ID_Sub_Bidang;
            $new->kualifikasi = $regta->ID_Kualifikasi;
            $new->asosiasi = $regta->ID_Asosiasi_Profesi;
            $new->ustk = $regta->id_unit_sertifikasi;
            $new->user_verifikator = $userVerifikatorName;
            $new->user_database = $userDatabaseName;
            $new->ttd_verifikator = $verifikatorSign;
            $new->ttd_database = $databaseSign;
            $new->created_by = Auth::user()->id;
            $new->updated_by = Auth::user()->id;

            if($new->save()){
                return response()->json('Input Succeeded', 201);
            } else {
                return response()->json('An error has occurred', 400);
            }
        }

        return response()->json(null, 204);
    }
}
