// 「ありがとう」ボタンのクリック処理
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.thank-button').forEach(button => {
        button.addEventListener('click', function () {
            const requestId = this.dataset.requestId;

            // ボタンの状態を一時的に更新（UIの即時反応）
            const heartIcon = this.querySelector('.heart-icon');
            heartIcon.textContent = '❤️';
            this.classList.add('liked');
            this.disabled = true;

            // APIリクエストで「ありがとう」を送信
            fetch(`/requests/${requestId}/thank`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })

                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        // エラー時は元に戻す
                        alert('エラーが発生しました。');
                        heartIcon.textContent = '🤍';
                        this.classList.remove('liked');
                        this.disabled = false;
                    }
                })
                .catch(() => {
                    alert('通信エラーが発生しました。');
                    heartIcon.textContent = '🤍';
                    this.classList.remove('liked');
                    this.disabled = false;
                });
        });
    });
});
