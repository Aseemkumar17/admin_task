<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\AuthUserController;
use App\Http\Controllers\UserprofileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Common\ImageUploadController;
use App\Http\Controllers\Admin\DbBackupController;
use App\Models\Access_permission;
use App\Http\Middleware\PermissionCheck;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('unauthorized', function () {
    return response()->json([
        'message' => 'Please provide details',
    ], 401);
})->name('login');


// Signup
Route::post('/signup', [AuthUserController::class, 'signup']);
Route::post('/verify-email', [AuthUserController::class, 'verifyEmail']);

//ADMIN APIS

// Admin  Log in
Route::post('/login', [AdminLoginController::class, 'login']);


Route::middleware('auth:sanctum','admin-profile')->group(function () {
    // User
    Route::middleware("permissioncheck:user")->group(function () {
        Route::post('/add-user', [UserController::class, 'addUser']);
        Route::get('/user-show/{id}', [UserController::class, 'userShow']);
        Route::put('/block-user/{id}', [UserController::class, 'blockUser']);
        Route::put('/deactivate-user/{id}', [UserController::class, 'deactivateUser']);
        Route::get('/delete-user/{id}', [UserController::class, 'deleteUser']);
        Route::post('/user-search', [UserController::class, 'searchUser']);
        Route::get('/user-listing', [UserController::class, 'userList']);
        Route::post('/export-data', [UserController::class, 'exportuser']); 
    });
    // Dashboard
    Route::middleware("permissioncheck:dashboard")->group(function () {
        Route::get('/user-products-count', [DashboardController::class, 'counts']);
        Route::get('/user-listing', [DashboardController::class, 'userlisting']);
    });
    // Products
    Route::middleware("permissioncheck:products")->group(function () {
        Route::post('/add-product', [ProductController::class, 'addProduct']);
        Route::get('/product-list', [ProductController::class, 'productList']);
        Route::get('/product-show/{id}', [ProductController::class, 'product_show']);
        Route::put('/edit-product/{id}', [ProductController::class, 'editProduct']);
        Route::post('/search-product', [ProductController::class, 'searchProduct']);
        Route::post('/export-product', [ProductController::class, 'exportProduct']);
        Route::post('product-gallery/{id}',[ProductController::class,'AddGallery']);
    });
    // Content
    Route::middleware("permissioncheck:content")->group(function () {
        Route::post('/add-content', [ContentController::class, 'addContent']);
        Route::get('/content-show/{id}', [ContentController::class, 'show']);
        Route::put('/content-edit/{id}', [ContentController::class, 'edit']);
        Route::post('/content-list', [ContentController::class, 'contentList']);
    });
    // FAQ
    Route::middleware("permissioncheck:faq")->group(function () {
        Route::post('/add-faq', [FaqController::class, 'addFaq']);
        Route::put('/edit-faq/{id}', [FaqController::class, 'edit']);
        Route::post('/faq-list-search', [FaqController::class, 'faqlist_search']);
    });
    // Contact Us
    Route::middleware("permissioncheck:contact")->group(function () {
        Route::post('/contact-list-search', [ContactController::class, 'contactlist_search']);
        Route::get('/change-status/{id}', [ContactController::class, 'chngestatus']);
        Route::get('/delete-contact/{id}', [ContactController::class, 'delete']);
        Route::post('/export-contact', [ContactController::class, 'exportContact']);
    });
    // Notification
    Route::middleware("permissioncheck:notification")->group(function () {
        Route::post('/notification', [NotificationController::class, 'notification']);
    });
    // Staff
    Route::middleware("permissioncheck:staff")->group(function () {
        Route::post('/add-staff', [StaffController::class, 'addStaff']);
        Route::get('/view-staff/{id}', [StaffController::class, 'viewStaff']);
        Route::put('/block-staff/{id}', [StaffController::class, 'blockStaff']);
        Route::get('/delete-staff/{id}', [StaffController::class, 'deleteStaff']);
        Route::post('/list-search', [StaffController::class, 'list_search']);
        Route::post('/edit-staff/{id}', [StaffController::class, 'edit']);
    });

    // db backup
    Route::middleware("permissioncheck:db_backup")->group(function () {
Route::get('/db-backup', [DbBackupController::class, 'DownloadBackup']);
    });

    // Profile
    Route::get('/show-profile', [AdminLoginController::class, 'profile']);
    Route::get('/logout', [AdminLoginController::class, 'logout']);
    Route::put('/edit-profile', [AdminLoginController::class, 'editProfile']);
    Route::put('/change-password', [AdminLoginController::class, 'changePassword']);



});

// image upload
Route::post('/image-upload',[ImageUploadController::class,'uploadImage']);






Route::post('/user-login',[AuthUserController::class, 'userlogin']); //user-login

// user apis
Route::middleware('auth:sanctum','user-profile')->group(function () {

   Route::get('/user-logout',[AuthUserController::class,'userlogout']);
   Route::get('/user-profile',[UserprofileController::class,'userprofile']);
    });









