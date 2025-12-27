// ================================
// AI CHATBOX JS
// ================================

/* Toggle hiển thị chatbox */
function toggleAIChat() {
    const box = document.getElementById('ai-chat-box');
    if (!box) return;

    box.style.display = (box.style.display === 'flex') ? 'none' : 'flex';
}

/* Thêm message vào UI */
function addMessage(text, type) {
    const container = document.getElementById('ai-chat-messages');
    if (!container) return;

    const div = document.createElement('div');
    div.className = (type === 'user') ? 'ai-user' : 'ai-bot';
    div.innerText = text;

    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

/* Lấy CSRF token */
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : null;
}

/* Gửi tin nhắn */
function sendAIMessage() {
    const input = document.getElementById('ai-chat-text');
    if (!input) return;

    const message = input.value.trim();
    if (!message) return;

    // Hiển thị user message ngay
    addMessage(message, 'user');
    input.value = '';

    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        console.error('CSRF token not found');
        addMessage('Lỗi bảo mật CSRF', 'ai');
        return;
    }

    fetch('/ai-chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ message })
    })
    .then(res => {
        if (!res.ok) throw new Error('Network error');
        return res.json();
    })
    .then(data => {
        if (data.reply) {
            addMessage(data.reply, 'ai');
        } else {
            addMessage('Xin lỗi, hiện tôi chưa thể trả lời.', 'ai');
        }
    })
    .catch(err => {
        console.error('AI chat error:', err);
        addMessage('Xin lỗi, hệ thống AI đang bận.', 'ai');
    });
}

/* Load lịch sử chat */
function loadChatHistory() {
    fetch('/ai-chat/history')
        .then(res => res.json())
        .then(messages => {
            if (!Array.isArray(messages)) return;

            const container = document.getElementById('ai-chat-messages');
            if (!container) return;

            container.innerHTML = '';

            messages.forEach(m => {
                addMessage(m.message, m.role === 'user' ? 'user' : 'ai');
            });
        })
        .catch(err => console.error('Load history error:', err));
}

/* Setup nút xoá lịch sử */
function setupClearChatButton() {
    const btn = document.getElementById('clearChatBtn');
    if (!btn) return;

    btn.addEventListener('click', () => {
        if (!confirm('Bạn muốn xoá toàn bộ lịch sử chat?')) return;

        const csrfToken = getCsrfToken();
        if (!csrfToken) return;

        fetch('/ai-chat/clear', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Clear failed');

            const container = document.getElementById('ai-chat-messages');
            if (container) {
                container.innerHTML = `
                    <div class="ai-bot">
                        Xin chào 👋 Tôi có thể hỗ trợ bạn về sản phẩm.
                    </div>
                `;
            }
        })
        .catch(err => console.error('Clear chat error:', err));
    });
}

/* DOM READY */
document.addEventListener('DOMContentLoaded', () => {
    loadChatHistory();
    setupClearChatButton();
});
