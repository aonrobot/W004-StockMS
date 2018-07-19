<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- API Token -->
    <meta name="api-token" content="{{ !empty(Session::get('api-token')) ? Session::get('api-token') : 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImQwMDFjY2EyMzE1ZmZiZGFhNjgzYzg5YjZlNTc4NWVjMzZiZjZiNDA3MWU1YWE1NjJkNTlhMzkxMTBmMWNmMDQ5NGJjMmFjYmU5YWY0NjA0In0.eyJhdWQiOiIxIiwianRpIjoiZDAwMWNjYTIzMTVmZmJkYWE2ODNjODliNmU1Nzg1ZWMzNmJmNmI0MDcxZTVhYTU2MmQ1OWEzOTExMGYxY2YwNDk0YmMyYWNiZTlhZjQ2MDQiLCJpYXQiOjE1MzE5NzAyMTgsIm5iZiI6MTUzMTk3MDIxOCwiZXhwIjoxNTYzNTA2MjE4LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.NhqMggIiDfa99oOY509P8sm9H79Zd86EVShKaUVWkPhwPT_VCcqimmrHRr925TSmTXALTKXVal9eWjAg9HUe4vkwC5pDjxsrhIKPtoPgDEhFxEOqmaoomqNYpHBsLXv6ggHv4H0fmnkhuD3gNdwLLex_8h2CLHKm5zrwTxySnJskkKcEK2vDxB98SnLWCDx2weLv8K4Nk5fhKTZ9YJe93s3TtZbmWVV4RVSsz07Dzx09YdM0Czdg5831pHY5_P9d8W-0QyiymV8D4rTeGGq2BrXR02Zlu861Nrz14Kdr0-mGra-W8ej93gmb-uJBiycA6UqtbeQ_J26h9H7PRQ6wnEcdGWcV70iXTBdjjc6MWRkt0Z4YZUN4sl9tT0-J6pq8Ia-scnsYAYVyuDukmZ0BSsyRN5g6dp3ChXoTpJTs26sN2gknhTIp8uLK97NL9xmYi4bUA81aqNSdbVTGyKg0dPVDRs0P_i_Bfuw77Fbs-vl-paNSHaaHMsunXDfrjXoLLu7WJmrrmo77e0y28iunjc-kjQsp8xKdPUSnFDav0H4SZO-e415wtAj-UoUjAPLgQLeFJtBn5M_L-OT8oz5AAtkhporvOTydortM4FD5TKpBFfOns8VoRnKEkcsYG1PCakDjcTzTCc57QIfBYf6b9xFqYGAvYkdKvihHDB7MxrY' }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" ></script>
    

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Kanit:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4"
        crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Custom style -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }} "/>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/odometer/css/odometer-theme-default.css') }}">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                <!-- <i class="fa fa-dog"></i> {{ 'สว่างแดนดินเจริญดีเซรามิค' }}<br> -->
                <small class="ml-2">Stock Management System</small>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <!-- Navbar: Menu -->
                    @guest
                    @else
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}"><i class="fa fa-home"></i> Home <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('report') }}"><i class="fa fa-book"></i> Report</a>
                        </li>
                    </ul>
                    @endguest
                    
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li> -->
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>


<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>
<script src="{{ asset('vendor/odometer/js/odometer.min.js') }}"></script>
<script src="{{ asset('js/main.js') }}"></script>

</html>

