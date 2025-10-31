<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\layout;

layout('components.layouts.app');

$sendVerification = function () {
    if (Auth::user()->hasVerifiedEmail()) {
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        return;
    }

    Auth::user()->sendEmailVerificationNotification();

    Session::flash('status', 'verification-link-sent');
};

$logout = function (Logout $logout) {
    $logout();
    $this->redirect('/', navigate: true);
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
            メール認証
        </h2>

        {{-- 説明文 --}}
        <div class="text-sm text-gray-600">
            ご登録ありがとうございます！続行する前に、メールアドレスを確認していただけますか？登録時に入力されたメールアドレスに確認リンクを送信しました。メールが届いていない場合は、再送信いたします。
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-md">
                新しい確認リンクをメールアドレスに送信しました。
            </div>
        @endif

        <div class="space-y-4">
            <button wire:click="sendVerification"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-pic-pink hover:bg-opacity-80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pic-pink transition duration-150">
                確認メールを再送信
            </button>

            <button wire:click="logout"
                class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pic-mint transition duration-150">
                ログアウト
            </button>
        </div>

    </div>
</div>
