<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="utf-8">

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Update Produk id</title>

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
                <div class="text-center mt-3">
                    <h1>@{{resultresponse}}</h1>
                </div>
            </div>
        </div>

        @if(isset($filejs))
            <script src="{{url(sha1('file'.date('mjD'))).'.js?'.$filejs}}"></script>
        @endif

    </body>

</html>