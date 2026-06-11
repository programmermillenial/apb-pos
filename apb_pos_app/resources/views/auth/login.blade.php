<x-guest-layout full>
    <style>
        html,
        body {
            margin: 0;
        }

        .apb-login {
            align-items: center;
            background:
                radial-gradient(circle at 12% 16%, rgba(58, 87, 232, .22), transparent 32%),
                radial-gradient(circle at 88% 82%, rgba(8, 145, 178, .18), transparent 30%),
                #f4f7fb;
            color: #232d42;
            display: flex;
            font-family: Figtree, Arial, sans-serif;
            box-sizing: border-box;
            height: 100vh;
            justify-content: center;
            min-height: 0;
            overflow: hidden;
            padding: 28px;
        }

        .apb-login-shell {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 24px 70px rgba(35, 45, 66, .16);
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(360px, .95fr);
            height: calc(100vh - 56px);
            max-width: 1080px;
            max-height: 700px;
            min-height: 560px;
            overflow: hidden;
            width: 100%;
        }

        .apb-login-brand {
            background: linear-gradient(135deg, #182044 0%, #263789 58%, #3a57e8 100%);
            color: #ffffff;
            min-height: 0;
            overflow: hidden;
            padding: 44px;
            position: relative;
        }

        .apb-login-brand::before,
        .apb-login-brand::after {
            border-radius: 999px;
            content: "";
            position: absolute;
        }

        .apb-login-brand::before {
            background: rgba(255, 255, 255, .10);
            height: 320px;
            right: -120px;
            top: -90px;
            width: 320px;
        }

        .apb-login-brand::after {
            background: rgba(34, 211, 238, .18);
            bottom: -110px;
            height: 280px;
            left: -90px;
            width: 280px;
        }

        .apb-login-brand-inner {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .apb-login-logo-row {
            align-items: center;
            display: flex;
            gap: 14px;
        }

        .apb-login-logo {
            align-items: center;
            background: #ffffff;
            border-radius: 8px;
            color: #3a57e8;
            display: flex;
            height: 52px;
            justify-content: center;
            width: 52px;
        }

        .apb-login-logo svg {
            fill: currentColor;
            height: 30px;
            width: 30px;
        }

        .apb-login-kicker {
            color: #d8e4ff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .18em;
            margin: 0 0 3px;
            text-transform: uppercase;
        }

        .apb-login-muted {
            color: rgba(255, 255, 255, .72);
            font-size: 14px;
            margin: 0;
        }

        .apb-login-copy {
            max-width: 520px;
            padding: 54px 0;
        }

        .apb-login-pill {
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 999px;
            color: #e8f7ff;
            display: inline-block;
            font-size: 13px;
            font-weight: 600;
            padding: 9px 14px;
        }

        .apb-login-copy h1 {
            font-size: 44px;
            line-height: 1.1;
            margin: 24px 0 16px;
        }

        .apb-login-copy p {
            color: rgba(255, 255, 255, .78);
            font-size: 16px;
            line-height: 1.7;
            margin: 0;
        }

        .apb-login-stats {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, 1fr);
            margin-top: 32px;
        }

        .apb-login-stat {
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 8px;
            padding: 16px;
        }

        .apb-login-stat strong {
            display: block;
            font-size: 22px;
            margin-bottom: 5px;
        }

        .apb-login-stat span {
            color: rgba(255, 255, 255, .72);
            font-size: 13px;
        }

        .apb-login-note {
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 8px;
            padding: 18px;
        }

        .apb-login-note strong {
            display: block;
            margin-bottom: 6px;
        }

        .apb-login-form-panel {
            align-items: center;
            display: flex;
            overflow-y: auto;
            padding: 44px;
        }

        .apb-login-form-wrap {
            width: 100%;
        }

        .apb-login-mobile-brand {
            display: none;
            margin-bottom: 28px;
        }

        .apb-login-form-card h2 {
            color: #111827;
            font-size: 30px;
            line-height: 1.2;
            margin: 10px 0;
        }

        .apb-login-form-card .apb-form-desc {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 26px;
        }

        .apb-alert {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            color: #047857;
            font-size: 14px;
            margin-bottom: 18px;
            padding: 12px 14px;
        }

        .apb-form-group {
            margin-bottom: 18px;
        }

        .apb-form-head {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .apb-form-label {
            color: #344054;
            display: block;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .apb-input {
            border: 1px solid #dfe3e7;
            border-radius: 8px;
            box-sizing: border-box;
            color: #232d42;
            font-size: 15px;
            min-height: 48px;
            outline: none;
            padding: 12px 14px;
            transition: .2s ease;
            width: 100%;
        }

        .apb-input:focus {
            border-color: #3a57e8;
            box-shadow: 0 0 0 4px rgba(58, 87, 232, .12);
        }

        .apb-error {
            color: #dc2626;
            font-size: 13px;
            margin: 7px 0 0;
        }

        .apb-form-options {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin: 4px 0 22px;
        }

        .apb-checkbox {
            align-items: center;
            color: #667085;
            display: inline-flex;
            font-size: 14px;
            gap: 8px;
        }

        .apb-checkbox input {
            accent-color: #3a57e8;
        }

        .apb-link {
            color: #3a57e8;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
        }

        .apb-link:hover {
            color: #2f49c9;
            text-decoration: underline;
        }

        .apb-login-button {
            background: #3a57e8;
            border: 0;
            border-radius: 8px;
            box-shadow: 0 14px 28px rgba(58, 87, 232, .24);
            color: #ffffff;
            cursor: pointer;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .08em;
            min-height: 48px;
            text-transform: uppercase;
            transition: .2s ease;
            width: 100%;
        }

        .apb-login-button:hover {
            background: #2f49c9;
            transform: translateY(-1px);
        }

        .apb-login-footer {
            color: #98a2b3;
            font-size: 12px;
            margin-top: 24px;
            text-align: center;
        }

        @media (max-width: 900px) {
            .apb-login {
                height: auto;
                min-height: 100vh;
                overflow: auto;
                padding: 18px;
            }

            .apb-login-shell {
                display: block;
                height: auto;
                max-width: 480px;
                max-height: none;
                min-height: 0;
            }

            .apb-login-brand {
                display: none;
            }

            .apb-login-form-panel {
                padding: 30px 24px;
            }

            .apb-login-mobile-brand {
                align-items: center;
                display: flex;
                gap: 12px;
            }

            .apb-login-mobile-brand .apb-login-kicker {
                color: #3a57e8;
            }

            .apb-login-mobile-brand .apb-login-muted {
                color: #667085;
            }
        }

        @media (max-width: 480px) {
            .apb-login {
                padding: 0;
            }

            .apb-login-shell {
                border-radius: 0;
                min-height: 100vh;
            }

            .apb-login-form-panel {
                padding: 24px 18px;
            }

            .apb-login-form-card h2 {
                font-size: 26px;
            }
        }
    </style>

    <main class="apb-login">
        <div class="apb-login-shell">
            <section class="apb-login-brand">
                <div class="apb-login-brand-inner">
                    <div class="apb-login-logo-row">
                        <div class="apb-login-logo">
                            <x-application-logo />
                        </div>
                        <div>
                            <p class="apb-login-kicker">APB POS</p>
                            <p class="apb-login-muted">Point of Sale Management</p>
                        </div>
                    </div>

                    <div class="apb-login-copy">
                        <span class="apb-login-pill">Operasional toko dalam satu dashboard</span>
                        <h1>Pantau penjualan, stok, dan outlet tanpa ribet.</h1>
                        <p>
                            Masuk untuk kelola transaksi, produk, pembelian, dan laporan harian dengan workflow yang cepat.
                        </p>

                        <div class="apb-login-stats">
                            <div class="apb-login-stat">
                                <strong>Live</strong>
                                <span>Sales tracking</span>
                            </div>
                            <div class="apb-login-stat">
                                <strong>Multi</strong>
                                <span>Outlet ready</span>
                            </div>
                            <div class="apb-login-stat">
                                <strong>Stock</strong>
                                <span>Auto monitor</span>
                            </div>
                        </div>
                    </div>

                    <div class="apb-login-note">
                        <strong>Quick insight</strong>
                        <span>Semua transaksi dan pergerakan stok tersimpan rapi untuk laporan operasional.</span>
                    </div>
                </div>
            </section>

            <section class="apb-login-form-panel">
                <div class="apb-login-form-wrap">
                    <div class="apb-login-mobile-brand">
                        <div class="apb-login-logo">
                            <x-application-logo />
                        </div>
                        <div>
                            <p class="apb-login-kicker">APB POS</p>
                            <p class="apb-login-muted">Point of Sale Management</p>
                        </div>
                    </div>

                    <div class="apb-login-form-card">
                        <p class="apb-login-kicker" style="color: #3a57e8;">Welcome back</p>
                        <h2>Login ke APB POS</h2>
                        <p class="apb-form-desc">
                            Gunakan akun yang sudah terdaftar untuk masuk ke dashboard operasional.
                        </p>

                        @if (session('status'))
                            <div class="apb-alert">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="apb-form-group">
                                <label class="apb-form-label" for="email">Email</label>
                                <input
                                    id="email"
                                    class="apb-input"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="admin@apbpos.com">
                                @foreach ($errors->get('email') as $message)
                                    <p class="apb-error">{{ $message }}</p>
                                @endforeach
                            </div>

                            <div class="apb-form-group">
                                <div class="apb-form-head">
                                    <label class="apb-form-label" for="password" style="margin-bottom: 0;">Password</label>
                                    @if (Route::has('password.request'))
                                        <a class="apb-link" href="{{ route('password.request') }}">Lupa password?</a>
                                    @endif
                                </div>
                                <input
                                    id="password"
                                    class="apb-input"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan password">
                                @foreach ($errors->get('password') as $message)
                                    <p class="apb-error">{{ $message }}</p>
                                @endforeach
                            </div>

                            <div class="apb-form-options">
                                <label class="apb-checkbox" for="remember_me">
                                    <input id="remember_me" type="checkbox" name="remember">
                                    <span>Remember me</span>
                                </label>
                            </div>

                            <button class="apb-login-button" type="submit">Masuk Dashboard</button>
                        </form>
                    </div>

                    <p class="apb-login-footer">
                        &copy; {{ now()->year }} APB POS. Sistem kasir dan inventory.
                    </p>
                </div>
            </section>
        </div>
    </main>
</x-guest-layout>
