<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state([
    'current_password' => '',
    'password' => '',
    'password_confirmation' => '',
]);

rules([
    'current_password' => ['required', 'string', 'current_password'],
    'password' => ['required', 'string', Password::defaults(), 'confirmed'],
]);

$updatePassword = function () {
    try {
        $validated = $this->validate();
    } catch (ValidationException $e) {
        $this->reset('current_password', 'password', 'password_confirmation');

        throw $e;
    }

    Auth::user()->update([
        'password' => Hash::make($validated['password']),
    ]);

    $this->reset('current_password', 'password', 'password_confirmation');

    $this->dispatch('password-updated');
};

?>

<section>
    <header class="border-b border-pic-mint pb-3 mb-6">
        <h2 class="text-xl font-bold text-gray-800">
            パスワード更新
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            セキュリティを保つため、長くランダムなパスワードを使用してください。
        </p>
    </header>

    <form wire:submit="updatePassword" class="space-y-6">
        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700">現在のパスワード</label>
            <input wire:model="current_password" id="update_password_current_password" name="current_password"
                type="password" autocomplete="current-password"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
            @error('current_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700">新しいパスワード</label>
            <input wire:model="password" id="update_password_password" name="password" type="password"
                autocomplete="new-password"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation"
                class="block text-sm font-medium text-gray-700">パスワード（確認）</label>
            <input wire:model="password_confirmation" id="update_password_password_confirmation"
                name="password_confirmation" type="password" autocomplete="new-password"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="px-6 py-2 bg-pic-pink text-white rounded-lg hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pic-pink transition duration-150">
                保存
            </button>

            <x-action-message class="text-sm text-green-600" on="password-updated">
                保存しました
            </x-action-message>
        </div>
    </form>
</section>
