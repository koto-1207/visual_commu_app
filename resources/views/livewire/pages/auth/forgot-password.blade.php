<?php

use Illuminate\Support\Facades\Password;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('components.layouts.app');

state(['email' => '']);

rules(['email' => ['required', 'string', 'email']]);

$sendPasswordResetLink = function () {
    $this->validate();

    $status = Password::sendResetLink($this->only('email'));

    if ($status != Password::RESET_LINK_SENT) {
        $this->addError('email', __($status));
        return;
    }

    $this->reset('email');

    session()->flash('status', __($status));
};

?>

<div class="min-h-screen flex items-center justify-center bg-pic-bg px-4">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-2xl shadow-lg">

        {{-- ロゴ --}}
        <div class="flex justify-center">
            <a href="/" wire:navigate>
                <img src="{{ asset('images/logo.jpg') }}" alt="PicCommu Logo"
                    class="h-24 w-24 rounded-full bg-white p-2 border-4 border-pic-mint shadow-md cursor-pointer hover:border-pic-pink hover:shadow-lg transition duration-150">
            </a>
        </div>

        {{-- タイトル --}}
        <h2 class="text-center text-3xl font-bold text-gray-700">
            パスワードリセット
        </h2>

        {{-- 説明文 --}}
        <div class="text-sm text-gray-600 text-center">
            パスワードをお忘れですか？メールアドレスを入力していただければ、新しいパスワードを設定するためのリンクをお送りします。
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-md">
                {{ session('status') }}
            </div>
        @endif

        {{-- フォーム --}}
        <form wire:submit="sendPasswordResetLink" class="space-y-6">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                <input id="email" name="email" type="email" autocomplete="email" required autofocus
                    wire:model="email"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-pic-pink hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pic-pink transition duration-150">
                    リセットリンクを送信
                </button>
            </div>
        </form>

        {{-- ログインリンク --}}
        <div class="text-center">
            <a href="{{ route('login') }}" wire:navigate
                class="text-sm text-gray-600 hover:text-pic-mint transition duration-150">
                ← ログイン画面に戻る
            </a>
        </div>

    </div>
</div>
