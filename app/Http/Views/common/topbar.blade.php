<div class="app-header header-shadow">
    <div class="app-header__logo">
        <h4>{{substr($data->store_name,0,13)}}</h4>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
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
            <button type="button" class="btn-icon btn-icon-only btn btn-link btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>
    <div class="app-header__content">
        <div class="app-header-right">
            <div class="header-btn-lg pr-0">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="btn-group">
                                <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                                    <img width="42" class="rounded-circle" src="{{asset('images/nathan-fillion.png')}}">
                                    <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                </a>
                                <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a href="javascript:;" class="nav-link">
                                                <i class="nav-link-icon fa fa-address-card"></i>
                                                <span>Profil</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{url('reset-password')}}" class="nav-link">
                                                <i class="nav-link-icon fa fa-unlock-alt"></i>
                                                <span>Reset Password</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{url('my-products')}}" class="nav-link">
                                                <i class="nav-link-icon fa fa-tags"></i>
                                                <span>Produk Ku</span>
                                                <div class="ml-auto badge badge-success">103</div>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{url('my-stores')}}" class="nav-link">
                                                <i class="nav-link-icon fa fa-cog"></i>
                                                <span>Konfigurasi Toko</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{url('general-histories')}}" class="nav-link">
                                                <i class="nav-link-icon fa fa-history"></i>
                                                <span>Histori</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{url('my-employes')}}" class="nav-link">
                                                <i class="nav-link-icon fa fa-users"></i>
                                                <span>Pengguna</span>
                                            </a>
                                        </li>
                                        <li class="nav-item-divider nav-item"></li>
                                        <li class="nav-item">
                                            <a href="javascript:;" class="nav-link text-danger" onclick="logoutAction()">
                                                <i class="nav-link-icon fa fa-times text-danger"></i>
                                                <span>Logout</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-left ml-3 header-user-info">
                            <div class="widget-heading">
                                {{ucwords(strtolower($data->firstname))}}
                            </div>
                            <div class="widget-subheading">
                                VP People Manager
                            </div>
                        </div>
                        <div class="widget-content-right header-user-info ml-3">
                            <button type="button"
                                class="btn-shadow p-1 btn btn-primary btn-sm show-toastr-example">
                                <i class="fa text-white fa-calendar pr-1 pl-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>