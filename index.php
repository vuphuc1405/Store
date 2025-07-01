<?php

session_start();
require_once 'config/database.php';
require_once 'app/core/Model.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Router.php';

$router = new Router();

$router->addRoute('GET', '/home', 'HomeController', 'index');
$router->addRoute('GET', '/products', 'ProductController', 'index');
$router->addRoute('GET', '/product', 'ProductController', 'detail');
$router->addRoute('GET', '/category', 'ProductController', 'category');

$router->addRoute('GET', '/login', 'AuthController', 'login');
$router->addRoute('POST', '/login', 'AuthController', 'doLogin');
$router->addRoute('GET', '/register', 'AuthController', 'register');
$router->addRoute('POST', '/register', 'AuthController', 'doRegister');
$router->addRoute('GET', '/logout', 'AuthController', 'logout');

$router->addRoute('GET', '/cart', 'CartController', 'index');
$router->addRoute('POST', '/cart/add', 'CartController', 'add');
$router->addRoute('POST', '/cart/update', 'CartController', 'update');
$router->addRoute('POST', '/cart/remove', 'CartController', 'remove');

$router->addRoute('GET', '/orders', 'OrderController', 'index');
$router->addRoute('GET', '/orders/detail', 'OrderController', 'detail');
$router->addRoute('GET', '/checkout', 'OrderController', 'checkout');
$router->addRoute('POST', '/checkout', 'OrderController', 'create');

$router->addRoute('GET', '/admin', 'AdminController', 'index');
$router->addRoute('GET', '/admin/products', 'AdminController', 'products');
$router->addRoute('GET', '/admin/orders', 'AdminController', 'orders');
$router->addRoute('GET', '/admin/users', 'AdminController', 'users');
$router->addRoute('POST', '/admin/updateOrderStatus', 'AdminController', 'updateOrderStatus');
$router->addRoute('GET', '/admin/products/add', 'AdminController', 'addProduct');
$router->addRoute('POST', '/admin/products/store', 'AdminController', 'storeProduct');
$router->addRoute('GET', '/admin/products/edit', 'AdminController', 'editProduct');
$router->addRoute('POST', '/admin/products/update', 'AdminController', 'updateProduct');
$router->addRoute('GET', '/admin/products/delete', 'AdminController', 'deleteProduct');
$router->addRoute('GET', '/admin/orders/detail', 'AdminController', 'orderDetail');
$router->addRoute('GET', '/admin/reports/sales', 'AdminController', 'salesReport');

$router->addRoute('GET', '/profile', 'ProfileController', 'index');
$router->addRoute('POST', '/profile/update', 'ProfileController', 'update');
$router->addRoute('POST', '/profile/change-password', 'ProfileController', 'changePassword');

$router->addRoute('GET', '/buy-now', 'OrderController', 'buyNowCheckout');
$router->addRoute('POST', '/create-buy-now-order', 'OrderController', 'createBuyNowOrder');

$router->addRoute('POST', '/reviews/create', 'ReviewController', 'create');
$router->addRoute('POST', '/reviews/update', 'ReviewController', 'update');
$router->addRoute('POST', '/reviews/delete', 'ReviewController', 'delete');

$router->addRoute('GET', '/admin/categories', 'AdminController', 'categories');
$router->addRoute('GET', '/admin/categories/add', 'AdminController', 'addCategory');
$router->addRoute('POST', '/admin/categories/store', 'AdminController', 'storeCategory');
$router->addRoute('GET', '/admin/categories/edit', 'AdminController', 'editCategory');
$router->addRoute('POST', '/admin/categories/update', 'AdminController', 'updateCategory');
$router->addRoute('GET', '/admin/categories/delete', 'AdminController', 'deleteCategory');

$router->addRoute('GET', '/admin/brands', 'AdminController', 'brands');
$router->addRoute('GET', '/admin/brands/add', 'AdminController', 'addBrand');
$router->addRoute('POST', '/admin/brands/store', 'AdminController', 'storeBrand');
$router->addRoute('GET', '/admin/brands/edit', 'AdminController', 'editBrand');
$router->addRoute('POST', '/admin/brands/update', 'AdminController', 'updateBrand');
$router->addRoute('GET', '/admin/brands/delete', 'AdminController', 'deleteBrand');

$router->addRoute('GET', '/about', 'PageController', 'about');
$router->addRoute('GET', '/contact', 'PageController', 'contact');
$router->addRoute('GET', '/warranty', 'PageController', 'warranty');
$router->dispatch();