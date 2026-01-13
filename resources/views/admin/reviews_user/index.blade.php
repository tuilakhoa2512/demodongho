@extends('pages.admin_layout')
@section('admin_content')

<div class="panel panel-default">

    {{-- heading --}}
    <div class="panel-heading">
        Danh sách đánh giá sản phẩm
        @if(isset($filterStatus) && $filterStatus == 1)
            <small>(Trạng thái: Hiển thị)</small>
        @elseif(isset($filterStatus) && $filterStatus === "0")
            <small>(Trạng thái: Ẩn)</small>
        @endif
    </div>

    {{-- form lọc --}}
    <div class="row" style="padding: 10px 15px;">
        <div class="col-sm-4">
            <form method="GET" action="{{ route('admin.reviewuser.index') }}" class="form-inline">
                <select name="status" class="form-control input-sm">
                    <option value="">Lọc trạng thái (Tất cả)</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                        Hiển thị
                    </option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                        Ẩn
                    </option>
                </select>

                <button type="submit" class="btn btn-sm btn-default" style="margin-left:5px;">
                    Áp dụng
                </button>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead style="background:#f5f5f5;">
                <tr>
                    <th>ID</th>
                    <th>Người đánh giá</th>
                    <th>Sản phẩm</th>
                    <th>Số sao</th>
                    <th>Nội dung</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                    <th>Ngày</th>
                    <th>Gộp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $key => $review)
                <tr>
                    <td>{{ $key + 1 }}</td>

                    {{-- user --}}
                    <td>{{ $review->user->fullname ?? 'Không xác định' }}</td>

                    {{-- prpduct --}}
                    <td>{{ $review->product->name ?? 'Đã xoá' }}</td>

                    {{-- rating --}}
                    <td style="color:#e60012;font-weight:600;">
                        {{ $review->rating }} ★
                    </td>

                    {{-- comment --}}
                    <td style="max-width:300px;">
                        {{ $review->comment }}
                    </td>
                    {{-- status --}}
                    <td>
                        @if($review->status == 1)
                            <span class="label label-success">Hiển thị</span>
                        @else
                            <span class="label label-default">Ẩn</span>
                        @endif
                    </td>

                    {{-- action --}}
                    <td>
                        @if($review->status == 1)
                            <a href="{{ route('admin.review.toggle', $review->id) }}"
                               class="btn btn-warning btn-xs">
                                Ẩn đánh giá
                            </a>
                        @else
                            <a href="{{ route('admin.review.toggle', $review->id) }}"
                               class="btn btn-success btn-xs">
                                Hiển thị
                            </a>
                        @endif
                    </td>

                    {{-- DATE --}}
                    <td>
                        {{ $review->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td>{{ $review->rating }} ★,
                    {{ $review->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">
                        Chưa có đánh giá nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- phân trang --}}
    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-5 text-center">
          <small class="text-muted inline m-t-sm m-b-sm">
            Hiển thị {{ $reviews->firstItem() }} - {{ $reviews->lastItem() }}
            / {{ $reviews->total() }} Comment
          </small>
        </div>

        <div class="text-center">
          {{ $reviews->links('vendor.pagination.number-only') }}
        </div>
      </div>
    </footer>

</div>

@endsection