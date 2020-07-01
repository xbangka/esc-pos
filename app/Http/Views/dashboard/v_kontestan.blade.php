@extends('layouts.admin')

@section('title', config('app.name') )

@section('content')

    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{url('dashboard')}}">Dashboard</a>
        </li>
        @if(!isset($peserta))
            <li class="breadcrumb-item active">Kontestan</li>
        @else
            <li class="breadcrumb-item">
                <a href="{{url('kontestan')}}">Kontestan</a>
            </li>
            <li class="breadcrumb-item active">{{$peserta}}</li>
        @endif
    </ol>

    <div class="container-fluid">

        <!-- Page Content -->
        <h1>
            Kontestan 
            @if(isset($peserta))
                {{$peserta}} Kota Cirebon
            @endif
        </h1>

        <hr>

        <!-- DataTables Example -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-table"></i>
                Kontestan
                <div style="float:right">
                    <a href="{{url('kontestan/create')}}" class="btn-sm btn-success">
                        <i class="fas fa-plus-square"></i>
                        Tambah
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:56px">No.&nbsp;Urut</th>
                                <th>Nama</th>
                                <th>Panggilan</th>
                                <th>JK</th>
                                <th>Mewakili</th>
                                <th style="width:56px">&nbsp;</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="card-footer small text-muted">Data update: {{date('d M Y H:i')}}</div>
        </div>

    </div><!-- /.container-fluid -->

@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                sorting: [[ 0, "asc" ]],
                ajax: "{{url('kontestan/data')}}{{isset($peserta) ? '?daftarpeserta='.$peserta : ''}}",
                columns: [
                    {   data: 'no_urut',    name: 'no_urut' },
                    {   data: 'nama',       name: 'nama' },
                    {   data: 'alias',      name: 'alias' },
                    {   data: 'jk',         name: 'jk' },
                    {   data: 'mewakili',   name: 'mewakili' },
                    {   data: null,         name: 'id',
                        render: function ( data, type, row ) {
                            var link_edit = "{{url('kontestan/edit')}}/"+data.id+"/"+data._i_;
                            var action_delete  = "deleteit('"+data.id+"','"+data._i_+"')";
                            return '<a href="'+link_edit+'" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a> &nbsp;'+
                                   '<a href="javascript:;" class="btn-sm btn-danger" onclick="'+action_delete+'">'+
                                        '<i class="fas fa-trash-alt"></i>'+
                                    '</a>';
                        }
                    }
                ]
            });
        });

        function deleteit(val1,val2){
            const imageURL = $('#img_question').attr('src');
            const valx1 = val1;
            const valx2 = val2;
            swal({
                title: "Konfirmasi Menghapus",
                text: "Ini adalah data master, yakin Anda ingin menghapusnya ?",
                icon: imageURL,
                buttons: [
                    ' Tidak ',
                    ' Ya, yakin! '
                ],
                dangerMode: true,
            }).then(function(isConfirm) {
                if (isConfirm)
                    window.location = "{{url('kontestan/delete')}}/"+valx1+"/"+valx2;
            });
        }
    </script>
@endpush