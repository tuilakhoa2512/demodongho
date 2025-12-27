<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\AIChatMessage;

class AIChatController extends Controller
{
    /**
     * Lấy session id (không dùng Auth)
     */
    private function sessionId()
    {
        return session()->getId();
    }

    /**
     * Giới hạn tối đa 50 tin / session
     */
    private function limitMessages($sessionId, $limit = 50)
    {
        $count = AIChatMessage::where('session_id', $sessionId)->count();

        if ($count > $limit) {
            AIChatMessage::where('session_id', $sessionId)
                ->orderBy('id')
                ->limit($count - $limit)
                ->delete();
        }
    }

    /**
     * API: load lịch sử chat
     */
    public function history()
    {
        return response()->json(
            AIChatMessage::where('session_id', $this->sessionId())
                ->orderBy('id')
                ->get()
        );
    }

    /**
     * API: xoá lịch sử chat
     */
    public function clear()
    {
        AIChatMessage::where('session_id', $this->sessionId())->delete();
        return response()->json(['status' => 'ok']);
    }

    /**
     * API: xử lý chat AI
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $sessionId   = $this->sessionId();
        $userMessage = trim($request->message);

        /* ======================
           1️⃣ LƯU USER MESSAGE
        ====================== */
        AIChatMessage::create([
            'session_id' => $sessionId,
            'role'       => 'user',
            'message'    => $userMessage
        ]);

        /* ======================
           2️⃣ LẤY DỮ LIỆU SẢN PHẨM
        ====================== */
        $products = Product::where('status', 1)
            ->select('name', 'price', 'stock')
            ->limit(20)
            ->get();

        $productText = '';
        foreach ($products as $p) {
            $productText .= "- {$p->name} | Giá: {$p->price} | Tồn kho: {$p->stock}\n";
        }

        /* ======================
           3️⃣ SYSTEM PROMPT (KHÓA AI)
        ====================== */
        $systemPrompt = <<<PROMPT
Bạn là chatbot tư vấn bán hàng của website.
CHỈ được dùng dữ liệu sản phẩm bên dưới.
KHÔNG bịa thông tin.
Nếu không có sản phẩm phù hợp, trả lời:
"Hiện shop chưa có sản phẩm phù hợp với yêu cầu của bạn."

DANH SÁCH SẢN PHẨM:
$productText
PROMPT;

        /* ======================
           4️⃣ GỌI OPENAI (CHAT COMPLETIONS)
        ====================== */
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'temperature' => 0.2,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

        if ($response->failed()) {
            return response()->json([
                'reply' => 'Xin lỗi, hệ thống AI đang bận. Vui lòng thử lại.'
            ], 500);
        }

        /* ======================
           5️⃣ LẤY CÂU TRẢ LỜI AI
        ====================== */
        $aiReply = data_get(
            $response->json(),
            'choices.0.message.content',
            'Xin lỗi, hiện tôi chưa thể trả lời.'
        );

        /* ======================
           6️⃣ LƯU AI MESSAGE
        ====================== */
        AIChatMessage::create([
            'session_id' => $sessionId,
            'role'       => 'ai',
            'message'    => $aiReply
        ]);

        /* ======================
           7️⃣ GIỚI HẠN 50 TIN
        ====================== */
        $this->limitMessages($sessionId, 50);

        return response()->json([
            'reply' => $aiReply
        ]);
    }
}
