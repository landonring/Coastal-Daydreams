<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminProjectController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectShowController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/projects/{project:slug}', ProjectShowController::class)->name('projects.show');

Route::get('/admin', [AdminAuthController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'store'])->name('admin.login.store');

Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [AdminProjectController::class, 'index'])->name('dashboard');
    Route::get('/projects/create', [AdminProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [AdminProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [AdminProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [AdminProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [AdminProjectController::class, 'destroy'])->name('projects.destroy');
    Route::patch('/projects/{project}/sold', [AdminProjectController::class, 'toggleSold'])->name('projects.toggle-sold');
    Route::patch('/projects/{project}/move', [AdminProjectController::class, 'move'])->name('projects.move');
    Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings/password', [AdminSettingsController::class, 'update'])->name('settings.password.update');
    Route::put('/settings/about-photos', [AdminSettingsController::class, 'updateAboutPhotos'])->name('settings.about-photos.update');
    Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
});
