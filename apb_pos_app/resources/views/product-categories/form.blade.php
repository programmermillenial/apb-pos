<div class="row">
    <div class="mb-3">
        <label class="form-label">Nama Category</label>
        <input type="text" name="name" value="{{ old('name', $productCategory->name ?? '') }}"
            class="form-control @error('name') is-invalid @enderror">

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $productCategory->description ?? '') }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
            <option value="1" {{ old('is_active', $productCategory->is_active ?? 1) == 1 ? 'selected' : '' }}>
                Active
            </option>
            <option value="0" {{ old('is_active', $productCategory->is_active ?? 1) == 0 ? 'selected' : '' }}>
                Inactive
            </option>
        </select>

        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <hr>

    <div class="text-end">
        <a href="{{ route('product-categories.index') }}" class="btn btn-light">
            Kembali
        </a>

        <button type="submit" class="btn btn-primary">
            {{ $button }}
        </button>
    </div>

</div>
