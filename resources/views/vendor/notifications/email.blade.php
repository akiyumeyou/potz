@component('mail::message')
{{-- カスタムロゴ --}}
<img src="{{ asset('img/logo.png') }}" alt="POTZ" style="width: 150px; height: auto;">

{{-- メールタイトル --}}
# メール認証のお願い

以下のボタンをクリックして、メールアドレスを確認してください。

{{-- アクションボタン --}}
@component('mail::button', ['url' => $actionUrl])
メールアドレスを確認する
@endcomponent

{{-- フッターテキスト --}}
メール認証がないと一部の機能が使えません。架空のメールアドレスではないことを確認するだけのボタンです。

株式会社ポチっとつながるPOTZ
[https://potz.jp](https://potz.jp)

@endcomponent
