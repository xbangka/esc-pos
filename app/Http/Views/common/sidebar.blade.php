<div class="app-sidebar sidebar-shadow">
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                    data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>
    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">
                <li class="app-sidebar__heading">Dashboards</li>
                <li>
                    <a href="{{url('dashboard')}}" class="{{$menu=='dashboard'?'mm-active':''}}">
                        <i class="metismenu-icon fa fa-home"></i>
                        Dashboards
                    </a>
                </li>
                <li class="app-sidebar__heading">Master</li>
                <li>
                    <a href="{{url('stores')}}" class="{{$menu=='stores'?'mm-active':''}}">
                        <i class="metismenu-icon fa fa-building"></i>
                        Toko
                    </a>
                    <a href="{{url('products')}}" class="{{$menu=='products'?'mm-active':''}}">
                        <i class="metismenu-icon fa fa-tags"></i>
                        Produk
                    </a>
                    <a href="{{url('users')}}" class="{{$menu=='users'?'mm-active':''}}">
                        <i class="metismenu-icon fa fa-users"></i>
                        Pengguna
                    </a>
                    <a href="{{url('general-settings')}}">
                        <i class="metismenu-icon fa fa-cog"></i>
                        Konfigurasi Umum
                    </a>
                    <a href="{{url('retail-units')}}" class="{{$menu=='retail_units'?'mm-active':''}}">
                        <i class="metismenu-icon fa fa-leaf"></i>
                        Satuan
                    </a>
                    <a href="{{url('categories')}}" class="{{$menu=='categories'?'mm-active':''}}">
                        <i class="metismenu-icon fa fa-copyright"></i>
                        Kategori
                    </a>
                    <a href="{{url('statuses')}}" class="{{$menu=='statuses'?'mm-active':''}}">
                        <i class="metismenu-icon fa fa-info"></i>
                        Statuses
                    </a>
                </li>
                <li class="app-sidebar__heading">Data</li>
                <li>
                    <a href="{{url('my-products')}}" class="{{$menu=='myproducts'?'mm-active':''}}">
                        <i class="metismenu-icon fa fa-tags"></i>
                        Produk Ku
                    </a>
                    <a href="{{url('my-stores')}}" class="{{$menu=='mystores'?'mm-active':''}}">
                        <i class="metismenu-icon fa fa-cog"></i>
                        Konfigurasi Toko
                    </a>
                    <a href="{{url('my-employes')}}" class="{{$menu=='myemployes'?'mm-active':''}}">
                        <i class="metismenu-icon fa fa-users"></i>
                        Pengguna
                    </a>
                </li>
                {{-- <li class="app-sidebar__heading">Transaksi</li>
                <li>
                    <a href="forms-controls.html">
                        <i class="metismenu-icon pe-7s-mouse">
                        </i>Forms Controls
                    </a>
                </li>
                <li>
                    <a href="forms-layouts.html">
                        <i class="metismenu-icon pe-7s-eyedropper">
                        </i>Forms Layouts
                    </a>
                </li>
                <li>
                    <a href="forms-validation.html">
                        <i class="metismenu-icon pe-7s-pendrive">
                        </i>Forms Validation
                    </a>
                </li> --}}
                <li class="app-sidebar__heading">Akses</li>
                <li>
                    <a href="{{url('general-histories')}}">
                        <i class="metismenu-icon fa fa-history">
                        </i>Histori
                    </a>
                    <a href="{{url('reset-password')}}">
                        <i class="metismenu-icon fa fa-unlock-alt">
                        </i>Reset Password
                    </a>
                    <a href="javascript:;" onclick="logoutAction()" class="text-danger">
                        <i class="metismenu-icon fa fa-times">
                        </i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>