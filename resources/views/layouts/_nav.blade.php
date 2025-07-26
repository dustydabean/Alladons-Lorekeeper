<nav class="navbar navbar-expand-md navbar-dark bg-dark" id="headerNav">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('lorekeeper.settings.site_name', 'Lorekeeper') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    @if (Auth::check() && Auth::user()->is_news_unread && config('lorekeeper.extensions.navbar_news_notif'))
                        <a class="nav-link d-flex text-warning" href="{{ url('news') }}"><strong>News</strong><i
                                class="fas fa-bell"></i></a>
                    @else
                        <a class="nav-link" href="{{ url('news') }}"> News</a>
                    @endif
                </li>
                @if (Auth::check() &&
                        Auth::user()->is_dev_logs_unread &&
                        Auth::user()->settings->dev_log_notif &&
                        config('lorekeeper.extensions.navbar_news_notif'))
                    <li class="nav-item">
                        <a class="nav-link d-flex text-warning" href="{{ url('devlogs') }}"><strong>Devlog</strong><i
                                class="fas fa-bell"></i></a>
                    </li>
                @endif
                <li class="nav-item">
                    @if (Auth::check() && Auth::user()->is_sales_unread && config('lorekeeper.extensions.navbar_news_notif'))
                        <a class="nav-link d-flex text-warning" href="{{ url('sales') }}"><strong>Sales</strong><i
                                class="fas fa-bell"></i></a>
                    @else
                        <a class="nav-link" href="{{ url('sales') }}"> Sales</a>
                    @endif
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ url('masterlist') }}"> Masterlist</a>
                </li>

                <li class="nav-item dropdown">
                    <a id="loreDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        World
                    </a>

                    <div class="dropdown-menu" aria-labelledby="loreDropdown">
                        <a class="dropdown-item" href="{{ url('world') }}">
                            Information
                        </a>
                        <a class="dropdown-item" href="{{ url('world/universaltraits') }}">
                            Mutations
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ url('users') }}">
                            Users
                        </a>
                        <a class="dropdown-item" href="{{ url('team') }}">
                            Staff
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('gallery') }}"> Gallery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('info/guides') }}"> Guides</a>
                </li>

                <li class="nav-item dropdown">
                    <a id="loreDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        Help
                    </a>

                    <div class="dropdown-menu" aria-labelledby="loreDropdown">
                        <a class="dropdown-item" href="{{ url('faq') }}">
                            FAQ
                        </a>
                        <a class="dropdown-item" href="{{ url('claims') }}">
                            Claims
                        </a>
                        <a class="dropdown-item" href="{{ url('reports') }}">
                            Reports
                        </a>
                        <a class="dropdown-item" href="{{ url('reports/bug-reports') }}">
                            Bug Reports
                        </a>
                        <a class="dropdown-item" href="{{ url('rules') }}">
                            Rules
                        </a>
                    </div>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                @include('layouts._searchindexbar')
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    @if (Auth::user()->isStaff)
                        <li class="nav-item d-flex">
                            <a class="nav-link position-relative display-inline-block" href="{{ url('admin') }}">
                                <i class="fas fa-crown"></i>
                                @if (Auth::user()->hasAdminNotification(Auth::user()))
                                    <span class="position-absolute rounded-circle bg-danger text-light"
                                        style="top: -2px; right: -5px; padding: 1px 6px 1px 6px; font-weight:bold; font-size: 0.8em; box-shadow: 1px 1px 1px rgba(0,0,0,.25);">
                                        {{ Auth::user()->hasAdminNotification(Auth::user()) }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->notifications_unread)
                        <li class="nav-item">
                            <a class="nav-link btn btn-secondary btn-sm" href="{{ url('notifications') }}">
                                <span class="fas fa-envelope"></span> {{ Auth::user()->notifications_unread }}
                            </a>
                        </li>
                    @endif
                    @if (Auth::check())
                        <li class="nav-item dropdown">
                            <a id="inventoryDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Home
                            </a>

                            <div class="dropdown-menu" aria-labelledby="inventoryDropdown">
                                <a class="dropdown-item" href="{{ url('characters') }}">
                                    My Alladons
                                </a>
                                <a class="dropdown-item" href="{{ url('inventory') }}">
                                    Inventory
                                </a>
                                <!--<a class="dropdown-item" href="{{ url('pets') }}">
                                    My Companions
                                </a>-->
                                <a class="dropdown-item" href="{{ url('breeding-permissions') }}">
                                    Breeding Permissions
                                </a>
                                <a class="dropdown-item" href="{{ url('collection') }}">
                                    Collections
                                </a>
                                <a class="dropdown-item" href="{{ url('bank') }}">
                                    Bank
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ url('friends') }}"
                                    class="{{ set_active('friends') }}">
                                    My Friends
                                </a>
                                <a class="dropdown-item" href="{{ url('comments/liked') }}">
                                    Liked Comments
                                </a>
                                <a class="dropdown-item" href="{{ url('account/bookmarks') }}">
                                    Bookmarks
                                </a>
                            </div>
                        </li>
                    @endif

                    @if (Auth::check())
                        <li class="nav-item dropdown">
                            @if (Auth::check() && Auth::user()->is_raffles_unread && config('lorekeeper.extensions.navbar_news_notif'))
                                <a id="queueDropdown" class="nav-link dropdown-toggle text-warning" href="#"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    v-pre>
                                    <strong>Activity</strong> <i class="fas fa-bell"></i>
                                </a>
                            @else
                                <a id="queueDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Activity
                                </a>
                            @endif
                            <div class="dropdown-menu" aria-labelledby="queueDropdown">
                                <a class="dropdown-item" href="{{ url('prompts/prompts') }}">
                                    Prompts
                                </a>
                                @if (Auth::check() && Auth::user()->is_raffles_unread && config('lorekeeper.extensions.navbar_news_notif'))
                                    <a class="dropdown-item text-warning" href="{{ url('raffles') }}">
                                        Raffles <i class="fas fa-bell"></i>
                                    </a>
                                @else
                                    <a class="dropdown-item" href="{{ url('raffles') }}">
                                        Raffles
                                    </a>
                                @endif
                                <a class="dropdown-item" href="{{ url('shops') }}">
                                    Shops
                                </a>
                                <a class="dropdown-item" href="{{ url('trades/listings') }}">
                                    Trade Center
                                </a>
                                <a class="dropdown-item" href="{{ url(__('dailies.dailies')) }}">
                                    {{ __('dailies.dailies') }}
                                </a>
                                <a class="dropdown-item" href="{{ url('crafting') }}">
                                    Crafting
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ url('submissions') }}">
                                    Prompt Submissions
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ url('characters/transfers/incoming') }}">
                                    Alladon Transfers
                                </a>
                                <a class="dropdown-item" href="{{ url('trades/open') }}">
                                    Trades
                                </a>
                            </div>
                        </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a id="browseDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Submit
                        </a>

                        <div class="dropdown-menu" aria-labelledby="browseDropdown">
                            <a class="dropdown-item" href="{{ url('submissions/new') }}">
                                Submit Prompt
                            </a>
                            <a class="dropdown-item" href="{{ url('claims/new') }}">
                                Submit Claim
                            </a>
                            <a class="dropdown-item" href="{{ url('reports/new') }}">
                                Submit Report
                            </a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="{{ Auth::user()->url }}"
                            role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ Auth::user()->url }}">
                                Profile
                            </a>
                            <a class="dropdown-item" href="{{ url('notifications') }}">
                                Notifications
                            </a>
                            <a class="dropdown-item" href="{{ url('account/settings') }}">
                                Settings
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
