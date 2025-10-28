<?php

use function Livewire\Volt\{state, layout};

layout('components.layouts.app');

state(['email' => '', 'password' => '']);

?>

<div class="min-h-screen flex items-center justify-center bg-pic-bg px-4">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-2xl shadow-lg">

        {{-- ロゴ --}}
        <div class="flex justify-center">
            <img src="{{ asset('images/logo.jpg') }}" alt="PicCommu Logo" class="h-20 w-auto">
        </div>

        {{-- タイトル --}}
        <h2 class="text-center text-3xl font-bold text-gray-700">
            ログイン
        </h2>

        {{-- ログインフォーム --}}
        <form class="space-y-6" action="#" method="POST">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                <input id="email" name="email" type="email" autocomplete="email" required wire:model="email"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    wire:model="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-pic-pink hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pic-pink transition duration-150">
                    ログインする
                </button>
            </div>
        </form>

    </div>
</div>
