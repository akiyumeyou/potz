<x-app-layout>
    <div class="p-4 bg-white shadow sm:rounded-lg">
        請求入金処理
        <div class="flex justify-between items-center mb-6">
            <form action="{{ route('receipts.update', ['request_id' => $matching->request_id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="p-4 bg-white shadow sm:rounded-lg">
                    <!-- 合計金額 -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-4xl font-bold text-gray-800">
                            合計金額: <span id="totalAmount">{{ number_format($matching->costkei ?? 0) }}</span> 円
                        </h2>
                    @if ($matching->status !== 4)
                        <button type="submit" class="px-8 py-4 bg-green-500 text-white text-2xl font-bold rounded shadow hover:bg-green-600">
                            領収
                        </button>
                    @endif
                    </div>
                     <!-- 領収書リンク（完了時のみ表示） -->
                    @if ($matching->status === 4)
                    <div class="mt-4">
                        <a href="{{ route('receipts.generatePdf', ['request_id' => $matching->request_id]) }}"
                        class="px-6 py-3 bg-blue-500 text-white text-xl font-bold rounded shadow hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        target="_blank">
                            領収書を見る
                        </a>
                    </div>
                    @endif


                    <!-- 内訳表 -->
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 border">内訳</th>
                                <th class="px-4 py-2 border">説明</th>
                                <th class="px-4 py-2 border">金額</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- サポート費 -->
                            <tr>
                                <td class="px-4 py-2 border">サポート費</td>
                                <td class="px-4 py-2 border">
                                    @ {{ number_format($matching->cost, 0) }} 円 ×
                                    <input type="number" name="time" id="timeInput"
                                           value="{{ old('time', $matching->time ?? 1) }}"
                                           class="w-16 px-2 py-1 border rounded text-right"
                                           step="0.5" min="0.5" max="8.0"
                                           oninput="updateTotal()">
                                    時間
                                </td>
                                <td class="px-4 py-2 border text-right" id="supportCostCell">
                                    {{ number_format(($matching->cost ?? 0) * ($matching->time ?? 0), 0) }}
                                </td>
                            </tr>

                            <!-- 交通費 -->
                            <tr>
                                <td class="px-4 py-2 border">交通費</td>
                                <td class="px-4 py-2 border">
                                    @ 15 円 ×
                                    <input type="number" name="distance" id="distanceInput"
                                        value="{{ old('distance', $matching->distance ?? 0) }}"
                                        class="w-16 px-2 py-1 border rounded text-right"
                                        oninput="updateTotal()">
                                    km
                                </td>
                                <td class="px-4 py-2 border text-right" id="transportationCostCell">
                                    {{ number_format(($matching->distance ?? 0) * 15, 0) }}
                                </td>
                            </tr>

                            <!-- その他費用1～3 -->
                            @for ($i = 1; $i <= 3; $i++)
                            <tr>
                                <td class="px-4 py-2 border">その他{{ $i }}</td>
                                <td class="px-4 py-2 border">
                                    <input type="number" name="sonotacost{{ $i }}" id="sonotacost{{ $i }}Input"
                                           value="{{ old("sonotacost{$i}", $matching->{'sonotacost'.$i} ?? 0) }}"
                                           class="w-full px-2 py-1 border rounded text-right"
                                           oninput="updateTotal()">
                                </td>
                                <td class="px-4 py-2 border text-right" id="sonotacost{{ $i }}Cell">
                                    {{ number_format($matching->{'sonotacost'.$i} ?? 0, 0) }}
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>

                    <!-- 備考欄 -->
                    <div class="mt-6">
                        <label for="remarks" class="block text-gray-700">備考</label>
                        <textarea name="remarks" id="remarks"
                                  class="w-full px-4 py-2 border rounded">{{ old('remarks', $matching->remarks) }}</textarea>
                    </div>
                </div>
            </form>


    </div>

    <!-- JavaScriptでリアルタイム計算 -->
    <script>
        function updateTotal() {
            const cost = @json($matching->cost);
            const distanceRate = 15;

            // 要素を取得（要素が存在しない場合はデフォルト値を使用）
            const timeInput = document.getElementById('timeInput');
            const distanceInput = document.getElementById('distanceInput');
            const sonotacost1Input = document.getElementById('sonotacost1Input');
            const sonotacost2Input = document.getElementById('sonotacost2Input');
            const sonotacost3Input = document.getElementById('sonotacost3Input');

            const time = timeInput ? parseFloat(timeInput.value) || 0 : 0;
            const distance = distanceInput ? parseFloat(distanceInput.value) || 0 : 0;
            const sonotacost1 = sonotacost1Input ? parseFloat(sonotacost1Input.value) || 0 : 0;
            const sonotacost2 = sonotacost2Input ? parseFloat(sonotacost2Input.value) || 0 : 0;
            const sonotacost3 = sonotacost3Input ? parseFloat(sonotacost3Input.value) || 0 : 0;

            // 各項目の計算
            const supportCost = cost * time;
            const transportationCost = distance * distanceRate;
            const total = supportCost + transportationCost + sonotacost1 + sonotacost2 + sonotacost3;

            // 表示更新
            if (document.getElementById('supportCostCell')) {
                document.getElementById('supportCostCell').textContent = supportCost.toLocaleString('ja-JP', { minimumFractionDigits: 0, maximumFractionDigits: 1 });
            }
            if (document.getElementById('transportationCostCell')) {
                document.getElementById('transportationCostCell').textContent = transportationCost.toLocaleString('ja-JP', { minimumFractionDigits: 0, maximumFractionDigits: 1 });
            }
            if (document.getElementById('sonotacost1Cell')) {
                document.getElementById('sonotacost1Cell').textContent = sonotacost1.toLocaleString('ja-JP', { minimumFractionDigits: 0, maximumFractionDigits: 1 });
            }
            if (document.getElementById('sonotacost2Cell')) {
                document.getElementById('sonotacost2Cell').textContent = sonotacost2.toLocaleString('ja-JP', { minimumFractionDigits: 0, maximumFractionDigits: 1 });
            }
            if (document.getElementById('sonotacost3Cell')) {
                document.getElementById('sonotacost3Cell').textContent = sonotacost3.toLocaleString('ja-JP', { minimumFractionDigits: 0, maximumFractionDigits: 1 });
            }
            if (document.getElementById('totalAmount')) {
                document.getElementById('totalAmount').textContent = total.toLocaleString('ja-JP', { minimumFractionDigits: 0, maximumFractionDigits: 1 });
            }
        }

        // 初期ロード時に計算を実行
        document.addEventListener('DOMContentLoaded', updateTotal);
    </script>

</x-app-layout>
