@extends('layouts.app')

@section('content')
<div class="container">
    <h1>会員トップページ</h1>
    <a href="{{ route('requests.create') }}" class="btn btn-primary">依頼を作成</a>
</div>
@endsection
