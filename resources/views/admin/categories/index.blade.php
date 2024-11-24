@extends('layouts.admin')

@section('content')
    <h1 style="text-align: center; margin-bottom: 20px;">カテゴリ一覧</h1>

    {{-- カテゴリ登録リンク --}}
    <div style="text-align: center; margin-bottom: 20px;">
        <a href="{{ route('admin.category3.create') }}"
           style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; text-decoration: none;">
            カテゴリを追加
        </a>
    </div>

    {{-- 成功メッセージの表示 --}}
    @if (session('success'))
        <p style="color: green; text-align: center;">{{ session('success') }}</p>
    @endif

    {{-- カテゴリ一覧テーブル --}}
    <table style="width: 100%; border-collapse: collapse; margin: 0 auto;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ID</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">カテゴリの名前</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">表示順番</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">1時間あたりの単価</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $category->id }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $category->category3 }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $category->order_no }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $category->cost }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <a href="{{ route('admin.category3.edit', $category) }}"
                           style="color: #007BFF; text-decoration: none; margin-right: 10px;">編集</a>
                        <form action="{{ route('admin.category3.destroy', $category) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('削除しますか？')"
                                    style="color: red; background: none; border: none; cursor: pointer; text-decoration: underline;">
                                削除
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
