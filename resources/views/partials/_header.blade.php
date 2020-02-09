<section class="hero is-small is-primary is-bold">
    <div class="hero-body">
        <div class="container">
            <div class="columns">
                <div class="column">
                    <h1 class="title">
                        <a href="/">Snippets</a>
                    </h1>
                    <h2 class="subtitle">
                        Let`s collect snippets folks...
                    </h2>
                    <p>
                        @guest
                            <span>To create snippet you must be </span><a href="{{ route('login') }}">logged in</a>
                        @endguest
                        @auth
                            <a href="{{route('snippets.create')}}" class="button">Create Snippet</a>
                        @endauth
                    </p>
                </div>
                @guest
                <div class="column is-3">
                    <a href="/" class="button is-info" title="home"><i class="fa fa-home"></i></a>
                    <a href="{{ route('login') }}" class="button is-info">Login</a>
                    <a href="{{ route('register') }}" class="button is-info">Register</a>
                </div>
                @else
                <div class="column is-2">
                    <a href="/" class="button is-info" title="home"><i class="fa fa-home"></i></a>
                    <div id="user-button" class="dropdown is-right" onclick="document.getElementById('user-button').classList.contains('is-active') ? document.getElementById('user-button').classList.remove('is-active') : document.getElementById('user-button').classList.add('is-active') ;">
                        <div class="dropdown-trigger">
                            <button class="button is-info" aria-haspopup="true" aria-controls="dropdown-menu">
                                <span><i class="fa fa-user"></i>&nbsp;{{ auth()->user()->name }}</span>
                                <span class="icon is-small">
                                    <i class="fas fa-angle-down" aria-hidden="true"></i>
                                </span>
                            </button>
                        </div>
                        <div class="dropdown-menu" id="dropdown-menu" role="menu">
                            <div class="dropdown-content">
                                <a href="{{ route('user.snippets', ['user' => auth()->user()->name]) }}" class="dropdown-item">
                                    My snippets ({{ auth()->user()->snippets()->count() }})
                                </a>
                                <a href="{{ route('favorite-snippets', ['user' => auth()->user()->name]) }}" class="dropdown-item">
                                    My collection ({{ auth()->user()->paginatedFavoriteSnippets()->count() }})
                                </a>
                                <a href="{{ route('user.forked-snippets', ['user' => auth()->user()->name]) }}" class="dropdown-item">
                                    My forked snippets ({{ auth()->user()->paginatedForkedSnippets()->count() }})
                                </a>
                                <a class="dropdown-item">
                                    Settings
                                </a>
                                <a href="#" class="dropdown-item" onclick="document.getElementById('logout').click()">
                                    Log out
                                </a>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="is-inline is-hidden">
                        @csrf
                        <button id="logout" type="submit" class="" title="log out">Log out</button>
                    </form>
                </div>
                @endguest
            </div>
        </div>
    </div>
</section>
