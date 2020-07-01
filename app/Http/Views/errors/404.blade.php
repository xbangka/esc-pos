<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>404</title>
    <link rel="stylesheet" href="{{asset('css/application.min.css')}}">
</head>

<body>
    <div class="mdl-layout mdl-js-layout is-small-screen not-found">
        <main class="mdl-layout__content">
            <div class="mdl-card mdl-card__login mdl-shadow--2dp">
                <div class="mdl-card__supporting-text color--dark-gray">
                    <div class="mdl-grid">
                        <div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-phone">
                            <span class="mdl-card__title-text text-color--smooth-gray">{{config('app.name')}}</span>
                        </div>
                        <div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-phone">
                            <span class="text--huge color-text--light-blue">404</span>
                        </div>
                        <div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-phone">
                            <span class="text--sorry text-color--white">Sorry, but there's nothing here</span>
                        </div>
                        <div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-phone">
                            <a href="{{url('')}}" class="mdl-button mdl-js-button color-text--light-blue pull-right">
                                Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="{{asset('js/material.min.js')}}"></script>
</body>

</html>