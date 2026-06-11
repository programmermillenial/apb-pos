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

    <div class="col-md-6 mb-3">
        <label class="form-label">Latitude</label>
        <input type="number" step="any" id="latitude" name="latitude"
            class="form-control @error('latitude') is-invalid @enderror"
            value="{{ old('latitude', $outlet?->latitude) }}" placeholder="-6.2088">

        @error('latitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Longitude</label>
        <input type="number" step="any" id="longitude" name="longitude"
            class="form-control @error('longitude') is-invalid @enderror"
            value="{{ old('longitude', $outlet?->longitude) }}" placeholder="106.8456">

        @error('longitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <div>
                <label class="form-label mb-0">Lokasi Outlet</label>
                <small class="text-muted d-block">Klik map atau geser marker untuk menentukan titik outlet.</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" id="useCurrentLocation">
                Gunakan Lokasi Saya
            </button>
        </div>
        <div id="outletLocationMap"></div>
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

@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        <style>
            #outletLocationMap {
                border: 1px solid #dfe3e7;
                border-radius: 8px;
                height: 380px;
                min-height: 380px;
                overflow: hidden;
                width: 100%;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mapElement = document.getElementById('outletLocationMap');
                const latitudeInput = document.getElementById('latitude');
                const longitudeInput = document.getElementById('longitude');
                const currentLocationButton = document.getElementById('useCurrentLocation');

                if (!mapElement || !latitudeInput || !longitudeInput || typeof L === 'undefined') {
                    return;
                }

                const defaultPosition = [-2.5489, 118.0149];
                const initialLatitude = parseFloat(latitudeInput.value);
                const initialLongitude = parseFloat(longitudeInput.value);
                const hasInitialPosition = !Number.isNaN(initialLatitude) && !Number.isNaN(initialLongitude);
                const initialPosition = hasInitialPosition ? [initialLatitude, initialLongitude] : defaultPosition;

                const map = L.map(mapElement, {
                    scrollWheelZoom: false
                }).setView(initialPosition, hasInitialPosition ? 13 : 5);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                const marker = L.marker(initialPosition, {
                    draggable: true
                }).addTo(map);

                function setCoordinate(latlng, shouldPan = true) {
                    const latitude = Number(latlng.lat).toFixed(7);
                    const longitude = Number(latlng.lng).toFixed(7);

                    latitudeInput.value = latitude;
                    longitudeInput.value = longitude;
                    marker.setLatLng([latitude, longitude]);

                    if (shouldPan) {
                        map.panTo([latitude, longitude]);
                    }
                }

                if (!hasInitialPosition) {
                    setCoordinate({
                        lat: defaultPosition[0],
                        lng: defaultPosition[1]
                    }, false);
                }

                map.on('click', function(event) {
                    setCoordinate(event.latlng);
                });

                marker.on('dragend', function(event) {
                    setCoordinate(event.target.getLatLng());
                });

                latitudeInput.addEventListener('change', function() {
                    const latitude = parseFloat(latitudeInput.value);
                    const longitude = parseFloat(longitudeInput.value);

                    if (!Number.isNaN(latitude) && !Number.isNaN(longitude)) {
                        setCoordinate({
                            lat: latitude,
                            lng: longitude
                        });
                    }
                });

                longitudeInput.addEventListener('change', function() {
                    const latitude = parseFloat(latitudeInput.value);
                    const longitude = parseFloat(longitudeInput.value);

                    if (!Number.isNaN(latitude) && !Number.isNaN(longitude)) {
                        setCoordinate({
                            lat: latitude,
                            lng: longitude
                        });
                    }
                });

                if (currentLocationButton && navigator.geolocation) {
                    currentLocationButton.addEventListener('click', function() {
                        currentLocationButton.disabled = true;
                        currentLocationButton.textContent = 'Mencari lokasi...';

                        navigator.geolocation.getCurrentPosition(function(position) {
                            setCoordinate({
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            });
                            map.setZoom(15);
                            currentLocationButton.disabled = false;
                            currentLocationButton.textContent = 'Gunakan Lokasi Saya';
                        }, function() {
                            currentLocationButton.disabled = false;
                            currentLocationButton.textContent = 'Gunakan Lokasi Saya';
                            alert('Lokasi tidak bisa diakses. Pastikan izin lokasi browser aktif.');
                        });
                    });
                }

                setTimeout(function() {
                    map.invalidateSize();
                }, 250);
            });
        </script>
    @endpush
@endonce
