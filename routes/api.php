<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Admin register and Login
Route::post('/register', [App\Http\Controllers\api\AuthController::class, 'register'])->name('register');
Route::post('/login', [App\Http\Controllers\api\AuthController::class, 'login'])->name('login');



// Route::get('/product', [App\Http\Controllers\api\ProductController::class, 'index'])->name('products.index')->middleware('auth::sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [App\Http\Controllers\api\AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');


    //state route
    Route::get('/stats', [App\Http\Controllers\api\StatsController::class, 'index'])->name('stats.index');

    //product routes
    Route::get('/products', [App\Http\Controllers\api\ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [App\Http\Controllers\api\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}', [App\Http\Controllers\api\ProductController::class, 'show'])->name('products.show');
    Route::put('/products/{id}', [App\Http\Controllers\api\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [App\Http\Controllers\api\ProductController::class, 'destroy'])->name('products.destroy');

    //Product with order
    Route::get('/products/{id}/with-orders', [App\Http\Controllers\api\ProductController::class, 'withOrders'])->name('products.WithOrders');


    //category routes
    Route::get('/categories', [App\Http\Controllers\api\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [App\Http\Controllers\api\CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}', [App\Http\Controllers\api\CategoryController::class, 'show'])->name('categories.show');
    Route::put('/categories/{id}', [App\Http\Controllers\api\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [App\Http\Controllers\api\CategoryController::class, 'delete'])->name('categories.delete');

    //user 
    Route::get('/user', [App\Http\Controllers\api\UserController::class, 'index'])->name('users.index');
    Route::post('/user', [App\Http\Controllers\api\UserController::class, 'store'])->name('users.store');
    Route::get('/user/{id}', [App\Http\Controllers\api\UserController::class, 'show'])->name('users.show');
    Route::put('/user/{id}', [App\Http\Controllers\api\UserController::class, 'update'])->name('users.update');
    Route::delete('/user/{id}', [App\Http\Controllers\api\UserController::class, 'destroy'])->name('users.destroy');

    //route Customer
    Route::get('/customer', [App\Http\Controllers\api\CustomerController::class, 'index'])->name('customer.index');
    Route::post('/customer', [App\Http\Controllers\api\CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customer/{id}', [App\Http\Controllers\api\CustomerController::class, 'details'])->name('customer.details');
    Route::put('/customer/{id}', [App\Http\Controllers\api\CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{id}', [App\Http\Controllers\api\CustomerController::class, 'delete'])->name('customer.delete');

    //Route Orders
    Route::get('/order', [App\Http\Controllers\api\OrderController::class, 'index'])->name('order.index');
    Route::post('/order', [App\Http\Controllers\api\OrderController::class, 'store'])->name('order.store');
    Route::get('/order/{id}', [App\Http\Controllers\api\OrderController::class, 'details'])->name('order.details');
    Route::put('/order/{id}', [App\Http\Controllers\api\OrderController::class, 'update'])->name('order.update');
    Route::delete('/order/{id}', [App\Http\Controllers\api\OrderController::class, 'destroy'])->name('order.destroy');


    //Route for Roles
    Route::get('/role', [App\Http\Controllers\api\RoleController::class, 'index'])->name('role.index');
    Route::post('/role', [App\Http\Controllers\api\RoleController::class, 'create'])->name('role.create');
    Route::get('/role/{id}', [App\Http\Controllers\api\RoleController::class, 'detail'])->name('role.detail');
    Route::put('/role/{id}', [App\Http\Controllers\api\RoleController::class, 'update'])->name('role.update');
    Route::delete('/role/{id}', [App\Http\Controllers\api\RoleController::class, 'delete'])->name('role.delete');


    //Route for Permissions
    Route::get('/permission', [App\Http\Controllers\api\PermissionController::class, 'index'])->name('permission.index');
    Route::post('/permission', [App\Http\Controllers\api\PermissionController::class, 'store'])->name('permission.store');
    Route::get('/permission/{id}', [App\Http\Controllers\api\PermissionController::class, 'show'])->name('permission.show');
    Route::put('/permission/{id}', [App\Http\Controllers\api\PermissionController::class, 'update'])->name('permission.update');
    Route::delete('/permission/{id}', [App\Http\Controllers\api\PermissionController::class, 'destroy'])->name('permission.destroy');

    //Route for Role-Permission
    Route::get('/role-permission', [App\Http\Controllers\api\RolePermissionController::class, 'index'])->name('role-permission.index');
    Route::post('/role-permission', [App\Http\Controllers\api\RolePermissionController::class, 'store'])->name('role-permission.store');
    Route::get('/role-permission/{id}', [App\Http\Controllers\api\RolePermissionController::class, 'show'])->name('role-permission.show');
    Route::put('/role-permission/{id}', [App\Http\Controllers\api\RolePermissionController::class, 'update'])->name('role-permission.update');
    Route::delete('/role-permission/{id}', [App\Http\Controllers\api\RolePermissionController::class, 'destroy'])->name('role-permission.destroy');

    //Route for Permission-Feature
    Route::get('/permission-feature', [App\Http\Controllers\api\PermissionFeatureController::class, 'index'])->name('permission-feature.index');
    Route::post('/permission-feature', [App\Http\Controllers\api\PermissionFeatureController::class, 'store'])->name('permission-feature.store');
    Route::get('/permission-feature/{id}', [App\Http\Controllers\api\PermissionFeatureController::class, 'show'])->name('permission-feature.show');
    Route::put('/permission-feature/{id}', [App\Http\Controllers\api\PermissionFeatureController::class, 'update'])->name('permission-feature.update');
    Route::delete('/permission-feature/{id}', [App\Http\Controllers\api\PermissionFeatureController::class, 'destroy'])->name('permission-feature.destroy');

    //Route for Report
    Route::get('/report', [App\Http\Controllers\api\ReportController::class, 'index'])->name('report.index');
    Route::post('/report', [App\Http\Controllers\api\ReportController::class, 'store'])->name('report.store');
    Route::get('/report/{id}', [App\Http\Controllers\api\ReportController::class, 'show'])->name('report.show');
    Route::put('/report/{id}', [App\Http\Controllers\api\ReportController::class, 'update'])->name('report.update');
    Route::delete('/report/{id}', [App\Http\Controllers\api\ReportController::class, 'destroy'])->name('report.destroy');

    // Additional routes for assigning permissions to roles and features to permissions
    Route::post('/role/{id}/assign-permissions', [App\Http\Controllers\api\RolePermissionController::class, 'assignPermissions']);
    Route::post('/permission/{id}/assign-features', [App\Http\Controllers\api\PermissionController::class, 'assignFeatures']);



    


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json(["id" => $request->user()->id, "name" => $request->user()->name, "email" => $request->user()->email,]); });


