@extends('layouts.admin')

@section('title', $data->store_name . ' Management' )

@section('topbar')
    @parent
@endsection

@section('sidebar')
    @parent
@endsection

@section('content')
    
    <div class="app-page-title" style="background-image: url(images/home_header.png);background-repeat: no-repeat;background-size: cover;background-position-y: center;background-position-x: right;">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="fa fa-home icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>Analytics Dashboard
                    <div class="page-title-subheading">
                        {{$weather->location}}, {{$weather->temperature}}<sup>&deg;</sup>, {{$weather->phrase}}
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <button type="button" data-toggle="tooltip" title="Example Tooltip" data-placement="bottom" class="btn-shadow mr-3 btn btn-dark">
                    <i class="fa fa-star"></i>
                </button>
                <div class="d-inline-block dropdown">
                    <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn-shadow dropdown-toggle btn btn-info">
                        <span class="btn-icon-wrapper pr-2 opacity-7">
                            <i class="fa fa-business-time fa-w-20"></i>
                        </span>
                        Buttons
                    </button>
                    <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="javascript:void(0);" class="nav-link">
                                    <i class="nav-link-icon lnr-inbox"></i>
                                    <span>
                                        Inbox
                                    </span>
                                    <div class="ml-auto badge badge-pill badge-secondary">86</div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" class="nav-link">
                                    <i class="nav-link-icon lnr-book"></i>
                                    <span>
                                        Book
                                    </span>
                                    <div class="ml-auto badge badge-pill badge-danger">5</div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:void(0);" class="nav-link">
                                    <i class="nav-link-icon lnr-picture"></i>
                                    <span>
                                        Picture
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a disabled href="javascript:void(0);" class="nav-link disabled">
                                    <i class="nav-link-icon lnr-file-empty"></i>
                                    <span>
                                        File Disabled
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-ripe-malin">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">Toko</div>
                        <div class="widget-subheading">Total Cabang Toko</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span>{{$n_store}}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-midnight-bloom">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">Transaksi</div>
                        <div class="widget-subheading">Total Pencapaian</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span>{{$trxs}}</span></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-grow-early">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">Pemasukan</div>
                        <div class="widget-subheading">Total Pemasukan</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span>{{$reve}}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-xl-none d-lg-block col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-premium-dark">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">Products Sold</div>
                        <div class="widget-subheading">Revenue streams</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-warning"><span>$14M</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-4 col-md-2 mb-3">
            <a href="{{url('my-stores')}}" class="card-link">
                <div class="text-center card card-body pl-1 pr-1">
                    <i class="fa fa-cog fa-3x icon-gradient bg-malibu-beach mb-1"></i>
                    <span>Config</span>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2 mb-3">
            <a href="{{url('my-products')}}" class="card-link">
                <div class="text-center card card-body pl-1 pr-1">
                    <i class="fa fa-tags fa-3x icon-gradient bg-ripe-malin mb-1"></i>
                    <span>Produk</span>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2 mb-3">
            <a href="{{url('#')}}" class="card-link">
                <div class="text-center card card-body pl-1 pr-1">
                    <i class="fa fa-list-alt fa-3x icon-gradient bg-grow-early mb-1"></i>
                    <span>Transaksi</span>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2 mb-3">
            <a href="{{url('my-employes')}}" class="card-link">
                <div class="text-center card card-body pl-1 pr-1">
                    <i class="fa fa-users fa-3x icon-gradient bg-plum-plate mb-1"></i>
                    <span>Pengguna</span>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2 mb-3">
            <a href="{{url('general-histories')}}" class="card-link">
                <div class="text-center card card-body pl-1 pr-1">
                    <i class="fa fa-history fa-3x icon-gradient bg-love-kiss mb-1"></i>
                    <span>Histori</span>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2 mb-3">
            <a href="{{url('reset-password')}}" class="card-link">
                <div class="text-center card card-body pl-1 pr-1">
                    <i class="fa fa-unlock-alt fa-3x icon-gradient bg-mixed-hopes mb-1"></i>
                    <span>Reset Pass</span>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">
                    Inputan Baru
                </div>
                <div class="table-responsive">
                    <table class="align-middle mb-3 table table-borderless table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-right">#</th>
                                <th>Barcode</th>
                                <th>Name</th>
                                <th>Kategori</th>
                                <th class="text-center">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody> @php $n=0; @endphp
                            @foreach ($new_products as $row) @php $n++; @endphp
                                <tr>
                                    <td class="text-right text-muted">{{$n}}</td>
                                    <td>{{$row->barcode}}</td>
                                    <td>{{$row->full_name}}</td>
                                    <td>{{isset($row->categories) ? $row->categories->name:''}}</td>
                                    <td class="text-center">{{date('d F Y H:i', strtotime($row->created_at))}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('footer')
    @parent
@endsection
