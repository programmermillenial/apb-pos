<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>Struk {{ $sale->invoice_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            color: #111;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .receipt-paper {
            padding: 6px;
            width: 100%;
        }

        .receipt-title {
            font-size: 13px;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }

        .receipt-subtitle {
            margin: 2px 0;
            text-align: center;
        }

        .receipt-line {
            border-top: 1px dashed #111;
            margin: 7px 0;
        }

        .receipt-row {
            display: table;
            margin: 2px 0;
            table-layout: fixed;
            width: 100%;
        }

        .receipt-label {
            display: table-cell;
            overflow-wrap: break-word;
            padding-right: 4px;
            vertical-align: top;
            width: 58%;
            word-break: break-word;
        }

        .receipt-value {
            display: table-cell;
            text-align: right;
            vertical-align: top;
            white-space: normal;
            width: 42%;
            word-break: break-word;
        }

        .receipt-item {
            margin-bottom: 6px;
        }

        .receipt-item-name {
            font-weight: bold;
        }

        .receipt-total {
            font-size: 12px;
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="receipt-paper">
        @include('sales.partials.receipt-content')
    </div>
</body>

</html>
