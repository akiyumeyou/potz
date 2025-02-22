document.addEventListener("DOMContentLoaded", function () {
    const aiGenerateToggle = document.getElementById("ai-generate-toggle");
    const aiPromptPanel = document.getElementById("ai-prompt-panel");
    const generateImageBtn = document.getElementById("generate-image-btn");

    // 画像生成の入力パネルを表示/非表示
    aiGenerateToggle.addEventListener("click", function () {
        if (aiPromptPanel.style.display === "none" || aiPromptPanel.style.display === "") {
            aiPromptPanel.style.display = "block";
        } else {
            aiPromptPanel.style.display = "none";
        }
    });

    // 画像生成実行ボタンの処理
    generateImageBtn.addEventListener("click", function () {
        const userComment = document.getElementById("user_comment").value;
        const theme = document.getElementById("theme").value;
        const s_text1 = document.getElementById("s_text1").value;
        const s_text2 = document.getElementById("s_text2").value;
        const s_text3 = document.getElementById("s_text3").value;

        if (!theme || !s_text1) {
            alert("テーマと川柳の1行目は必須です！");
            return;
        }

        fetch("/generate-image", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ theme, s_text1, s_text2, s_text3, userComment })
        })
        .then(response => response.json())
        .then(data => {
            if (data.image_url) {
                document.getElementById("preview-container").innerHTML =
                    `<img src="${data.image_url}" alt="生成画像" style="max-width: 60%; height: auto;">`;
                document.getElementById("generated_image_name").value = data.image_name;
            } else {
                alert("画像生成に失敗しました: " + data.error);
            }
        })
        .catch(error => {
            console.error("画像生成エラー:", error);
        });
    });
});
