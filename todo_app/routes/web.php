<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/todos')->name('home');
Route::livewire('/todos', 'pages::todos.index')->name('todos.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/todos')->name('dashboard');
    Route::livewire('/hello', 'pages::hello')->name('hello');
    Route::livewire('/todos/create', 'pages::todos.create')->name('todos.create');
    Route::livewire('/todos/{todo}/edit', 'pages::todos.edit')->name('todos.edit');
});

require __DIR__.'/settings.php';
