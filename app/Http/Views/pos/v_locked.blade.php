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
    
        <div class="d-flex justify-content-center {{$app}} mt-5">

            <div style="width:320px">
                <h5 class="text-center mb-3 text-primary" v-html="placeholder()">&nbsp;</h5>
                <p class="lead text-center">{{$mystore->name}}</p>
                <div class="card p-5">
                    <div class="text-center row">
                        <div class="col-4 mb-4">
                            <button type="button" class="btn btn-outline-primary btn-lg btn-block mb-1" @click="btnClick(1)"><b>1</b></button>
                        </div>
                        <div class="col-4 mb-4">
                            <button type="button" class="btn btn-outline-primary btn-lg btn-block mb-1" @click="btnClick(2)"><b>2</b></button>
                        </div>
                        <div class="col-4 mb-4">
                            <button type="button" class="btn btn-outline-primary btn-lg btn-block mb-1" @click="btnClick(3)"><b>3</b></button>
                        </div>
                        
                        <div class="col-4 mb-4">
                            <button type="button" class="btn btn-outline-primary btn-lg btn-block mb-1" @click="btnClick(4)"><b>4</b></button>
                        </div>
                        <div class="col-4 mb-4">
                            <button type="button" class="btn btn-outline-primary btn-lg btn-block mb-1" @click="btnClick(5)"><b>5</b></button>
                        </div>
                        <div class="col-4 mb-4">
                            <button type="button" class="btn btn-outline-primary btn-lg btn-block mb-1" @click="btnClick(6)"><b>6</b></button>
                        </div>

                        <div class="col-4 mb-4">
                            <button type="button" class="btn btn-outline-primary btn-lg btn-block mb-1" @click="btnClick(7)"><b>7</b></button>
                        </div>
                        <div class="col-4 mb-4">
                            <button type="button" class="btn btn-outline-primary btn-lg btn-block mb-1" @click="btnClick(8)"><b>8</b></button>
                        </div>
                        <div class="col-4 mb-4">
                            <button type="button" class="btn btn-outline-primary btn-lg btn-block mb-1" @click="btnClick(9)"><b>9</b></button>
                        </div>

                        <div class="col-4">
                            <button type="button" class="btn btn-warning btn-lg btn-block" @click="btnClick('<')"><i class="fa fa-arrow-left"></i></button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-primary btn-lg btn-block" @click="btnClick(0)"><b>0</b></button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-danger btn-lg btn-block" @click="btnClick('c')">C</button>
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