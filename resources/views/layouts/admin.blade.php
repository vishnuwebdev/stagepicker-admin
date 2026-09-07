@include('admin.inc.function')
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acthound Casting</title>
    <link rel="icon" type="image/x-icon" href="{{asset('public/assets/img/favicon.png')}}"/>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <!-- Styles -->
    @include('admin.inc.styles')  
</head>
<body class="">
    <!-- BEGIN LOADER -->
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->
    @include('admin.inc.navbar')
    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>
        @include('admin.inc.sidebar')
        <!--  BEGIN CONTENT PART  -->
        <div id="content" class="main-content">
            @yield('content')
                @include('admin.inc.footer')
        </div>
        <!--  END CONTENT PART  -->
    </div>
    <!-- END MAIN CONTAINER -->
    @include('admin.inc.scripts')
    <script>
        // Sidebar module dropdowns (Audition Control, Production Crew, Animal
        // Audition, etc.) are plain `<a class="menu ..." href="#">` headers
        // with a sibling `<ul class="menus ...">` submenu — there was no
        // click handler anywhere in the theme JS to open/close them, so any
        // submenu that starts collapsed (inline `style="display:none"`, e.g.
        // Store, Animal Audition) had no way to be reopened. This binds a
        // generic toggle to every such header instead of one per module.
        (function ($) {
            $(document).on('click', '#sidebar a.menu[href="#"]', function (e) {
                e.preventDefault();
                var $submenu = $(this).siblings('ul.menus');
                if (!$submenu.length) {
                    return;
                }
                $('#sidebar ul.menus').not($submenu).slideUp(200);
                $submenu.stop(true, true).slideToggle(200);
            });
        })(jQuery);
    </script>
    @yield('script')
</body>
</html>