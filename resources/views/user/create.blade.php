@extends('layouts.app')

@section('content')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url("/")}}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{url("/users")}}">Users</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
</nav>
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
											<div class="card-body">
    <!-- Main content -->
    <section class="content">
    	<div class="row">

	      <div class="col-md-6  col-md-offset-3">

	        <!-- general form elements -->
	        <div class="box box-primary">
	          <div class="box-header with-border">
	            <h3 class="box-title">Create User</h3>
	          </div>
	          <!-- /.box-header -->
	          <!-- form start -->
	          <form role="form" method="post" action="{{url("users")}}">
              @csrf
	            <div class="box-body">
	              <div class="form-group">
	                <label for="name">Nama</label>
	                <input type="text" class="form-control" name="name" id="name" placeholder="Enter name">
	              </div>
	              <div class="form-group">
	                <label for="username">Username</label>
	                <input type="text" class="form-control" name="username" id="username" placeholder="Enter username">
	              </div>
	              <div class="form-group">
	                <label for="password">Password</label>
	                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
	              </div>
	              <div class="form-group">
                  <label>Role</label>
                  <select class="form-control" name="role_id">
                    @foreach ($roles as $role)
                    <option value="{{$role->id}}">{{$role->name}}</option>
                    @endforeach
                  </select>
                </div>
	              <div class="form-group">
                  <label>Asosiasi</label>
                  <select class="form-control" name="asosiasi_id">
										<option value="">-- pilih asosiasi --</option>
                    @foreach ($asosiasi as $as)
                    <option value="{{$as->id_asosiasi}}">{{$as->nama}}</option>
                    @endforeach
                  </select>
                </div>
	              <div class="form-group">
                  <label>Provinsi</label>
                  <select class="form-control" name="provinsi_id">
										<option value="">-- pilih provinsi --</option>
										@foreach ($provinsi as $prov)
											@if($prov->id_provinsi == "04")
												<option value="{{$prov->id_provinsi}}" {{$prov->id_provinsi == "04" ? "selected" : ""}}>{{$prov->nama}}</option>
											@endif
                    @endforeach
                  </select>
                </div>
	              <div class="checkbox">
	                <label>
	                  <input type="checkbox" name="is_active" checked="checked"> Active
	                </label>
	              </div>
	            </div>
	            <!-- /.box-body -->

	            <div class="box-footer">
	              <button type="submit" name="submit" class="btn btn-primary">Submit</button>
	            </div>
	          </form>
	        </div>
	        <!-- /.box -->

	      </div>

	    </div>
    </section>
    <!-- /.content -->
											</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
