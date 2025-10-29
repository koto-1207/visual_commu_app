<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// ★ Breezeがこのあたりに、
// ★ 'auth.php' を読み込むコードなどを自動で追加しているはずです。
// ★ ↓ require __DIR__.'/auth.php'; のような行があれば、それは触らないでください。
require __DIR__ . '/auth.php';

// ↓↓↓ ここからが私たちが修正する部分 ↓↓↓

// ★ ログインした人だけがアクセスできる「管理グループ」
Route::middleware(['auth', 'verified'])->group(function () { // 'verified' も追加推奨 (メール認証を使う場合)

    // ダッシュボード（管理画面にリダイレクト）
    Route::redirect('/dashboard', '/places')->name('dashboard');

    // プロフィール
    Route::view('/profile', 'profile')->name('profile');

    // 1. 管理画面（一覧） (ログインが必要)
    Volt::route('/places', 'places.index')->name('places.index'); // ★ name() を追加

    // 2. 管理画面（新規登録） (ログインが必要)
    Volt::route('/places/create', 'places.create')->name('places.create'); // ★ name() を追加

})->name('admin.'); // ★ (オプション) グループに名前をつけると便利

// ★ 利用者用のページは、ログイン不要のまま
// 3. 利用者画面（スケジュール）
Volt::route('/schedule', 'schedule.index')->name('schedule'); // ★ name() を追加推奨

// 4. 利用者画面（選択スライドショー）
Volt::route('/choice', 'choice.index')->name('choice'); // ★ name() を追加推奨

// 5. トップページ -> ウェルカム画面へ (変更なし)
Volt::route('/', 'welcome')->name('welcome');

// ★ /login ルートは Breeze が auth.php 内で定義するので、ここからは削除します
// Volt::route('/login', 'auth.login')->name('login'); // ← この行は削除またはコメントアウト
