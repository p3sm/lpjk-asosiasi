<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\ApiKey;
use App\User;
use App\Personal;
use App\PersonalKursus;
use App\PersonalOrganisasi;
use App\PersonalPendidikan;
use App\PersonalProyek;
use App\PersonalRegTA;
use App\PersonalRegTT;

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
        $key = ApiKey::first();

        $postData = [
            "id_personal" => $request->id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => env("LPJK_ENDPOINT") . "Service/Biodata/Get",
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
        $result->data = $obj->response > 0 ? $obj->result[0] : [];

        $local = Personal::find($request->id_personal);

        if($local && $obj->response > 0){
            $result->data->file = [
                "persyaratan_5" => asset("storage/" . $local->persyaratan_5),
                "persyaratan_8" => asset("storage/" . $local->persyaratan_8),
                "persyaratan_4" => asset("storage/" . $local->persyaratan_4),
                "persyaratan_11" => asset("storage/" . $local->persyaratan_11),
            ];
        }

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

        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Biodata/Tambah",
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

        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Biodata/Ubah",
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

    public function apiGetPendidikan(Request $request, $id_personal)
    {
        $key = ApiKey::first();

        $postData = [
            "id_personal" => $id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => env("LPJK_ENDPOINT") . "Service/Pendidikan/Get",
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
        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Pendidikan/Tambah",
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
        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Pendidikan/Ubah",
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

    public function apiGetKursus(Request $request)
    {
        $key = ApiKey::first();

        $postData = [
            "id_personal" => $request->id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => env("LPJK_ENDPOINT") . "Service/Kursus/Get",
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
        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Kursus/Tambah",
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
        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Kursus/Ubah",
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

    public function apiGetOrganisasi(Request $request)
    {
        $key = ApiKey::first();

        $postData = [
            "id_personal" => $request->id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => env("LPJK_ENDPOINT") . "Service/Organisasi/Get",
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
        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Organisasi/Tambah",
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
        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Organisasi/Ubah",
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

    public function apiGetProyek(Request $request)
    {
        $key = ApiKey::first();

        $postData = [
            "id_personal" => $request->id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => env("LPJK_ENDPOINT") . "Service/Proyek/Get",
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
        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Proyek/Tambah",
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
        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Proyek/Ubah",
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

    public function apiGetKualifikasiTA(Request $request)
    {
        $key = ApiKey::first();

        $postData = [
            "ID_Personal" => $request->id_personal
            // "status_99" => 0
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => env("LPJK_ENDPOINT") . "Service/Klasifikasi/Get-TA",
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

    	return response()->json($result, $obj->response > 0 ? 200 : 400);
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

        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Klasifikasi/Tambah-TA",
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

        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Klasifikasi/Hapus-TA",
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

    public function apiGetKualifikasiTT(Request $request)
    {
        $key = ApiKey::first();

        $postData = [
            "ID_Personal" => $request->id_personal,
            // "limit" => 10
          ];

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
            CURLOPT_URL            => env("LPJK_ENDPOINT") . "Service/Klasifikasi/Get-TT",
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

    	return response()->json($result, $obj->response > 0 ? 200 : 400);
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

        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Klasifikasi/Tambah-TT",
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

        $key = ApiKey::first();

        $curl = curl_init();
        $header[] = "X-Api-Key:" . $key->lpjk_key;
        $header[] = "Token:" . $key->token;
        $header[] = "Content-Type:multipart/form-data";
        curl_setopt_array($curl, array(
        CURLOPT_URL => env("LPJK_ENDPOINT") . "Service/Klasifikasi/Hapus-TT",
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
}
