<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// 一覧表示ページの住所は '/places' にします
Volt::route('/places', 'places.index');

// 登録ページの住所は '/places/create' です
Volt::route('/places/create', 'places.create');

// 利用者用のスケジュール表示ページ
Volt::route('/schedule', 'schedule.index');

// 利用者用の選択ページ（例：/choice/1/2 のようにアクセスする）
Volt::route('/choice', 'choice.index');

// トップページ('/')にアクセスが来たら、一覧ページ('/places')に移動させます
Route::get('/', function () {
    return redirect('/places');
});
