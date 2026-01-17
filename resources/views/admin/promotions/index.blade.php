@extends('pages.admin_layout')
@section('admin_content')

<div class="table-agile-info">
  <div class="panel panel-default">

    <div class="panel-heading" style="font-size:18px; font-weight:600;">
      QUẢN LÝ ƯU ĐÃI (CAMPAIGNS)
    </div>

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

    {{-- Filters --}}
    <div class="row w3-res-tb" style="margin-bottom:10px;">
      <div class="col-sm-12">
        <form method="GET" action="{{ route('admin.promotions.index') }}" class="form-inline" style="display:flex; gap:10px; flex-wrap:wrap;">
          <select name="status" class="form-control input-sm">
            <option value="">-- Trạng thái: tất cả --</option>
            <option value="1" {{ (string)($status ?? '') === '1' ? 'selected' : '' }}>Hiện</option>
            <option value="0" {{ (string)($status ?? '') === '0' ? 'selected' : '' }}>Ẩn</option>
          </select>

          <input type="text" name="q" class="form-control input-sm"
                 placeholder="Tìm theo tên chiến dịch..."
                 value="{{ $q ?? '' }}" style="min-width:260px;">

          <button class="btn btn-sm btn-default" type="submit">Lọc</button>

          <a href="{{ route('admin.promotions.create') }}" class="btn btn-sm btn-primary" style="margin-left:auto;">
            <i class="fa fa-plus"></i> Thêm Chiến Dịch
          </a>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <style>
        table td, table th { text-align:center !important; vertical-align:middle !important; }
        .text-left { text-align:left !important; }
      </style>

      <table class="table table-striped b-t b-light">
        <thead>
          <tr>
            <th>ID</th>
            <th class="text-left">Tên Chiến Dịch</th>
            <th>Thời gian</th>
            <th>Độ Ưu Tiên</th>
            <th>Rules</th>
            <th>Trạng Thái</th>
            <th style="width:140px;">Thao Tác</th>
          </tr>
        </thead>

        <tbody>
          @forelse($campaigns as $c)
            @php
              $timeText = '—';
              if ($c->start_at || $c->end_at) {
                $timeText =
                  ($c->start_at ? \Carbon\Carbon::parse($c->start_at)->format('d/m/Y H:i') : '…')
                  . ' → ' .
                  ($c->end_at ? \Carbon\Carbon::parse($c->end_at)->format('d/m/Y H:i') : '…');
              }
              $ruleCount = isset($c->rules_count) ? (int)$c->rules_count : (isset($c->rules) ? $c->rules->count() : 0);
            @endphp

            <tr>
              <td>{{ $c->id }}</td>

              <td class="text-left" style="font-weight:700;">
                {{ $c->name }}
                @if(!empty($c->description))
                  <div style="color:#777; font-weight:400; font-size:12px; margin-top:3px;">
                    {{ \Illuminate\Support\Str::limit($c->description, 90) }}
                  </div>
                @endif
              </td>

              <td style="font-size:12px;">{{ $timeText }}</td>

              <td>{{ (int)($c->priority ?? 0) }}</td>

              <td><span class="label label-info">{{ $ruleCount }}</span></td>

              <td>
                @if($c->status)
                  <span class="label label-success">Hiện</span>
                @else
                  <span class="label label-default">Ẩn</span>
                @endif
              </td>

              <td>
                <a href="{{ route('admin.promotions.edit', $c->id) }}"
                   class="btn btn-xs" 
                  style="background:none; border:none;"
                   title="Sửa Campaign + quản lý Rules/Targets/Codes">
                  <i class="fa fa-pencil-square-o text-success text-active" style="font-size:18px;"></i>
                </a>

                <form action="{{ route('admin.promotions.toggle-status', $c->id) }}"
                      method="POST"
                      style="display:inline-block;">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                            style="border:none; background:none; padding:0;"
                            title="{{ $c->status ? 'Ẩn khuyến mãi' : 'Hiển thị khuyến mãi' }}">

                        @if($c->status)
                            <i class="fa fa-eye-slash text-warning" style="font-size:18px;"></i>
                        @else
                            <i class="fa fa-eye text-warning" style="font-size:18px;"></i>
                        @endif

                    </button>
                </form>

              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">Chưa có campaign nào.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-5 text-center">
          @if($campaigns->total() > 0)
            <small class="text-muted inline m-t-sm m-b-sm">
              Hiển thị {{ $campaigns->firstItem() }} - {{ $campaigns->lastItem() }}
              / {{ $campaigns->total() }} campaign
            </small>
          @else
            <small class="text-muted inline m-t-sm m-b-sm">Không có campaign nào.</small>
          @endif
        </div>

        <div class="col-sm-7 text-right text-center-xs">
          {{ $campaigns->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </footer>

  </div>
</div>

@endsection
