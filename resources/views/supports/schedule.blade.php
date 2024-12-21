<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">サポート予定</h3>

    @if ($supportSchedules->isNotEmpty())
        <ul class="space-y-4">
            @foreach ($supportSchedules as $schedule)
                <li class="border border-gray-300 rounded p-4 bg-gray-50">
                    <h4 class="text-lg font-semibold">{{ $schedule->category3->category3 ?? '未設定' }}</h4>
                    <p class="text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($schedule->date)->isoFormat('YYYY年MM月DD日（dddd）') }} {{ $schedule->time_start }}から{{ $schedule->time }}時間
                    </p>
                    <p class="text-sm text-gray-600">場所: {{ $schedule->spot ?? '未設定' }}</p>
                    <p class="text-sm text-gray-600">見積金額: {{ number_format($schedule->estimate) }}円</p>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-gray-600">現在、予定されているサポート案件はありません。</p>
    @endif
</div>
