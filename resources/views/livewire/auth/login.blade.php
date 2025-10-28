<?php

// Voltの layout 機能を使って、app.blade.php を基本レイアウトとして使用します
use function Livewire\Volt\{state, layout};

// resources/views/components/layouts/app.blade.php を使用
layout('components.layouts.app');

// フォームの入力値を保持するための変数 (今はまだ使いません)
state(['email' => '', 'password' => '']);

?>

{{-- min-h-screen: 画面の高さいっぱいに広げる, flex items-center justify-center: 中身を上下左右中央揃え --}}
{{-- bg-pic-bg: tailwind.config.js で定義した背景色 --}}
<div class="min-h-screen flex items-center justify-center bg-pic-bg px-4">

    {{-- ログインフォームのコンテナ --}}
    {{-- w-full max-w-md: 幅を最大448pxに制限, p-8: 内側の余白, bg-white: 背景白, rounded-2xl: 大きな角丸, shadow-lg: 影 --}}
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-2xl shadow-lg">

        {{-- ロゴを表示 --}}
        <div class="flex justify-center">
            {{-- public/images/logo.jpg にロゴ画像を配置した場合の例 --}}
            {{-- ロゴファイルがない場合は、 /ぴくこみゅ.jpg のように直接指定も可能ですが、publicフォルダに置くのが推奨です --}}
            <img src="{{ asset('images/logo.jpg') }}" alt="PicCommu Logo" class="h-20 w-auto">
            {{-- もしロゴファイルが public/ぴくこみゅ.jpg なら: --}}
            {{-- <img src="{{ asset('ぴくこみゅ.jpg') }}" alt="PicCommu Logo" class="h-20 w-auto"> --}}
        </div>

        {{-- タイトル --}}
        <h2 class="text-center text-3xl font-bold text-gray-700">
            ログイン
        </h2>

        {{-- ログインフォーム (まだ送信機能はありません) --}}
        <form class="space-y-6" action="#" method="POST">

            {{-- メールアドレス入力欄 --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                <input id="email" name="email" type="email" autocomplete="email" required wire:model="email"
                    {{-- Livewire用 (今はまだ機能しない) --}}
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
            </div>

            {{-- パスワード入力欄 --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    wire:model="password" {{-- Livewire用 (今はまだ機能しない) --}}
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
            </div>

            {{-- ログインボタン --}}
            <div>
                {{-- bg-pic-pink: tailwind.config.js の色, hover:bg-opacity-80: ホバー時に少し薄く --}}
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-pic-pink hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pic-pink transition duration-150">
                    ログインする
                </button>
            </div>
        </form>

        {{-- (オプション) 新規登録へのリンクなど --}}
        {{-- <p class="mt-2 text-center text-sm text-gray-600">
            アカウントをお持ちでないですか？
            <a href="#" class="font-medium text-pic-mint hover:text-green-500">
                新規登録はこちら
            </a>
        </p> --}}

    </div>
</div>
