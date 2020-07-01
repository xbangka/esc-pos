<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="utf-8">

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Generate UUID</title>

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
                
                <div class="col-6">
                    <div class="card border-primary mb-3">
                        <div class="card-header text-center">Response</div>
                        <div class="card-body">
                            <h1 class="text-center text-primary card-title">
                                <strong>@{{resultresponse}}</strong>
                            </h1>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @if(isset($filejs))
            <script src="{{url(sha1('file'.date('mjD'))).'.js?'.$filejs}}"></script>
        @endif

    </body>

</html>