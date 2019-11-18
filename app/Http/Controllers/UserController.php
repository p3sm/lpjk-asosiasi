<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\UserAsosiasi;
use App\Role;

class UserController extends Controller
{
	public function __construct(User $user){
		$this->user = $user;
	}

    public function index(){
        $data["user"] = User::where("is_active", ">=", 0)->get();
    	return view('user/index')->with($data);
    }

    public function create(){
        $roles = Role::all()->sortBy("name");

        return view('user/create', [
            "roles" => $roles
        ]);
    }

    public function store(Request $request)
    {
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

        if($user->save()){
            $uAsosiasi = new UserAsosiasi();
            $uAsosiasi->user_id = $user->id;
            $uAsosiasi->asosiasi_id = $request->get('asosiasi');
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
        $user = User::findOrFail($id);
        $roles = Role::all()->sortBy("name");

        return view('user/edit', [
            "user" => $user,
            "roles" => $roles
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->username  = $request->get('username');

        if($request->get('password') != null){
            $user->password  = Hash::make($request->get('password'));
        }

        $user->name      = $request->get('name');
        $user->role_id   = $request->get('role_id');
        $user->is_active = $request->get('is_active') ? 1 : 0;

        $user->save();
        return redirect('/users')->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        $item = User::findOrFail($id);
        $item->delete();

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
