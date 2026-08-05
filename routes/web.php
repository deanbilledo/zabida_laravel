<?php

use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacebookSyncController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\PublicationController as AdminPublicationController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PublicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
| Laravel routes never expose index.php or a .php extension in the URL —
| that is automatic (public/.htaccess rewrites everything through
| public/index.php invisibly). Nothing extra needed here.
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/activities', [ActivitiesController::class, 'programs'])->name('activities');
Route::get('/activities-post', [ActivitiesController::class, 'activities'])->name('activities.posts');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('contact.submit');

Route::get('/journal/{post}', [PostController::class, 'show'])->name('posts.show');

// PeaceWorks and Knowledge Products — the public PDF archive
Route::get('/peaceworks-knowledge-products', [PublicationController::class, 'index'])
    ->name('publications.index');
Route::get('/peaceworks-knowledge-products/{publication}/view', [PublicationController::class, 'view'])
    ->name('publications.view');
Route::get('/peaceworks-knowledge-products/{publication}/download', [PublicationController::class, 'download'])
    ->name('publications.download');

/*
|--------------------------------------------------------------------------
| Admin authentication
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminLoginController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.attempt');
    });

    // Original sign-out was a plain link — no CSRF-protected POST, no
    // session invalidation. Fixed here as a real POST route.
    Route::post('logout', [AdminLoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('posts', AdminPostController::class)->except(['show']);
        Route::delete('posts/{post}/media/{media}', [AdminPostController::class, 'destroyMedia'])
            ->name('posts.media.destroy');

        Route::resource('publications', AdminPublicationController::class)->except(['show']);

        Route::get('facebook-sync', [FacebookSyncController::class, 'index'])->name('facebook.index');
        Route::post('facebook-sync', [FacebookSyncController::class, 'sync'])->name('facebook.sync');
    });
});
