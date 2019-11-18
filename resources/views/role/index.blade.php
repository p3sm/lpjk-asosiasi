@extends('layouts.app')

@section('content')
        
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url("/")}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Role</li>
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

      <!-- Default box -->
      <div class="box">
        <div class="box-body">
            @if(session()->get('success'))
            <div class="alert alert-success">
              {{ session()->get('success') }}  
            </div><br />
            @endif
            {{--  sub menu  --}}
            <div style="margin-bottom: 20px">
                 <a href="{{url('user_role/create')}}" class="btn btn-primary"><span>Add new</span></a>
            </div>
            {{--  end of sub menu  --}}

            {{--  table data of user  --}}
            <div>
                <table id="table-user" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Permission</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($role as $k => $d)
                        <tr>
                            <td>{{$k + 1}}</td>
                            <td>{{$d->name}}</td>
                            <td>{{$d->permission->count()}} permission(s)</td>
                            <td>
                                <a href="{{url('user_role/' . $d->id . '/edit')}}" class="btn btn-outline-secondary btn-sm"><span class='cui-pencil'></span> Edit</a>
                                <button class='btn btn-sm btn-outline-danger delete' data-url="user_role/{{$d->id}}" data-id="{{$d->id}}" data-name="{{$d->name}}"><span class='cui-trash'></span> Delete</button></td>
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