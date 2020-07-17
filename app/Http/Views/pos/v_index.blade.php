<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="utf-8">

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>{{$mystore->name}}</title>

        @if(isset($filecss))
            <link rel="stylesheet" href="{{url(sha1('file'.date('mY'))).'.css?'.$filecss}}">
        @endif

    </head>

    <body>
        <div class="{{$app}}">
            
            <div class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
                <div class="container" style="max-width: none !important;">
                    
                    <div class="collapse navbar-collapse">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link">{{$mystore->name}}</a>
                            </li>
                            <li class="nav-item">
                                <div class="input-group ml-4">
                                    <div class="custom-file">
                                        <input type="text" class="form-control" id="txtcode" v-model="UPC">
                                    </div>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-success" @click="btnCekProduct()">Cek</button>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item ml-5">
                                <a class="nav-link">@{{timenow}}</a>
                            </li>
                        </ul>
                        <button id="showmodal" type="button" data-toggle="modal" data-target=".opt-eceran" class="d-none"></button>

                        <h2 class="text-light ml-auto">
                            <small>Total :</small>
                            <strong><span id="transakstion_total">@{{numThousans(totaltrx)}}</span></strong>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="container" style="max-width: none !important;">

                <div class="row">
                    <div class="col-6 mt-3">
                        <h2>
                            Detail Transaksi
                            <button type="button" class="btn btn-secondary btn-sm" @click="btnreset()">RESET</button>
                        </h2>
                    </div>
                    <div class="col-6 text-right mb-3">
                        <button type="button" :data-toggle="(detailtransaksi.length==0) ? '':'modal'" :data-target="(detailtransaksi.length==0) ? '':'.source-modal'" class="btn btn-success btn-lg">&nbsp;&nbsp; BAYAR &nbsp;&nbsp;</button>
                    </div>
                    <div class="col-12">
                        
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th>CODE</th>
                                    <th class="text-center">NAMA_BARANG</th>
                                    <th class="text-center">SATUAN</th>
                                    <th class="text-center" width="200px">HARGA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in detailtransaksi" :class="row.bg">
                                    <td>
                                        <a href="javascript:;" @click="btnremoveitem(row.uuid)">
                                            <span class="badge badge-pill badge-secondary pb-2">x</span> 
                                        </a>
                                        @{{row.code}}
                                    </td>
                                    <td>@{{row.name}}</td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span :class="row.bg"><label :class="(beatheart===row.uuid) ? 'beat-heart shadow-red':''"><b class="text-danger">@{{row.qty}}</b></label> @{{row.unit_name}}</span>
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-warning" @click="btnqtycontrol(row.uuid,'-')">
                                                    <input type="checkbox"> <b>-</b>&nbsp;
                                                </label>
                                                &nbsp;
                                                <label class="btn btn-success" @click="btnqtycontrol(row.uuid,'+')">
                                                    <input type="checkbox"> <b>+</b>
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <span :class="(row.price<=0) ? 'blink-me text-danger shadow-red':''">@{{numThousans(row.price * row.qty)}}</span>
                                        <small v-html="discountHits(row.price, row.qty, row.discount)"></small>
                                    </td>
                                </tr>
                                <tr v-if="detailtransaksi.length==0"><td>_ _ _ _ _ _ _</td><td>_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</td><td>_ _ _ _</td><td class="text-right">_ _ _ _ _</td></tr>
                            </tbody>
                            <tfoot>
                                <tr class="text-right">
                                    <th colspan="3">Sub Total</th>
                                    <th class="bg-primary text-white">@{{numThousans(subtotal)}}</th>
                                </tr>
                                <tr class="text-right">
                                    <th colspan="3">Discount</th>
                                    <th class="bg-primary text-white">@{{numThousans(discount)}}</th>
                                </tr>
                                <tr class="text-right">
                                    <th colspan="3">TOTAL</th>
                                    <th class="bg-primary text-white">@{{numThousans(totaltrx)}}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mt-1 text-center">
                        <a href="javascript:;" @click="btnUpdateDataLocal()">
                            Update <span v-html="numThousans(htmlCountDataLocal())"></span> Data Lokal 
                        </a>&nbsp; | &nbsp;
                        <a href="javascript:;" @click="btnLogout()">
                            Logout
                        </a>
                    </div>
                </div>

            </div>

            <div class="modal fade opt-eceran">
                <div class="modal-dialog">
                    <div class="modal-content border-success">
                        <div class="modal-header card-header">
                            <h5 class="modal-title">@{{responses[0].name}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-right">
                                <small>Code</small> : <span class="badge badge-light">@{{responses[0].code}}</span>
                                <br>
                                <small>Kategori</small> : <span class="badge badge-secondary">@{{responses[0].category}}</span>
                            </p>
                            
                            <div class="list-group">
                                <a v-for="row in responses" @click="btnvarianitem(row.uuid)" href="javascript:;" data-dismiss="modal" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    1 @{{row.unit_name}}
                                    <div><b class=" text-primary">@{{numThousans(row.price)}}</b> <small v-html="discountHitsModal(row.discount)"></small></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal source-modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-5">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            TAGIHAN
                                            <span style="font-family:'courier new';font-size:14pt"><strong>@{{numThousans(totaltrx)}}</strong></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            BAYAR
                                            <h4 style="font-family:'courier new';font-size:20pt" class="text-primary mb-0"><strong>@{{numThousans(cash)}}</strong></h4>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            KEMBALIAN
                                            <h4 style="font-family:'courier new';font-size:20pt" :class="(changedue<0) ? 'blink-me text-danger mb-0' : 'mb-0'"><strong>@{{numThousans(changedue)}}</strong></h4>
                                        </li>
                                    </ul>
                                    <div class="mt-3">
                                        <i class="text-muted text-right" v-html="changedue_composition"></i>
                                    </div>
                                </div>
                                <div class="col-7 pl-0">
                                    <div class="card p-4">
                                        <div class="text-center row">
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(1)"><b>1</b></button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(2)"><b>2</b></button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(3)"><b>3</b></button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <div class="dropdown">
                                                    <button onclick="showdropdown()" class="dropbtn btn btn-warning btn-lg btn-block">RP</button>
                                                    <div id="myDropdown" class="dropdown-content">
                                                        <a href="javascript:;" @click="btnkeypadnum('10K')">10.000</a>
                                                        <a href="javascript:;" @click="btnkeypadnum('20K')">20.000</a>
                                                        <a href="javascript:;" @click="btnkeypadnum('50K')">50.000</a>
                                                        <a href="javascript:;" @click="btnkeypadnum('100K')">100.000</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(4)"><b>4</b></button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-info btn-lg btn-block" @click="btnkeypadnum(5)"><b>5</b></button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(6)"><b>6</b></button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                &nbsp;
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(7)"><b>7</b></button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(8)"><b>8</b></button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(9)"><b>9</b></button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-success btn-lg btn-block" @click="btnkeypadnum('p')"><b>PAS</b></button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-danger btn-lg btn-block" @click="btnkeypadnum('<')"><</button>
                                            </div>
                                            <div class="col-3 mb-4">
                                                <button type="button" class="btn btn-outline-warning btn-lg btn-block" @click="btnkeypadnum(0)"><b>0</b></button>
                                            </div>
                                            <div class="col-6 mb-4">
                                                <button type="button" class="btn btn-warning btn-lg btn-block" @click="btnkeypadnum('000')">000</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content:space-between">
                            <button v-if="tempuniqid!==uniqid" type="button" class="btn btn-dark btn-sm" :data-dismiss="(changedue>=0) ? 'modal':''" @click="onlySave()">HANYA SIMPAN</button>
                            <button v-if="tempuniqid===uniqid" type="button" class="btn btn-dark btn-sm" disabled>HANYA SIMPAN</button>

                            <button v-if="tempuniqid===uniqid" type="button" class="btn btn-dark btn-sm" :data-dismiss="(changedue>=0) ? 'modal':''" @click="onlyPrint()">HANYA CETAK</button>
                            <button v-if="tempuniqid!==uniqid" type="button" class="btn btn-dark btn-sm" disabled>HANYA CETAK</button>

                            <button v-if="tempuniqid!==uniqid" type="button" class="btn btn-success" :data-dismiss="(changedue>=0) ? 'modal':''" @click="saveAndPrint()">SIMPAN & CETAK</button>
                            <button v-if="tempuniqid===uniqid" type="button" class="btn btn-success" disabled>SIMPAN & CETAK</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="section-to-print" align="center" :class="(cash<=0) ? 'modal':''" style="font-family:'Letter Gothic Std';font-size:12pt">
                {{strtoupper($mystore->name)}}
                <br>
                ---------------------------------
                <table style="width:100%" border="0">
                    <tbody>
                        <tr>
                            <td class="text-left">@{{datetimestuk}}</td>
                            <td class="text-right">{{$user->nameshow}}</td>
                        </tr>
                    </tbody>
                </table>
                ---------------------------------
                <div width="345px" align="justify">
                    <table style="width:100%" border="0">
                        <tbody>
                            <tr v-for="row in detailtransaksi">
                                <td>@{{row.alias}}</td>
                                <td class="text-center">
                                    @{{row.qty + row.unit}}
                                </td>
                                <td class="text-right">
                                    @{{numThousans(row.price * row.qty)}}
                                </td>
                            </tr>
                            <tr v-if="discount>=1" class="text-right">
                                <td colspan="3">-----------------</td>
                            </tr>
                            <tr v-if="discount>=1" class="text-right">
                                <td colspan="2">SUB TOTAL :</td>
                                <td>@{{numThousans(subtotal)}}</td>
                            </tr>
                            <tr v-if="discount>=1" class="text-right">
                                <td colspan="2">DISCOUNT :</td>
                                <td>@{{numThousans(discount)}}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">-----------------</td>
                            </tr>
                            <tr class="text-right">
                                <td colspan="2">TOTAL :</td>
                                <td>@{{numThousans(totaltrx)}}</td>
                            </tr>
                            <tr class="text-right">
                                <td colspan="2">BAYAR :</td>
                                <td>@{{numThousans(cash)}}</td>
                            </tr>
                            <tr v-if="changedue>=1" class="text-right">
                                <td colspan="2">KEMBALI :</td>
                                <td>@{{numThousans(changedue)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        @if(isset($filejs))
            <script type="application/javascript" src="{{url(sha1('file'.date('mjD'))).'.js?'.$filejs}}"></script>
        @endif

    </body>

</html>