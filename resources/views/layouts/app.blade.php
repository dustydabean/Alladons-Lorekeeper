<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
        header("Permissions-Policy: interest-cohort=()");
    ?>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')">
    <meta name="description" content="@if(View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('lorekeeper.settings.site_desc', 'A Lorekeeper ARPG') }} @endif">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url', 'http://localhost') }}">
    <meta property="og:image" content="@if(View::hasSection('meta-img')) @yield('meta-img') @else {{ asset('images/meta-image.png') }} @endif">
    <meta property="og:title" content="{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')">
    <meta property="og:description" content="@if(View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('lorekeeper.settings.site_desc', 'A Lorekeeper ARPG') }} @endif">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ config('app.url', 'http://localhost') }}">
    <meta property="twitter:image" content="@if(View::hasSection('meta-img')) @yield('meta-img') @else {{ asset('images/meta-image.png') }} @endif">
    <meta property="twitter:title" content="{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')">
    <meta property="twitter:description" content="@if(View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('lorekeeper.settings.site_desc', 'A Lorekeeper ARPG') }} @endif">

    <!-- No AI scraping directives -->
    <meta name="robots" content="noai">
    <meta name="robots" content="noimageai">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/site.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap4-toggle.min.js') }}"></script>
    <script src="{{ asset('js/tinymce.min.js') }}"></script>
    <script src="{{ asset('js/jquery.tinymce.min.js') }}"></script>
    <script src="{{ asset('js/lightbox.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('js/selectize.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui-timepicker-addon.js') }}"></script>
    <script src="{{ asset('js/croppie.min.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/lorekeeper.css') }}" rel="stylesheet">

    {{-- Font Awesome --}}
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet">

    {{-- jQuery UI --}}
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">

    {{-- Bootstrap Toggle --}}
    <link href="{{ asset('css/bootstrap4-toggle.min.css') }}" rel="stylesheet">


    <link href="{{ asset('css/lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-colorpicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui-timepicker-addon.css') }}" rel="stylesheet">
    <link href="{{ asset('css/croppie.css') }}" rel="stylesheet">
    <link href="{{ asset('css/selectize.bootstrap4.css') }}" rel="stylesheet">

    @if(file_exists(public_path(). '/css/custom.css'))
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @endif

    @php $theme = Auth::user()->theme ?? $defaultTheme ?? null; @endphp
    @if($theme?->prioritize_css) @include('layouts.editable_theme') @endif
    @if($theme?->has_css)
        <style type="text/css" media="screen">
            @php include_once($theme?->cssUrl) @endphp
            {{-- css in style tag to so that order matters --}}
        </style>
    @endif
    @if(!$theme?->prioritize_css) @include('layouts.editable_theme') @endif
    
    {{-- Conditional Themes are dependent on other site features --}}
    @php 
        $conditionalTheme = null;
        if(class_exists('\App\Models\Weather\WeatherSeason')) {
            $conditionalTheme = \App\Models\Theme::where('link_type', 'season')->where('link_id', Settings::get('site_season'))->first() ??
                \App\Models\Theme::where('link_type', 'weather')->where('link_id', Settings::get('site_weather'))->first() ??
                $theme;
        }
    @endphp
    @if($conditionalTheme?->prioritize_css) @include('layouts.editable_theme', ['theme' => $conditionalTheme]) @endif
    @if($conditionalTheme?->has_css)
        <style type="text/css" media="screen">
            @php include_once($conditionalTheme?->cssUrl) @endphp
            {{-- css in style tag to so that order matters --}}
        </style>
    @endif
    @if(!$conditionalTheme?->prioritize_css) @include('layouts.editable_theme', ['theme' => $conditionalTheme]) @endif
    
    @php $decoratorTheme = Auth::user()->decoratorTheme ?? null; @endphp
    @if($decoratorTheme?->prioritize_css) @include('layouts.editable_theme', ['theme' => $decoratorTheme]) @endif
    @if($decoratorTheme?->has_css)
        <style type="text/css" media="screen">
            @php include_once($decoratorTheme?->cssUrl) @endphp
            {{-- css in style tag to so that order matters --}}
        </style>
    @endif
    @if(!$decoratorTheme?->prioritize_css) @include('layouts.editable_theme', ['theme' => $decoratorTheme]) @endif

</head>
<body>
    <div id="app">

        <div class="site-header-image" id="header" style="background-image: url('{{ $decoratorTheme?->headerImageUrl ?? $conditionalTheme?->headerImageUrl ?? $theme?->headerImageUrl ?? asset('images/header.png') }}');"></div>

        @include('layouts._nav')
        @if ( View::hasSection('sidebar') )
			<div class="site-mobile-header bg-secondary"><a href="#" class="btn btn-sm btn-outline-light" id="mobileMenuButton">Menu <i class="fas fa-caret-right ml-1"></i></a></div>
		@endif

        <main class="container-fluid" id="main">
            <div class="row">

                <div class="sidebar col-lg-2" id="sidebar">
                    @yield('sidebar')
                </div>
                <div class="main-content col-lg-8 p-4">
                    <div>
                        @if(Auth::check() && !Config::get('lorekeeper.extensions.navbar_news_notif'))
                            @if(Auth::user()->is_news_unread)
                                <div class="alert alert-info"><a href="{{ url('news') }}">There is a new news post!</a></div>
                            @endif
                            @if(Auth::user()->is_sales_unread)
                                <div class="alert alert-info"><a href="{{ url('sales') }}">There is a new sales post!</a></div>
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
        <script>
            $(function() {
                $('[data-toggle="tooltip"]').tooltip({html: true});
                
                class BlurValid extends $.colorpicker.Extension {
                    constructor(colorpicker, options = {}) {
                        super(colorpicker, options);

                        if (this.colorpicker.inputHandler.hasInput()) {
                            const onBlur = function (colorpicker, fallback) {
                                return () => {
                                    colorpicker.setValue(colorpicker.blurFallback._original.color);
                                }
                            };
                            this.colorpicker.inputHandler.input[0].addEventListener('blur', onBlur(this.colorpicker));
                        }
                    }
                    
                    onInvalid(e) {
                        const color = this.colorpicker.colorHandler.getFallbackColor();
                        if(color._original.valid)
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
                
                tinymce.init({
                    selector: '.wysiwyg',
                    height: 500,
                    menubar: false,
                    convert_urls: false,
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks code fullscreen spoiler',
                        'insertdatetime media table paste code help wordcount'
                    ],
                    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | spoiler-add spoiler-remove | removeformat | code',
                    content_css: [
                        '{{ asset('css/app.css') }}',
                        '{{ asset('css/lorekeeper.css') }}',
                        '{{ asset('css/custom.css') }}',
                        '{{ asset($theme?->cssUrl) }}'
                    ],
                    spoiler_caption: 'Toggle Spoiler',
                    target_list: false
                });
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
                    $('.spoiler-toggle').click(function(){
                        $(this).next().toggle();
                    });
                });
        </script>
    </div>
</body>
</html>
