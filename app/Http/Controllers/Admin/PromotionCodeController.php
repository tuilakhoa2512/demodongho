<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionCampaign;
use App\Models\PromotionRule;
use App\Models\PromotionCode;

class PromotionCodeController extends Controller
{
    public function store(Request $request, $id, $ruleId)
    {
        $campaign = PromotionCampaign::findOrFail($id);

        $rule = PromotionRule::where('campaign_id', $campaign->id)
            ->where('id', (int)$ruleId)
            ->firstOrFail();

        // Codes: chỉ hợp lý cho rule scope=order
        if ($rule->scope !== 'order') {
            return back()->with('error', 'Codes chỉ áp dụng cho rule scope=order.');
        }

        $data = $request->validate([
            'code'             => 'required|string|max:50',
            'min_subtotal'     => 'nullable|integer|min:0',
            'max_discount'     => 'nullable|integer|min:0',
            'max_uses'         => 'nullable|integer|min:0',
            'max_uses_per_user'=> 'nullable|integer|min:0',
            'start_at'         => 'nullable|date',
            'end_at'           => 'nullable|date|after_or_equal:start_at',
            'status'           => 'required|in:0,1',
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['rule_id'] = (int)$rule->id;

        // unique code (toàn hệ thống)
        $exists = PromotionCode::where('code', $data['code'])->exists();
        if ($exists) {
            return back()->with('error', 'Code đã tồn tại. Hãy dùng code khác.');
        }

        PromotionCode::create($data);

        return back()->with('success', 'Đã thêm code.');
    }

    public function toggleStatus($id, $ruleId, $codeId)
    {
        $campaign = PromotionCampaign::findOrFail($id);

        $rule = PromotionRule::where('campaign_id', $campaign->id)
            ->where('id', (int)$ruleId)
            ->firstOrFail();

        $code = PromotionCode::where('rule_id', $rule->id)
            ->where('id', (int)$codeId)
            ->firstOrFail();

        $code->status = $code->status ? 0 : 1;
        $code->save();

        return back()->with('success', 'Đã đổi trạng thái code.');
    }
}
