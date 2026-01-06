@extends('pages.admin_layout')
@section('admin_content')

<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading" style="color:#000; font-weight:600;">
        Tạo Chiến Dịch Ưu Đãi (Campaign)
      </header>

      <div class="panel-body">

        <!-- <div class="alert alert-info" style="margin-bottom:12px;">
          <div style="font-weight:800; margin-bottom:6px;">Luồng đúng (hệ thống mới):</div>
          <ol style="margin:0; padding-left:18px; line-height:1.6;">
            <li>Tạo <strong>Campaign</strong> (chiến dịch).</li>
            <li>Sau khi tạo xong → vào <strong>Edit</strong> để tạo <strong>Rules</strong>.</li>
            <li>Mỗi Rule → gắn <strong>Targets</strong> (all/product/category/brand).</li>
            <li>Nếu Rule scope=order và cần nhập mã → tạo <strong>Codes</strong>.</li>
          </ol>
        </div> -->

        @if ($errors->any())
          <div class="alert alert-danger">
            <ul style="margin:0; padding-left:18px;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.promotions.store') }}" class="form-horizontal">
          @csrf

          @include('admin.promotions._campaign_form', ['campaign' => null])

          <div class="form-group" style="margin-top:16px;">
            <div class="col-lg-offset-3 col-lg-6">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Tạo Chiến Dịch
              </button>

              <a href="{{ route('admin.promotions.index') }}" class="btn btn-default" style="margin-left:8px;">
                <i class="fa fa-arrow-left"></i> Quay Lại
              </a>
            </div>
          </div>

        </form>

      </div>
    </section>
  </div>
</div>

@endsection
