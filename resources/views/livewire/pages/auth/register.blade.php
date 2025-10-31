<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('components.layouts.app');

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
]);

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
]);

$register = function () {
    $validated = $this->validate();

    $validated['password'] = Hash::make($validated['password']);

    event(new Registered(($user = User::create($validated))));

    Auth::login($user);

    $this->redirect(route('places.index', absolute: false), navigate: true);
};

?>

<div class="min-h-screen flex items-center justify-center bg-pic-bg px-4">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-2xl shadow-lg">

        {{-- ロゴ --}}
        <div class="flex justify-center">
            <img src="{{ asset('images/logo.jpg') }}" alt="PicCommu Logo" class="h-20 w-auto">
        </div>

        {{-- タイトル --}}
        <h2 class="text-center text-3xl font-bold text-gray-700">
            新規登録
        </h2>

        {{-- 登録フォーム --}}
        <form wire:submit="register" class="space-y-6">

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">お名前</label>
                <input id="name" name="name" type="text" autocomplete="name" required autofocus
                    wire:model="name"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                <input id="email" name="email" type="email" autocomplete="email" required wire:model="email"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required
                    wire:model="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">パスワード（確認）</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                    autocomplete="new-password" required wire:model="password_confirmation"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-pic-pink hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pic-pink transition duration-150">
                    登録する
                </button>
            </div>
        </form>

        {{-- ログインリンク --}}
        <div class="text-center">
            <p class="text-sm text-gray-600">
                既にアカウントをお持ちの方は
                <a href="{{ route('login') }}" wire:navigate
                    class="font-medium text-pic-pink hover:text-pic-mint transition duration-150">
                    ログイン
                </a>
            </p>
        </div>

    </div>
</div>
