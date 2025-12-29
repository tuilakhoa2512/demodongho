<!-- AI CHATBOX -->
<div id="ai-chat-wrapper">

    <!-- ICON TRÒN -->
    <div id="ai-chat-icon" onclick="toggleAIChat()">
        🤖
    </div>

    <!-- HỘP CHAT -->
    <div id="ai-chat-box">

        <!-- HEADER -->
        <div class="ai-chat-header">
            <span>Trợ lý AI</span>

            <div class="ai-chat-actions">
                <button id="clearChatBtn" title="Xoá lịch sử">🗑</button>
                <i onclick="toggleAIChat()">✕</i>
            </div>
        </div>

        <!-- BODY -->
        <div class="ai-chat-body" id="ai-chat-messages">
            <div class="ai-bot">
                Xin chào 👋 Tôi có thể hỗ trợ bạn về sản phẩm.
            </div>
        </div>

        <!-- INPUT -->
        <div class="ai-chat-input">
            <input type="text"
                   id="ai-chat-text"
                   placeholder="Nhập tin nhắn..."
                   onkeydown="if(event.key==='Enter') sendAIMessage()">
            <button onclick="sendAIMessage()">➤</button>
        </div>

        <!-- 🔥 POPUP XÁC NHẬN XOÁ CHAT -->
        <div id="ai-chat-confirm" class="ai-confirm hidden">
            <div class="ai-confirm-content">
                <div class="ai-confirm-text">
                    Bạn có chắc muốn xoá toàn bộ lịch sử chat không?
                </div>
                <div class="ai-confirm-actions">
                    <button id="aiConfirmCancel">Huỷ</button>
                    <button id="aiConfirmOk">Xoá</button>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
   #ai-chat-box {
    position: relative;
}

.ai-confirm {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999;
}

.ai-confirm.hidden {
    display: none;
}

.ai-confirm-content {
    background: #fff;
    padding: 16px;
    border-radius: 12px;
    width: 90%;
    max-width: 280px;
    text-align: center;
}

.ai-confirm-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 12px;
}

.ai-confirm-actions button {
    flex: 1;
    margin: 0 4px;
    padding: 6px 0;
    border-radius: 8px;
    border: none;
    cursor: pointer;
}


</style>