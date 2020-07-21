<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="utf-8">

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>Get Similiar Product</title>

        @if(isset($filecss))
            <link rel="stylesheet" href="{{url(sha1('file'.date('mY'))).'.css?'.$filecss}}">
        @endif

    </head>

    <body>
        <div class="{{$app}}">
            <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
                <div class="container">
                    <div class="navbar-brand">
                        <div class="input-group ml-3">
                            <div class="custom-file">
                                <input type="text" class="form-control" v-model="myword">
                            </div>
                            <div class="input-group-append mt-1 mb-1">
                                <button type="submit" class="btn btn-success" @click="btnCariProduct()">Cari</button>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="mt-5">
                <div class="row">
                    <div class="col-12 mt-5">
                        <div class="container">
                            
                            <div v-for="row in mytables" :class="'card '+ ((row.exist) ? 'text-white bg-secondary':'border-primary') + ' mb-2' ">
                                <div class="card-header">@{{row.code}}</div>
                                <div class="card-body">
                                    <p class="card-text">
                                        @{{row.name}}
                                    </p>
                                    <div v-if="!row.exist" class="d-flex w-100 justify-content-between">
                                        <div>&nbsp;</div>
                                        <img v-if="row.loading" src="images/loadingthree.gif" height="10">
                                        <button v-else @click="btnSave(row)" type="button" class="btn btn-outline-secondary btn-sm">save</button>
                                    </div>
                                </div>
                            </div>

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