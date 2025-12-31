<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountProductDetailController extends Controller
{
    public function index($id)
    {
        $discount = DiscountProduct::findOrFail($id);

        $today = now()->toDateString();
        DB::table('discount_product_details')
            ->where('discount_product_id', $discount->id)
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<', $today)
            ->where('status', 1)
            ->update([
                'status'     => 0,
                'updated_at' => now(),
            ]);

        // Danh sách sản phẩm đã gắn (paginate)
        $attached = $discount->products()
            ->with('productImage')
            ->orderByDesc('products.id')
            ->paginate(15);

        $availableProducts = Product::with('productImage')
            ->leftJoin('discount_product_details as dpd_this', function ($join) use ($discount) {
                $join->on('products.id', '=', 'dpd_this.product_id')
                     ->where('dpd_this.discount_product_id', '=', $discount->id);
            })
            ->where(function ($query) use ($today) {
                // 1) Sản phẩm KHÔNG có bản ghi "còn hạn" ở bất kỳ ưu đãi nào
                $query->whereNotExists(function ($q) use ($today) {
                    $q->select(DB::raw(1))
                      ->from('discount_product_details as dpd_any')
                      ->whereColumn('dpd_any.product_id', 'products.id')
                      ->where(function ($qq) use ($today) {
                          $qq->whereNull('dpd_any.expiration_date')
                             ->orWhere('dpd_any.expiration_date', '>=', $today);
                      });
                })
               
                ->orWhere(function ($q) use ($today) {
                    $q->whereNotNull('dpd_this.product_id')
                      ->whereNotNull('dpd_this.expiration_date')
                      ->where('dpd_this.expiration_date', '<', $today);
                });
            })
            ->select('products.*')
            ->orderByDesc('products.id')
            ->get();

        return view('admin.discount_products.products', compact(
            'discount',
            'attached',
            'availableProducts'
        ));
    }
    public function attach(Request $request, $id)
    {
        $discount = DiscountProduct::findOrFail($id);

        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'expiration_date' => 'nullable|date',
        ]);

        $productId = (int) $request->product_id;
        $exp       = $request->expiration_date;

        $today = now()->toDateString();

        $hasAnyValid = DB::table('discount_product_details')
            ->where('product_id', $productId)
            ->where(function ($q) use ($today) {
                $q->whereNull('expiration_date')
                  ->orWhere('expiration_date', '>=', $today);
            })
            ->exists();

        if ($hasAnyValid) {
            return back()->with('error', 'Sản phẩm này đang còn hạn ở một ưu đãi khác, không thể gán thêm.')->withInput();
        }

        // Nếu đã tồn tại pivot trong discount hiện tại thì báo lỗi
        $exists = DB::table('discount_product_details')
            ->where('discount_product_id', $discount->id)
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Sản phẩm này đã được gán vào ưu đãi này.')->withInput();
        }

        $isExpired = ($exp && $exp < $today);

        // status chỉ 1 hoặc 0
        $pivotStatus = 0;
        if ((int) $discount->status === 1 && !$isExpired) {
            $pivotStatus = 1;
        }

        $discount->products()->attach($productId, [
            'expiration_date' => $exp,
            'status'          => $pivotStatus,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return back()->with('success', 'Đã gán sản phẩm vào ưu đãi.');
    }

    public function updateExpiration(Request $request, $id, $productId)
    {
        $discount = DiscountProduct::findOrFail($id);

        $request->validate([
            'expiration_date' => 'nullable|date',
        ]);

        $exp = $request->expiration_date;

        $today = now()->toDateString();
        $isExpired = ($exp && $exp < $today);

        $pivot = DB::table('discount_product_details')
            ->where('discount_product_id', $discount->id)
            ->where('product_id', $productId)
            ->first();

        if (!$pivot) {
            return back()->with('error', 'Không tìm thấy sản phẩm trong ưu đãi.');
        }

        $newStatus = (int) $pivot->status;

        // Hết hạn => ép về 0
        if ($isExpired) {
            $newStatus = 0;
        }

        // Chương trình tắt => ép về 0
        if ((int) $discount->status === 0) {
            $newStatus = 0;
        }

        DB::table('discount_product_details')
            ->where('discount_product_id', $discount->id)
            ->where('product_id', $productId)
            ->update([
                'expiration_date' => $exp,
                'status'          => $newStatus,
                'updated_at'      => now(),
            ]);

        return back()->with('success', 'Đã cập nhật hạn ưu đãi.');
    }

    public function toggle($id, $productId)
    {
        $discount = DiscountProduct::findOrFail($id);

        $pivot = DB::table('discount_product_details')
            ->where('discount_product_id', $discount->id)
            ->where('product_id', $productId)
            ->first();

        if (!$pivot) {
            return back()->with('error', 'Không tìm thấy sản phẩm trong ưu đãi.');
        }

        $today = now()->toDateString();
        $exp = $pivot->expiration_date;
        $isExpired = ($exp && $exp < $today);

        // Nếu hết hạn mà pivot đang bật => tự ép về 0 trước
        if ($isExpired && (int) $pivot->status === 1) {
            DB::table('discount_product_details')
                ->where('discount_product_id', $discount->id)
                ->where('product_id', $productId)
                ->update([
                    'status'     => 0,
                    'updated_at' => now(),
                ]);

            return back()->with('error', 'Ưu đãi đã hết hạn nên đã tự chuyển sang Ngừng.');
        }

        // Nếu chương trình đang tắt => không cho bật
        if ((int) $discount->status === 0 && (int) $pivot->status === 0) {
            return back()->with('error', 'Loại Ưu Đãi đang tắt. Không thể áp dụng.');
        }

        // Nếu hết hạn => không cho bật
        if ($isExpired && (int) $pivot->status === 0) {
            return back()->with('error', 'Ưu đãi đã hết hạn. Không thể bật áp dụng.');
        }

        // Toggle bình thường
        $newStatus = ((int) $pivot->status === 1) ? 0 : 1;

        // Chặn bật nếu chương trình đang tắt
        if ($newStatus === 1 && (int) $discount->status === 0) {
            return back()->with('error', 'Loại Ưu Đãi đang tắt. Không thể áp dụng.');
        }

        // Chặn bật nếu đã hết hạn
        if ($newStatus === 1 && $isExpired) {
            return back()->with('error', 'Ưu đãi đã hết hạn. Không thể bật áp dụng.');
        }

        DB::table('discount_product_details')
            ->where('discount_product_id', $discount->id)
            ->where('product_id', $productId)
            ->update([
                'status'     => $newStatus,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Đã cập nhật trạng thái áp dụng.');
    }
}
