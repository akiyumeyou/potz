// 「ありがとう」ボタンのクリック処理
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.thank-button').forEach(button => {
        button.addEventListener('click', function () {
            const requestId = this.dataset.requestId;

            // ボタンの状態を一時的に更新（UIの即時反応）
            const heartIcon = this.querySelector('.heart-icon');
            const originalText = this.innerHTML;
            heartIcon.textContent = '❤️';
            this.classList.add('liked');
            this.disabled = true;
            this.innerHTML = '<span class="heart-icon">❤️</span> ありがとう送信済';

            // APIリクエストで「ありがとう」を送信
            fetch(`/requests/${requestId}/thank`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('サーバーエラー');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        // サーバー側のエラー時は元に戻す
                        alert('エラーが発生しました: ' + data.message);
                        this.innerHTML = originalText;
                        this.classList.remove('liked');
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    // 通信エラー時は元に戻す
                    console.error('通信エラー:', error);
                    alert('通信エラーが発生しました。');
                    this.innerHTML = originalText;
                    this.classList.remove('liked');
                    this.disabled = false;
                });
        });
    });
});

