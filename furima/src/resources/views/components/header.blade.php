<header class="header">
    <div class="header__logo">
        <a href="login"><img src="{{ asset('img/logo.svg') }}" alt="ロゴ"></a>
    </div>
@unless(request()->is('register') || request()->is('login') || Route::currentRouteName() == 'verification.notice')
    <form class="header_search" action="/" method="get">
        @csrf
        <input id="inputElement" class="header_search--input" type="text" name="search" placeholder="なにをお探しですか？">
        <button id="buttonElement" class="header_search--button">
            <img src="{{ asset('img/search_icon.svg') }}" alt="検索アイコン">
        </button>
    </form>
    <nav class="header__nav">
        <ul>
            @if(Auth::check())
            <li>
                <form action="/logout" method="post">
                    @csrf
                    <button class="header__logout">ログアウト</button>
                </form>
            </li>
            <li><a href="{{ route('profile.profile') }}">マイページ</a></li>
            @else
            <li><a href="/login">ログイン</a></li>
            <li><a href="/register">会員登録</a></li>
            @endif
            <a href="#">
                <li class="header__btn">出品</li>
            </a>
        </ul>
    </nav>
@endunless
</header>