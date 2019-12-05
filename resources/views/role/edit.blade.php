@extends('layouts.app')

@section('content')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url("/")}}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{url("/user_role")}}">Role</a></li>
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
											
<!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
    	<div class="row">

	      <div class="col-md-6  col-md-offset-3">

	        <!-- general form elements -->
	        <div class="box box-primary">
	          <div class="box-header with-border">
	            <h3 class="box-title">Edit Role</h3>
	          </div>
	          <!-- /.box-header -->
	          <!-- form start -->
	          <form role="form" method="post" action="{{route('user_role.update', $role->id)}}">
	          	@method('PATCH') 
              @csrf
	            <div class="box-body">
	              <div class="form-group">
	                <label for="name">Nama</label>
	                <input type="text" class="form-control" name="name" id="name" value="{{$role->name}}" placeholder="Enter name">
								</div>
								
	              <div class="form-group">
									<label>Permission</label>
									<div style="column-count: 3">
										@foreach($permission as $perm)
											<div class="checkbox" style="margin-top: 0">
												<label><input type="checkbox" name="permission[]" {{$role_permission->contains('permission_id', $perm->id) ? "checked" : ""}} value="{{$perm->id}}">{{$perm->label}}</label>
											</div>
										@endforeach
									</div>
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
