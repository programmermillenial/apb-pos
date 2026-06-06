<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Nama Supplier</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $supplier->name ?? '') }}" placeholder="Nama supplier">

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label class="form-label">Telepon</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
            value="{{ old('phone', $supplier->phone ?? '') }}">

        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $supplier->email ?? '') }}">

        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
            <option value="1" {{ old('is_active', $supplier->is_active ?? 1) == 1 ? 'selected' : '' }}>
                Active
            </option>
            <option value="0" {{ old('is_active', $supplier->is_active ?? 1) == 0 ? 'selected' : '' }}>
                Inactive
            </option>
        </select>

        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Nama PIC</label>
        <input type="text" name="pic_name" class="form-control @error('pic_name') is-invalid @enderror"
            value="{{ old('pic_name', $supplier->pic_name ?? '') }}">

        @error('pic_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Telepon PIC</label>
        <input type="text" name="pic_phone" class="form-control @error('pic_phone') is-invalid @enderror"
            value="{{ old('pic_phone', $supplier->pic_phone ?? '') }}">

        @error('pic_phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="address" rows="4" class="form-control @error('address') is-invalid @enderror"
            placeholder="Alamat supplier">{{ old('address', $supplier->address ?? '') }}</textarea>

        @error('address')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="text-end">
        <a href="{{ route('suppliers.index') }}" class="btn btn-light">
            Kembali
        </a>

        <button type="submit" class="btn btn-primary">
            {{ $button }}
        </button>
    </div>
</div>
