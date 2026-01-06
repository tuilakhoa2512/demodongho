<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\PromotionRule;
use App\Models\PromotionCode;
use App\Models\PromotionRedemption;

class PromotionService
{
    /**
     * ============================
     * A) PRODUCT: Tính giá sau ưu đãi
     * ============================
     *
     * @param  object $product  (Eloquent Product) cần có: id, price, category_id, brand_id
     * @return array
     */
    public function calcProductFinalPrice($product): array
    {
        $price = (int) $product->price;

        $best = $this->findBestRuleForProduct($product);

        if (!$best) {
            return [
                'campaign'        => null,
                'rule'            => null,
                'final_price'     => $price,
                'discount_amount' => 0,
                'promo_label'     => null,
            ];
        }

        [$campaign, $rule] = $best;

        $discountAmount = $this->calcDiscountAmount(
            $price,
            $rule->discount_type,
            (int) $rule->discount_value,
            $rule->max_discount_amount ?? null
        );

        $final = max(0, $price - $discountAmount);

        return [
            'campaign'        => $campaign,
            'rule'            => $rule,
            'final_price'     => $final,
            'discount_amount' => $discountAmount,
            'promo_label'     => $this->buildPromoLabel($rule->discount_type, (int)$rule->discount_value),
        ];
    }

    /**
     * Tìm rule tốt nhất cho product theo:
     * - campaign active + rule active + scope=product
     * - target match (all/product/category/brand)
     * - ưu tiên theo: campaign.priority desc, rule.priority desc, campaign.id desc, rule.id desc
     */
    protected function findBestRuleForProduct($product): ?array
    {
        $now = Carbon::now();

        $rules = PromotionRule::query()
            ->with([
                'campaign',
                'targets' => function ($q) {
                    $q->where('status', 1);
                }
            ])
            ->where('scope', 'product')
            ->where('status', 1)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->whereHas('campaign', function ($q) use ($now) {
                $q->where('status', 1)
                  ->where(function ($qq) use ($now) {
                      $qq->whereNull('start_at')->orWhere('start_at', '<=', $now);
                  })
                  ->where(function ($qq) use ($now) {
                      $qq->whereNull('end_at')->orWhere('end_at', '>=', $now);
                  });
            })
            ->get();

        $candidates = [];

        foreach ($rules as $rule) {
            if (!$rule->campaign) continue;

            if ($this->ruleMatchesProductTargets($rule, $product)) {
                $candidates[] = $rule;
            }
        }

        if (empty($candidates)) return null;

        usort($candidates, function ($a, $b) {
            $ap = (int)($a->campaign->priority ?? 0);
            $bp = (int)($b->campaign->priority ?? 0);
            if ($ap !== $bp) return $bp <=> $ap;

            $arp = (int)($a->priority ?? 0);
            $brp = (int)($b->priority ?? 0);
            if ($arp !== $brp) return $brp <=> $arp;

            if ((int)$a->campaign_id !== (int)$b->campaign_id) return (int)$b->campaign_id <=> (int)$a->campaign_id;

            return (int)$b->id <=> (int)$a->id;
        });

        $bestRule = $candidates[0];

        return [$bestRule->campaign, $bestRule];
    }

    /**
     * Match target cho product:
     * - all
     * - product: target_id = product.id
     * - category: target_id = product.category_id
     * - brand: target_id = product.brand_id
     */
    protected function ruleMatchesProductTargets(PromotionRule $rule, $product): bool
    {
        $targets = $rule->targets ?? collect();
        if ($targets->isEmpty()) return false;

        foreach ($targets as $t) {
            if ((int)$t->status !== 1) continue;

            if ($t->target_type === 'all') return true;

            if ($t->target_type === 'product' && (int)$t->target_id === (int)$product->id) return true;

            if ($t->target_type === 'category' && (int)$t->target_id === (int)($product->category_id ?? 0)) return true;

            if ($t->target_type === 'brand' && (int)$t->target_id === (int)($product->brand_id ?? 0)) return true;
        }

        return false;
    }

    /**
     * ============================
     * B) ORDER: Tính giảm giá hóa đơn (+ code)
     * ============================
     *
     * @param int $subtotal
     * @param array $cartProducts
     * @param string|null $code
     * @param int|null $userId
     * @return array
     */
    public function calcOrderDiscount(int $subtotal, array $cartProducts = [], ?string $code = null, ?int $userId = null): array
    {
        $subtotal = max(0, (int)$subtotal);
        $now = Carbon::now();

        // 1) Có code => validate code => lấy rule
        if (!empty($code)) {
            $codeRow = $this->validateAndGetCode($code, $subtotal, $userId, $now);

            if (!$codeRow) {
                return [
                    'ok'              => false,
                    'message'         => 'Mã giảm giá không hợp lệ hoặc đã hết hiệu lực.',
                    'campaign'        => null,
                    'rule'            => null,
                    'code'            => null,
                    'discount_amount' => 0,
                    'final_total'     => $subtotal,
                ];
            }

            $rule = $codeRow->rule;
            $campaign = $rule?->campaign;

            // match targets (nếu order rule có target)
            if (!$this->ruleMatchesOrderTargets($rule, $cartProducts)) {
                return [
                    'ok'              => false,
                    'message'         => 'Mã này không áp dụng cho giỏ hàng hiện tại (không khớp target).',
                    'campaign'        => $campaign,
                    'rule'            => $rule,
                    'code'            => $codeRow,
                    'discount_amount' => 0,
                    'final_total'     => $subtotal,
                ];
            }

            // check min_order_subtotal ở rule (nếu có)
            if (!is_null($rule->min_order_subtotal) && $subtotal < (int)$rule->min_order_subtotal) {
                return [
                    'ok'              => false,
                    'message'         => 'Chưa đạt giá trị tối thiểu của hóa đơn để áp dụng ưu đãi.',
                    'campaign'        => $campaign,
                    'rule'            => $rule,
                    'code'            => $codeRow,
                    'discount_amount' => 0,
                    'final_total'     => $subtotal,
                ];
            }

            $discountAmount = $this->calcDiscountAmount(
                $subtotal,
                $rule->discount_type,
                (int)$rule->discount_value,
                $rule->max_discount_amount ?? null
            );

            // clamp theo code.max_discount nếu có
            if (!is_null($codeRow->max_discount)) {
                $discountAmount = min($discountAmount, (int)$codeRow->max_discount);
            }

            $discountAmount = min($discountAmount, $subtotal);
            $final = max(0, $subtotal - $discountAmount);

            return [
                'ok'              => true,
                'message'         => 'Áp dụng mã thành công.',
                'campaign'        => $campaign,
                'rule'            => $rule,
                'code'            => $codeRow,
                'discount_amount' => $discountAmount,
                'final_total'     => $final,
            ];
        }

        // 2) Không có code => auto apply rule order (chỉ lấy rule KHÔNG có codes active)
        $best = $this->findBestAutoOrderRule($subtotal, $cartProducts, $now);

        if (!$best) {
            return [
                'ok'              => true,
                'message'         => 'Không có ưu đãi hóa đơn tự áp dụng.',
                'campaign'        => null,
                'rule'            => null,
                'code'            => null,
                'discount_amount' => 0,
                'final_total'     => $subtotal,
            ];
        }

        [$campaign, $rule] = $best;

        $discountAmount = $this->calcDiscountAmount(
            $subtotal,
            $rule->discount_type,
            (int)$rule->discount_value,
            $rule->max_discount_amount ?? null
        );

        $discountAmount = min($discountAmount, $subtotal);
        $final = max(0, $subtotal - $discountAmount);

        return [
            'ok'              => true,
            'message'         => 'Đã tự áp dụng ưu đãi hóa đơn.',
            'campaign'        => $campaign,
            'rule'            => $rule,
            'code'            => null,
            'discount_amount' => $discountAmount,
            'final_total'     => $final,
        ];
    }

    /**
     * Validate code (status/time/rule/campaign + min_subtotal + usage limits)
     * Return PromotionCode|null
     */
    protected function validateAndGetCode(string $code, int $subtotal, ?int $userId, Carbon $now): ?PromotionCode
    {
        $code = trim(strtoupper($code));
        if ($code === '') return null;

        $codeRow = PromotionCode::query()
            ->with(['rule.campaign'])
            ->where('code', $code)
            ->where('status', 1)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->first();

        if (!$codeRow || !$codeRow->rule || !$codeRow->rule->campaign) return null;

        $rule = $codeRow->rule;
        $campaign = $rule->campaign;

        // rule/campaign phải active + đúng scope=order
        if ($rule->scope !== 'order') return null;
        if ((int)$rule->status !== 1) return null;
        if ((int)$campaign->status !== 1) return null;

        if (!$this->isActiveWindow($rule->start_at, $rule->end_at, $now)) return null;
        if (!$this->isActiveWindow($campaign->start_at, $campaign->end_at, $now)) return null;

        // min_subtotal của code
        $minSubtotal = (int)($codeRow->min_subtotal ?? 0);
        if ($subtotal < $minSubtotal) return null;

        // usage limits theo code: max_uses
        if (!is_null($codeRow->max_uses)) {
            $used = PromotionRedemption::query()
                ->where('code_id', $codeRow->id)
                ->count();
            if ($used >= (int)$codeRow->max_uses) return null;
        }

        // usage limits per user
        if (!is_null($codeRow->max_uses_per_user) && !empty($userId)) {
            $usedUser = PromotionRedemption::query()
                ->where('code_id', $codeRow->id)
                ->where('user_id', (int)$userId)
                ->count();
            if ($usedUser >= (int)$codeRow->max_uses_per_user) return null;
        }

        return $codeRow;
    }

    /**
     * Auto order rule: chỉ lấy rule order không có codes active
     */
    protected function findBestAutoOrderRule(int $subtotal, array $cartProducts, Carbon $now): ?array
    {
        $rules = PromotionRule::query()
            ->with([
                'campaign',
                'targets' => function ($q) {
                    $q->where('status', 1);
                },
                'codes' => function ($q) use ($now) {
                    $q->where('status', 1)
                      ->where(function ($qq) use ($now) {
                          $qq->whereNull('start_at')->orWhere('start_at', '<=', $now);
                      })
                      ->where(function ($qq) use ($now) {
                          $qq->whereNull('end_at')->orWhere('end_at', '>=', $now);
                      });
                }
            ])
            ->where('scope', 'order')
            ->where('status', 1)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            })
            ->whereHas('campaign', function ($q) use ($now) {
                $q->where('status', 1)
                  ->where(function ($qq) use ($now) {
                      $qq->whereNull('start_at')->orWhere('start_at', '<=', $now);
                  })
                  ->where(function ($qq) use ($now) {
                      $qq->whereNull('end_at')->orWhere('end_at', '>=', $now);
                  });
            })
            ->get();

        $candidates = [];

        foreach ($rules as $rule) {
            if (!$rule->campaign) continue;

            // nếu rule có codes active => không auto apply
            if (!empty($rule->codes) && $rule->codes->count() > 0) continue;

            // min order subtotal theo rule
            if (!is_null($rule->min_order_subtotal) && $subtotal < (int)$rule->min_order_subtotal) continue;

            // match targets theo giỏ
            if (!$this->ruleMatchesOrderTargets($rule, $cartProducts)) continue;

            $candidates[] = $rule;
        }

        if (empty($candidates)) return null;

        usort($candidates, function ($a, $b) {
            $ap = (int)($a->campaign->priority ?? 0);
            $bp = (int)($b->campaign->priority ?? 0);
            if ($ap !== $bp) return $bp <=> $ap;

            $arp = (int)($a->priority ?? 0);
            $brp = (int)($b->priority ?? 0);
            if ($arp !== $brp) return $brp <=> $arp;

            if ((int)$a->campaign_id !== (int)$b->campaign_id) return (int)$b->campaign_id <=> (int)$a->campaign_id;

            return (int)$b->id <=> (int)$a->id;
        });

        $bestRule = $candidates[0];

        return [$bestRule->campaign, $bestRule];
    }

    /**
     * Match target cho order rule:
     * - Nếu targets có 'all' => true
     * - Nếu có product/category/brand => chỉ cần giỏ có ÍT NHẤT 1 item match
     * - Nếu rule không có target => false
     */
    protected function ruleMatchesOrderTargets(PromotionRule $rule, array $cartProducts): bool
    {
        $targets = $rule->targets ?? collect();
        if ($targets->isEmpty()) return false;

        $products = [];
        foreach ($cartProducts as $item) {
            if (is_array($item) && isset($item['product'])) $products[] = $item['product'];
            else $products[] = $item;
        }

        foreach ($targets as $t) {
            if ((int)$t->status !== 1) continue;

            if ($t->target_type === 'all') return true;

            foreach ($products as $p) {
                if (!$p) continue;

                if ($t->target_type === 'product' && (int)$t->target_id === (int)$p->id) return true;

                if ($t->target_type === 'category' && (int)$t->target_id === (int)($p->category_id ?? 0)) return true;

                if ($t->target_type === 'brand' && (int)$t->target_id === (int)($p->brand_id ?? 0)) return true;
            }
        }

        return false;
    }

    /**
     * ============================
     * C) REDEMPTION: ghi log dùng ưu đãi
     * ============================
     *
     * Gọi khi tạo order thành công.
     *
     * @param array $data keys:
     * - promotion_rule_id (required)
     * - code_id (nullable)
     * - order_id (required)
     * - user_id (nullable)
     * - discount_amount (required)
     */
    public function logRedemption(array $data): PromotionRedemption
    {
        $payload = [
            'promotion_rule_id' => (int)($data['promotion_rule_id'] ?? 0),
            'code_id' => !empty($data['code_id']) ? (int)$data['code_id'] : null,
            'order_id'          => (int)($data['order_id'] ?? 0),
            'user_id'           => !empty($data['user_id']) ? (int)$data['user_id'] : null,
            'discount_amount'   => (int)($data['discount_amount'] ?? 0),
        ];

        return PromotionRedemption::create($payload);
    }

    /**
     * ============================
     * Helpers
     * ============================
     */
    protected function isActiveWindow($startAt, $endAt, Carbon $now): bool
    {
        if (!empty($startAt) && Carbon::parse($startAt)->gt($now)) return false;
        if (!empty($endAt) && Carbon::parse($endAt)->lt($now)) return false;
        return true;
    }

    protected function buildPromoLabel(string $type, int $value): string
    {
        if ($type === 'percent') {
            return '-' . (int)$value . '%';
        }
        return '-' . number_format((int)$value, 0, ',', '.') . ' đ';
    }

// ✅ đổi sang public để service khác gọi được
    public function calcDiscountAmount(int $amount, string $discountType, int $discountValue, $maxDiscountAmount = null): int
    {
        $amount = max(0, (int)$amount);
        if ($amount <= 0) return 0;

        if ($discountType === 'percent') {
            $discount = (int) floor($amount * ($discountValue / 100));
        } else {
            $discount = (int) $discountValue;
        }

        $discount = max(0, $discount);

        if (!is_null($maxDiscountAmount)) {
            $discount = min($discount, (int)$maxDiscountAmount);
        }

        return min($discount, $amount);
    }
}
