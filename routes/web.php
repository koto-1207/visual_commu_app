<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// ★ 1. トップページ（ウェルカム画面）
Volt::route('/', 'welcome'); // ★ '/' に welcome を割り当て

// ★ 2. ログインフォーム画面
Volt::route('/login', 'auth.login')->name('login'); // ★ '/login' はそのまま

// 3. 管理画面（一覧）
Volt::route('/places', 'places.index');

// 4. 管理画面（新規登録）
Volt::route('/places/create', 'places.create');

// 5. 利用者画面（スケジュール）
Volt::route('/schedule', 'schedule.index');

// 6. 利用者画面（選択スライドショー）
Volt::route('/choice', 'choice.index');

// ★ トップページ -> 管理画面へのリダイレクトは削除 (コメントアウトまたは削除)
// Route::get('/', function () {
//     return redirect('/places');
// });
