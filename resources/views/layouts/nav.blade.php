    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto" style="margin-top: 15px;">
                    <li style="margin-left: 20px;">
                        <a href="{{ route('threads.create') }}">New Thread</a>
                    </li>

                    <li class="dropdown" style="margin-left: 20px;">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                          Browse <span class="caret"></span>
                      </a>
                      <ul class="dropdown-menu">
                        <li>
                            <a href="/threads">All Threads</a>
                        </li>
                        @if (auth()->check())
                            <li>
                                <a href="/threads?by={{ auth()->user()->name }}">My Threads</a>
                            </li>
                        @endif
                        <li>
                            <a href="/threads?popular=1">Popular Threads</a>
                            <a href="/threads?unanswered=1">Unanswered Threads</a>
                        </li>
                      </ul>
                    </li>

                    <li class="dropdown" style="margin-left: 20px;">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Channels <span class="caret"></span></a>
                      <ul class="dropdown-menu">
                        @forelse ($channels as $channel)
                            <li>
                                <a href="{{ route('threads.channel.index', ['channel' => $channel->slug]) }}">{{ $channel->slug }}</a>
                            </li>
                        @empty
                        @endforelse
                      </ul>
                    </li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto" style="float: right;">
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

                        <user-notifications></user-notifications>

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('profiles.show', ['user' => Auth::user()->name]) }}">My Profile</a>
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
