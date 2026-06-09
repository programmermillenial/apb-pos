<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $sale->invoice_number }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/remixicon.css') }}">

    <style>
        body {
            background: #f5f6fa;
            color: #111827;
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
        }

        .receipt-shell {
            align-items: flex-start;
            display: flex;
            gap: 20px;
            justify-content: center;
            padding: 24px;
        }

        .receipt-actions {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
            width: 220px;
        }

        .receipt-paper {
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .12);
            padding: 12px;
            width: 80mm;
        }

        .receipt-title {
            font-size: 15px;
            font-weight: 700;
            margin: 0;
            text-align: center;
        }

        .receipt-subtitle {
            color: #374151;
            margin: 2px 0;
            text-align: center;
        }

        .receipt-line {
            border-top: 1px dashed #111827;
            margin: 8px 0;
        }

        .receipt-row {
            display: table;
            margin: 2px 0;
            table-layout: fixed;
            width: 100%;
        }

        .receipt-label {
            display: table-cell;
            overflow-wrap: anywhere;
            padding-right: 8px;
            vertical-align: top;
            width: 58%;
        }

        .receipt-value {
            display: table-cell;
            text-align: right;
            vertical-align: top;
            width: 42%;
            word-break: break-word;
        }

        .receipt-item {
            margin-bottom: 7px;
        }

        .receipt-item-name {
            font-weight: 700;
        }

        .receipt-total {
            font-size: 14px;
            font-weight: 700;
        }

        .text-end {
            text-align: right;
        }

        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }

            body {
                background: #ffffff;
            }

            .receipt-shell {
                display: block;
                padding: 0;
            }

            .receipt-actions {
                display: none !important;
            }

            .receipt-paper {
                box-shadow: none;
                padding: 8px;
                width: 80mm;
            }
        }
    </style>
</head>

<body>
    @php
        $encryptedId = Crypt::encryptString($sale->id);
    @endphp

    <div class="receipt-shell">
        <div class="receipt-actions">
            <h5 class="mb-3">Struk Invoice</h5>

            <button type="button" class="btn btn-primary w-100 mb-2" onclick="window.print()">
                <i class="ri-printer-line"></i> Print Thermal
            </button>

            <a href="{{ route('sales.receipt-pdf', $encryptedId) }}" class="btn btn-success w-100 mb-2">
                <i class="ri-file-pdf-line"></i> Download PDF
            </a>

            <a href="{{ route('sales.index') }}" class="btn btn-light w-100 mb-2">
                Transaksi Baru
            </a>

            <a href="{{ route('sales.history') }}" class="btn btn-outline-secondary w-100">
                Sales History
            </a>
        </div>

        <div class="receipt-paper">
            @include('sales.partials.receipt-content')
        </div>
    </div>
@if (request('print'))
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 350);
        });
    </script>
@endif
</body>

</html>
