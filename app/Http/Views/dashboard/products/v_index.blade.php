@extends('layouts.admin')

@section('title', $data->store_name . ' Management' )

@section('content')
    
    <div class="app-page-title" style="background-image: url(images/rak-mini-market.png);background-repeat: no-repeat;background-size: cover;background-position-y: center;background-position-x: right;">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-ticket icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>Master Produk
                    <div class="page-title-subheading">
                        Master Produk
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row {{$app}}">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">Master Produk
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
                                <th>Barcode</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Modif</th>
                                <th style="width:40px">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div :style="(showform) ? '':'display:none'" class="row pl-3 pr-3">
                    <div class="offset-md-3 col-md-6 col-xs-12">
                        <h3 class="mt-3">@{{labl}}</h3>
                        <div class="position-relative row form-group">
                            <label for="a1" class="col-sm-4 col-form-label mt-1">Kode</label>
                            <div class="col-sm-8">
                                <input id="a1" type="text" class="form-control" v-model="code">
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a2" class="col-sm-4 col-form-label mt-1">Nama Lengkap</label>
                            <div class="col-sm-8">
                                <input id="a2" type="text" class="form-control" v-model="name">
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a3" class="col-sm-4 col-form-label mt-1">Nama 20char</label>
                            <div class="col-sm-8">
                                <input id="a3" type="text" class="form-control" v-model="sname">
                            </div>
                        </div>

                        <div class="position-relative row form-group">
                            <label for="s1" class="col-sm-4 col-form-label">Kategori</label>
                            <div class="col-sm-8">
                                <select id="s1" class="custom-select" v-model="category">
                                    <option v-for="cate in categories">@{{cate.name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="position-relative row form-group">
                            <label class="col-sm-4 col-form-label" for="a4">Keterangan</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" type="text" id="a4" v-model="description"></textarea>
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