@extends('layouts.login')

@section('title', ucwords(str_replace('_',' ',config('app.name'))) . ' Management' )

@section('content')
    <div class="app-container app-theme-white fixed-header">
        <div class="app-main">
            <div class="app-main__outer">
                <div class="app-main__inner">
                    <div class="row">
                        <div class="offset-md-4 col-md-4 col-sm-12">
                            <div class="main-card pb-4 pt-5 card">
                                <div class="card-body {{$app}}">
                                    <h3 class="text-center mb-4">Login</h3>
                                    <div class="position-relative form-group">
                                        <label for="youremail">Email</label>
                                        <input 
                                            id          ="youremail" 
                                            v-model     ="email"
                                            type        ="email" 
                                            class       ="form-control" 
                                            autocomplete="off"
                                            autofocus   ="autofocus"
                                        >
                                    </div>
                                    <div class="position-relative form-group">
                                        <label for="password">Password</label>
                                        <input 
                                            id          ="password" 
                                            v-model     ="password"
                                            type        ="password" 
                                            class       ="form-control"
                                        >
                                    </div>
                                    <div v-if="loading" class="mt-4 mb-5 text-center">
                                        <img src="{{asset('images/loading.gif')}}">
                                    </div>
                                    <button v-else class="mt-4 mb-5 btn btn-primary btn-lg btn-block" v-on:click="login">LOGIN</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="mdl-layout mdl-js-layout color--gray is-small-screen login">
        <main class="mdl-layout__content {{$app}}">
            <div class="mdl-card mdl-card__login mdl-shadow--2dp">
                <div class="mdl-card__supporting-text color--dark-gray">
                    <div class="mdl-grid">
                        <div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-phone">
                            <span class="mdl-card__title-text text-color--smooth-gray">Login</span>
                        </div>
                        <div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-phone">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label full-size">
                                <input 
                                    id          ="youremail" 
                                    v-model     ="email"
                                    type        ="email" 
                                    class       ="mdl-textfield__input" 
                                    autocomplete="off"
                                    autofocus   ="autofocus"
                                >
                                <label class="mdl-textfield__label" for="youremail">Email</label>
                            </div>
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label full-size">
                                <input 
                                    id          ="password" 
                                    v-model     ="password"
                                    type        ="password" 
                                    class       ="mdl-textfield__input"
                                >
                                <label class="mdl-textfield__label" for="password">Password</label>
                            </div>
                        </div>
                        <div class="mdl-progress mdl-js-progress mdl-progress__indeterminate progress--colored-orange" :style="(loading)?'min-width: 100% !important':'display: none'"></div>
                        <div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-phone submit-cell" :style="(loading)?'display: none':''">
                            <div class="mdl-layout-spacer"></div>
                            <button class="mdl-button mdl-js-button mdl-button--raised color--light-blue" v-on:click="login">
                                LOGIN
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div> --}}

@endsection