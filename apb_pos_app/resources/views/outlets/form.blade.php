<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Kode Outlet</label>
        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', $outlet?->code) }}" placeholder="Contoh: JKT01">

        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Outlet</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $outlet?->name) }}" placeholder="Nama outlet">

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Telepon</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $outlet?->phone) }}" placeholder="Nomor telepon">

        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
            <option value="1" {{ old('is_active', $outlet?->is_active ?? 1) == 1 ? 'selected' : '' }}>
                Aktif
            </option>
            <option value="0" {{ old('is_active', $outlet?->is_active ?? 1) == 0 ? 'selected' : '' }}>
                Nonaktif
            </option>
        </select>

        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="4"
            placeholder="Alamat outlet">{{ old('address', $outlet?->address) }}</textarea>

        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('outlets.index') }}" class="btn btn-light">
            Kembali
        </a>

        <button type="submit" class="btn btn-primary">
            {{ $button }}
        </button>
    </div>
</div>
