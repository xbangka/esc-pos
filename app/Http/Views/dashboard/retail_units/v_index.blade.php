@extends('layouts.admin')

@section('title', $data->store_name . ' Management' )

@section('content')
    
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-leaf icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>Satuan Produk
                    <div class="page-title-subheading">
                        Satuan Produk
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row {{$app}}">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">Satuan Produk
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
                            <label for="a1" class="col-sm-4 col-form-label mt-1">Kode</label>
                            <div class="col-sm-8">
                                <input id="a1" type="text" class="form-control" v-model="code">
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a2" class="col-sm-4 col-form-label mt-1">Name Satuan</label>
                            <div class="col-sm-8">
                                <input id="a2" type="text" class="form-control" v-model="name">
                            </div>
                        </div>

                        <div class="mb-5 mt-2 progress" :style="(loading)?'min-width: 100% !important':'display: none'">
                            <div class="progress-bar progress-bar-animated bg-primary progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                        </div>

                        <div class="offset-sm-4 col-sm-8 pb-5 pt-2" :style="(!loading)?'':'display: none'">
                            <button @click="cancel()" class="btn btn-outline-secondary float-left">
                                <i class="fa fa-times"></i> BATAL
                            </button>
                            <input type="hidden" v-model="uuidedit">
                            <button @click="save()" class="btn btn-outline-primary float-right">
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
