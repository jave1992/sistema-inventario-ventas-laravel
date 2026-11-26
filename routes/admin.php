<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\MovementController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TransferController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Inventario

Route::resource('categories', CategoryController::class)->except(['show']);
Route::get('categories/import', [CategoryController::class, 'import'])->name('categories.import');
Route::resource('products', ProductController::class)->except(['show']);
Route::post('products/{product}/dropzone', [ProductController::class, 'dropzone'])->name('products.dropzone');
Route::get('products/{product}/kardex', [ProductController::class, 'kardex'])->name('products.kardex');
Route::get('products/import', [ProductController::class, 'import'])->name('products.import');
Route::resource('warehouses', WarehouseController::class)->except(['show']);
Route::get('warehouses/import', [WarehouseController::class, 'import'])->name('warehouses.import');

// Compras
Route::resource('customers', CustomerController::class)->except(['show']);
Route::get('customers/import', [CustomerController::class, 'import'])->name('customers.import');
Route::resource('purchase-orders', PurchaseOrderController::class)->only(['index', 'create']);
Route::get('purchase-orders/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'pdf'])->name('purchase-orders.pdf');
Route::resource('purchases', PurchaseController::class)->only(['index', 'create']);
Route::get('purchases/{purchase}/pdf', [PurchaseController::class, 'pdf'])->name('purchases.pdf');

// Ventas
Route::resource('suppliers', SupplierController::class)->except(['show']);
Route::get('suppliers/import', [SupplierController::class, 'import'])->name('suppliers.import');
Route::resource('quotes', QuoteController::class)->only(['index', 'create']);
Route::get('quotes/{quote}/pdf', [QuoteController::class, 'pdf'])->name('quotes.pdf');
Route::resource('sales', SaleController::class)->only(['index', 'create']);
Route::get('sales/{sale}/pdf', [SaleController::class, 'pdf'])->name('sales.pdf');

// Movimientos
Route::resource('movements', MovementController::class)->only(['index', 'create']);
Route::get('movements/{movement}/pdf', [MovementController::class, 'pdf'])->name('movements.pdf');
Route::resource('transfers', TransferController::class)->only(['index', 'create']);
Route::get('transfers/{transfer}/pdf', [TransferController::class, 'pdf'])->name('transfers.pdf');

Route::delete('images/{image}', [ImageController::class, 'destroy'])->name('images.destroy');

// Reportes
Route::get('reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');
Route::get('reports/top-customers', [ReportController::class, 'topCustomers'])->name('reports.top-customers');
Route::get('reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');

// Usuarios
Route::resource('users', UserController::class);

// Roles
Route::resource('roles', RoleController::class);

// Compañías
Route::resource('companies', CompanyController::class)->except(['show']);
Route::get('companies/import', [CompanyController::class, 'import'])->name('companies.import');
Route::post('companies/{company}/logo', [CompanyController::class, 'logo'])->name('companies.logo');
Route::post('companies/{company}/cert', [CompanyController::class, 'cert'])->name('companies.cert');
Route::delete('companies/logo/{company}/{logo}', [CompanyController::class, 'destroyLogo'])->name('companies.logo.destroy');
Route::delete('companies/cert/{company}/{cert}', [CompanyController::class, 'destroyCert'])->name('companies.cert.destroy');

// Sunat

// Invoices
Route::get('sunat/invoices', [InvoiceController::class, 'index'])->name('sunat.invoices');
Route::get('sunat/invoices/send/{invoice}', [InvoiceController::class, 'send'])->name('sunat.invoices.send');
Route::get('sunat/invoices/xml/{invoice}', [InvoiceController::class, 'xml'])->name('sunat.invoices.xml');
Route::get('sunat/invoices/cdr/{invoice}', [InvoiceController::class, 'cdr'])->name('sunat.invoices.cdr');
Route::get('sunat/invoices/pdf/{invoice}', [InvoiceController::class, 'pdf'])->name('sunat.invoices.pdf');

// Notes
Route::get('sunat/notes', [NoteController::class, 'index'])->name('sunat.notes');
Route::get('sunat/notes/send/{invoice}', [NoteController::class, 'send'])->name('sunat.notes.send');
Route::get('sunat/notes/xml/{note}', [NoteController::class, 'xml'])->name('sunat.notes.xml');
Route::get('sunat/notes/cdr/{note}', [NoteController::class, 'cdr'])->name('sunat.notes.cdr');
Route::get('sunat/notes/pdf/{note}', [NoteController::class, 'pdf'])->name('sunat.notes.pdf');

// Settings
Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::put('settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general.update');
Route::put('settings/perfil', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
Route::put('settings/seguridad', [SettingsController::class, 'updateSecurity'])->name('settings.security.update');
Route::put('settings/apariencia', [SettingsController::class, 'updateAppearance'])->name('settings.appearance.update');
Route::post('settings/respaldo', [SettingsController::class, 'backup'])->name('settings.backup');