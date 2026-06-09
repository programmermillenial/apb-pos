<div class="row">

    @if(!isset($product) || !$product->id)
    <div class="col-md-6 mb-3">
        <label class="form-label">Outlet</label>
        <select name="outlet_id" class="form-select @error('outlet_id') is-invalid @enderror">
            <option value="">Pilih Outlet</option>
            @foreach ($outlets as $outlet)
                <option value="{{ $outlet->id }}"
                    {{ old('outlet_id') == $outlet->id ? 'selected' : '' }}>
                    {{ $outlet->name }}
                </option>
            @endforeach
        </select>
        @error('outlet_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @endif

    <div class="col-md-6 mb-3">
        <label class="form-label">Product Category</label>
        <select name="product_category_id" class="form-select @error('product_category_id') is-invalid @enderror">
            <option value="">Pilih Category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    {{ old('product_category_id', $product->product_category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('product_category_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Brand</label>
        <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
            <option value="">Pilih Brand</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}"
                    {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
        @error('brand_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Unit</label>
        <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
            <option value="">Pilih Unit</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}"
                    {{ old('unit_id', $product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                    {{ $unit->name }}
                </option>
            @endforeach
        </select>
        @error('unit_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">SKU</label>
        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
            value="{{ old('sku', $product->sku ?? '') }}">
        @error('sku')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Barcode</label>
        <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror"
            value="{{ old('barcode', $product->barcode ?? '') }}">
        @error('barcode')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $product->name ?? '') }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Cost Price</label>
        <input type="text" name="cost_price" id="cost_price" class="form-control autonumeric"
            value="{{ old('cost_price', $product->cost_price ?? 0) }}">
        @error('cost_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Sell Price</label>
        <input type="text" name="sell_price" id="sell_price" class="form-control autonumeric"
            value="{{ old('sell_price', $product->sell_price ?? 0) }}">
        @error('sell_price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if(!isset($product) || !$product->id)
    <div class="col-md-6 mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
            value="{{ old('stock', 0) }}">
        @error('stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    @endif

    <div class="col-md-6 mb-3">
        <label class="form-label">Min Stock</label>
        <input type="number" name="min_stock" class="form-control @error('min_stock') is-invalid @enderror"
            value="{{ old('min_stock', $product->min_stock ?? 0) }}">
        @error('min_stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Weight (Kg)</label>

        <input type="number" step="0.01" min="0" name="weight"
            class="form-control @error('weight') is-invalid @enderror"
            value="{{ old('weight', $product->weight ?? 0) }}">

        @error('weight')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Status</label>
        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
            <option value="1" {{ old('is_active', $product->is_active ?? 1) == 1 ? 'selected' : '' }}>Active
            </option>
            <option value="0" {{ old('is_active', $product->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive
            </option>
        </select>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <hr>

    <div class="text-end">
        <a href="{{ route('products.index') }}" class="btn btn-light">
            Kembali
        </a>

        <button type="submit" class="btn btn-primary">
            {{ $button }}
        </button>
    </div>

</div>

@push('scripts')
<script>
    $(document).ready(function() {
        initAutoNumeric('.autonumeric');
    });
</script>
@endpush
