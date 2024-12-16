<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理画面</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .sidebar { width: 250px; background-color: #333; color: #fff; height: 100vh; position: fixed; }
        .sidebar a { color: #fff; text-decoration: none; padding: 10px; display: block; }
        .sidebar a:hover { background-color: #444; }
        .content { margin-left: 250px; padding: 20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>管理画面</h2>
        <a href="{{ route('admin.home') }}">ホーム</a>
        <a href="{{ route('admin.users.index') }}">ユーザー一覧</a>
        <a href="{{ route('admin.categories') }}">カテゴリ一覧</a>
        <a href="{{ route('admin.supports.index') }}">サポート一覧</a>

        <a href="{{ route('logout') }}">ログアウト</a>
    </div>
    <div class="content">
        @yield('content')
    </div>
</body>
</html>
