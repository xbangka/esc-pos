@extends('layouts.admin')

@section('title', $data->store_name . ' Management' )

@section('content')
    
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="fa fa-building icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>Toko / Lapak / Mitra
                    <div class="page-title-subheading">
                        Toko / Lapak / Mitra
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row {{$app}}">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">Toko / Lapak / Mitra
                    <div v-if="btnadd" class="btn-actions-pane-right">
                        <div role="group" class="btn-group-sm btn-group">
                            <button @click="addNew()" class="btn btn-outline-secondary"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive pb-3" :style="(!showform) ? '':'display:none'">
                    <table id="dataTable" class="align-middle pb-3 table table-striped table-hover display responsive w-100">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Telp</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Modif</th>
                                <th style="width:60px">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div :style="(showform) ? '':'display:none'" class="row pl-3 pr-3">
                    <div :class="(labl=='BARU') ? 'offset-md-3 col-md-6 col-xs-12':'col-md-4 col-xs-12'">
                        <div class="row mt-4">
                            <div class="pb-3 col-md-12 col-xs-12">
                                DATA TOKO @{{labl}}
                                <div v-if="(labl=='EDIT')" class="pb-4 float-right">
                                    <button class="btn btn-outline-secondary btn-sm"><i class="fa fa-history"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="labl=='EDIT'" class="position-relative row form-group">
                            <label for="a1" class="col-sm-4 col-form-label">Kode</label>
                            <div class="col-sm-8">
                                <input id="a1" type="text" class="form-control" v-model="code" readonly>
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label class="col-sm-4 col-form-label" for="a2">Nama Toko</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" id="a2" v-model="name">
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label class="col-sm-4 col-form-label" for="a3">Nomor Telpon</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" id="a3" v-model="phone">
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label class="col-sm-4 col-form-label" for="a4">Alamat Lengkap</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" type="text" id="a4" v-model="address"></textarea>
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label class="col-sm-4 col-form-label" for="a5">Keterangan</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" type="text" id="a5" v-model="description"></textarea>
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="s1" class="col-sm-4 col-form-label">Status</label>
                            <div class="col-sm-8">
                                <select id="s1" class="custom-select" v-model="status">
                                    <option v-for="status in statuses" :style="'color:'+status.bgcolor">@{{status.name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-5 mt-2 progress" :style="(loading)?'min-width: 100% !important':'display: none'">
                            <div class="progress-bar progress-bar-animated bg-primary progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                        </div>

                        <div class="offset-sm-4 col-sm-8 pt-3" :style="(!loading)?'':'display: none'">
                            <button @click="cancel()" class="btn btn-outline-secondary float-left mb-5">
                                <i class="fa fa-times"></i> BATAL
                            </button>
                            <input type="hidden" v-model="uuidedit">
                            <button @click="save()" class="btn btn-outline-primary float-right mb-5">
                                <i class="fa fa-check"></i> SIMPAN
                            </button>
                        </div>
                    </div>

                    <div v-if="(labl=='EDIT')" class="col-md-8 col-xs-12">
                        <div class="mt-4">ORANG DI TOKO INI</div>
                        <div class="table-responsive">
                            <button v-if="usersStore.length==0" @click="showUsers()" class="btn btn-outline-primary mt-4">
                                <i class="fa fa-users"></i>Lihat
                            </button>
                            <table v-if="usersStore.length>=1" class="align-middle pb-3 mt-4 table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Telp</th>
                                        <th>Status</th>
                                        <th style="width:10px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, index) in usersStore">
                                        <td>@{{(index+1)}}</td>
                                        <td>@{{row.nama}}</td>
                                        <td>@{{row.mail}}</td>
                                        <td>@{{row.telp}}</td>
                                        <td>
                                            <div :style="'background:'+row.sttsbgcolor+';color:'+row.sttsfontcolor" class="badge badge-light">@{{row.stts}}</div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-secondary" onclick="czzx()">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="btnadd" style="position:fixed;bottom:0;right:0;">
            <a @click="addNew()" href="javascript:void(0);" class="btn">
                <div class="swatch-holder swatch-holder-lg bg-night-sky bg-primary">
                    <i class="fa fa-plus fa-2x icon-gradient bg-heavy-rain"></i>
                </div>
            </a>
        </div>
    </div>
    
@endsection
