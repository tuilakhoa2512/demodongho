<!-- ========================= -->
<!-- AI CHATBOX (UI ONLY) -->
<!-- ========================= -->

<div id="ai-chat-wrapper">

    <!-- ICON MỞ CHAT -->
    <div id="ai-chat-icon" onclick="toggleAIChat()">
        🤖
    </div>

    <!-- BOX CHAT -->
    <div id="ai-chat-box" style="display:none;">

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
                Xin chào 👋 Tôi có thể hỗ trợ bạn về sản phẩm.<br>
                Nhập <b>reset</b> để hỏi lại!
            </div>
        </div>

        <!-- INPUT -->
        <div class="ai-chat-input">
            <input type="text"
                   id="ai-chat-text"
                   placeholder="Nhập tin nhắn..."
                   autocomplete="off"
                   onkeydown="if(event.key === 'Enter') sendAIMessage()">
            <button onclick="sendAIMessage()">➤</button>
        </div>

        <!-- POPUP XÁC NHẬN XOÁ CHAT -->
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

<!-- ========================= -->
<!-- CSS RIÊNG CHO CHATBOX -->
<!-- ========================= -->
<style>
#ai-chat-wrapper {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 99999;
    font-family: 'Roboto', sans-serif;
}

/* ICON */
#ai-chat-icon {
    width: 52px;
    height: 52px;
    background: #d70018;
    color: #fff;
    font-size: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 6px 18px rgba(0,0,0,.25);
}

/* BOX */
#ai-chat-box {
    width: 340px;
    height: 460px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 10px 35px rgba(0,0,0,.3);
    display: flex;
    flex-direction: column;
    margin-bottom: 12px;
}

/* HEADER */
.ai-chat-header {
    padding: 12px 14px;
    background: #d70018;
    color: #fff;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 14px 14px 0 0;
}

.ai-chat-actions button,
.ai-chat-actions i {
    background: none;
    border: none;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    margin-left: 6px;
}

/* BODY */
.ai-chat-body {
    flex: 1;
    padding: 12px;
    overflow-y: auto;
    background: #f6f6f6;
}

/* MESSAGE */
.ai-user {
    background: #d70018;
    color: #fff;
    padding: 8px 12px;
    border-radius: 12px 12px 0 12px;
    margin-bottom: 8px;
    max-width: 85%;
    margin-left: auto;
}

.ai-bot {
    background: #fff;
    color: #333;
    padding: 8px 12px;
    border-radius: 12px 12px 12px 0;
    margin-bottom: 8px;
    max-width: 85%;
}

.ai-loading {
    font-style: italic;
    opacity: .7;
}

/* INPUT */
.ai-chat-input {
    display: flex;
    padding: 10px;
    border-top: 1px solid #eee;
}

.ai-chat-input input {
    flex: 1;
    border-radius: 20px;
    border: 1px solid #ccc;
    padding: 8px 12px;
    outline: none;
}

.ai-chat-input button {
    margin-left: 8px;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    border: none;
    background: #d70018;
    color: #fff;
    cursor: pointer;
}

/* CONFIRM POPUP */
.ai-confirm {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.45);
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
    gap: 8px;
    margin-top: 12px;
}

.ai-confirm-actions button {
    flex: 1;
    padding: 6px 0;
    border-radius: 8px;
    border: none;
    cursor: pointer;
}
</style>
