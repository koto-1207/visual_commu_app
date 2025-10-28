<?php
use function Livewire\Volt\layout;
layout('components.layouts.user');
?>

<div
    class="min-h-screen flex flex-col items-center justify-center p-6 text-center bg-gradient-to-br from-pic-mint via-pic-bg to-pic-pink relative overflow-hidden">

    {{-- 背景装飾 --}}
    <div class="absolute top-10 left-10 w-24 h-16 bg-white rounded-full opacity-60 filter blur-sm animate-float"></div>
    <div
        class="absolute bottom-5 right-5 w-20 h-12 bg-white rounded-full opacity-50 filter blur-sm transform rotate-45 animate-float delay-1">
    </div>

    {{-- メインコンテンツ --}}
    <div class="z-10 flex flex-col items-center">

        {{-- ロゴ --}}
        <img src="{{ asset('images/logo.jpg') }}" alt="PicCommu Logo"
            class="h-28 w-auto mb-6 mx-auto bg-white rounded-full p-2 shadow-lg">

        {{-- タイトル --}}
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-700 mb-2">
            見つけよう、伝えよう！
        </h1>

        {{-- 説明文 --}}
        <p class="text-md lg:text-lg text-gray-600 mb-8 max-w-md">
            見てわかる、指で伝わる、新しいコミュニケーション。
        </p>

        {{-- ログインボタン --}}
        <a href="/login" wire:navigate
            class="inline-block px-10 py-4 bg-pic-pink text-white rounded-full text-xl lg:text-2xl font-bold hover:bg-opacity-80 transition duration-150 shadow-md">
            ログインする
        </a>

    </div>

</div>
