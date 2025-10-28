<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// 公開ページ
Volt::route('/', 'welcome');
Volt::route('/login', 'auth.login')->name('login');

// 管理画面
Volt::route('/places', 'places.index');
Volt::route('/places/create', 'places.create');

// 利用者画面
Volt::route('/schedule', 'schedule.index');
Volt::route('/choice', 'choice.index');
