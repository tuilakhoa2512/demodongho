<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionRedemption;

class PromotionRedemptionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $query = PromotionRedemption::query()
            ->with(['campaign', 'rule', 'code'])
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where('code', 'like', "%{$q}%");
        }

        $redemptions = $query->paginate(20)->appends($request->query());

        return view('admin.promotion_redemptions.index', compact('redemptions', 'q'));
    }

    public function show($id)
    {
        $redemption = PromotionRedemption::with(['campaign', 'rule', 'code'])->findOrFail($id);

        return view('admin.promotion_redemptions.show', compact('redemption'));
    }
}
