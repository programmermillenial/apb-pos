<div class="row">
    @isset($customer)
        <div class="col-md-6 mb-3">
            <label class="form-label">Kode Customer</label>
            <input type="text" class="form-control" value="{{ $customer->code }}" readonly>
        </div>
    @endisset

    <div class="col-md-6 mb-3">
        <label class="form-label">Outlet</label>
        <select name="outlet_id" class="form-select @error('outlet_id') is-invalid @enderror">
            <option value="">Pilih Outlet</option>
            @foreach ($outlets as $outlet)
                <option value="{{ $outlet->id }}"
                    {{ old('outlet_id', $customer->outlet_id ?? '') == $outlet->id ? 'selected' : '' }}>
                    {{ $outlet->name }}
                </option>
            @endforeach
        </select>

        @error('outlet_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $customer->name ?? '') }}" placeholder="Masukkan nama customer">

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">No. HP / WhatsApp</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $customer->phone ?? '') }}" placeholder="Contoh: 08123456789">

        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $customer->email ?? '') }}" placeholder="customer@email.com">

        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="4"
            placeholder="Masukkan alamat customer">{{ old('address', $customer->address ?? '') }}</textarea>

        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <div class="form-check form-switch">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                {{ old('is_active', $customer->is_active ?? 1) ? 'checked' : '' }}>

            <label class="form-check-label" for="is_active">
                Customer Aktif
            </label>
        </div>
    </div>

    <hr>

    <div class="text-end">
        <a href="{{ route('brands.index') }}" class="btn btn-light">
            Kembali
        </a>

        <button type="submit" class="btn btn-primary">
            {{ $button }}
        </button>
    </div>
</div>
