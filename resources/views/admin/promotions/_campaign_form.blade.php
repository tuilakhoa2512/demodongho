{{-- admin/promotions/_campaign_form.blade.php --}}

<div class="form-group">
  <label class="col-lg-3 control-label">Tên Chiến Dịch *</label>
  <div class="col-lg-6">
    <input type="text" name="name" class="form-control" required maxlength="120"
           value="{{ old('name', $campaign->name ?? '') }}"
           placeholder="VD: TẾT 2026 / SALE CUỐI TUẦN ...">
  </div>
</div>

<div class="form-group">
  <label class="col-lg-3 control-label">Mô Tả</label>
  <div class="col-lg-6">
    <textarea name="description" class="form-control" rows="3"
              placeholder="(optional)">{{ old('description', $campaign->description ?? '') }}</textarea>
  </div>
</div>

<div class="form-group">
  <label class="col-lg-3 control-label">Thời Gian</label>
  <div class="col-lg-3">
    <input type="datetime-local" name="start_at" class="form-control"
           value="{{ old('start_at', isset($campaign->start_at) && $campaign->start_at ? \Carbon\Carbon::parse($campaign->start_at)->format('Y-m-d\TH:i') : '') }}">
    <small class="text-muted">Bắt Đầu</small>
  </div>
  <div class="col-lg-3">
    <input type="datetime-local" name="end_at" class="form-control"
           value="{{ old('end_at', isset($campaign->end_at) && $campaign->end_at ? \Carbon\Carbon::parse($campaign->end_at)->format('Y-m-d\TH:i') : '') }}">
    <small class="text-muted">Kết Thúc</small>
  </div>
</div>

<div class="form-group">
  <label class="col-lg-3 control-label">Độ Ưu Tiên</label>
  <div class="col-lg-2">
    <input type="number" name="priority" class="form-control" min="0" step="1"
           value="{{ old('priority', (int)($campaign->priority ?? 0)) }}">
    <small class="text-muted">Số càng lớn càng ưu tiên</small>
  </div>

  <label class="col-lg-2 control-label">Trạng Thái</label>
  <div class="col-lg-2">
    <select name="status" class="form-control">
      @php $st = (string)old('status', (string)($campaign->status ?? 1)); @endphp
      <option value="1" {{ $st === '1' ? 'selected' : '' }}>Hiện</option>
      <option value="0" {{ $st === '0' ? 'selected' : '' }}>Ẩn</option>
    </select>
  </div>
</div>
