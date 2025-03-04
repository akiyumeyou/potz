@php
$manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
@endphp
<link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">

<!-- Scripts -->
<script type="module" src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>

<style>
    body {
        background-color: #fff7e6; /* Lighter orange background */
        font-family: 'Arial', sans-serif;
    }
    .container {
        max-width: 400px;
        margin: 0 auto;
        padding: 2em;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .logo {
        display: flex;
        justify-content: center;
        margin-bottom: 1em;
    }
    .logo img {
        max-width: 100px;
    }
    h2 {
        text-align: center;
        color: #ff6f61;
    }
    .form-group {
        margin-bottom: 1em;
    }
    label {
        font-weight: bold;
        color: #555;
    }
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 0.75em;
        margin-top: 0.5em;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1em;
    }
    input[type="checkbox"] {
        margin-right: 0.5em;
    }
    .btn {
        background-color: #ff8f61; /* Changed button color */
        color: #fff;
        padding: 0.75em;
        border: none;
        border-radius: 5px;
        width: 100%;
        font-size: 1.1em;
        cursor: pointer;
    }
    .btn:hover {
        background-color: #ff6f4c; /* Changed button hover color */
    }
    .link {
        text-align: center;
        display: block;
        margin-top: 1em;
        color: #ff6f61;
        text-decoration: none;
    }
    .link:hover {
        text-decoration: underline;
    }
    .google-signin {
        display: flex;
        justify-content: center;
        margin-top: 1em;
    }
    .google-signin img {
        max-width: 250px; /* Increased the size of the Google sign-in button */
    }
    .custom-google-button {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #4285F4; /* Google Blue */
    color: white;
    padding: 0.75em;
    border: none;
    border-radius: 5px;
    width: 100%;
    font-size: 1.1em;
    cursor: pointer;
    text-decoration: none;
    max-width: 400px; /* オレンジのログインボタンと同じ幅 */
    height: 50px; /* 高さも合わせる */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: background-color 0.3s ease;
}

.custom-google-button:hover {
    background-color: #357ae8;
}

.google-logo {
    width: 24px;
    height: 24px;
    margin-right: 10px;
}


    .separator {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 20px 0;
    }
    .separator:before,
    .separator:after {
        content: "";
        flex: 1;
        border-bottom: 1px solid #ddd;
    }
    .separator:not(:empty)::before {
        margin-right: .25em;
    }
    .separator:not(:empty)::after {
        margin-left: .25em;
    }
</style>

<x-guest-layout>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo"> <!-- Ensure the correct path -->
        </div>
        @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

        <h2>Gmail</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="google-signin">
                <a href="{{ route('login.google') }}" class="custom-google-button">
                    <img src="{{ asset('img/web_neutral_sq_na@3x.png') }}" alt="Google ロゴ" class="google-logo">
                    <span>Googleでログイン</span>
                </a>
            </div>


            <div class="separator">または</div>

            <a class="link" href="{{ route('register') }}">Gmail以外<br>初めての方の登録はココ</a> <!-- Added registration link -->

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>

            <div class="form-group">
                <label for="remember_me">
                    <input id="remember_me" type="checkbox" name="remember">パスワードを保存
                </label>
            </div>

            <button type="submit" class="btn">ログイン</button>

            <a class="link" href="{{ route('password.request') }}">パスワードを忘れたらココ</a>

        </form>
    </div>
</x-guest-layout>
