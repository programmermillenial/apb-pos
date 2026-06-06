<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'APB POS'))</title>

    {{-- Hope UI CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/hope-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/rtl.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" />

    {{-- Plugins --}}
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
    <style>
        /* =========================
            APB POS DATATABLE STYLE
           ========================= */
        .table.dataTable {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        /* HEADER */
        .table.dataTable thead th {
            background: #3a57e8 !important;
            color: #fff !important;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
            border: none !important;
            padding: 12px 10px;
            border: 0;
        }

        /* BODY */
        .table.dataTable tbody td {
            vertical-align: middle;
            padding: 10px;
            font-size: 13px;
        }

        /* STRIPED */
        .table.dataTable tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        /* HOVER */
        .table.dataTable tbody tr:hover {
            background: #eef2ff;
            transition: .2s;
        }

        /* BORDER */
        .table.dataTable {
            border: 1px solid #e9ecef;
        }

        /* PAGINATION */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0 !important;
            border: none !important;
        }

        .dataTables_wrapper .pagination {
            margin-top: 15px;
        }

        .dataTables_wrapper .page-link {
            border-radius: 8px !important;
            margin: 0 3px;
        }

        /* SEARCH */
        .dataTables_filter input {
            border-radius: 10px !important;
            border: 1px solid #dfe3e7 !important;
            padding: 8px 12px !important;
            min-width: 250px;
        }

        /* LENGTH */
        .dataTables_length select {
            border-radius: 10px !important;
            border: 1px solid #dfe3e7 !important;
        }

        /* INFO */
        .dataTables_info {
            font-size: 13px;
            color: #6c757d;
        }

        /* PROCESSING */
        .dataTables_processing {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 0 20px rgba(0, 0, 0, .1);
        }

        /* ===============================
            TRANSACTION TABLE / FORM TABLE
           ================================ */
        .table-transaction {
            border-collapse: collapse !important;
            width: 100% !important;
            border: 1px solid #e9ecef !important;
            background: #fff;
        }

        .table-transaction thead th {
            background: #3a57e8 !important;
            color: #fff !important;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
            border: 1px solid #2f49c9 !important;
            padding: 12px 10px;
        }

        .table-transaction tbody td {
            vertical-align: middle;
            padding: 10px;
            font-size: 13px;
            border: 1px solid #e9ecef !important;
            color: #232d42;
        }

        .table-transaction tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .table-transaction tbody tr:hover {
            background: #eef2ff;
            transition: .2s;
        }

        .table-transaction .form-control,
        .table-transaction .form-select {
            font-size: 13px;
            border-radius: 8px;
            border: 1px solid #dfe3e7;
            min-height: 42px;
        }

        .table-transaction .form-control:focus,
        .table-transaction .form-select:focus {
            border-color: #3a57e8;
            box-shadow: 0 0 0 0.15rem rgba(58, 87, 232, .15);
        }

        /* ===============================
           SELECT2 INSIDE TRANSACTION TABLE
           ================================ */
        .table-transaction .select2-container {
            width: 100% !important;
        }

        .table-transaction .select2-container--bootstrap-5 .select2-selection,
        .table-transaction .select2-container--default .select2-selection {
            min-height: 42px !important;
            border: 1px solid #dfe3e7 !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            background-color: #fff !important;
        }

        .table-transaction .select2-container--bootstrap-5 .select2-selection--single,
        .table-transaction .select2-container--default .select2-selection--single {
            height: 42px !important;
            padding: 6px 10px !important;
        }

        .table-transaction .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered,
        .table-transaction .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #232d42 !important;
            font-size: 13px !important;
            font-weight: 500;
            line-height: 28px !important;
            padding-left: 0 !important;
            padding-right: 20px !important;
        }

        .table-transaction .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow,
        .table-transaction .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }

        .table-transaction .select2-selection__clear {
            display: none !important;
        }

        .select2-dropdown {
            border: 1px solid #dfe3e7 !important;
            z-index: 9999 !important;
        }

        .select2-results__option {
            font-size: 13px;
            padding: 8px 12px;
        }

         /* ===============================
            TRANSACTION TABLE / FORM TABLE
           ================================ */
        .transaction-detail-table tfoot th {
            border: 1px solid #e9ecef !important;
            padding: 14px 12px;
            vertical-align: middle;
        }

        .transaction-detail-table .po-summary-row th {
            background: #ffffff !important;
            color: #344054 !important;
            font-size: 14px;
            font-weight: 600;
        }

        .transaction-detail-table .po-summary-label {
            background: #f8f9fa !important;
            color: #475467 !important;
            width: 180px;
        }

        .transaction-detail-table .po-summary-value {
            background: #ffffff !important;
            color: #101828 !important;
            width: 220px;
            font-weight: 700;
        }

        .transaction-detail-table .po-grand-total-row th {
            background: #3a57e8 !important;
            color: #ffffff !important;
            font-size: 15px;
            font-weight: 700;
        }

        .transaction-detail-table .po-grand-total-row .po-summary-value {
            color: #ffffff !important;
            font-size: 16px;
        }
    </style>

    <script>
        let table = '';
    </script>

    @stack('styles')
</head>

<body>
    <!-- loader Start -->
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div>
    <!-- loader END -->
    @include('layouts.sidebar')

    <main class="main-content">

        @include('layouts.navbar')

        <div class="container-fluid content-inner py-0">
            @yield('content')
        </div>

        @include('layouts.footer')

    </main>

    {{-- Hope UI JS --}}
    <script src="{{ asset('assets/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/external.min.js') }}"></script>
    <script src="{{ asset('assets/js/hope-ui.js') }}"></script>

    {{-- Plugins --}}
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/autonumeric.js') }}"></script>

    @include('layouts.alert')

    <script>
        let autoNumericOptions = {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            minimumValue: '0',
            modifyValueOnWheel: false,
            unformatOnSubmit: true,
        };

        function initAutoNumeric(selector = '.autonumeric, .autonumeric-readonly') {
            $(selector).each(function() {
                if (!$(this).data('autonumeric')) {
                    let an = new AutoNumeric(this, autoNumericOptions);
                    $(this).data('autonumeric', an);
                }
            });
        }

        function getAutoNumericValue(element) {
            if (!element) {
                return 0;
            }

            let an = $(element).data('autonumeric');

            if (!an) {
                let value = $(element).val() || 0;
                return parseFloat(value.toString().replace(/\./g, '').replace(/,/g, '.')) || 0;
            }

            return parseFloat(an.getNumber()) || 0;
        }

        function setAutoNumericValue(element, value) {
            if (!element) {
                return;
            }

            let an = $(element).data('autonumeric');

            if (!an) {
                an = new AutoNumeric(element, autoNumericOptions);
                $(element).data('autonumeric', an);
            }

            an.set(value || 0);
        }

        $(document).on('click', '[data-loading="true"]', function() {
            Swal.fire({
                title: 'Mohon Tunggu',
                text: 'Sedang memproses...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

        });

        function deleteData(url, module) {
            Swal.fire({
                title: 'Hapus ' + module + '?',
                text: 'Data yang dihapus tidak bisa dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: xhr.responseJSON?.message ??
                                    'Terjadi kesalahan.'
                            });
                        }
                    });
                }
            });
        }
    </script>

    @stack('scripts')
</body>

</html>
