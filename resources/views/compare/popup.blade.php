<div class="row">
    <div class="col-sm-6">
        @if($sp1)
            <img src="{{ $sp1->main_image_url }}" width="90">
            <p>{{ $sp1->name }}</p>
        @else
            <p>Chưa chọn SP1</p>
        @endif
    </div>

    <div class="col-sm-6">
        @if($sp2)
            <img src="{{ $sp2->main_image_url }}" width="90">
            <p>{{ $sp2->name }}</p>
        @else
            <p>Chưa chọn SP2</p>
        @endif
    </div>
</div>
