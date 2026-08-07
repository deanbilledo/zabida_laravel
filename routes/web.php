<?php

use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\Admin\AdminUserController;
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
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/activities', [ActivitiesController::class, 'programs'])->name('activities');
Route::get('/activities-post', [ActivitiesController::class, 'activities'])->name('activities.posts');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('contact.submit');

Route::get('/journal/{post}', [PostController::class, 'show'])->name('posts.show');

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

    Route::post('logout', [AdminLoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // 'update' pulled out of the resource below and registered to accept
        // BOTH put and post directly — removes any dependency on _method
        // spoofing being recognized correctly for an XHR-submitted
        // multipart/form-data upload, which is what the async post/publication
        // forms use (see assets/js/ui.js). Keep @method('PUT') in the Blade
        // forms regardless — it still works when it's a genuine PUT and is
        // harmless when the route also accepts POST directly.
        Route::resource('posts', AdminPostController::class)->except(['show', 'update']);
        Route::match(['put', 'post'], 'posts/{post}', [AdminPostController::class, 'update'])
            ->name('posts.update');
        Route::delete('posts/{post}/media/{media}', [AdminPostController::class, 'destroyMedia'])
            ->name('posts.media.destroy');

        Route::resource('publications', AdminPublicationController::class)->except(['show', 'update']);
        Route::match(['put', 'post'], 'publications/{publication}', [AdminPublicationController::class, 'update'])
            ->name('publications.update');

        Route::get('facebook-sync', [FacebookSyncController::class, 'index'])->name('facebook.index');
        Route::post('facebook-sync', [FacebookSyncController::class, 'sync'])->name('facebook.sync');

        Route::middleware(['super_admin'])->group(function () {
        Route::get('admins', [AdminUserController::class, 'index'])->name('admins.index');
        Route::get('admins/create', [AdminUserController::class, 'create'])->name('admins.create');
        Route::post('admins', [AdminUserController::class, 'store'])->name('admins.store');
        Route::post('admins/{admin}/promote', [AdminUserController::class, 'promote'])->name('admins.promote');
        Route::delete('admins/{admin}', [AdminUserController::class, 'destroy'])->name('admins.destroy');

        Route::post('facebook-sync/settings', [FacebookSyncController::class, 'updateSettings'])
            ->name('facebook.settings.update');
        });
    });
});