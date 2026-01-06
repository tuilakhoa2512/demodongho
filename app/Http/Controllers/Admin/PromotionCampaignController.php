<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotionCampaign;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class PromotionCampaignController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim((string)$request->get('q', ''));
        $status = $request->get('status', '');

        $query = PromotionCampaign::query()
            ->with(['rules' => function ($qq) {
                $qq->orderByDesc('priority')->orderByDesc('id');
            }])
            ->orderByDesc('priority')
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        if ($status !== '' && $status !== null) {
            $query->where('status', (int)$status);
        }

        $campaigns = $query->paginate(15)->appends($request->query());

        return view('admin.promotions.index', compact('campaigns', 'q', 'status'));
    }

    public function create()
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at'    => 'nullable|date',
            'end_at'      => 'nullable|date|after_or_equal:start_at',
            'priority'    => 'nullable|integer|min:0',
            'status'      => 'required|in:0,1',
        ]);

        $data['priority'] = (int)($data['priority'] ?? 0);

        $campaign = PromotionCampaign::create($data);

        return redirect()
            ->route('admin.promotions.edit', $campaign->id)
            ->with('success', 'Tạo campaign thành công. Bây giờ thêm rule.');
    }

    public function edit($id)
    {
        $campaign = PromotionCampaign::with([
            'rules' => function ($q) {
                $q->orderByDesc('priority')->orderByDesc('id');
            },
            'rules.targets' => function ($q) {
                $q->orderByDesc('id');
            },
            'rules.codes' => function ($q) {
                $q->orderByDesc('id');
            },
        ])->findOrFail($id);

        // dropdown cho target
        $brands     = Brand::where('status', 1)->orderBy('name')->get();
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $products   = Product::where('status', 1)->orderByDesc('id')->limit(500)->get();

        return view('admin.promotions.edit', compact('campaign', 'brands', 'categories', 'products'));
    }

    public function update(Request $request, $id)
    {
        $campaign = PromotionCampaign::findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at'    => 'nullable|date',
            'end_at'      => 'nullable|date|after_or_equal:start_at',
            'priority'    => 'nullable|integer|min:0',
            'status'      => 'required|in:0,1',
        ]);

        $data['priority'] = (int)($data['priority'] ?? 0);

        $campaign->update($data);

        return back()->with('success', 'Cập nhật campaign thành công.');
    }

    public function toggleStatus($id)
    {
        $campaign = PromotionCampaign::findOrFail($id);
        $campaign->status = $campaign->status ? 0 : 1;
        $campaign->save();

        return back()->with('success', 'Đã đổi trạng thái campaign.');
    }
}
