@extends('layouts.app')

@section('content')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url("/")}}">Dashboard</a></li>
        <li class="breadcrumb-item active">Pengajuan Naik Status SKA</li>
    </ol>
</nav>
<div class="container-fluid">
    <div class="animated fadeIn">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="docs-title" id="content">Pengajuan Naik Status SKA</h3>
                          
                            @if(session()->get('success'))
                            <div class="alert alert-success">
                              {{ session()->get('success') }}  
                            </div><br />
                            @endif

                            <!-- modal konfirmasi -->
                  
                            <div class="modal fade" id="modal-konfirmasi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Konfirmasi</h4>
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
                            <div id="pengajuan_ska"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(function(){
  $(".fancybox").fancybox({
    "iframe" : {
		  "preload" : false
	  },
    'width': 600,
    // 'height': 250,
    // 'transitionIn': 'elastic', // this option is for v1.3.4
    // 'transitionOut': 'elastic', // this option is for v1.3.4
    // if using v2.x AND set class fancybox.iframe, you may not need this
    'type': 'iframe',
    'autoSize': false,
    // if you want your iframe always will be 600x250 regardless the viewport size
    // 'fitToView' : false  // use autoScale for v1.3.4
  });
});
</script>
@endpush

<style>
  .fancybox-content {
    width: 900px!important;
    padding: 20px!important;
  }
  .fancybox-iframe {
    padding: 40px!important;
  }
</style>