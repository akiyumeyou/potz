<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category3; // 正しいモデルを参照

class Category3Controller extends Controller
{
    public function index()
    {
        $categories = Category3::orderBy('order_no')->get(); // 並び順で取得
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create'); // 新規作成フォーム
    }

    public function store(Request $request)
    {
        $request->validate([
            'category3' => 'required|string|max:255',
            'order_no' => 'nullable|integer',
            'cost' => 'nullable|integer|min:0',
        ]);

        Category3::create($request->only('category3', 'order_no', 'cost'));

        return redirect()->route('admin.category3.index')->with('success', 'カテゴリを登録しました。');
    }

    public function edit(Category3 $category3)
    {
        return view('admin.categories.edit', compact('category3')); // 編集画面
    }

    public function update(Request $request, Category3 $category3)
    {
        $request->validate([
            'category3' => 'required|string|max:255',
            'order_no' => 'nullable|integer',
            'cost' => 'nullable|integer|min:0',
        ]);

        $category3->update($request->only('category3', 'order_no', 'cost'));

        return redirect()->route('admin.categories.index')->with('success', 'カテゴリを更新しました。');
    }

    public function destroy(Category3 $category3)
    {
        $category3->delete();

        return redirect()->route('admin.categories.index')->with('success', 'カテゴリを削除しました。');
    }
}
