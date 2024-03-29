@extends('layouts.app')

@section('content')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url("/")}}">Dashboard</a></li>
        <li class="breadcrumb-item active">Users</li>
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

      <!-- Default box -->
      <div class="box">
        <div class="box-body">
            @if(session()->get('success'))
            <div class="alert alert-success">
              {{ session()->get('success') }}  
            </div><br />
            @endif
            @if(session()->get('error'))
            <div class="alert alert-danger">
              {{ session()->get('error') }}  
            </div><br />
            @endif
            {{--  sub menu  --}}
            <div style="margin-bottom: 20px">
                 <a href="{{url('users/create')}}" class="btn btn-primary"><span>Add new</span></a>
            </div>
            {{--  end of sub menu  --}}

            {{--  table data of user  --}}
            <div>
                <table id="table-user" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>User Name</th>
                            <th>Jabatan</th>
                            <th>Asosiasi</th>
                            <th>Provinsi</th>
                            <th>Tgl Input</th>
                            <th>KTP</th>
                            <th>Photo</th>
                            <th>File Pernyataan</th>
                            <th>Aktif</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user as $k => $d)
                        <tr>
                            <td>{{$k + 1}}</td>
                            <td>{{$d->name}}</td>
                            <td>{{$d->username}}</td>
                            <td>{{$d->role->name}}</td>
                            <td>{{$d->asosiasi ? $d->asosiasi->detail->nama : "-"}}</td>
                            <td>{{$d->asosiasi ? $d->asosiasi->provinsi->nama : "-"}}</td>
                            <td>{{$d->created_at}}</td>
                            <td>
                                @if($d->profile && $d->profile->file_ktp != "")
                                    <a data-type="iframe" data-fancybox href={{asset("storage/" . $d->profile->file_ktp)}}>View</a>
                                @endif
                            </td>
                            <td>
                                @if($d->profile && $d->profile->file_photo != "")
                                    <a data-type="iframe" data-fancybox href={{asset("storage/" . $d->profile->file_photo)}}>View</a>
                                @endif
                            </td>
                            <td>
                                @if($d->profile && $d->profile->file_pernyataan != "")
                                    <a data-type="iframe" data-fancybox href={{asset("storage/" . $d->profile->file_pernyataan)}}>View</a>
                                @endif
                            </td>
                            <td><div class="label label-{{$d->is_active ? "success" : "danger"}}">{{$d->is_active ? "Active" : "Inactive"}}</div></td>
                            <td>
                                <a href="{{url('users/' . $d->id . '/edit')}}" class="btn btn-outline-secondary btn-sm"><span class='cui-pencil'></span> Edit</a>
                                <button class='btn btn-sm btn-outline-danger delete' data-url="users/{{$d->id}}" data-id="{{$d->id}}" data-name="{{$d->name}}"><span class='cui-trash'></span> Delete</button></td>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{--  end of user data  --}}
            

            <!-- modal konfirmasi -->
   
            <div class="modal fade" id="modal-konfirmasi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Konfirmasi</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    </div>
                    <div class="modal-body" id="konfirmasi-body">
                        test
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" data-id="" id="btn-hapus">Yes</button>
                    </div>
                    </div>
                </div>
                </div>
            <!-- end of modal konfirmais -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer"></div>
        <!-- /.box-footer-->
      </div>
      <!-- /.box -->

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