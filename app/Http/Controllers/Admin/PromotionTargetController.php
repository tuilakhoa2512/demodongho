<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionCampaign;
use App\Models\PromotionRule;
use App\Models\PromotionTarget;

class PromotionTargetController extends Controller
{
    public function store(Request $request, $id, $ruleId)
    {
        $campaign = PromotionCampaign::findOrFail($id);

        $rule = PromotionRule::where('campaign_id', $campaign->id)
            ->where('id', (int)$ruleId)
            ->firstOrFail();

        $data = $request->validate([
            'target_type' => 'required|in:all,product,category,brand',
            'target_id'   => 'nullable|integer|min:1',
            'status'      => 'required|in:0,1',
        ]);

        // target_type=all => target_id null
        if ($data['target_type'] === 'all') {
            $data['target_id'] = null;
        } else {
            if (empty($data['target_id'])) {
                return back()->with('error', 'Bạn phải chọn Target khi target_type != all.');
            }
        }

        $data['rule_id'] = (int)$rule->id;

        PromotionTarget::create($data);

        return back()->with('success', 'Đã thêm target cho rule.');
    }

    public function toggleStatus($id, $ruleId, $targetId)
    {
        $campaign = PromotionCampaign::findOrFail($id);

        $rule = PromotionRule::where('campaign_id', $campaign->id)
            ->where('id', (int)$ruleId)
            ->firstOrFail();

        $target = PromotionTarget::where('rule_id', $rule->id)
            ->where('id', (int)$targetId)
            ->firstOrFail();

        $target->status = $target->status ? 0 : 1;
        $target->save();

        return back()->with('success', 'Đã đổi trạng thái target.');
    }
}
