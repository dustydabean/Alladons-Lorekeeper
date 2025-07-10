<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    header('Permissions-Policy: interest-cohort=()');
    ?>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (config('lorekeeper.extensions.use_recaptcha'))
        <!-- ReCaptcha v3 -->
        {!! RecaptchaV3::initJs() !!}
    @endif

    <title>{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')">
    <meta name="description" content="@if (View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('lorekeeper.settings.site_desc', 'A Lorekeeper ARPG') }} @endif">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url', 'http://localhost') }}">
    <meta property="og:image" content="@if (View::hasSection('meta-img')) @yield('meta-img') @else {{ asset('images/meta-image.png') }} @endif">
    <meta property="og:title" content="{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')">
    <meta property="og:description" content="@if (View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('lorekeeper.settings.site_desc', 'A Lorekeeper ARPG') }} @endif">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ config('app.url', 'http://localhost') }}">
    <meta property="twitter:image" content="@if (View::hasSection('meta-img')) @yield('meta-img') @else {{ asset('images/meta-image.png') }} @endif">
    <meta property="twitter:title" content="{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')">
    <meta property="twitter:description" content="@if (View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('lorekeeper.settings.site_desc', 'A Lorekeeper ARPG') }} @endif">

    <!-- No AI scraping directives -->
    <meta name="robots" content="noai">
    <meta name="robots" content="noimageai">

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}"></script>
    <script defer src="{{ mix('js/app-secondary.js') }}"></script>
    <script defer src="{{ asset('js/site.js') }}"></script>
    <script src="{{ asset('js/tinymce.min.js') }}"></script>
    <script src="{{ asset('js/jquery.tinymce.min.js') }}"></script>
    <script defer src="{{ asset('js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('js/bs-custom-file-input.min.js') }}"></script>
    <script defer src="{{ asset('js/jquery-ui-timepicker-addon.js') }}"></script>
    <script defer src="{{ asset('js/croppie.min.js') }}"></script>
    <script src="{{ asset('js/jquery.ui.touch-punch.min.js') }}"></script>

    <!-- Scripts for wheel of fortune dailies -->
    <script src="{{ asset('js/winwheel.min.js') }}"></script>
    <script src="{{ asset('js/tweenmax.min.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/lorekeeper.css?v=' . filemtime(public_path('css/lorekeeper.css'))) }}" rel="stylesheet">

    {{-- Font Awesome --}}
    <link defer href="{{ faVersion() }}" rel="stylesheet">

    {{-- jQuery UI --}}
    <link defer href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">

    {{-- Bootstrap Toggle --}}
    <link defer href="{{ asset('css/bootstrap4-toggle.min.css') }}" rel="stylesheet">

    <link defer href="{{ asset('css/lightbox.min.css') }}" rel="stylesheet">
    <link defer href="{{ asset('css/bootstrap-colorpicker.min.css') }}" rel="stylesheet">
    <link defer href="{{ asset('css/jquery-ui-timepicker-addon.css') }}" rel="stylesheet">
    <link defer href="{{ asset('css/croppie.css') }}" rel="stylesheet">
    <link defer href="{{ asset('css/selectize.bootstrap4.css') }}" rel="stylesheet">

    @if (file_exists(public_path() . '/css/custom.css'))
        <link href="{{ asset('css/custom.css') . '?v=' . filemtime(public_path('css/custom.css')) }}" rel="stylesheet">
    @endif

    @if ($theme?->prioritize_css)
        @include('layouts.editable_theme')
    @endif
    @if ($theme?->has_css)
        <style type="text/css" media="screen">
            @php include_once($theme?->cssUrl)
            @endphp
            {{-- css in style tag to so that order matters --}}
        </style>
    @endif
    @if (!$theme?->prioritize_css)
        @include('layouts.editable_theme')
    @endif

    {{-- Conditional Themes are dependent on other site features --}}
    @if ($conditionalTheme?->prioritize_css)
        @include('layouts.editable_theme', ['theme' => $conditionalTheme])
    @endif
    @if ($conditionalTheme?->has_css)
        <style type="text/css" media="screen">
            @php include_once($conditionalTheme?->cssUrl)
            @endphp
            {{-- css in style tag to so that order matters --}}
        </style>
    @endif
    @if (!$conditionalTheme?->prioritize_css)
        @include('layouts.editable_theme', ['theme' => $conditionalTheme])
    @endif

    @if ($decoratorTheme?->prioritize_css)
        @include('layouts.editable_theme', ['theme' => $decoratorTheme])
    @endif
    @if ($decoratorTheme?->has_css)
        <style type="text/css" media="screen">
            @php include_once($decoratorTheme?->cssUrl)
            @endphp
            {{-- css in style tag to so that order matters --}}
        </style>
    @endif
    @if (!$decoratorTheme?->prioritize_css)
        @include('layouts.editable_theme', ['theme' => $decoratorTheme])
    @endif

    @include('feed::links')

    @include('js._external_link_alert_js')
    @yield('head')
</head>

<body>
    <div id="app">

        <div class="site-header-image" id="header" style="background-image: url('{{ $decoratorTheme?->headerImageUrl ?? $conditionalTheme?->headerImageUrl ?? $theme?->headerImageUrl ?? asset('images/header.png') }}'); position: relative; background-position: center;">
            @include('layouts._clock')
        </div>

        @include('layouts._nav')
        @if (View::hasSection('sidebar'))
            <div class="site-mobile-header bg-secondary"><a href="#" class="btn btn-sm btn-outline-light" id="mobileMenuButton">Menu <i class="fas fa-caret-right ml-1"></i></a></div>
        @endif

        <main class="container-fluid" id="main">
            <div class="row">

                <div class="sidebar col-lg-2" id="sidebar">
                    @yield('sidebar')
                </div>
                <div class="main-content col-lg-8 p-4">
                    <div>
                        @if (Settings::get('is_maintenance_mode'))
                            <div class="alert alert-secondary">
                                The site is currently in maintenance mode!
                                @if (!Auth::check() || !Auth::user()->hasPower('maintenance_access'))
                                    You can browse public content, but cannot make any submissions.
                                @endif
                            </div>
                        @endif
                        @if (Auth::check() && !config('lorekeeper.extensions.navbar_news_notif'))
                            @if (Auth::user()->is_news_unread)
                                <div class="alert alert-info"><a href="{{ url('news') }}">There is a new news post!</a></div>
                            @endif
                            @if (Auth::user()->is_sales_unread)
                                <div class="alert alert-info"><a href="{{ url('sales') }}">There is a new sales post!</a></div>
                            @endif
                            @if(Auth::user()->is_raffles_unread)
                                <div class="alert alert-info"><a href="{{ url('raffles') }}">There is a new raffle!</a></div>
                            @endif
                        @endif
                        @include('flash::message')
                        @yield('content')
                    </div>

                    <div class="site-footer mt-4" id="footer">
                        @include('layouts._footer')
                    </div>
                </div>
            </div>

        </main>


        <div class="modal fade" id="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="modal-title h5 mb-0"></span>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>

        @yield('scripts')
        @include('layouts._pagination_js')
        <script>
            $(document).on('focusin', function(e) {
                if ($(e.target).closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
                    e.stopImmediatePropagation();
                }
            });

            $(document).ready(function() {
                $('[data-toggle="tooltip"]').tooltip({
                    html: true
                });

                class BlurValid extends $.colorpicker.Extension {
                    constructor(colorpicker, options = {}) {
                        super(colorpicker, options);

                        if (this.colorpicker.inputHandler.hasInput()) {
                            const onBlur = function(colorpicker, fallback) {
                                return () => {
                                    colorpicker.setValue(colorpicker.blurFallback._original.color);
                                }
                            };
                            this.colorpicker.inputHandler.input[0].addEventListener('blur', onBlur(this.colorpicker));
                        }
                    }

                    onInvalid(e) {
                        const color = this.colorpicker.colorHandler.getFallbackColor();
                        if (color._original.valid)
                            this.colorpicker.blurFallback = color;
                    }
                }

                $.colorpicker.extensions.blurvalid = BlurValid;
                console.log($['colorpicker'].extensions);

                $('.cp').colorpicker({
                    'autoInputFallback': false,
                    'autoHexInputFallback': false,
                    'format': 'auto',
                    'useAlpha': true,
                    extensions: [{
                        name: 'blurValid'
                    }]
                });

                bsCustomFileInput.init();
                var $mobileMenuButton = $('#mobileMenuButton');
                var $sidebar = $('#sidebar');
                $('#mobileMenuButton').on('click', function(e) {
                    e.preventDefault();
                    $sidebar.toggleClass('active');
                });

                $('.inventory-log-stack').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('items') }}/" + $(this).data('id') + "?read_only=1", $(this).data('name'));
                });

                $('.spoiler-text').hide();
                $('.spoiler-toggle').click(function() {
                    $(this).next().toggle();
                });
            });

             // CLOCK
             function time() {
                    setInterval(function() { 
                        var date = new Date(); // initial date, this acts kinda like a first carbon instance so we can preform functions on it
                        var time = new Date(date.getTime() + 60*60*1000);  // preform function on first date (basically get time in timestamp format, the 60*60*1000 is an offset of +1 hour. To do other timezones just convert it to the necessary amount of hours +- UTC
                        var cycle = time.getUTCHours() >= 12 ? ' PM' : ' AM'; // this gets the hour in military time so if it's greater than 12 it's pm
                        // substr is a function that'll knock of certain letters from a given input. 
                        // Because ours is -2, if we have 001, it'll read as 01. If we have 042, it'll be 42
                        // we want this because getUTCSeconds() for example gives a single integer value for values < 10 (ex 1 second shows as 1)
                        // this doesn't look correct so we basically ''force'' it to be correct by adding and (sometimes) removed the extra 0
                        // we do getUTC so that it doesn't change per person and is universal
                        // you can see more here https://stackoverflow.com/a/39418437/11052835
                        var display = time.getUTCHours() + ":" +  ('0' + time.getUTCMinutes()).substr(-2) + ":" +  ('0' + time.getUTCSeconds()).substr(-2) + " " + cycle; // make it look pretty
                        $("#clock").text(display); // set the div to new time
                    }, 1000)} // times it out for 1 second so loop
                
                setInterval(time(), 1000); // loop

        </script>
        @include('js._liveclock')
    </div>
</body>

</html>
