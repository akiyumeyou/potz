<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('ご登録ありがとうございます。本人確認のためメールをお送りしました。メールを開いて了解ボタンを押してくださいね') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('ご登録のメールアドレスに確認リンクが再送信されました.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('メール再送信') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('ログアウト') }}
            </button>
        </form>
    </div>
</x-guest-layout>
