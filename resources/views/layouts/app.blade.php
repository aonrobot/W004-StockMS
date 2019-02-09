<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @guest
    @else
    <!-- API Token -->
    <meta name="api-token" content="{{ !empty(Session::get('api-token')) ? Session::get('api-token') : App\Library\Token::getToken() }}">
    @endguest

    <title>Stock Management System</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" ></script>
    
    

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit:400,700">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4"
        crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    <!-- Custom style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }} "/>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.2.0/css/solid.css" integrity="sha384-wnAC7ln+XN0UKdcPvJvtqIH3jOjs9pnKnq9qX68ImXvOGz2JuFoEiCjT8jyZQX2z" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.2.0/css/regular.css" integrity="sha384-zkhEzh7td0PG30vxQk1D9liRKeizzot4eqkJ8gB3/I+mZ1rjgQk+BSt2F6rT2c+I" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.2.0/css/fontawesome.css" integrity="sha384-HbmWTHay9psM8qyzEKPc8odH4DsOuzdejtnr+OFtDmOcIVnhgReQ4GZBH7uwcjf6" crossorigin="anonymous">
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.css') }}"> -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/odometer/css/odometer-theme-default.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/busy-load/dist/app.min.css">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel nav-style">
            <div class="container" style="max-width: 1100px;">
                <a class="navbar-brand nav-style__logo" href="{{ url('/') }}">
                    @guest 
                        <i class="fa fa-dog"></i><p> Login</p>
                    @else
                        <i class="fa fa-dog"></i><p> {{ Auth::user()->name }}</p>
                    @endguest
                    <sub>Stock Management System</sub>
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
                            <a class="nav-link" href="{{ route('home') }}"><i class="fa fa-home" ></i> Home <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre id="navbarInvoice">
                                <i class="fa fa-book"></i> 
                                รายการขาย <span class="caret"></span>
                            </a>
                            <!-- Invoice Order Menu -->
                                <div class="dropdown-menu dropdown-menu__wrap" aria-labelledby="navbarInvoice">

                                <a class="dropdown-item flex flex-v-center" 
                                href="{{ route('invoice_create') }}" >
                                    <div>
                                        <span> สร้างรายการขาย</span>
                                    </div>
                                </a>

                                <a class="dropdown-item flex flex-v-center" 
                                href="{{ route('invoice_view') }}">
                                    <div>
                                        <span> ดูรายการขาย</span>
                                    </div>
                                </a>
                            </div>
                            <!--  -->
                        
                        </li>
                        <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre id="navbarPurchase">
                                <i class="fa fa-shopping-cart"></i> 
                                รายการซื้อ <span class="caret"></span>
                            </a>
                            <!-- Purchase Order Menu -->
                                <div class="dropdown-menu dropdown-menu__wrap" aria-labelledby="navbarPurchase">

                                    <a class="dropdown-item flex flex-v-center" 
                                       href="{{ route('purchase_create') }}" >
                                        <div>
                                            <span> เพิ่มรายการซื้อ</span>
                                        </div>
                                    </a>

                                    <a class="dropdown-item flex flex-v-center" 
                                       href="{{ route('purchase_view') }}">
                                        <div>
                                            <span> ดูรายการซื้อ</span>
                                        </div>
                                    </a>
                                </div>
                            <!--  -->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('product') }}"><i class="fa fa-warehouse"></i> สินค้า</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('purchase_create') }}"><i class="fa fa-retweet" aria-hidden="true"></i> รับคืนสินค้า</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('report') }}"><i class="fa fa-flag"></i> รายงาน</a>
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
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li> -->
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li> -->
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fa fa-user-astronaut"></i> {{Auth::user()->branchName}} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    @if(strpos(Auth::user()->email, 'admin@') !== FALSE)
                                    <a class="dropdown-item" href="{{ route('newbranch') }}">
                                        <i class="fa fa-plus"></i> {{ __('เพิ่มสาขา') }}
                                    </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fa fa-power-off"></i> {{ __('Logout') }}
                                    </a>
                                </div>
                            </li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
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

@guest
@else
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>
<script src="{{ asset('vendor/odometer/js/odometer.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/busy-load/dist/app.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.26.9/dist/sweetalert2.all.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/promise-polyfill"></script> -->
<!-- Date Picker
<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script> -->
<!-- Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<script>
    var busyBoxOptions = {
        background: "rgba(0, 0, 0, 0.6)",
        spinner: "cube",
        animation: "fade"
    }
</script>

@yield('page_script')

@endguest
</html>

