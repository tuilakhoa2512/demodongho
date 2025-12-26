<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\AIChatMessage;

class AIChatController extends Controller
{
    private function sessionId()
    {
        return session()->getId();
    }

    private function limitMessages($sessionId, $limit = 50)
    {
        $count = AIChatMessage::where('session_id', $sessionId)->count();
        if ($count > $limit) {
            $deleteCount = $count - $limit;
            AIChatMessage::where('session_id', $sessionId)
                ->orderBy('id')
                ->limit($deleteCount)
                ->delete();
        }
    }

    public function history()
    {
        $sessionId = $this->sessionId();
        $messages = AIChatMessage::where('session_id', $sessionId)
            ->orderBy('id')
            ->get();
        return response()->json($messages);
    }

    public function clear()
    {
        $sessionId = $this->sessionId();
        AIChatMessage::where('session_id', $sessionId)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        $sessionId = $this->sessionId();
        $userMessage = trim($request->message);

        // 1️⃣ LƯU MESSAGE USER
        AIChatMessage::create([
            'session_id' => $sessionId,
            'role'       => 'user',
            'message'    => $userMessage
        ]);

        // 2️⃣ LẤY LỊCH SỬ GẦN NHẤT
        $history = AIChatMessage::where('session_id', $sessionId)
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->reverse();

        // 3️⃣ LẤY DỮ LIỆU SẢN PHẨM
        $products = Product::where('status', 1)
            ->select('name', 'price', 'description', 'stock')
            ->limit(20)
            ->get();

        $productText = '';
        foreach ($products as $p) {
            $productText .= "- {$p->name} | Giá: {$p->price} | Tồn kho: {$p->stock}\n";
        }

        // 4️⃣ SYSTEM PROMPT
        $systemPrompt = "
Bạn là chatbot tư vấn bán hàng của website.
CHỈ được sử dụng dữ liệu bên dưới để trả lời.
KHÔNG được bịa thông tin.
KHÔNG suy đoán.
Nếu không có thông tin phù hợp, hãy trả lời:
'Hiện shop chưa có sản phẩm phù hợp với yêu cầu của bạn.'

DANH SÁCH SẢN PHẨM:
$productText
        ";

        // 5️⃣ GHÉP LỊCH SỬ CHAT
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        foreach ($history as $msg) {
            $messages[] = [
                'role'    => $msg->role === 'user' ? 'user' : 'assistant',
                'content' => $msg->message
            ];
        }

        // 6️⃣ GỌI OPENAI (DÙNG LỊCH SỬ THẬT)
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => 'gpt-4o-mini',
                'temperature' => 0.2,
                'messages'    => $messages
            ]);

        if ($response->failed()) {
            dd('OPENAI FAIL', $response->status(), $response->body());
        }

        $aiReply = data_get(
            $response->json(),
            'choices.0.message.content',
            'Xin lỗi, hiện tôi chưa thể trả lời.'
        );

        // 7️⃣ LƯU TIN NHẮN AI
        AIChatMessage::create([
            'session_id' => $sessionId,
            'role'       => 'ai',
            'message'    => $aiReply
        ]);

        // 8️⃣ GIỚI HẠN 50 TIN
        $this->limitMessages($sessionId, 50);

        return response()->json(['reply' => $aiReply]);
    }
}
