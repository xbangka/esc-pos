<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="utf-8">

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Get Price Product</title>

        @if(isset($filecss))
            <link rel="stylesheet" href="{{url(sha1('file'.date('mY'))).'.css?'.$filecss}}">
        @endif

    </head>

    <body>
    
        <div class="d-flex justify-content-center {{$app}} mt-2">

            <div style="width:910px" class="row">
                <div class="col-12 mt-4 mb-3" v-if="!loading">
                    <div style="width:265px" class="text-center">
                        <button type="button" class="btn btn-primary btn-lg btn-block" @click="recursivex()">
                            Start
                        </button>
                    </div>
                </div>
                <div class="col-12 mt-4 mb-3" v-if="loading">
                    <div class="text-center mb-3">
                        <img src="{{asset('images/loading.gif')}}">
                    </div>
                </div>
                <p>&nbsp;</p>
                <div class="col-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th>CODE</th>
                                <th class="text-center">NAMA_BARANG</th>
                                <th class="text-center">HARGA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in mytables">
                                <td>@{{row.barcode}}</td>
                                <td>@{{row.name}}</td>
                                <td class="text-right">
                                    <b v-if="row.price=='-'">
                                        <img src="images/loadingthree.gif">
                                    </b>
                                    <b v-else>
                                        @{{row.price}}
                                    </b>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 mt-4 mb-3">
                    <div style="width:265px" class="text-center">
                        <button type="button" class="btn btn-warning btn-lg btn-block" @click="clearTable()">
                            Clear Table
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($filejs))
            <script src="{{url(sha1('file'.date('mjD'))).'.js?'.$filejs}}"></script>
        @endif

    </body>

</html>