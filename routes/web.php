<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => 'auth'], function(){
  Route::get('/clear-cache', function() {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    return 'Application cache cleared';
  });

  Route::get('api/users', 'UserController@apiList');
  Route::get('api/user/me', 'UserController@apiMe');
  Route::get('api/negara', 'NegaraController@apiGetList');
  Route::get('api/provinsi', 'ProvinsiController@apiGetList');
  Route::get('api/kabupaten/{provinsi_id}', 'KabupatenController@apiGetList');
  Route::get('api/kualifikasi', 'KualifikasiController@apiGetList');
  Route::get('api/bidang/{tipe_profesi}', 'BidangController@apiGetList');
  Route::get('api/subbidang/{bidang_id}', 'SubBidangController@apiGetList');
  Route::get('api/ustk/{provinsi_id}', 'UstkController@apiGetList');

  Route::get('api/pendidikan', 'PendidikanController@apiGetList');
  Route::post('api/biodata', 'PersonalController@apiGetBiodata');
  Route::post('api/biodata/create', 'PersonalController@apiCreateBiodata');
  Route::post('api/biodata/{id}', 'PersonalController@apiUpdateBiodata');
  Route::get('api/pendidikan/{id_personal}', 'PersonalController@apiGetPendidikan');
  Route::post('api/pendidikan', 'PersonalController@apiCreatePendidikan');
  Route::post('api/pendidikan/update', 'PersonalController@apiUpdatePendidikan');
  Route::post('api/pendidikan/delete', 'PersonalController@apiDeletePendidikan');
  Route::post('api/kursus', 'PersonalController@apiGetKursus');
  Route::post('api/kursus/create', 'PersonalController@apiCreateKursus');
  Route::post('api/kursus/update', 'PersonalController@apiUpdateKursus');
  Route::post('api/kursus/delete', 'PersonalController@apiDeleteKursus');
  Route::post('api/organisasi', 'PersonalController@apiGetOrganisasi');
  Route::post('api/organisasi/create', 'PersonalController@apiCreateOrganisasi');
  Route::post('api/organisasi/update', 'PersonalController@apiUpdateOrganisasi');
  Route::post('api/organisasi/delete', 'PersonalController@apiDeleteOrganisasi');
  Route::post('api/proyek', 'PersonalController@apiGetProyek');
  Route::post('api/proyek/create', 'PersonalController@apiCreateProyek');
  Route::post('api/proyek/update', 'PersonalController@apiUpdateProyek');
  Route::post('api/proyek/delete', 'PersonalController@apiDeleteProyek');
  Route::post('api/kualifikasi_ta', 'PersonalController@apiGetKualifikasiTA');
  Route::post('api/kualifikasi_ta/create', 'PersonalController@apiCreateKualifikasiTA');
  Route::post('api/kualifikasi_ta/delete', 'PersonalController@apiDeleteKualifikasiTA');
  Route::post('api/kualifikasi_tt', 'PersonalController@apiGetKualifikasiTT');
  Route::post('api/kualifikasi_tt/create', 'PersonalController@apiCreateKualifikasiTT');
  Route::post('api/kualifikasi_tt/delete', 'PersonalController@apiDeleteKualifikasiTT');

  Route::get('api/profile', 'ProfileController@apiGetProfile');
  Route::post('api/profile/edit', 'ProfileController@apiEditProfile');
  Route::post('api/profile/changepassword', 'ProfileController@apiChangePassword');
  Route::get('api/profile/file', 'ProfileController@apiGetFile');
  Route::get('api/profile/filetemplate', 'ProfileController@apiGetFileTemplate');
  Route::post('api/profile/uploadfile', 'ProfileController@apiUploadFile');

  Route::get('/', 'HomeController@index')->name('home');

	Route::group(['middleware' => 'authorization:user'], function () {
    Route::resources([
        'users' => 'UserController',
    ]);
  });

	Route::group(['middleware' => 'authorization:role'], function () {
    Route::resources([
      'user_role' => 'UserRoleController',
    ]);
  });

  Route::resources([
      'personal' => 'PersonalController',
  ]);

	Route::group(['middleware' => 'authorization:ska'], function () {
    Route::resources([
        'permohonan_ska' => 'PermohonanSKAController',
    ]);
  });

	Route::group(['middleware' => 'authorization:skt'], function () {
    Route::resources([
        'permohonan_skt' => 'PermohonanSKTController',
    ]);
  });
  
  Route::get('profile', 'ProfileController@index')->name('profile');
});
