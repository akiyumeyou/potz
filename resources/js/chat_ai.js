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

        console.log("送信メッセージ:", message);

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
            console.log("AI応答:", data);

            if (data.success && data.chat) {
                // appendMessage(data.chat); // ✅ AIのメッセージを直接追加
                messageInput.value = "";
                fetchChats(); // ✅ チャット更新
                window.scrollToBottom(true);
            } else {
                alert("AI応答エラー: " + (data.error || "不明なエラー"));
            }
        } catch (error) {
            console.error("AI応答エラー:", error);
        }

        aiButton.innerText = "AIが返事";
        aiButton.disabled = false;
    });

    // function appendMessage(chat) {
    //     let chatContainer = document.getElementById("chat-container");
    //     let messageDiv = document.createElement("div");
    //     messageDiv.classList.add("flex", "flex-col", "mb-2", "items-start"); // ✅ AIのメッセージは左寄せ

    //     let userInfo = document.createElement("span");
    //     userInfo.classList.add("text-sm", "text-gray-500");
    //     userInfo.innerText = `${chat.user_name || "不明なユーザー"} - ${chat.created_at}`;

    //     let messageContent = document.createElement("div");
    //     messageContent.classList.add("p-3", "rounded-lg", "text-lg", "max-w-[75%]", "bg-white", "border", "border-gray-300");
    //     messageContent.innerText = chat.content || "";

    //     messageDiv.appendChild(userInfo);
    //     messageDiv.appendChild(messageContent);
    //     chatContainer.appendChild(messageDiv);
    // }

});
