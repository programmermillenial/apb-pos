# APB POS - Product Overview

## Project Purpose
APB POS is a Point of Sale and inventory management web application built for retail businesses (particularly multi-outlet operations). It handles the full procurement-to-sale cycle: purchase orders, goods receipts, inventory tracking, and sales — all within a single system.

## Key Features & Capabilities

### Procurement & Inventory
- **Purchase Order Management**: Full PO lifecycle — draft → submitted → approved → received/cancelled
- **Goods Receipt**: Receive goods against approved POs with partial receiving support; auto-updates stock
- **Stock Movement Tracking**: Ledger-style record of every IN/OUT/ADJUSTMENT with running balance
- **Inventory Adjustments**: Manual stock correction via InventoryService

### Product Catalog
- Products with SKU, barcode, cost price, sell price, stock quantity
- Categorized by ProductCategory, Brand, Unit
- Per-outlet product association

### Master Data
- Stores and Outlets (multi-outlet support)
- Suppliers and Customers
- Users with authentication (Laravel Breeze)

### Reporting & Export
- Dashboard with charts (ApexCharts)
- PDF export via barryvdh/laravel-dompdf
- Excel export via maatwebsite/excel

## Target Users
- Retail store owners and managers operating single or multiple outlets
- Purchasing staff managing supplier orders
- Warehouse/receiving staff processing goods receipts
- Administrators managing products, users, and master data

## Business Domain
Indonesian retail market (UI messages and number formatting in Indonesian: "Rp", "." as thousand separator, "," as decimal separator).
