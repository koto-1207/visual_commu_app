<?php
use function Livewire\Volt\layout;
layout('components.layouts.user');
?>

<div
    class="min-h-screen flex flex-col items-center justify-center p-6 text-center bg-gradient-to-br from-pic-mint via-pic-bg to-pic-pink relative overflow-hidden">


    {{-- メインコンテンツ --}}
    <div class="z-10 flex flex-col items-center">

        {{-- ロゴ --}}
        <img src="{{ asset('images/logo.jpg') }}" alt="PicCommu Logo"
            class="h-40 w-auto mb-6 mx-auto bg-white rounded-full p-2 shadow-lg">

        {{-- タイトル --}}
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-700 mb-2">
            外出支援サポートアプリ
        </h1>

        {{-- 説明文 --}}
        <p class="text-md lg:text-lg text-gray-600 mb-8 max-w-md whitespace-pre-line">
            『ぴくこみゅ』は、写真カードで
            「きょうの予定」をかんたんに作ったり、
            「行きたい場所」を選ぶお手伝いをする
            コミュニケーション・アプリです。
        </p>

        {{-- ログインボタン --}}
        <a href="/login" wire:navigate
            class="inline-block px-10 py-4 bg-pic-pink text-white rounded-full text-xl lg:text-2xl font-bold hover:bg-opacity-80 transition duration-150 shadow-md">
            ログインする
        </a>

    </div>

</div>
