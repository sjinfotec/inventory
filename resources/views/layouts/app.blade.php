<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="{{ asset('js/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}" defer></script>
    <script src="{{ asset('js/offcanvas.js') }}" defer></script>
    <script src="{{ asset('js/popper.min.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" >
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/site.css') }}" rel="stylesheet">
</head>
<body class="pb-0">
    <div id="app" class="min-height-full">
    @if(Auth::check())
        <!-- header -->
        <header>
            <!-- header nav -->
            <nav class="navbar navbar-expand-lg fixed-top bg-white border border-top-0 border-left-0  border-right-0 border-light">
                <!-- offcanvas-left toggle button -->
                <button type="button" class="btn btn-secondary btn-sm d-xl-none mr-2" type="button" data-toggle="offcanvas-left">
                    <span class="navbar-toggler-icon"><img class="icon-size-sm" src="{{ asset('images/round-menu-w.svg') }}" alt=""></span>
                </button>
                <!-- /offcanvas-left toggle button -->
                <!-- editable title -->
                <a class="navbar-brand mr-auto mr-lg-0" href="{{ url('/') }}">
                    <!--<img class="logo-height" src="{{ asset('images/home-solid.svg') }}" alt=>-->
                    <img class="logo_height" src="{{ asset('images/order_logo1.svg') }}" alt="受発注管理システム">
                    
                </a>
                <!-- /editable title -->
                <div class="form-inline my-lg-0 ml-auto">
                    @if(Auth::check())
                    <company-set></company-set>
                    @else
                    <span class="pr-2">
                        <a href="{{ route('login') }}">{{ __('Login') }}</a>
                    </span>
                    @if (Route::has('register'))
                    <span class="pr-2">
                        <a href="{{ route('register') }}">{{ __('Register') }}</a>
                    </span>
                    @endif
                    </ul>
                    @endif
                    <!-- group name -->
                    <!--
                    <span class="pr-2 d-none d-md-inline">三条印刷株式会社</span>
                    -->
                    <!-- /group name -->
                </div>
            </nav>
            <!-- /header nav -->
        </header>
        <!-- /header -->
    @endif

		<!-- .container-fluid -->
		<div class="container-fluid min-height-full">
			<!-- .row -->
			<div class="row min-height-full">
                @include('layouts.sidemenu2')
                @yield('content')
                @if(Auth::check())
                <!-- main contentns row -->
                <div class="row justify-content-between print-none">
                    <!-- .panel -->
                    <div class="col-md p-3">
                        <div class="text-center">
                            <small>© 2021 Ordering System</small>
                        </div>
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /main contentns row -->
				<!-- offcanvas-right -->
				<div class="offcanvas-collapse offcanvas-collapse-from-right side-base">
					<aside>
					</aside>
				</div>
				<!-- /offcanvas-right -->
                @endif
			</div>
			<!-- /.row -->
		</div>
		<!-- /.container-fluid -->
    </div>
</body>
</html>
