<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionCampaign;
use App\Models\PromotionRule;

class PromotionRuleController extends Controller
{
    public function store(Request $request, $id)
    {
        $campaign = PromotionCampaign::findOrFail($id);

        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'scope'              => 'required|in:product,order',
            'discount_type'      => 'required|in:percent,fixed',
            'discount_value'     => 'required|integer|min:0',
            'max_discount_amount'=> 'nullable|integer|min:0',
            'min_order_subtotal' => 'nullable|integer|min:0', // chỉ meaningful cho scope=order
            'start_at'           => 'nullable|date',
            'end_at'             => 'nullable|date|after_or_equal:start_at',
            'priority'           => 'nullable|integer|min:0',
            'status'             => 'required|in:0,1',
        ]);

        $data['campaign_id'] = (int)$campaign->id;
        $data['priority'] = (int)($data['priority'] ?? 0);

        // Nếu scope=product => min_order_subtotal nên null
        if ($data['scope'] === 'product') {
            $data['min_order_subtotal'] = null;
        }

        PromotionRule::create($data);

        return back()->with('success', 'Đã thêm rule.');
    }

    public function update(Request $request, $id, $ruleId)
    {
        $campaign = PromotionCampaign::findOrFail($id);

        $rule = PromotionRule::where('campaign_id', $campaign->id)
            ->where('id', (int)$ruleId)
            ->firstOrFail();

        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'scope'              => 'required|in:product,order',
            'discount_type'      => 'required|in:percent,fixed',
            'discount_value'     => 'required|integer|min:0',
            'max_discount_amount'=> 'nullable|integer|min:0',
            'min_order_subtotal' => 'nullable|integer|min:0',
            'start_at'           => 'nullable|date',
            'end_at'             => 'nullable|date|after_or_equal:start_at',
            'priority'           => 'nullable|integer|min:0',
            'status'             => 'required|in:0,1',
        ]);

        $data['priority'] = (int)($data['priority'] ?? 0);

        if ($data['scope'] === 'product') {
            $data['min_order_subtotal'] = null;
        }

        $rule->update($data);

        return back()->with('success', 'Đã cập nhật rule.');
    }

    public function toggleStatus($id, $ruleId)
    {
        $campaign = PromotionCampaign::findOrFail($id);

        $rule = PromotionRule::where('campaign_id', $campaign->id)
            ->where('id', (int)$ruleId)
            ->firstOrFail();

        $rule->status = $rule->status ? 0 : 1;
        $rule->save();

        return back()->with('success', 'Đã đổi trạng thái rule.');
    }
}
