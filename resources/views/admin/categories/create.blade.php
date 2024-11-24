@extends('layouts.admin')

@section('content')
    <h1 style="text-align: center; margin-bottom: 20px;">カテゴリを追加</h1>

    {{-- バリデーションエラーの表示 --}}
    @if ($errors->any())
        <div style="color: red; margin-bottom: 10px; text-align: center;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- カテゴリ登録フォーム --}}
    <form action="{{ route('admin.category3.store') }}" method="POST" style="max-width: 500px; margin: auto;">
        @csrf
        <div style="margin-bottom: 15px;">
            <label for="category3" style="display: block; font-weight: bold; margin-bottom: 5px;">カテゴリの名前:</label>
            <input type="text" id="category3" name="category3" value="{{ old('category3') }}" required
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="order_no" style="display: block; font-weight: bold; margin-bottom: 5px;">表示順番（数字）:</label>
            <input type="number" id="order_no" name="order_no" value="{{ old('order_no') }}"
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="cost" style="display: block; font-weight: bold; margin-bottom: 5px;">1時間あたりの単価（数字）:</label>
            <input type="number" id="cost" name="cost" value="{{ old('cost') }}" min="0"
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="text-align: center;">
            <button type="submit" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
                登録
            </button>
        </div>
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('admin.category3.index') }}" style="color: #007BFF; text-decoration: underline;">カテゴリ一覧に戻る</a>
    </div>
@endsection
