<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ApiKey;
use App\User;

class PermohonanSKTController extends Controller
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
        return view('permohonan_skt/index');
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
            "url_pdf_ktp"                             => curl_file_create($request->file("file_ktp")->path()),
            "url_pdf_npwp"                            => curl_file_create($request->file("file_npwp")->path()),
            "url_pdf_photo"                           => curl_file_create($request->file("file_photo")->path()),
            "url_pdf_surat_pernyataan_kebenaran_data" => curl_file_create($request->file("file_pernyataan")->path()),
            "url_pdf_daftar_riwayat_hidup"            => curl_file_create($request->file("file_cv")->path())
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
            "url_pdf_ktp"                             => curl_file_create($request->file("file_ktp")->path()),
            "url_pdf_npwp"                            => curl_file_create($request->file("file_npwp")->path()),
            "url_pdf_photo"                           => curl_file_create($request->file("file_photo")->path()),
            "url_pdf_surat_pernyataan_kebenaran_data" => curl_file_create($request->file("file_pernyataan")->path()),
            "url_pdf_daftar_riwayat_hidup"            => curl_file_create($request->file("file_cv")->path())
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
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
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
            "url_pdf_ijazah"                             => curl_file_create($request->file("file_ijazah")->path()),
            "url_pdf_data_pendidikan"                    => curl_file_create($request->file("file_data_pendidikan")->path()),
            "url_pdf_data_surat_keterangan_dari_sekolah" => curl_file_create($request->file("file_keterangan_sekolah")->path()),
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
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
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
            "url_pdf_persyaratan_kursus" => curl_file_create($request->file("file_persyaratan")->path()),
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
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
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
            "url_pdf_persyaratan_pengalaman_organisasi" => curl_file_create($request->file("file_pengalaman")->path()),
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
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
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
            "url_pdf_persyaratan_pengalaman_proyek" => curl_file_create($request->file("file_pengalaman")->path()),
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
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiGetKualifikasiTA(Request $request)
    {
        $key = ApiKey::first();

        $postData = [
            "id_personal" => $request->id_personal,
            "status_99" => 0
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
            "url_pdf_berita_acara_vva"          => curl_file_create($request->file("file_berita_acara_vva")->path()),
            "url_pdf_surat_permohonan_asosiasi" => curl_file_create($request->file("file_surat_permohonan_asosiasi")->path()),
            "url_pdf_surat_permohonan"          => curl_file_create($request->file("file_surat_permohonan")->path()),
            "url_pdf_penilaian_mandiri_f19"     => curl_file_create($request->file("file_penilaian_mandiri")->path()),
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
                return response()->json($result, 200);
            }
            return response()->json($result, 400);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiGetKualifikasiTT(Request $request)
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
}
