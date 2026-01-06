{{-- admin/promotions/_rule_form.blade.php --}}

<div class="form-group">
  <label class="col-lg-3 control-label">Tên rule *</label>
  <div class="col-lg-6">
    <input type="text" name="name" class="form-control" required maxlength="120"
           value="{{ old('name', $rule->name ?? '') }}"
           placeholder="VD: Giảm 10% toàn shop / Giảm 50K đơn từ 500K ...">
  </div>
</div>

<div class="form-group">
  <label class="col-lg-3 control-label">Scope *</label>
  <div class="col-lg-3">
    @php $sc = old('scope', $rule->scope ?? 'product'); @endphp
    <select name="scope" class="form-control" required>
      <option value="product" {{ $sc === 'product' ? 'selected' : '' }}>product</option>
      <option value="order"   {{ $sc === 'order' ? 'selected' : '' }}>order</option>
    </select>
    <small class="text-muted">product = giảm theo SP, order = giảm hóa đơn</small>
  </div>

  <label class="col-lg-2 control-label">Trạng thái</label>
  <div class="col-lg-2">
    @php $st = (string)old('status', (string)($rule->status ?? 1)); @endphp
    <select name="status" class="form-control">
      <option value="1" {{ $st === '1' ? 'selected' : '' }}>Hiện</option>
      <option value="0" {{ $st === '0' ? 'selected' : '' }}>Ẩn</option>
    </select>
  </div>
</div>

<div class="form-group">
  <label class="col-lg-3 control-label">Giảm *</label>
  <div class="col-lg-3">
    @php $dt = old('discount_type', $rule->discount_type ?? 'percent'); @endphp
    <select name="discount_type" class="form-control" required>
      <option value="percent" {{ $dt === 'percent' ? 'selected' : '' }}>percent (%)</option>
      <option value="fixed"   {{ $dt === 'fixed' ? 'selected' : '' }}>fixed (đ)</option>
    </select>
  </div>
  <div class="col-lg-3">
    <input type="number" name="discount_value" class="form-control" required min="0" step="1"
           value="{{ old('discount_value', (int)($rule->discount_value ?? 0)) }}"
           placeholder="VD: 10 hoặc 50000">
  </div>
</div>

<div class="form-group">
  <label class="col-lg-3 control-label">Max discount</label>
  <div class="col-lg-3">
    <input type="number" name="max_discount_amount" class="form-control" min="0" step="1"
           value="{{ old('max_discount_amount', $rule->max_discount_amount ?? '') }}"
           placeholder="(optional)">
    <small class="text-muted">Giới hạn tiền giảm tối đa (rule-level)</small>
  </div>

  <label class="col-lg-2 control-label">Min subtotal (order)</label>
  <div class="col-lg-2">
    <input type="number" name="min_order_subtotal" class="form-control" min="0" step="1"
           value="{{ old('min_order_subtotal', $rule->min_order_subtotal ?? '') }}"
           placeholder="(optional)">
    <small class="text-muted">Chỉ dùng khi scope=order</small>
  </div>
</div>

<div class="form-group">
  <label class="col-lg-3 control-label">Thời gian rule</label>
  <div class="col-lg-3">
    <input type="datetime-local" name="start_at" class="form-control"
           value="{{ old('start_at', isset($rule->start_at) && $rule->start_at ? \Carbon\Carbon::parse($rule->start_at)->format('Y-m-d\TH:i') : '') }}">
    <small class="text-muted">Bắt đầu (optional)</small>
  </div>
  <div class="col-lg-3">
    <input type="datetime-local" name="end_at" class="form-control"
           value="{{ old('end_at', isset($rule->end_at) && $rule->end_at ? \Carbon\Carbon::parse($rule->end_at)->format('Y-m-d\TH:i') : '') }}">
    <small class="text-muted">Kết thúc (optional)</small>
  </div>
</div>

<div class="form-group">
  <label class="col-lg-3 control-label">Priority rule</label>
  <div class="col-lg-2">
    <input type="number" name="priority" class="form-control" min="0" step="1"
           value="{{ old('priority', (int)($rule->priority ?? 0)) }}">
  </div>
</div>
