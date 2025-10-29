<x-layouts.admin>
    <div class="min-h-screen bg-pic-bg py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            {{-- ヘッダー --}}
            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                    アカウント設定
                </h1>
                <p class="text-sm text-gray-600">
                    プロフィール情報とアカウント設定を管理できます
                </p>
            </div>

            {{-- プロフィール情報 --}}
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
                <livewire:profile.update-profile-information-form />
            </div>

            {{-- パスワード更新 --}}
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
                <livewire:profile.update-password-form />
            </div>

            {{-- アカウント削除 --}}
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
                <livewire:profile.delete-user-form />
            </div>

            {{-- 戻るボタン --}}
            <div class="mt-6 text-center">
                <a href="/places" wire:navigate
                    class="inline-flex items-center gap-2 px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    管理メニューに戻る
                </a>
            </div>
        </div>
    </div>
</x-layouts.admin>
