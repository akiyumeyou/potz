document.addEventListener("DOMContentLoaded", function () {
    const aiButton = document.getElementById("ai-button");
    if (!aiButton) {
        console.error("AIボタンが見つかりません");
        return;
    }

    aiButton.addEventListener("click", async function () {
        console.log("AIボタンがクリックされました");

        const messageInput = document.getElementById("message-input");
        const message = messageInput.value.trim();

        if (!message) {
            console.warn("メッセージが空です");
            return;
        }

        aiButton.disabled = true;
        aiButton.innerText = "AI応答中...";

        try {
            const response = await fetch("/ai-response", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({ message: message })
            });

            if (!response.ok) {
                throw new Error(`HTTPエラー: ${response.status}`);
            }

            const data = await response.json();
            console.log("✅ AI応答:", data);

            if (data.success && data.chat) {
                messageInput.value = "";

                // ✅ **Method 2: イベント通知で fetchChats() を実行**
                window.dispatchEvent(new Event("ai-response-complete"));

            } else {
                alert("AI応答エラー: " + (data.error || "不明なエラー"));
            }
        } catch (error) {
            console.error("❌ AI応答エラー:", error);
        }

        aiButton.innerText = "AIが返事";
        aiButton.disabled = false;
    });

    // ✅ AI応答完了時に `fetchChats()` を実行
    window.addEventListener("ai-response-complete", function () {
        console.log("✅ ai-response-complete イベントを受信。fetchChats() を実行");
        fetchChats();
    });
});
