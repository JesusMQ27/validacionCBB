<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
	 <title>:: Sistema de Validaci&oacute;n::</title>
        <!-- Styles -->
        <!--<link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
        <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    </head>
    <body>
        <div id="app">            
            @if(session()->has('flash'))
            <div class="card bg-danger text-white shadow">
                <div class="card-body">
                    <div class="text-white-100 large">
                        {{ session('flash') }}
                    </div>
                </div>
            </div>
            @endif
            <main class="">
                @yield('content')
            </main>
            <!-- chinita-->
        </div>
    </body>
</html>
