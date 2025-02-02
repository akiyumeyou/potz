<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight">
            参加者管理 - {{ $event->title }} : {{ $event->event_date }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-4">
        @if (session('success'))
            <div class="alert alert-success bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- 参加者一覧 --}}
        <h2 class="text-xl font-semibold mb-4">参加者一覧</h2>
        <table class="table-auto w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2">名前</th>
                    <th class="border border-gray-300 px-4 py-2">参加承認</th>
                    <th class="border border-gray-300 px-4 py-2">支払い方法</th>
                    <th class="border border-gray-300 px-4 py-2">入金</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($participants as $participant)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">{{ $participant->user->name }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <form method="POST" action="{{ route('admin.event-participants.toggle-status', $participant->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1 rounded
                                    {{ $participant->status === 0 ? 'bg-gray-500 text-white' : 'bg-green-500 text-white' }}">
                                    {{ $participant->status === 0 ? '未承認' : '承認済み' }}
                                </button>
                            </form>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">{{ $participant->payment_method }}</td>
                        {{-- 入金ボタン・金額 --}}
                        <td class="border border-gray-300 px-4 py-2">
                            <form method="POST" action="{{ route('admin.event-participants.toggle-payment', $participant->id) }}" class="inline">
                                @csrf
                                @method('PATCH')

                                @if ($participant->payment_status === 0)
                                    <input type="number" name="amount_paid" class="border p-1 w-20" value="{{ old('amount_paid', $participant->amount_paid) }}">
                                    <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">入金済み</button>
                                @else
                                    <span>{{ number_format($participant->amount_paid) }} 円</span>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- 参加者追加フォーム --}}
        <h2 class="text-xl font-semibold mt-6 mb-4">参加者追加</h2>
        <form method="POST" action="{{ route('admin.events.add-participant', $event->id) }}">
            @csrf
            <label class="block text-gray-700">ユーザー選択:</label>
            <select name="user_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

            <label class="block text-gray-700 mt-2">支払い方法:</label>
            <select name="payment_method" class="w-full border-gray-300 rounded-lg shadow-sm">
                <option value="銀行振込">銀行振込</option>
                <option value="PayPay">PayPay</option>
                <option value="その他">その他</option>
            </select>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 mt-3 rounded">参加者を追加</button>
        </form>
    </div>
    </x-app-layout>
