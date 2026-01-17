@extends('pages.admin_layout')
@section('admin_content')

@php
  $campaign = $campaign ?? $promotion ?? null; // fallback nếu bạn còn đặt tên biến cũ
  $c = $campaign;

  $rules = $rules ?? ($c->rules ?? collect());

  $brands     = $brands ?? collect();
  $categories = $categories ?? collect();
  $products   = $products ?? collect();

  $brandMap    = $brands->pluck('name', 'id');
  $categoryMap = $categories->pluck('name', 'id');
  $productMap  = $products->pluck('name', 'id');

  $timeText = '—';
  if ($c->start_at || $c->end_at) {
    $timeText =
      ($c->start_at ? \Carbon\Carbon::parse($c->start_at)->format('d/m/Y H:i') : '…')
      . ' → ' .
      ($c->end_at ? \Carbon\Carbon::parse($c->end_at)->format('d/m/Y H:i') : '…');
  }
@endphp

<style>
  .promo-toolbar{
    display:flex; gap:10px; flex-wrap:wrap; align-items:center;
    justify-content:center; margin: 10px 0 16px;
  }
  .promo-toolbar .btn{ min-width: 160px; }
  .promo-toolbar form{ margin:0; }

  .target-row{
    display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;
  }
  .target-row .form-group{
    margin:0; min-height:72px; display:flex; flex-direction:column; justify-content:flex-end;
  }
  .target-row label{ margin-bottom:6px; font-weight:600; }
  .target-row .btn{ height:34px; margin-top:24px; }
  .target-hint{ margin-top:6px; color:#777; font-size:12px; }

  .rule-card{
    border:1px solid #eee; border-radius:8px; padding:12px 14px; margin-bottom:14px;
  }
  .rule-head{
    display:flex; gap:10px; align-items:center; justify-content:space-between; flex-wrap:wrap;
    margin-bottom:10px;
  }
</style>

<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading" style="color:#000; font-weight:600;">
        Sửa Campaign: {{ $c->name }}
        <span style="font-weight:400; color:#666; font-size:12px;">({{ $timeText }})</span>
      </header>

      <div class="panel-body">

        {{-- alerts --}}
        @if(session('success'))
          <script>
            Swal.fire({ title:"Thành công!", text:"{{ session('success') }}", icon:"success", confirmButtonText:"OK" });
          </script>
        @endif
        @if(session('error'))
          <script>
            Swal.fire({ icon:"error", title:"Không thể thực hiện!", text:"{{ session('error') }}", confirmButtonText:"OK" });
          </script>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger">
            <ul style="margin:0; padding-left:18px;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- A) UPDATE CAMPAIGN --}}
        <div class="alert alert-info">
          <strong>A. Campaign (Chiến dịch)</strong><br>
          Campaign chỉ chứa thông tin chiến dịch. Giảm giá/áp dụng nằm trong <strong>Rules</strong>.
        </div>

        {{-- TOOLBAR ngoài form (tránh nested) --}}
        <div class="promo-toolbar">
          <button type="submit" class="btn btn-primary" form="campaign-update-form">
             Lưu Campaign
          </button>

          <form action="{{ route('admin.promotions.toggle-status', $c->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-warning">
             Đổi trạng thái (Hiện/Ẩn)
            </button>
          </form>

          <a href="{{ route('admin.promotions.index') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Quay lại
          </a>
        </div>

        <form id="campaign-update-form" method="POST" action="{{ route('admin.promotions.update', $c->id) }}" class="form-horizontal">
          @csrf
          @method('PUT')
          @include('admin.promotions._campaign_form', ['campaign' => $c])
        </form>

        <hr>

        {{-- B) RULES --}}
        <div class="alert alert-info">
          <strong>B. Rules (Luật áp dụng)</strong><br>
          Mỗi Rule có <code>scope=product</code> hoặc <code>scope=order</code>. Mỗi Rule sẽ gắn Targets; Codes chỉ dành cho scope=order.
        </div>

        {{-- FORM TẠO RULE --}}
        <form method="POST" action="{{ route('admin.promotions.rules.store', $c->id) }}" class="form-horizontal">
          @csrf
          <div class="well" style="padding:12px 15px; margin-bottom:14px;">
            <div style="font-weight:800; margin-bottom:8px;">+ Thêm Rule</div>
            @include('admin.promotions._rule_form', ['rule' => null])
            <div class="form-group" style="margin-top:10px;">
              <div class="col-lg-offset-3 col-lg-6">
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-plus"></i> Tạo Rule
                </button>
              </div>
            </div>
          </div>
        </form>

        {{-- LIST RULES --}}
        @forelse($rules as $r)
          @php
            $discountText = $r->discount_type === 'percent'
              ? ('-' . (int)$r->discount_value . '%')
              : ('-' . number_format((int)$r->discount_value, 0, ',', '.') . ' đ');

            $rTime = '—';
            if ($r->start_at || $r->end_at) {
              $rTime =
                ($r->start_at ? \Carbon\Carbon::parse($r->start_at)->format('d/m/Y H:i') : '…')
                . ' → ' .
                ($r->end_at ? \Carbon\Carbon::parse($r->end_at)->format('d/m/Y H:i') : '…');
            }

            $targets = $r->targets ?? collect();
            $codes   = $r->codes ?? collect();
          @endphp

          <div class="rule-card">
            <div class="rule-head">
              <div>
                <div style="font-weight:800; font-size:15px;">
                  Rule #{{ $r->id }}: {{ $r->name }}
                </div>
                <div style="color:#666; font-size:12px; margin-top:2px;">
                  <span class="label {{ $r->scope === 'product' ? 'label-info' : 'label-warning' }}">{{ $r->scope }}</span>
                  <span style="margin-left:6px;"><strong>{{ $discountText }}</strong></span>
                  <span style="margin-left:8px;">Time: {{ $rTime }}</span>
                  @if(!is_null($r->min_order_subtotal))
                    <span style="margin-left:8px;">Min subtotal: <strong>{{ number_format((int)$r->min_order_subtotal,0,',','.') }}</strong></span>
                  @endif
                </div>
              </div>

              <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <span>Priority: <strong>{{ (int)($r->priority ?? 0) }}</strong></span>

                @if($r->status)
                  <span class="label label-success">Hiện</span>
                @else
                  <span class="label label-default">Ẩn</span>
                @endif

                <form action="{{ route('admin.promotions.rules.toggle-status', ['id'=>$c->id, 'ruleId'=>$r->id]) }}"
                      method="POST" style="display:inline-block;">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="btn btn-xs btn-warning" title="Đổi trạng thái rule">
                    <i class="fa fa-refresh"></i>
                  </button>
                </form>

                <a class="btn btn-xs btn-info" data-toggle="collapse" href="#ruleEdit{{ $r->id }}">
                  <i class="fa fa-pencil"></i> Sửa
                </a>
              </div>
            </div>

            {{-- FORM UPDATE RULE (collapse) --}}
            <div id="ruleEdit{{ $r->id }}" class="panel-collapse collapse">
              <div class="well" style="margin-bottom:12px;">
                <form method="POST" action="{{ route('admin.promotions.rules.update', ['id'=>$c->id, 'ruleId'=>$r->id]) }}" class="form-horizontal">
                  @csrf
                  @method('PUT')
                  @include('admin.promotions._rule_form', ['rule' => $r])
                  <div class="form-group" style="margin-top:10px;">
                    <div class="col-lg-offset-3 col-lg-6">
                      <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Lưu Rule
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            {{-- TARGETS --}}
            <div class="alert alert-info" style="margin:0 0 10px;">
              <strong>Targets</strong> (Rule này áp cho: all/product/category/brand)
            </div>

            <form method="POST" action="{{ route('admin.promotions.targets.store', ['id'=>$c->id, 'ruleId'=>$r->id]) }}">
              @csrf

              <div class="target-row">
                <div class="form-group" style="min-width:220px;">
                  <label>Target type *</label>
                  <select name="target_type" class="form-control target_type" data-rule="{{ $r->id }}" required>
                    <option value="all">all (toàn shop)</option>
                    <option value="product">product (theo sản phẩm)</option>
                    <option value="category">category (theo danh mục)</option>
                    <option value="brand">brand (theo thương hiệu)</option>
                  </select>
                </div>

                <div class="form-group" style="min-width:380px;">
                  <label>Target *</label>

                  <input type="text" class="form-control target_all_hint"
                         value="Toàn shop (không cần chọn)" disabled style="display:none;">

                  <select class="form-control target_id_brand" style="display:none;">
                    <option value="">-- Chọn thương hiệu --</option>
                    @foreach($brands as $b)
                      <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                  </select>

                  <select class="form-control target_id_category" style="display:none;">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $cat)
                      <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                  </select>

                  <select class="form-control target_id_product" style="display:none;">
                    <option value="">-- Chọn sản phẩm --</option>
                    @foreach($products as $pr)
                      <option value="{{ $pr->id }}">#{{ $pr->id }} - {{ $pr->name }}</option>
                    @endforeach
                  </select>

                  <input type="hidden" name="target_id" class="target_id_hidden" value="">
                </div>

                <div class="form-group" style="min-width:140px;">
                  <label>Trạng thái</label>
                  <select name="status" class="form-control">
                    <option value="1" selected>Hiện</option>
                    <option value="0">Ẩn</option>
                  </select>
                </div>

                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-plus"></i> Thêm Target
                </button>
              </div>

              <div class="target-hint target_hint_text" style="display:none;">
                Với type=all thì không cần chọn target.
              </div>
            </form>

            <div class="table-responsive" style="margin-top:10px;">
              <table class="table table-striped b-t b-light">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Target</th>
                    <th>Target id</th>
                    <th>Trạng thái</th>
                    <th style="width:120px;">Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($targets as $t)
                    @php
                      $targetName = '—';
                      if ($t->target_type === 'all') {
                        $targetName = 'Toàn shop';
                      } elseif ($t->target_type === 'brand') {
                        $targetName = $brandMap[$t->target_id] ?? ('Brand #' . $t->target_id);
                      } elseif ($t->target_type === 'category') {
                        $targetName = $categoryMap[$t->target_id] ?? ('Category #' . $t->target_id);
                      } elseif ($t->target_type === 'product') {
                        $targetName = $productMap[$t->target_id] ?? ('Product #' . $t->target_id);
                      }
                    @endphp
                    <tr style="text-align:center;">
                      <td>{{ $t->id }}</td>
                      <td><span class="label label-info">{{ $t->target_type }}</span></td>
                      <td style="font-weight:700;">{{ $targetName }}</td>
                      <td>{{ $t->target_id ?? '—' }}</td>
                      <td>
                        @if($t->status)
                          <span class="label label-success">Hiện</span>
                        @else
                          <span class="label label-default">Ẩn</span>
                        @endif
                      </td>
                      <td>
                        <form action="{{ route('admin.promotions.targets.toggle-status', ['id' => $c->id, 'ruleId'=>$r->id, 'targetId' => $t->id]) }}"
                              method="POST" style="display:inline-block;">
                          @csrf
                          @method('PATCH')
                          <button type="submit" class="btn btn-xs btn-warning" title="Đổi trạng thái">
                            <i class="fa fa-refresh"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center">Chưa có target nào (Rule nên có ít nhất 1 target).</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            {{-- CODES (chỉ scope=order) --}}
            @if($r->scope === 'order')
              <hr>
              <div class="alert alert-info" style="margin:0 0 10px;">
                <strong>Codes</strong> (Mã giảm giá cho Rule scope=order)
              </div>

              <form method="POST" action="{{ route('admin.promotions.codes.store', ['id'=>$c->id, 'ruleId'=>$r->id]) }}" class="form-horizontal">
                @csrf

                <div class="form-group">
                  <label class="col-lg-3 control-label">Code *</label>
                  <div class="col-lg-3">
                    <input type="text" name="code" class="form-control" maxlength="50" required
                           value="{{ old('code') }}" placeholder="VD: TET2026">
                    <small class="text-muted">Nên viết HOA, không dấu, không khoảng trắng.</small>
                  </div>

                  <label class="col-lg-2 control-label">Min subtotal</label>
                  <div class="col-lg-2">
                    <input type="number" name="min_subtotal" class="form-control" min="0" step="1"
                           value="{{ old('min_subtotal', 0) }}">
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-lg-3 control-label">Max discount</label>
                  <div class="col-lg-3">
                    <input type="number" name="max_discount" class="form-control" min="0" step="1"
                           value="{{ old('max_discount') }}" placeholder="(optional)">
                  </div>

                  <label class="col-lg-2 control-label">Max uses</label>
                  <div class="col-lg-2">
                    <input type="number" name="max_uses" class="form-control" min="0" step="1"
                           value="{{ old('max_uses') }}" placeholder="(optional)">
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-lg-3 control-label">Max uses / user</label>
                  <div class="col-lg-3">
                    <input type="number" name="max_uses_per_user" class="form-control" min="0" step="1"
                           value="{{ old('max_uses_per_user') }}" placeholder="(optional)">
                  </div>

                  <label class="col-lg-2 control-label">Trạng thái</label>
                  <div class="col-lg-2">
                    <select name="status" class="form-control">
                      <option value="1" selected>Hiện</option>
                      <option value="0">Ẩn</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-lg-3 control-label">Hiệu lực code</label>
                  <div class="col-lg-3">
                    <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at') }}">
                    <small class="text-muted">Bắt đầu (optional)</small>
                  </div>
                  <div class="col-lg-3">
                    <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at') }}">
                    <small class="text-muted">Kết thúc (optional)</small>
                  </div>

                  <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary" style="margin-top:0;">
                      <i class="fa fa-plus"></i> Thêm Code
                    </button>
                  </div>
                </div>
              </form>

              <div class="table-responsive" style="margin-top:10px;">
                <table class="table table-striped b-t b-light">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Code</th>
                      <th>Min subtotal</th>
                      <th>Max discount</th>
                      <th>Max uses</th>
                      <th>Max / user</th>
                      <th>Thời gian</th>
                      <th>Trạng thái</th>
                      <th style="width:120px;">Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($codes as $code)
                      @php
                        $codeTime = '—';
                        if ($code->start_at || $code->end_at) {
                          $codeTime =
                            ($code->start_at ? \Carbon\Carbon::parse($code->start_at)->format('d/m/Y H:i') : '…')
                            . ' → ' .
                            ($code->end_at ? \Carbon\Carbon::parse($code->end_at)->format('d/m/Y H:i') : '…');
                        }
                      @endphp
                      <tr style="text-align:center;">
                        <td>{{ $code->id }}</td>
                        <td style="font-weight:800;">{{ $code->code }}</td>
                        <td>{{ number_format((int)($code->min_subtotal ?? 0), 0, ',', '.') }}</td>
                        <td>{{ is_null($code->max_discount) ? '—' : number_format((int)$code->max_discount, 0, ',', '.') }}</td>
                        <td>{{ $code->max_uses ?? '—' }}</td>
                        <td>{{ $code->max_uses_per_user ?? '—' }}</td>
                        <td style="font-size:12px;">{{ $codeTime }}</td>
                        <td>
                          @if($code->status)
                            <span class="label label-success">Hiện</span>
                          @else
                            <span class="label label-default">Ẩn</span>
                          @endif
                        </td>
                        <td>
                          <form action="{{ route('admin.promotions.codes.toggle-status', ['id' => $c->id, 'ruleId'=>$r->id, 'codeId' => $code->id]) }}"
                                method="POST" style="display:inline-block;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-xs btn-warning" title="Đổi trạng thái">
                              <i class="fa fa-refresh"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="9" class="text-center">Chưa có code nào (tuỳ chọn).</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            @endif
          </div>

        @empty
          <div class="alert alert-warning">
            Campaign này chưa có rule nào. Hãy tạo ít nhất 1 rule để hệ thống áp dụng ưu đãi.
          </div>
        @endforelse

        {{-- JS xử lý dropdown target cho nhiều rule (không trùng id) --}}
        <script>
        document.addEventListener('DOMContentLoaded', function () {
          document.querySelectorAll('.rule-card').forEach(function(card){
            const typeEl   = card.querySelector('.target_type');
            const allInput = card.querySelector('.target_all_hint');
            const selBrand = card.querySelector('.target_id_brand');
            const selCat   = card.querySelector('.target_id_category');
            const selProd  = card.querySelector('.target_id_product');
            const hiddenId = card.querySelector('.target_id_hidden');
            const hintText = card.querySelector('.target_hint_text');

            if (!typeEl) return;

            function hideAll(){
              allInput.style.display = 'none';
              selBrand.style.display = 'none';
              selCat.style.display   = 'none';
              selProd.style.display  = 'none';
              selBrand.value = '';
              selCat.value = '';
              selProd.value = '';
              hiddenId.value = '';
            }

            function syncHidden(){
              const t = typeEl.value;
              if (t === 'brand') hiddenId.value = selBrand.value || '';
              else if (t === 'category') hiddenId.value = selCat.value || '';
              else if (t === 'product') hiddenId.value = selProd.value || '';
              else hiddenId.value = '';
            }

            function render(){
              hideAll();
              const t = typeEl.value;

              if (t === 'all'){
                allInput.style.display = 'block';
                hintText.style.display = 'block';
                hiddenId.value = '';
                return;
              }

              hintText.style.display = 'none';
              if (t === 'brand') selBrand.style.display = 'block';
              if (t === 'category') selCat.style.display = 'block';
              if (t === 'product') selProd.style.display = 'block';
              syncHidden();
            }

            typeEl.addEventListener('change', render);
            selBrand.addEventListener('change', syncHidden);
            selCat.addEventListener('change', syncHidden);
            selProd.addEventListener('change', syncHidden);

            render();
          });
        });
        </script>

      </div>
    </section>
  </div>
</div>

@endsection
