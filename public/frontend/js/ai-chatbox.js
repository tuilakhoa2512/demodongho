// Toggle hiển thị chatbox
function toggleAIChat() {
    const box = document.getElementById('ai-chat-box');
    if (!box) return;
    box.style.display = box.style.display === 'flex' ? 'none' : 'flex';
}

// Thêm message vào UI
function addMessage(text, type) {
    const container = document.getElementById('ai-chat-messages');
    if (!container) return;

    const div = document.createElement('div');
    div.className = type === 'user' ? 'ai-user' : 'ai-bot';
    div.innerText = text;
    container.appendChild(div);

    // Cuộn xuống cuối
    container.scrollTop = container.scrollHeight;
}

// Gửi tin nhắn user đến server
function sendAIMessage() {
    const input = document.getElementById('ai-chat-text');
    if (!input) return;

    const message = input.value.trim();
    if (!message) return;

    // Hiển thị message user ngay
    addMessage(message, 'user');
    input.value = '';

    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (!tokenMeta) {
        console.error('CSRF token not found!');
        return;
    }

    fetch('/ai-chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': tokenMeta.content
        },
        body: JSON.stringify({ message })
    })
    .then(res => res.json())
    .then(data => {
        if (data.reply) {
            addMessage(data.reply, 'ai');
        }
    })
    .catch(err => {
        console.error('Error sending message:', err);
        addMessage('Xin lỗi, hiện tôi chưa thể trả lời.', 'ai');
    });
}

// Load lịch sử chat khi trang load
function loadChatHistory() {
    fetch('/ai-chat/history')
        .then(res => res.json())
        .then(messages => {
            if (!Array.isArray(messages)) return;
            messages.forEach(m => {
                addMessage(m.message, m.role === 'user' ? 'user' : 'ai');
            });
        })
        .catch(err => console.error('Error loading history:', err));
}

// Xoá lịch sử chat
function setupClearChatButton() {
    const btn = document.getElementById('clearChatBtn');
    if (!btn) return;

    btn.addEventListener('click', () => {
        if (!confirm('Bạn muốn xoá toàn bộ lịch sử chat?')) return;

        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!tokenMeta) return;

        fetch('/ai-chat/clear', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': tokenMeta.content
            }
        })
        .then(() => {
            const container = document.getElementById('ai-chat-messages');
            if (container) container.innerHTML = '';
        })
        .catch(err => console.error('Error clearing chat:', err));
    });
}

// Khi DOM load xong
document.addEventListener('DOMContentLoaded', () => {
    loadChatHistory();
    setupClearChatButton();
});
