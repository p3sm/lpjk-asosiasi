<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\UserAsosiasi;
use App\Role;
use App\Asosiasi;
use App\Provinsi;
use Carbon\Carbon;

class UserController extends Controller
{
	public function __construct(User $user){
		$this->user = $user;
	}

    public function index(){
        $user = User::with(['asosiasi'])->where("is_active", ">=", 0)->where("is_deleted", 0);

        if(Auth::user()->id != 1){
            $user = $user->where('id', '!=', 1)->whereHas('asosiasi', function ($query){
                return $query->where('provinsi_id', Auth::user()->asosiasi->provinsi_id);
            });
        }

         $data["user"] = $user->get();
    	return view('user/index')->with($data);
    }

    public function create(){
        if(Auth::user()->id == 1)
          $data["roles"] = Role::all()->sortBy("name");
        else
          $data["roles"] = Role::where("created_by", Auth::user()->id)->orWhere("id", Auth::user()->role_id)->get()->sortBy("name");

        $data["asosiasi"] = Asosiasi::all()->sortBy("nama");
        if(Auth::user()->id == 1)
            $data["provinsi"] = Provinsi::all()->sortBy("nama");
        else
            $data["provinsi"] = Provinsi::where("id_provinsi", Auth::user()->asosiasi->provinsi_id)->get()->sortBy("nama");

        return view('user/create')->with($data);
    }

    public function store(Request $request)
    {
        $role = Role::findOrFail($request->get('role_id'));
        if($request->get('role_id') != Auth::user()->role_id && Auth::user()->id != $role->created_by){
          return redirect('/users')->with('error', 'Pembuatan data tidak diizinkan');
        }

        $find = User::where("username", $request->get('username'))->first();

        if($find){
            return redirect('/users/create')->with('error', 'User sudah ada');
        }

        $user = new User();
        $user->username  = $request->get('username');
        $user->password  = Hash::make($request->get('password'));
        $user->name      = $request->get('name');
        $user->role_id   = $request->get('role_id');
        $user->is_active = $request->get('is_active') ? 1 : 0;
        $user->created_by  = Auth::user()->id;

        if($user->save()){
            $uAsosiasi = new UserAsosiasi();
            $uAsosiasi->user_id = $user->id;
            $uAsosiasi->asosiasi_id = $request->get('asosiasi_id');
            $uAsosiasi->provinsi_id = Auth::user()->id == 1 ? $request->get('provinsi_id') : Auth::user()->asosiasi->provinsi_id;
            $uAsosiasi->save();
        }

        return redirect('/users')->with('success', 'User berhasil dibuat');
    }

    public function show($id)
    {
        echo $id;
    }

    public function edit($id)
    {
        $data["user"] = User::findOrFail($id);

        if(Auth::user()->id == 1)
          $data["roles"] = Role::all()->sortBy("name");
        else
          $data["roles"] = Role::where("created_by", Auth::user()->id)->orWhere("id", Auth::user()->role_id)->get()->sortBy("name");

        $data["asosiasi"] = Asosiasi::all()->sortBy("nama");
        if(Auth::user()->id == 1)
            $data["provinsi"] = Provinsi::all()->sortBy("nama");
        else
            $data["provinsi"] = Provinsi::where("id_provinsi", Auth::user()->asosiasi->provinsi_id)->get()->sortBy("nama");

        return view('user/edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($request->get('role_id'));
        if($request->get('role_id') != Auth::user()->role_id && Auth::user()->id != $role->created_by){
          return redirect('/users')->with('error', 'Perubahan data tidak diizinkan');
        }

        if(Auth::user()->id != $id && $id == 1){
            return redirect('/users')->with('error', 'Proses update tidak diizinkan');
        }

        $user = User::findOrFail($id);
        $user->username  = $request->get('username');

        if($request->get('password') != null){
            $user->password  = Hash::make($request->get('password'));
        }

        $user->name      = $request->get('name');
        $user->role_id   = $request->get('role_id');
        $user->is_active = $request->get('is_active') ? 1 : 0;
        $user->updated_by  = Auth::user()->id;
        

        if($user->save()){
            $uAsosiasi = UserAsosiasi::where("user_id", $user->id)->first();
            $uAsosiasi->asosiasi_id = $request->get('asosiasi_id');
            $uAsosiasi->provinsi_id = Auth::user()->id == 1 ? $request->get('provinsi_id') : Auth::user()->asosiasi->provinsi_id;
            $uAsosiasi->save();
            return redirect('/users')->with('success', 'User berhasil diupdate');
        }
        return redirect('/users')->with('error', 'User gagal diupdate');
    }

    public function destroy($id)
    {
        if(Auth::user()->id == $id || $id == 1){
            return response()->json(['status'=>'akun tidak boleh dihapus']);
        }

        $user = User::findOrFail($id);
        $user->is_deleted = 1;
        $user->deleted_by  = Auth::user()->id;
        $user->deleted_at  = Carbon::now();
        $user->save();

        return response()->json(['status'=>'berhasil hapus']);
    }

    public function apiList(){
      $user = User::where("is_active", ">=", 0)->with("role")->get();
      
    	return response()->json($user, 200);
    }

    public function apiMe(){
        $user = User::where("id", Auth::user()->id)->with(["asosiasi" => function ($query) {
            $query->with('provinsi')->with('detail');
        }])->first();
      
    	return response()->json($user, 200);
    }
}
