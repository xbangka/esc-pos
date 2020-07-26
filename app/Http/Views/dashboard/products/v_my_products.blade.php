@extends('layouts.admin')

@section('title', $data->store_name . ' Management' )

@section('content')
    
    <div class="app-page-title" style="background-image: url(images/rak-mini-market.png);background-repeat: no-repeat;background-size: cover;background-position-y: center;background-position-x: right;">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-ticket icon-gradient bg-mean-fruit"></i>
                </div>
                <div>Produk ku
                    <div class="page-title-subheading">
                        Daftar barang yang ada di toko {{$data->store_name . ' / ' .$data->store_code}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row {{$app}}">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header">
                    <div v-if="div.showDetail.display" role="group" class="btn-group-sm btn-group mr-3">
                        <button @click="backToList()" class="btn btn-outline-secondary"><i class="fa fa-angle-left"></i> Kembali</button>
                    </div>
                    Produk ku
                    <div v-if="div.showTable" class="btn-actions-pane-right">
                        <div role="group" class="btn-group-sm btn-group">
                            <button @click="addNew()" class="btn btn-outline-secondary"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive pb-3" :style="(div.showTable) ? '':'display:none'">
                    <table id="dataTable" class="align-middle pb-3 table table-striped table-hover display responsive w-100">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Modif</th>
                                <th style="width:10px">Act</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div :style="(div.showDetail.display) ? '':'display:none'" class="row pl-3 pr-3">
                    <div class="col-md-6 col-xs-12">
                        <div class="row mt-4">
                            <div class="pb-3 col-md-12 col-xs-12">
                                <i class="fa fa-minus-square"></i> ITEM PRODUK
                                <div class="float-right">
                                    <button class="btn btn-outline-secondary btn-sm"><i class="fa fa-history"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="position-relative row form-group">
                            <div class="offset-2 offset-sm-4 col-6">
                                <div v-html="div.showDetail.svgHTML"></div>
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label class="col-4 col-form-label text-right">Barcode</label>
                            <div class="col-8">
                                <h5 class="text-primary">: @{{div.showDetail.code}}</h5>
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label class="col-4 col-form-label text-right">Nama Lengkap</label>
                            <label class="col-8 col-form-label text-primary">: @{{div.showDetail.name}}</label>
                        </div>
                        <div class="position-relative row form-group">
                            <label class="col-4 col-form-label text-right">Nama 20char</label>
                            <label class="col-8 col-form-label text-primary">: @{{div.showDetail.sname}}</label>
                        </div>

                        <div class="position-relative row form-group">
                            <label class="col-4 col-form-label text-right">Kategori</label>
                            <label class="col-8 col-form-label text-primary">: @{{div.showDetail.category}}</label>
                        </div>

                        <div class="position-relative row form-group">
                            <label class="col-4 col-form-label text-right">Keterangan</label>
                            <label class="col-8 col-form-label text-primary">: @{{div.showDetail.description}}</label>
                        </div>
                        <div class="offset-sm-4 col-sm-8 pt-3">
                            <button @click="backToList()" class="btn btn-outline-secondary mb-5">
                                <i class="fa fa-angle-left"></i> KEMBALI
                            </button>
                            <button v-if="div.showDetail.svgHTML!=''" class="btn btn-secondary mb-5" @click="printBarcode()">
                                <i class="fa fa-print"></i> PRINT BARCODE
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12">
                        <div class="mt-4 mb-4"><i class="fa fa-minus-square"></i> VARIASI HARGA</div>
                        <div v-if="!div.showDetail.formShow.display">
                            <div v-if="div.showDetail.loadingvariations" class="text-center mt-4 pt-5 mb-4 pb-5" v-html="loading"></div>
                            <div class="main-card mb-3 card" v-if="!div.showDetail.loadingvariations">
                                <ul class="list-group">
                                    <li v-for="va in div.showDetail.variations" class="list-group-item">
                                        <div class="widget-content p-0">
                                            <div class="widget-content-outer">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">1 @{{va.unit_name}}  <small>(@{{va.unit}})</small></div>
                                                        <small v-html="discountHitsModal(va.discounts)"></small>
                                                    </div>
                                                    <div class="widget-content-right text-right">
                                                        <div class="widget-numbers text-primary" :style="(va.status==1) ? '':'text-decoration:3px line-through red;color:#c2c4c6 !important'">@{{numThousans(va.price)}}</div>
                                                        <button class="btn btn-outline-link btn-sm" @click="formPrice(va)"><i class="fa fa-edit text-success"></i></button>
                                                        <button v-if="va.status==1" class="btn btn-outline-link btn-sm" @click="disablePrice(va.uuid)"><i class="fa fa-eye-slash text-warning"></i></button>
                                                        <button v-else class="btn btn-outline-link btn-sm" @click="enablePrice(va.uuid)"><i class="fa fa-eye text-info"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div v-if="div.showDetail.formShow.display">
                            <div class="row mt-4">
                                <div class="col-md-12 col-xs-12">
                                    @{{(div.showDetail.formShow.uuid=='') ? 'BARU':'EDIT'}}
                                    <div v-if="(div.showDetail.formShow.uuid!='')" class="pb-2 float-right">
                                        <button class="btn btn-outline-secondary btn-sm"><i class="fa fa-history"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="position-relative row form-group">
                                <div class="input-group col-sm-8 mb-3">
                                    <div class="input-group-prepend"><span class="input-group-text">1</span></div>
                                    <select class="custom-select" style="font-size:medium;font-weight:bold" v-model="div.showDetail.formShow.id_unit">
                                        <option v-for="unit in units" :value="unit.id" >@{{unit.name + ' ('+ unit.code +')'}}</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <input class="form-control text-right text-primary" type="text" style="font-size:x-large;font-weight:bold" v-model="div.showDetail.formShow.price">
                                </div>
                            </div>

                            <div class="mb-5 mt-2 progress" :style="(div.showDetail.formShow.loading)?'min-width: 100% !important':'display: none'">
                                <div class="progress-bar progress-bar-animated bg-primary progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                            </div>

                            <div class="offset-sm-4 col-sm-8 pt-3 text-right" :style="(!div.showDetail.formShow.loading)?'':'display: none'">
                                <button @click="cancelPrice()" class="btn btn-outline-secondary mb-5 mr-2">
                                    <i class="fa fa-times"></i> KEMBALI
                                </button>
                                <button @click="savePrice()" class="btn btn-outline-primary mb-5">
                                    <i class="fa fa-check"></i> SIMPAN
                                </button>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12 col-xs-12">
                                    <i class="fa fa-minus-square"></i> DISCOUNTS
                                </div>
                            </div>
                            <ul class="list-group mb-1">
                                <li class="list-group-item" v-for="discount in div.showDetail.formShow.discounts">
                                    <div class="position-relative row form-group" style="margin-bottom:0">
                                        <label class="col-4 col-sm-3 col-form-label text-right pl-0">Nama :</label>
                                        <label v-if="typeof discount.mode === 'undefined'" class="col-8 col-sm-9 col-form-label text-primary pl-0 pr-0">
                                            <b :style="(discount.status==1) ? '':'text-decoration:1px line-through red;color:#666 !important'">
                                                @{{discount.event_name}}
                                            </b>
                                        </label>
                                        <div v-if="typeof discount.mode !== 'undefined'" class="col-8 col-sm-9 pl-0">
                                            <input type="text" class="form-control form-control-sm" v-model="discount.event_name">
                                        </div>
                                    </div>
                                    <div class="position-relative row form-group" style="margin-bottom:0">
                                        <label class="col-4 col-sm-3 col-form-label text-right pl-0">Potongan :</label>
                                        <label v-if="typeof discount.mode === 'undefined'" class="col-8 col-sm-9 col-form-label text-primary pl-0 pr-0">
                                            <b :style="(discount.status==1) ? '':'text-decoration:1px line-through red;color:#999 !important'">
                                                @{{(discount.value_type==1) ? 'Rp '+numThousans(parseInt(discount.value)) : numThousans(parseFloat(discount.value)) + ' %'}}
                                            </b>
                                        </label>
                                        <div v-if="typeof discount.mode !== 'undefined'" class="col-4 col-sm-4 pl-0">
                                            <input type="number" class="form-control form-control-sm" v-model="discount.value">
                                        </div>
                                        <div v-if="typeof discount.mode !== 'undefined'" class="col-4 col-sm-5 pl-0">
                                            <select class="form-control form-control-sm" v-model="discount.value_type">
                                                <option value="1">Amount</option>
                                                <option value="2">Persentage</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="position-relative row form-group" style="margin-bottom:0">
                                        <label class="col-4 col-sm-3 col-form-label text-right pl-0">Pembelian :</label>
                                        <label v-if="typeof discount.mode === 'undefined'" class="col-8 col-sm-9 col-form-label text-primary pl-0 pr-0">
                                            <b :style="(discount.status==1) ? '':'text-decoration:1px line-through red;color:#aaa !important'">
                                                @{{(discount.condition_qty_from==discount.condition_qty_to) ? discount.condition_qty_from + ' ' + div.showDetail.formShow.unit : discount.condition_qty_from + ' - ' + discount.condition_qty_to + ' ' + div.showDetail.formShow.unit}}
                                            </b>
                                        </label>
                                        <div v-if="typeof discount.mode !== 'undefined'" class="col-3 col-sm-4 pl-0 pr-0">
                                            <input type="number" class="form-control form-control-sm" v-model="discount.condition_qty_from">
                                        </div>
                                        <div v-if="typeof discount.mode !== 'undefined'" class="col-1 col-sm-1 pl-0 pr-0 text-center">
                                            -
                                        </div>
                                        <div v-if="typeof discount.mode !== 'undefined'" class="col-4 col-sm-4 pl-0">
                                            <input type="number" class="form-control form-control-sm" v-model="discount.condition_qty_to">
                                        </div>
                                    </div>
                                    <div class="position-relative row form-group" style="margin-bottom:0">
                                        <label class="col-4 col-sm-3 col-form-label text-right pl-0">Berlaku :</label>
                                        <label v-if="typeof discount.mode === 'undefined'" class="col-8 col-sm-9 col-form-label text-primary pl-0 pr-0">
                                            <b :style="(discount.status==1) ? '':'text-decoration:1px line-through red;color:#bbb !important'">
                                                @{{formatDateIna(discount.start_date) + ' - ' + formatDateIna(discount.end_date)}}
                                            </b>
                                        </label>
                                        <div v-if="typeof discount.mode !== 'undefined'" class="col-4 col-sm-4 pl-0">
                                            <input type="text" class="form-control form-control-sm" v-model="discount.start_date">
                                        </div>
                                        <div v-if="typeof discount.mode !== 'undefined'" class="col-4 col-sm-5 pl-0">
                                            <input type="text" class="form-control form-control-sm" v-model="discount.end_date">
                                        </div>
                                    </div>
                                    <div v-if="typeof discount.mode === 'undefined'">
                                        <button @click="discountEditMode(discount.uuid)" type="button" class="btn btn-link btn-sm">
                                            <i class="fa fa-edit mr-1 text-success"></i>
                                        </button>
                                        <button v-if="discount.status==1" @click="disableDiscount(discount.uuid)" type="button" class="btn btn-link btn-sm">
                                            <i class="fa fa-eye-slash text-warning"></i>
                                        </button>
                                        <button v-else @click="enableDiscount(discount.uuid)" type="button" class="btn btn-link btn-sm">
                                            <i class="fa fa-eye text-info"></i>
                                        </button>
                                    </div>
                                    <div class="text-right" v-if="typeof discount.mode !== 'undefined'">
                                        <button @click="cancelEditDiscount(discount.uuid)" class="btn btn-outline-secondary btn-sm mr-2">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button @click="saveDataDiscount(discount.uuid)" class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                            <button @click="addNewDiscount()" class="mb-5 btn btn-transition btn-outline-light btn-lg btn-block">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div :style="(div.showNewProduct.display) ? '':'display:none'" class="row pl-3 pr-3">
                    <div class="offset-md-3 col-md-6 col-xs-12">
                        <div class="row mt-4">
                            <div class="pb-3 col-md-12 col-xs-12">BARU</div>
                        </div>
                        
                        <div class="position-relative row form-group">
                            <label for="a1" class="col-sm-4 col-form-label">Barcode</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input id="a1" type="text" class="form-control" v-model="div.showNewProduct.code">
                                    <div class="input-group-append">
                                        <button @click="cekNewBarcode()" class="btn btn-outline-primary">Cek</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a2" class="col-sm-4 col-form-label">Nama Lengkap</label>
                            <div class="col-sm-8">
                                <input id="a2" type="text" class="form-control" v-model="div.showNewProduct.name">
                            </div>
                            <div class="col-12 offset-sm-4 col-sm-8">
                                <small class="text-danger" v-html="40 - div.showNewProduct.name.length"></small>
                            </div>
                        </div>
                        <div class="position-relative row form-group">
                            <label for="a3" class="col-sm-4 col-form-label">Nama 20char</label>
                            <div class="col-sm-8">
                                <input id="a3" type="text" class="form-control" v-model="div.showNewProduct.sname">
                            </div>
                            <div class="col-12 offset-sm-4 col-sm-8">
                                <small class="text-danger" v-html="(20 - div.showNewProduct.sname.length)>=0?(20 - div.showNewProduct.sname.length):0"></small>
                            </div>
                        </div>

                        <div class="position-relative row form-group">
                            <label for="s1" class="col-sm-4 col-form-label">Kategori</label>
                            <div class="col-sm-8">
                                <select id="s1" class="custom-select" v-model="div.showNewProduct.category">
                                    <option v-for="cate in categories" :value="cate.code">@{{cate.name}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="position-relative row form-group">
                            <label for="a4" class="col-sm-4 col-form-label">Keterangan</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" type="text" id="a4" v-model="div.showNewProduct.description"></textarea>
                            </div>
                        </div>

                        <div class="mb-5 mt-2 progress" :style="(div.showNewProduct.loading)?'min-width: 100% !important':'display: none'">
                            <div class="progress-bar progress-bar-animated bg-primary progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                        </div>

                        <div class="offset-sm-4 col-sm-8 pt-3" :style="(!div.showNewProduct.loading)?'':'display: none'">
                            <button @click="backToList()" class="btn btn-outline-secondary float-left mb-5">
                                <i class="fa fa-times"></i> BATAL
                            </button>
                            <button @click="saveNewProdact()" class="btn btn-outline-primary float-right mb-5">
                                <i class="fa fa-check"></i> SIMPAN
                            </button>
                        </div>
                    </div>
                </div>
                <div :style="(div.showDetail.display) ? '':'display:none'">
                    <div class="card-footer">
                        <button type="button" data-toggle="collapse" href="#q3" class="btn btn-outline-secondary" aria-expanded="true">Ref. Harga</button>
                    </div>
                    <div class="collapse mb-5" id="q3">
                        <table class="mb-0 table table-bordered">
                            <tbody>
                                <tr v-for="pr in div.showDetail.priceReferences">
                                    <th class="text-right"><b v-html="pr.source"></b></th>
                                    <td>Rp.@{{numThousans(pr.price)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="div.showDetail.display && !div.showDetail.formShow.display" style="position:fixed;bottom:0;right:0;">
            <a href="javascript:void(0);" class="btn" @click="formPrice()">
                <div class="swatch-holder swatch-holder-lg bg-night-sky bg-primary">
                    <i class="fa fa-plus fa-2x icon-gradient bg-heavy-rain"></i>
                </div>
            </a>
        </div>
    </div>
@endsection