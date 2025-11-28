@extends('pages.layout')
@section('content')

<div class="container" style="margin-top: 40px;">

    <h2 style="margin: 0; position: absolute; top: -30px; left: 50%; transform: translateX(-50%);">
        Thông Tin Cá Nhân : {{ Session::get('fullname') }}
    </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($user)
        <div class="profile-image"
             style="margin: 30px;position: absolute;top: -30px; left: 50%; transform: translateX(-50%);">
            @if($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="User Image" class="user-image">
            @else
                <img src="{{ asset('backend/images/3.png') }}" width="150" height="150"
                     style="border-radius: 50%; object-fit: cover; border: 2px solid #ccc; margin-right:20px;"
                     alt="User Image" class="user-image">
            @endif
        </div>
    @endif

    <br><br><br><br><br>


    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" name="fullname" class="form-control"
                           value="{{ $user->fullname }}" required style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Email (không thể sửa)</label>
                    <input type="email" class="form-control"
                           value="{{ $user->email }}" disabled style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" class="form-control"
                           value="{{ $user->phone }}" style="max-width: 800px;">
                </div>

                <div class="form-group">
                    <label>Địa chỉ (số nhà, đường)</label>
                    <input type="text" name="address" class="form-control"
                           value="{{ $user->address }}" style="max-width: 800px;">
                </div>


                <div class="form-group">
                    <label for="province">Tỉnh / Thành phố</label>
                    <select name="province" id="province" class="form-control" style="max-width: 800px;">
                        <option value="">-- Chọn Tỉnh / Thành phố --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="district">Quận / Huyện</label>
                    <select name="district" id="district" class="form-control" style="max-width: 800px;">
                        <option value="">-- Chọn Quận / Huyện --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ward">Phường / Xã</label>
                    <select name="ward" id="ward" class="form-control" style="max-width: 800px;">
                        <option value="">-- Chọn Phường / Xã --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Ảnh đại diện</label>
                    <input type="file" name="image" class="form-control" style="max-width: 800px;">
                </div>

                <button class="btn btn-primary">Cập nhật</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect     = document.getElementById('ward');

    const currentProvinceName = @json($user->province);
    const currentDistrictName = @json($user->district);
    const currentWardName     = @json($user->ward);

    fetch('{{ route('location.provinces') }}')
        .then(response => response.json())
        .then(provinces => {
            console.log('Provinces from backend:', provinces);

            if (!Array.isArray(provinces)) {
                console.error('Sai format provinces:', provinces);
                return;
            }

            provinces.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.name;
                opt.textContent = p.name;
          
                opt.dataset.code = p.code;
                provinceSelect.appendChild(opt);
            });

            if (currentProvinceName) {
                provinceSelect.value = currentProvinceName;
                const selectedProvince = provinceSelect.selectedOptions[0];
                if (selectedProvince) {
                    loadDistricts(selectedProvince.dataset.code);
                }
            }
        })
        .catch(err => console.error('Lỗi load provinces:', err));

    provinceSelect.addEventListener('change', function () {
        const selected = this.selectedOptions[0];
        const provinceCode = selected ? selected.dataset.code : null;

        districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
        wardSelect.innerHTML     = '<option value="">-- Chọn Phường / Xã --</option>';

        if (!provinceCode) return;

        loadDistricts(provinceCode);
    });

    function loadDistricts(provinceCode) {
        const url = '{{ url('location/provinces') }}/' + provinceCode + '/districts';

        fetch(url)
            .then(response => response.json())
            .then(districts => {
                console.log('Districts:', districts);

                if (!Array.isArray(districts)) {
                    console.error('Sai format districts:', districts);
                    return;
                }

                districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.name;       
                    opt.textContent = d.name;
                    opt.dataset.code = d.code; 
                    districtSelect.appendChild(opt);
                });

                if (currentDistrictName) {
                    districtSelect.value = currentDistrictName;
                    const selectedDistrict = districtSelect.selectedOptions[0];
                    if (selectedDistrict) {
                        loadWards(selectedDistrict.dataset.code);
                    }
                }
            })
            .catch(err => console.error('Lỗi load districts:', err));
    }

    districtSelect.addEventListener('change', function () {
        const selected = this.selectedOptions[0];
        const districtCode = selected ? selected.dataset.code : null;

        wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';

        if (!districtCode) return;

        loadWards(districtCode);
    });

    function loadWards(districtCode) {
        const url = '{{ url('location/districts') }}/' + districtCode + '/wards';

        fetch(url)
            .then(response => response.json())
            .then(wards => {
                console.log('Wards:', wards);

                if (!Array.isArray(wards)) {
                    console.error('Sai format wards:', wards);
                    return;
                }

                wards.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.name; 
                    opt.textContent = w.name;
                    wardSelect.appendChild(opt);
                });

                if (currentWardName) {
                    wardSelect.value = currentWardName;
                }
            })
            .catch(err => console.error('Lỗi load wards:', err));
    }
});
</script>

@endsection
