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

        <style>
            .footer {
                position: fixed;
                left: 0;
                bottom: 0;
                width: 100%;
            }
        </style>

    </head>

    <body>
        <div class="{{$app}}">
            
            <div :class="(pagePayData) ? 'd-none':''">
                <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
                    <div class="container">
                        <div class="navbar-brand">
                            <div class="input-group ml-3">
                                <div class="custom-file">
                                    <input type="text" class="form-control" id="txtcode" v-model="UPC">
                                </div>
                                <div class="input-group-append mt-1 mb-1">
                                    <button type="submit" class="btn btn-success" @click="btnCekProduct()">Cek</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="container">

                    <div v-if="detailtransaksi.length==0" class="page-header" id="banner">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h1>{{strtoupper($mystore->name)}}</h1>
                                <p class="lead">@{{timenow}}</p>
                            </div>
                        </div>
                    </div>

                    <div v-if="detailtransaksi.length>0" class="row">
                        <div class="col-12">
                            <div class="bs-component">
                                <div class="list-group">

                                    <div v-for="row in detailtransaksi" :class="'list-group-item list-group-item-action flex-column align-items-start '+row.bg">
                                        <h5 class="mb-1">@{{row.name}}</h5>
                                        <small>@{{row.code}}</small>
                                        <div class="d-flex w-100 justify-content-between">
                                            <div data-toggle="buttons" class="btn-group btn-group-toggle">
                                                <label class="btn btn-warning" @click="btnqtycontrol(row.uuid,'-')">
                                                    <input type="checkbox"> <b>-</b>&nbsp;
                                                </label>
                                                <p :class="'mb-1 mr-3 ml-3 mt-1 '+row.bg">
                                                    <label :class="(beatheart===row.uuid) ? 'beat-heart shadow-red':''">
                                                        <b class="text-danger">@{{row.qty}}</b>
                                                    </label>
                                                    @{{row.unit_name}}
                                                </p>
                                                <label class="btn btn-success" @click="btnqtycontrol(row.uuid,'+')">
                                                    <input type="checkbox"> <b>+</b>
                                                </label>
                                            </div>
                                            <p class="mb-1">
                                                <b :class="(row.price<=0) ? 'blink-me text-danger shadow-red':'text-primary'">@{{numThousans(row.price * row.qty)}}</b>
                                                <small v-html="discountHits(row.price, row.qty, row.discount)"></small>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <div class="card bg-light mt-4">
                                            <div class="card-header">KALKULASI</div>
                                            <div class="card-body">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <span>Sub Total</span>
                                                    <h5 class="text-primary"><b>@{{numThousans(subtotal)}}</b></h5>
                                                </div>
                                                <hr class="mt-2 mb-2" />
                                                <div class="d-flex w-100 justify-content-between">
                                                    <span>Discount</span>
                                                    <h5 class="text-primary"><b>@{{numThousans(discount)}}</b></h5>
                                                </div>
                                                <hr class="mt-2 mb-2" />
                                                <div class="d-flex w-100 justify-content-between">
                                                    <span>TOTAL</span>
                                                    <h5 class="text-primary"><b>@{{numThousans(totaltrx)}}</b></h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-5 mt-4">
                                            <button v-if="(detailtransaksi.length>0)" type="button" class="btn btn-success btn-lg btn-block" @click="pagePay(true)">BAYAR</button>
                                            <button v-else type="button" class="btn btn-secondary btn-lg btn-block">BAYAR</button>
                                            <button type="button" class="btn btn-secondary btn-sm btn-block mt-4" @click="btnreset()">RESET</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="navbar footer navbar-dark bg-primary">
                        <div class="container pt-2 pb-1">
                            <div class="d-flex w-100 justify-content-between text-white">
                                <span>TOTAL</span>
                                <h5 class="text-white"><b>@{{numThousans(totaltrx)}}</b></h5>
                            </div>
                        </div>
                    </div>

                    <button id="showmodal" type="button" data-toggle="modal" data-target=".opt-eceran" class="d-none"></button>

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
                </div>
            </div>

            <div :class="(pagePayData) ? '':'d-none'">
                <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-light">
                    <div class="navbar-brand vw-100">
                        <button class="btn btn-link" @click="pagePay(false)"><i class="fa fa-arrow-left"></i></button> {{strtoupper($mystore->name)}}
                        <div class="float-right"><small>{{$user->nameshow}}</small></div>
                    </div>
                </nav>
                <div class="container">
                    <div class="row">
                        <div class="col-12">
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
                        </div>
                        <div class="col-12 mt-4">
                            <div class="card p-4">
                                <div class="text-center row">
                                    <div class="col-6 mb-4">
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
                                    <div class="col-6 mb-4">
                                        <button type="button" class="btn btn-success btn-lg btn-block" @click="btnkeypadnum('p')"><b>PAS</b></button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(1)"><b>1</b></button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(2)"><b>2</b></button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(3)"><b>3</b></button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(4)"><b>4</b></button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-info btn-lg btn-block" @click="btnkeypadnum(5)"><b>5</b></button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(6)"><b>6</b></button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(7)"><b>7</b></button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(8)"><b>8</b></button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnkeypadnum(9)"><b>9</b></button>
                                    </div>
                                    
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-danger btn-lg btn-block" @click="btnkeypadnum('<')"><</button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-outline-warning btn-lg btn-block" @click="btnkeypadnum(0)"><b>0</b></button>
                                    </div>
                                    <div class="col-4 mb-4">
                                        <button type="button" class="btn btn-warning btn-lg btn-block" @click="btnkeypadnum('000')">000</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button v-if="tempuniqid!==uniqid" type="button" class="btn btn-success btn-lg btn-block" :data-dismiss="(changedue>=0) ? 'modal':''" @click="saveAndPrint()">SIMPAN & CETAK</button>
                            <button v-if="tempuniqid===uniqid" type="button" class="btn btn-success btn-lg btn-block" disabled>SIMPAN & CETAK</button>
                        </div>
                        <div class="col-6 mt-4 mb-3">
                            <button v-if="tempuniqid!==uniqid" type="button" class="btn btn-dark btn-sm btn-block" :data-dismiss="(changedue>=0) ? 'modal':''" @click="onlySave()">HANYA SIMPAN</button>
                            <button v-if="tempuniqid===uniqid" type="button" class="btn btn-dark btn-sm btn-block" disabled>HANYA SIMPAN</button>
                        </div>
                        <div class="col-6 mt-4 mb-3">
                            <button v-if="tempuniqid===uniqid" type="button" class="btn btn-dark btn-sm btn-block" :data-dismiss="(changedue>=0) ? 'modal':''" @click="onlyPrint()">HANYA CETAK</button>
                            <button v-if="tempuniqid!==uniqid" type="button" class="btn btn-dark btn-sm btn-block" disabled>HANYA CETAK</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @if(isset($filejs))
            <script type="application/javascript" src="{{url(sha1('file'.date('mjD'))).'.js?'.$filejs}}"></script>
        @endif

    </body>

</html>