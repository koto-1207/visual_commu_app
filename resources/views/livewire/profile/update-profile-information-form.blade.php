<?php

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

use function Livewire\Volt\state;

state([
    'name' => fn() => auth()->user()->name,
    'email' => fn() => auth()->user()->email,
]);

$updateProfileInformation = function () {
    $user = Auth::user();

    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
    ]);

    $user->fill($validated);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    $this->dispatch('profile-updated', name: $user->name);
};

$sendVerification = function () {
    $user = Auth::user();

    if ($user->hasVerifiedEmail()) {
        $this->redirectIntended(default: route('dashboard', absolute: false));

        return;
    }

    $user->sendEmailVerificationNotification();

    Session::flash('status', 'verification-link-sent');
};

?>
<section>
    <header class="border-b border-pic-mint pb-3 mb-6">
        <h2 class="text-xl font-bold text-gray-800">
            プロフィール情報
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            アカウントのプロフィール情報とメールアドレスを更新できます。
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">お名前</label>
            <input wire:model="name" id="name" name="name" type="text" required autofocus autocomplete="name"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
            <input wire:model="email" id="email" name="email" type="email" required autocomplete="username"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-pic-mint focus:border-pic-mint sm:text-sm">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if (auth()->user() instanceof MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-gray-800">
                        メールアドレスが未確認です。

                        <button wire:click.prevent="sendVerification"
                            class="underline text-pic-pink hover:text-pic-mint">
                            確認メールを再送信する
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-green-600 font-medium">
                            新しい確認リンクをメールアドレスに送信しました。
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="px-6 py-2 bg-pic-pink text-white rounded-lg hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pic-pink transition duration-150">
                保存
            </button>

            <x-action-message class="text-sm text-green-600" on="profile-updated">
                保存しました
            </x-action-message>
        </div>
    </form>
</section>
