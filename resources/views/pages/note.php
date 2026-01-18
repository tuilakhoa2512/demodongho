<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\AIChatMessage;

class AIChatController extends Controller
{
     //Session ID (không dùng Auth)
     
    private function sessionId()
    {
        return session()->getId();
    }

    private function userId()
    {
        return session()->get('id');
    }
    private function normalizeText(string $text): string
{
    $text = mb_strtolower($text);

    // bỏ dấu tiếng Việt
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

    // bỏ ký tự đặc biệt
    $text = preg_replace('/[^a-z0-9\s]/', '', $text);

    // chuẩn hoá khoảng trắng
    $text = preg_replace('/\s+/', ' ', trim($text));

    return $text;
}


//Chuẩn hoá giới tính từ câu hỏi userOUTPUT CHỈ TRẢ: male | female | null
 
private function detectGender(string $message): ?string
{
    $message = mb_strtolower($message);

    $maleKeywords = [
        'nam', 'men', 'male', 'boy', 'đàn ông', 'đồng hồ nam'
    ];

    $femaleKeywords = [
        'nữ', 'nu', 'female', 'girl', 'phụ nữ', 'đồng hồ nữ'
    ];

    foreach ($maleKeywords as $word) {
        if (str_contains($message, $word)) {
            return 'male';
        }
    }

    foreach ($femaleKeywords as $word) {
        if (str_contains($message, $word)) {
            return 'female';
        }
    }

    return null;
}


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

    
    //Load lịch sử chat
    public function history()
    {
        $messages = AIChatMessage::where('session_id', $this->sessionId())
            ->where(function ($q) {
                $q->where('user_id', $this->userId())
                  ->orWhereNull('user_id');
            })
            ->orderBy('id')
            ->get()
            ->map(function ($msg) {
                return [
                    'id'       => $msg->id,
                    'role'     => $msg->role,
                    'message'  => $msg->message,
                    'products' => $msg->products ?? []
                ];
            });

        return response()->json($messages);
    }


    //Xoá lịch sử chat
    
    public function clear()
    {
        AIChatMessage::where('session_id', $this->sessionId())->delete();
        session()->forget('ai_filter_context');
        return response()->json(['status' => 'ok']);
    }

    
    //Chat Gemini AI
    public function chat(Request $request)
    {     
        $request->validate([
            'message' => 'required|string'
        ]);

        $sessionId   = $this->sessionId();
        $userMessage = trim($request->message);

        //Lưu user message
        AIChatMessage::create([
            'session_id' => $sessionId,
            'user_id'    => $this->userId(),
            'role'       => 'user',
            'message'    => $userMessage
        ]);
        //lưu thông tin lọc để bot nhớ ngữ cảnh
        $context = session()->get('ai_filter_context', [
            'gender' => null,
            'strap'  => null,
            'brand'  => null,
            'price_min' => null,
            'price_max' => null,
        ]);

        //lọc sản phẩm
        $productsForUI = [];

        $query = Product::where('status', 1);
        
        
        $hasValidFilter = false;
        $brandDetectedThisTurn = false;


        
         //Lọc hãng tự động từ DB
        $allBrands = DB::table('brands')
            ->where('status', 1)
            ->select('id', 'name')
            ->get();
            

        $normalizedUser = $this->normalizeText($userMessage);
        foreach ($allBrands as $brand) {
            $normalizedBrand = $this->normalizeText($brand->name);
        
            if (str_contains($normalizedUser, $normalizedBrand)) {
        
                $context['brand']     = $brand->id;
                $context['gender']    = null;
                $context['strap']     = null;
                $context['price_min'] = null;
                $context['price_max'] = null;
            
                $hasValidFilter = true;
                $brandDetectedThisTurn = true;
                break;
            }
        }
        
         //Giới tính
         
        $gender = $this->detectGender($userMessage);

        if ($gender) {
            // $query->where('gender', $gender);
            $context['gender'] = $gender;
            $context['strap']  = null;// reset strap khi đổi giới tính
            $context['price_min']  = null;
            $context['price_max']  = null; 
            $hasValidFilter = true;
        }
        //Dây đeo
         
        $hasStrap = false;
        
        if (str_contains($userMessage, 'nhựa')) {
            // $query->where('strap_material', 'kim loại');
            $context['strap'] = 'nhựa';
            $hasValidFilter = true;
            $hasStrap = true;
            // CLEAR CONTEXT SAU KHI ĐÃ CHỌN XONG
        session()->forget('ai_filter');
        }
        else if (str_contains($userMessage, 'thép không gỉ')) {
            // $query->where('strap_material', 'kim loại');
            $context['strap'] = 'thép không gỉ';
            $hasValidFilter = true;
            $hasStrap = true;
            // CLEAR CONTEXT SAU KHI ĐÃ CHỌN XONG
        session()->forget('ai_filter');
        }
        else if (str_contains($userMessage, 'da')) {
            // $query->where('strap_material', 'da');
            $context['strap'] = 'da';
            $hasValidFilter = true;
            $hasStrap = true;
            // CLEAR CONTEXT SAU KHI ĐÃ CHỌN XONG
        session()->forget('ai_filter');
        }
        
        //Giá dưới (triệu)
        if (preg_match('/(dưới|<)\s*(\d+)\s*(triệu|tr)?/u', $userMessage, $m)) {
            $maxPrice = ((int)$m[2]) * 1_000_000;
            $context['price_max'] = $maxPrice;
            $query->where('price', '<=', $maxPrice);
            $hasValidFilter = true;
        }
        // Giá trên (triệu)
        if (preg_match('/(trên|>)\s*(\d+)\s*(triệu|tr)?/u', $userMessage, $m)) {
            $minPrice = ((int)$m[2]) * 1_000_000;
            $context['price_min'] = $minPrice;
            $query->where('price', '>=', $minPrice);
            $hasValidFilter = true;
        }

        $resetKeywords = [
            'reset', 'bỏ lọc',
            'làm lại', 'tìm lại'
        ];
        
        foreach ($resetKeywords as $kw) {
            if (str_contains($userMessage, $kw)) {
                session()->forget('ai_filter_context');
        
                AIChatMessage::create([
                    'session_id' => $sessionId,
                    'user_id'    => $this->userId(),
                    'role'       => 'ai',
                    'message'    => '👍 Mình đã reset bộ lọc. Bạn muốn tìm đồng hồ như thế nào?'
                ]);
        
                return response()->json([
                    'reply'    => '👍 Mình đã reset bộ lọc. Bạn muốn tìm đồng hồ như thế nào?',
                    'products' => []
                ]);
            }
        }
        
        // LƯU NGỮ CẢNH SAU KHI PARSE USER MESSAGE
        session()->put('ai_filter_context', $context);

        if ($context['gender']) {
            $query->where('gender', $context['gender']);
        }
        
        if ($context['strap']) {
            $query->where('strap_material', $context['strap']);
        }
        
        if ($context['brand']) {
            $query->where('brand_id', $context['brand']);
        }
        
        if ($context['price_min']) {
            $query->where('price', '>=', $context['price_min']);
        }
        
        if ($context['price_max']) {
            $query->where('price', '<=', $context['price_max']);
        }        
        // ===============================
// CASE: chỉ nhập "đồng hồ nam / nữ"
// ===============================
if (
    $context['gender']
    && !$context['strap']
    && !$context['brand']
    && !$context['price_min']
    && !$context['price_max']
) {
    $products = $query->get();

    if ($products->isEmpty()) {
        return response()->json([
            'reply'    => 'Hiện shop chưa có sản phẩm phù hợp với yêu cầu của bạn.',
            'products' => []
        ]);
    }

    $productsForUI = [];

    foreach ($products as $p) {
        $image = DB::table('product_images')
            ->where('product_id', $p->id)
            ->value('image_1');

        $productsForUI[] = [
            'id'    => $p->id,
            'name'  => $p->name,
            'price' => number_format($p->price) . ' ₫',
            'image' => $image ? asset('storage/' . $image) : asset('images/no-image.png'),
            'link'  => url('/product/' . ($p->slug ?? $p->id))
        ];
    }

    // Lưu gender để hỏi tiếp bước dây
    session([
        'ai_filter' => [
            'gender' => $context['gender']
        ]
    ]);

    $reply = "Shop có đồng hồ " . ($context['gender'] === 'male' ? 'nam' : 'nữ') . " 👍  
👉 Bạn thích loại dây nào (dây da, dây nhựa, thép không gỉ)?";

    AIChatMessage::create([
        'session_id' => $sessionId,
        'user_id'    => $this->userId(),
        'role'       => 'ai',
        'message'    => $reply,
        'products'   => $productsForUI
    ]);

    return response()->json([
        'reply'    => $reply,
        'products' => $productsForUI
    ]);
}



 //XỬ LÝ TRẢ LỜI TIẾP THEO (dựa trên context cũ)
 
$sessionFilter = session('ai_filter');

if ($sessionFilter && !$gender) {

    // ÁP LẠI FILTER CŨ
    if (!empty($sessionFilter['gender'])) {
        $query->where('gender', $sessionFilter['gender']);
        $hasValidFilter = true;
    }
}

      
 //Xác định user có ý định hỏi hãng hay không

$askForBrand = false;

if (
    str_contains($userMessage, 'hiệu') ||
    str_contains($userMessage, 'hãng')
) {
    $askForBrand = true;
}


 // PHÁT HIỆN KEYWORD KHÔNG TỒN TẠI TRONG DB

// Danh sách tên brand (lowercase)
$brandNames = $allBrands
->pluck('name')
->map(fn ($name) => mb_strtolower($name))
->toArray();

// Tách từ khoá trong câu hỏi
$words = preg_split('/\s+/', $userMessage);

// Cờ kiểm tra user có yêu cầu hãng không tồn tại
$invalidBrand = null;

 //Các keyword KHÔNG PHẢI brand (bỏ qua khi phát hiện brand không tồn tại)
 
$ignoreKeywords = [
    'da',  'nhựa', 'thép', 'không', 'gỉ',
    'sản', 'pham', 'phẩm', 'san',
    'đồng', 'hồ',
    'rẻ', 'đắt',
    'dưới', 'trên', 'tầm', 'giá',
    'triệu'
];

if ($askForBrand) {
    foreach ($words as $word) {
        $word = trim($word);

        if (mb_strlen($word) < 3) continue;
        if (in_array($word, $ignoreKeywords)) continue;

        if (!in_array($word, $brandNames)) {
            $invalidBrand = $word;
            break;
        }
    }
}

 // TRẢ VỀ SỚM NẾU HÃNG KHÔNG TỒN TẠI

if ($invalidBrand) {
    $reply = "Xin lỗi 😥 shop hiện **không có sản phẩm hiệu \"$invalidBrand\"**.";

    AIChatMessage::create([
        'session_id' => $sessionId,
        'user_id'    => $this->userId(),
        'role'       => 'ai',
        'message'    => $reply
    ]);

    return response()->json([
        'reply'    => $reply,
        'products' => []
    ]);
}

if (!$hasValidFilter) {
    $reply = '😅 Mình chưa hiểu rõ yêu cầu của bạn. Bạn có thể hỏi theo ví dụ như:
- đồng hồ nam
- đồng hồ nữ
- đồng hồ nữ/nam dây da
- đồng hồ (hiệu)tissot dưới/trên 10 triệu';

    AIChatMessage::create([
        'session_id' => $sessionId,
        'user_id'    => $this->userId(),
        'role'       => 'ai',
        'message'    => $reply
    ]);

    return response()->json([
        'reply'    => $reply,
        'products' => []
    ]);
}

if (
    !$hasValidFilter &&
    !preg_match('/(nam|nữ|trên|dưới|triệu|da|nhựa|thép|hãng|hiệu)/u', $userMessage)
) {
    return response()->json([
        'reply'    => '😅 Mình chưa hiểu yêu cầu. Bạn có thể hỏi: đồng hồ nam, đồng hồ nữ dây da...',
        'products' => []
    ]);
}

        $products = $query->get();

if (!isset($productsForUI)) {
    $productsForUI = [];
}

        $followUpQuestion = null;

         // KHÔNG CÓ SẢN PHẨM

        if ($products->isEmpty()) {
            $reply = 'Hiện shop chưa có sản phẩm phù hợp với yêu cầu của bạn.';

            AIChatMessage::create([
                'session_id' => $sessionId,
                'user_id'    => $this->userId(),
                'role'       => 'ai',
                'message'    => $reply
            ]);

            return response()->json(['reply' => $reply,'products' => []]);
        }


         // CHUẨN BỊ DATA SẢN PHẨM CHO UI
        $productsForUI = [];

        foreach ($products as $p) {

            $image = DB::table('product_images')
                ->where('product_id', $p->id)
                ->value('image_1');

            // Fallback link nếu không có slug
            $productLink = $p->slug
                ? url('/product/' . $p->slug)
                : url('/product/' . $p->id);

            $productsForUI[] = [
                'id'    => $p->id,
                'name'  => $p->name,
                'price' => number_format($p->price) . ' ₫',
                'image' => $image ? asset('storage/' . $image) : asset('images/no-image.png'),
                'link'  => url('/product/' . ($p->slug ?? $p->id))
            ];
        }


         // CHUẨN BỊ PROMPT CHO GEMINI
        $productText = '';
        foreach ($products as $p) {
        $productText .= "- {$p->name}, "
        . "giá {$p->price} VNĐ, "
        . "giới tính {$p->gender}, "
        . "dây {$p->strap_material}\n";
}

 $prompt = <<<PROMPT
Bạn là chatbot bán đồng hồ của website thương mại điện tử.

⚠️ QUY TẮC BẮT BUỘC (PHẢI TUÂN THỦ):
- KHÔNG tự ý lọc sản phẩm
- KHÔNG đề xuất sản phẩm ngoài danh sách được cung cấp
- KHÔNG hỏi lại những tiêu chí đã có (giới tính, dây, hãng, giá)
- Chỉ tư vấn dựa trên dữ liệu PHP gửi vào
- Trả lời NGẮN GỌN, thân thiện, đúng vai trò nhân viên bán hàng

============================
DANH SÁCH SẢN PHẨM PHÙ HỢP
============================
$productText

============================
CÂU HỎI CỦA KHÁCH
============================
{$request->message}

============================
HƯỚNG DẪN TRẢ LỜI
============================
1. Nếu có sản phẩm:
   - Giới thiệu ngắn gọn 1–2 mẫu tiêu biểu
   - Có thể so sánh nhẹ (giá, dây, phong cách)
   - Không liệt kê lại toàn bộ danh sách

2. Nếu cần hỏi thêm để lọc chính xác hơn:
   - Chỉ hỏi MỘT câu duy nhất
   - Ưu tiên hỏi theo thứ tự:
     a. Dây đeo (da / nhựa / thép không gỉ)
     b. Hãng
     c. Tầm giá

3. Nếu đã đủ điều kiện:
   - Kết thúc bằng câu gợi ý hành động
     (ví dụ: “Bạn muốn xem chi tiết mẫu nào không?”)

============================
GỢI Ý HỎI NGƯỢC (NẾU CÓ)
============================
{$followUpQuestion}

 LƯU Ý CUỐI:
- Không nói về "AI", "hệ thống", "dữ liệu"
- Không dùng emoji quá nhiều (tối đa 1–2 cái)
- Giữ giọng thân thiện như nhân viên shop thật

PROMPT;
         // GỌI GEMINI API
        try {
            $response = Http::timeout(30)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key='
                . config('services.gemini.key'),
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => 300,
                    ]
                ]
            );

            $aiReply = data_get(
                $response->json(),
                'candidates.0.content.parts.0.text',
                'Shop có một số mẫu phù hợp, bạn vui lòng tham khảo.'
            );

        } catch (\Throwable $e) {
            Log::error('GEMINI ERROR: ' . $e->getMessage());
            $aiReply = 'Xin lỗi, hệ thống AI đang bận.';
        }

        //Lưu AI message
        AIChatMessage::create([
            'session_id' => $sessionId,
            'user_id'    => $this->userId(),
            'role'       => 'ai',
            'message'    => $aiReply,
            'products'   => $productsForUI
        ]);

        
         //Giới hạn message
        
        $this->limitMessages($sessionId);

        //response cuối
        return response()->json([
            'reply'    => $aiReply,
            'products' => $productsForUI
        ]);
    }
}
