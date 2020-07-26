@extends('layouts.admin')

@section('title', $data->store_name . ' Management' )

@section('content')
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-users icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>Master Pengguna
                    <div class="page-title-subheading">
                        Master Pengguna
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row {{$app}}">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">Master Pengguna
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
                                <th>Nama</th>
                                <th>Alias</th>
                                <th>User</th>
                                <th>Toko</th>
                                <th style="width:20px">Status</th>
                                <th>Modif</th>
                                <th style="width:60px">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div :style="(showform) ? '':'display:none'">
                    <div class="offset-md-3 col-md-6 col-xs-12">
                        <h3 class="mt-3">@{{labl}}</h3>
                        <div class="position-relative row form-group">
                            <label for="a1" class="col-sm-4 col-form-label text-right">Title</label>
                            <div class="col-sm-8">
                                <select id="a1" class="custom-select" v-model="title">
                                    <option value="0">Mr.</option>
                                    <option value="1">Mrs.</option>
                                    <option value="2">Ms.</option>
                                </select>
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a2" class="col-sm-4 col-form-label text-right">Nama Depan</label>
                            <div class="col-sm-8">
                                <input id="a2" type="text" class="form-control" v-model="fname">
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a3" class="col-sm-4 col-form-label text-right">Nama Belakang</label>
                            <div class="col-sm-8">
                                <input id="a3" type="text" class="form-control" v-model="lname">
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a4" class="col-sm-4 col-form-label text-right">Alias</label>
                            <div class="col-sm-8">
                                <input id="a4" type="text" class="form-control" v-model="alias">
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a5" class="col-sm-4 col-form-label text-right">Username</label>
                            <div class="col-sm-8">
                                <input id="a5" type="text" class="form-control" v-model="username">
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a6" class="col-sm-4 col-form-label text-right">Email</label>
                            <div class="col-sm-8">
                                <input id="a6" type="email" class="form-control" v-model="email">
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a7" class="col-sm-4 col-form-label text-right">Phone</label>
                            <div class="col-sm-8">
                                <input id="a7" type="text" class="form-control" v-model="phone">
                            </div>
                        </div>

                        <div class="position-relative row form-group">
                            <label for="r1" class="col-sm-4 col-form-label text-right">Status</label>
                            <div class="col-sm-8">
                                <select id="r1" class="custom-select" v-model="status">
                                    <option v-for="status in statuses" :style="'color:'+status.bgcolor">@{{status.name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="position-relative row form-group">
                            <label for="s1" class="col-sm-4 col-form-label text-right">Di Toko</label>
                            <div class="col-sm-8">
                                <select id="s1" class="form-control" v-model="store">
                                    <option v-for="store in stores" :value="store.uuid">@{{store.name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-5 mt-2 progress" :style="(loading)?'min-width: 100% !important':'display: none'">
                            <div class="progress-bar progress-bar-animated bg-primary progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                        </div>

                        <div class="offset-sm-4 col-sm-8 pt-2" :style="(!loading)?'':'display: none'">
                            <button @click="cancel()" class="btn btn-outline-secondary float-left mb-5">
                                <i class="fa fa-times"></i> BATAL
                            </button>
                            <input type="hidden" v-model="uuidedit">
                            <button @click="save()" class="btn btn-outline-primary float-right mb-5">
                                <i class="fa fa-check"></i> SIMPAN
                            </button>
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
