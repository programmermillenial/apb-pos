<div class="row">
    <div class="col-md-12 mb-3">
        <label for="outlet_id" class="form-label">Outlet <span class="text-danger">*</span></label>
        <select name="outlet_id" id="outlet_id" class="form-select @error('outlet_id') is-invalid @enderror">
            <option value="">-- Pilih Outlet --</option>
            @foreach ($outlets as $outlet)
                <option value="{{ $outlet->id }}"
                    {{ old('outlet_id', $user?->outlet_id ?? '') == $outlet->id ? 'selected' : '' }}>
                    {{ $outlet->code ?? '' }} - {{ $outlet->name }}
                </option>
            @endforeach
        </select>

        @error('outlet_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Nama User <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $user?->name ?? '') }}"
            class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama user">

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
        <input type="text" name="username" id="username" value="{{ old('username', $user?->username ?? '') }}"
            class="form-control @error('username') is-invalid @enderror" placeholder="Masukkan username">

        @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" value="{{ old('email', $user?->email ?? '') }}"
            class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email">

        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
            <option value="">-- Pilih Role --</option>
            @foreach ($roles as $key => $label)
                <option value="{{ $key }}" {{ old('role', $user?->role ?? '') == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">
            Password
            @if (empty($user))
                <span class="text-danger">*</span>
            @else
                <small class="text-muted">(Kosongkan jika tidak diganti)</small>
            @endif
        </label>

        <input type="password" name="password" id="password"
            class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password">

        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
            placeholder="Ulangi password">
    </div>

    <div class="col-md-12 mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active"
                value="1" {{ old('is_active', $user?->is_active ?? 1) ? 'checked' : '' }}>

            <label class="form-check-label" for="is_active">
                User Aktif
            </label>
        </div>
    </div>

    <hr>

    <div class="text-end">
        <a href="{{ route('users.index') }}" class="btn btn-light">
            Kembali
        </a>

        <button type="submit" class="btn btn-primary">
            {{ $button }}
        </button>
    </div>
</div>
