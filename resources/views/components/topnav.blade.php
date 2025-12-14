    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Brand/logo -->
            <a class="navbar-brand" href="/">Bitcoin MI</a>

            <!-- Create a button to toggle the navigation menu on small screens -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Add the navigation links in a collapsible container -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() === 'meetup.index') active @endif" href="{{ route('meetup.index') }}">Meetups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() === 'price.history.index') active @endif" href="{{ route('price.history.index') }}">Price History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() === 'news.index') active @endif" href="{{ route('news.index') }}">News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() === 'contact.index') active @endif" href="{{ route('contact.index') }}">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
