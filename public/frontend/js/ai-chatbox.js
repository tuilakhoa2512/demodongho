// ================================
// GEMINI AI CHATBOX JS
// Frontend -> Laravel -> Gemini
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
    if (!container) return null;

    const wrap = document.createElement('div');
    wrap.className = 'ai-message-wrap';

    const div = document.createElement('div');
    div.className = (type === 'user') ? 'ai-user' : 'ai-bot';

    // dùng innerHTML để render HTML
    div.innerHTML = text;

    wrap.appendChild(div);
    container.appendChild(wrap);
    container.scrollTop = container.scrollHeight;

    return wrap;
}

/* Hiển thị loading */
function addLoading() {
    const container = document.getElementById('ai-chat-messages');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'ai-bot ai-loading';
    div.id = 'ai-loading';
    div.innerHTML = 'Đang tư vấn...';

    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

/* Xoá loading */
function removeLoading() {
    const loading = document.getElementById('ai-loading');
    if (loading) loading.remove();
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

    /* RESET CHAT */
    if (message.toLowerCase() === 'reset') {
        const container = document.getElementById('ai-chat-messages');
        container.innerHTML = `
            <div class="ai-bot">
                Chat đã được reset<br>
                Tôi có thể hỗ trợ bạn tiếp nhé!<br>
                <small style="color:#999">Nhập câu hỏi mới ✨</small>
            </div>
        `;
        input.value = '';
        return;
    }

    // Hiển thị user message
    addMessage(message, 'user');
    input.value = '';

    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        addMessage('Lỗi bảo mật CSRF', 'ai');
        return;
    }

    addLoading();

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
        removeLoading();

        const aiWrap = addMessage(
            data.reply || 'Xin lỗi, hiện tôi chưa thể trả lời.',
            'ai'
        );

        if (data.products && data.products.length > 0) {
            renderProductCards(data.products, aiWrap);
        }
    })
    .catch(err => {
        console.error('Gemini AI error:', err);
        removeLoading();
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

            // CHƯA CÓ TIN NHẮN → HIỆN LỜI CHÀO
            if (messages.length === 0) {
                container.innerHTML = `
                    <div class="ai-bot">
                        Xin chào 👋 Tôi có thể hỗ trợ bạn về sản phẩm.<br>
                        <small style="color:#999">
                            Nhập <b>reset</b> để hỏi lại
                        </small>
                    </div>
                `;
                return;
            }

            messages.forEach(m => {
                const wrap = addMessage(
                    m.message,
                    m.role === 'user' ? 'user' : 'ai'
                );

                if (m.products && m.products.length > 0) {
                    renderProductCards(m.products, wrap);
                }
            });
        })
        .catch(err => console.error('Load history error:', err));
}

/* Setup nút xoá lịch sử */
function setupClearChatButton() {
    const btn = document.getElementById('clearChatBtn');
    const popup = document.getElementById('ai-chat-confirm');
    const cancelBtn = document.getElementById('aiConfirmCancel');
    const okBtn = document.getElementById('aiConfirmOk');

    if (!btn || !popup) return;

    btn.addEventListener('click', () => {
        popup.classList.remove('hidden');
    });

    cancelBtn.addEventListener('click', () => {
        popup.classList.add('hidden');
    });

    okBtn.addEventListener('click', () => {
        popup.classList.add('hidden');

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
                        🗑️ Lịch sử chat đã được xoá<br>
                        Tôi có thể hỗ trợ bạn tiếp nhé!<br>
                        <small style="color:#999">
                            Nhập <b>reset</b> để hỏi lại
                        </small>
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

/* Render product cards */
function renderProductCards(products, messageWrap) {
    if (!products || products.length === 0 || !messageWrap) return;

    const list = document.createElement('div');
    list.className = 'ai-product-list';

    products.forEach(p => {
        const card = document.createElement('a');
        card.className = 'ai-product-card';
        card.href = p.link;
        card.target = '_self';

        card.innerHTML = `
            <img src="${p.image}" alt="${p.name}">
            <div class="info">
                <div class="name">${p.name}</div>
                <div class="price">${p.price} đ</div>
                <div class="view">Xem chi tiết →</div>
            </div>
        `;

        list.appendChild(card);
    });

    messageWrap.appendChild(list);
}
