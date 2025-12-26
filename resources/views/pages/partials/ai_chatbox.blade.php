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

    </div>
</div>
