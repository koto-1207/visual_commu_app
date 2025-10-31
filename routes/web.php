<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


require __DIR__ . '/auth.php';


// ★ ログインした人だけがアクセスできる「管理グループ」
Route::middleware(['auth', 'verified'])->group(function () { // 'verified' も追加推奨 (メール認証を使う場合)

    // ダッシュボード（管理画面にリダイレクト）
    Route::redirect('/dashboard', '/places')->name('dashboard');

    // プロフィール
    Route::view('/profile', 'profile')->name('profile');

    // 1. 管理画面（一覧） (ログインが必要)
    Volt::route('/places', 'places.index')->name('places.index');

    // 2. 管理画面（新規登録） (ログインが必要)
    Volt::route('/places/create', 'places.create')->name('places.create');
})->name('admin.');

// ★ 利用者用のページは、ログイン不要のまま
// 3. 利用者画面（スケジュール）
Volt::route('/schedule', 'schedule.index')->name('schedule');

// 4. 利用者画面（選択スライドショー）
Volt::route('/choice', 'choice.index')->name('choice');

// 5. トップページ -> ウェルカム画面へ 
Volt::route('/', 'welcome')->name('welcome');
