<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Unit</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $unit->name ?? '') }}" placeholder="Contoh: Kilogram, Liter, Pieces">

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Singkatan</label>
        <input type="text" name="short_name" class="form-control @error('short_name') is-invalid @enderror"
            value="{{ old('short_name', $unit->short_name ?? '') }}" placeholder="Contoh: kg, ltr, pcs">

        @error('short_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
            placeholder="Masukkan deskripsi">{{ old('description', $unit->description ?? '') }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-4">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                {{ old('is_active', $unit->is_active ?? true) ? 'checked' : '' }}>

            <label class="form-check-label" for="is_active">
                Aktif
            </label>
        </div>
    </div>

    <hr>

    <div class="text-end">
        <a href="{{ route('units.index') }}" class="btn btn-light">
            Kembali
        </a>

        <button type="submit" class="btn btn-primary">
            {{ $button }}
        </button>
    </div>
</div>
