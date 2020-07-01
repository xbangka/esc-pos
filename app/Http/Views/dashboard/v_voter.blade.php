@extends('layouts.admin')

@section('title', config('app.name') )

@section('content')

    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{url('dashboard')}}">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">Voter</li>
    </ol>

    <div class="container-fluid">

        <!-- Page Content -->
        <h1>Data Voter</h1>

        <hr>

        <!-- DataTables Example -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-table"></i> Data Voter
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nama&nbsp;Voter</th>
                                <th>Email</th>
                                <th>Jenis&nbsp;Kelamin</th>
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
                sorting: [[ 0, "desc" ]],
                ajax: "{{url('voter/data')}}",
                columns: [
                    { data: 'nama',  name: 'nama' },
                    { data: 'email', name: 'email' },
                    { data: 'jk',    name: 'jk' }
                ]
            });
        });
    </script>
@endpush