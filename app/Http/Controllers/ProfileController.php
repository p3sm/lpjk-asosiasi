<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("profile/index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show(Profile $profile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Profile $profile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        //
    }

    public function apiGetProfile()
    {
        $userId = Auth::user()->id;

        $data = Profile::where("user_id", $userId)->with('user')->first();
        
        if($data){
            $result = new \stdClass();
            $result->data = $data;
            $result->message = "Success";
            $result->status = 200;

            return response()->json($result, 200);
        }
        $result = new \stdClass();
        $result->data = null;
        $result->message = "Data not available";
        $result->status = 200;

    	return response()->json($result, 200);
    }

    public function apiEditProfile(Request $request)
    {
        $userId = Auth::user()->id;

        $data = Profile::where("user_id", $userId)->first();
        
        if(!$data){
            $data = new Profile();
            $data->user_id = $userId;
        }

        $data->nama = $request->nama;
        $data->email = $request->email;
        $data->ktp = $request->ktp;
        $data->jabatan = $request->jabatan;
        $data->phone = $request->phone;

        if($data->save()){
            $result = new \stdClass();
            $result->message = "Profile updated successfully";
            $result->status = 200;

            return response()->json($result, 200);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }

    public function apiGetFile()
    {
        $userId = Auth::user()->id;

        $data = Profile::where("user_id", $userId)->with('user')->first();
        
        if($data){
            $obj =  new \stdClass();
            $obj->file_ktp = asset("storage/" . $data->file_ktp);
            $obj->file_photo = asset("storage/" . $data->file_photo);
            $obj->file_pernyataan = asset("storage/" . $data->file_pernyataan);

            $result = new \stdClass();
            $result->data = $obj;
            $result->message = "Success";
            $result->status = 200;

            return response()->json($result, 200);
        }
        $result = new \stdClass();
        $result->data = null;
        $result->message = "Data not available";
        $result->status = 200;

    	return response()->json($result, 200);
    }

    public function apiUploadFile(Request $request)
    {
        $userId = Auth::user()->id;

        $data = Profile::where("user_id", $userId)->first();
        
        if(!$data){
            $data = new Profile();
            $data->user_id = $userId;
        }
        
        $ktp = $request->file("file_ktp") ? $request->file_ktp->store('profile/ktp') : null;
        $photo = $request->file("file_photo") ? $request->file_photo->store('profile/photo') : null;
        $pernyataan = $request->file("file_pernyataan") ? $request->file_pernyataan->store('profile/pernyataan') : null;

        if($ktp != null){
            Storage::delete($data->file_ktp);
            $data->file_ktp = $ktp;
        }
        if($photo != null){
            Storage::delete($data->file_photo);
            $data->file_photo = $photo;
        }
        if($pernyataan != null){
            Storage::delete($data->file_pernyataan);
            $data->file_pernyataan = $pernyataan;
        }

        if($data->save()){
            $result = new \stdClass();
            $result->message = "Document updated successfully";
            $result->status = 200;

            return response()->json($result, 200);
        }
        
        $result = new \stdClass();
        $result->message = "An error occurred";
        $result->status = 500;

    	return response()->json($result, 500);
    }
}
