<?php

use function Livewire\Volt\{state, mount, layout, computed};
use App\Models\Place;
use Illuminate\Database\Eloquent\Collection;

// 利用者画面用のレイアウト('user.blade.php')を使う
layout('components.layouts.user');

// データベースから読み込んだ全てのカード
state(['allPlaces' => null]);
// 現在表示しているカードのインデックス（順番）
state(['currentIndex' => 0]);
// カードの総数
state(['totalPlaces' => 0]);

// ページが読み込まれた時に、全カード情報を取得
mount(function () {
    $this->allPlaces = Place::latest()->get();
    $this->totalPlaces = $this->allPlaces->count();
});

// 「つぎへ」ボタン（または右スワイプ）の処理
$next = function () {
    if ($this->totalPlaces > 0) {
        $this->currentIndex = ($this->currentIndex + 1) % $this->totalPlaces;
    }
};

// 「もどる」ボタン（または左スワイプ）の処理
$prev = function () {
    if ($this->totalPlaces > 0) {
        $newIndex = $this->currentIndex - 1;
        if ($newIndex < 0) {
            $newIndex = $this->totalPlaces - 1; // 最後尾に移動
        }
        $this->currentIndex = $newIndex;
    }
};

// 表示すべき「前の」カードを計算
$prevPlace = computed(function () {
    if ($this->totalPlaces < 2) {
        return null;
    } // 2枚未満なら不要
    $prevIndex = $this->currentIndex - 1;
    if ($prevIndex < 0) {
        $prevIndex = $this->totalPlaces - 1; // 0 -> 最後尾へ
    }
    return $this->allPlaces[$prevIndex];
});

// 表示すべき「現在の」カードを計算
$currentPlace = computed(function () {
    if ($this->totalPlaces > 0) {
        return $this->allPlaces[$this->currentIndex];
    }
    return null; // 表示するカードがない場合
});

// 表示すべき「次の」カードを計算
$nextPlace = computed(function () {
    if ($this->totalPlaces < 2) {
        return null;
    } // 2枚未満なら不要
    $nextIndex = ($this->currentIndex + 1) % $this->totalPlaces; // 最後尾 -> 0へ
    return $this->allPlaces[$nextIndex];
});

?>
<div class="p-5 md:p-8 flex flex-col items-center justify-between min-h-[90vh]">

    {{-- 1. プレビューエリア --}}
    <div class="w-full max-w-lg flex justify-center items-center gap-4 mb-4">
        <div class="w-24 h-24">
            @if ($this->prevPlace)
                <img src="{{ asset('storage/' . $this->prevPlace->image_path) }}"
                    class="w-full h-full object-cover rounded-lg opacity-50">
            @endif
        </div>
        <div class="w-32 h-32">
            @if ($this->currentPlace)
                <img src="{{ asset('storage/' . $this->currentPlace->image_path) }}"
                    class="w-full h-full object-cover rounded-2xl border-4 border-blue-400 shadow-lg">
            @endif
        </div>
        <div class="w-24 h-24">
            @if ($this->nextPlace)
                <img src="{{ asset('storage/' . $this->nextPlace->image_path) }}"
                    class="w-full h-full object-cover rounded-lg opacity-50">
            @endif
        </div>
    </div>

    {{-- 2. メインのカード表示エリア --}}
    <div id="swipe-area" class="w-full text-center">
        @if ($this->currentPlace)
            <h1 class="text-3xl md:text-4xl font-bold text-gray-700 mb-6">
                {{ $this->currentPlace->name }}
            </h1>
            <div class="flex justify-center">
                <div class="card w-full max-w-lg">
                    <img src="{{ asset('storage/' . $this->currentPlace->image_path) }}"
                        alt="{{ $this->currentPlace->name }}" class="card-image h-80 md:h-96">
                </div>
            </div>
        @else
            <h1 class="text-3xl md:text-4xl font-bold text-gray-700 mb-6">カードがありません</h1>
            <p class="text-xl text-gray-500">まず管理画面からカードを登録してください。</p>
        @endif
    </div>

    {{-- 3. 操作ボタンのエリア (常時表示) --}}
    <div class="choice-buttons w-full max-w-lg flex justify-between mt-8">
        <button wire:click="prev" {{ $this->totalPlaces < 2 ? 'disabled' : '' }}
            class="inline-block px-10 py-5 bg-blue-200 text-gray-700 rounded-2xl text-4xl font-bold hover:bg-blue-300 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
            &lt;
        </button>
        <button wire:click="next" {{ $this->totalPlaces < 2 ? 'disabled' : '' }}
            class="inline-block px-10 py-5 bg-green-200 text-gray-700 rounded-2xl text-4xl font-bold hover:bg-green-300 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
            &gt;
        </button>
    </div>

    {{-- 管理画面に戻るリンク --}}
    <div class="text-center mt-8">
        <div class="text-center mt-8">
            <a href="/places" wire:navigate
                class="inline-block px-2 py-1 bg-gray-400 text-white rounded text-xs hover:bg-gray-500 transition duration-150 opacity-75 hover:opacity-100">
                {{-- ★ クラスを変更 --}}
                メニュー
            </a>
        </div>
    </div>

    {{-- スワイプ用JavaScript --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            const swipeArea = document.getElementById('swipe-area'); // ★ IDが swipe-area であることを確認
            if (!swipeArea) return;
            let touchStartX = 0,
                touchEndX = 0,
                touchStartY = 0,
                touchEndY = 0;
            const swipeThreshold = 50;
            swipeArea.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
                touchStartY = e.changedTouches[0].screenY;
            }, {
                passive: true
            });
            swipeArea.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                touchEndY = e.changedTouches[0].screenY;
                handleSwipe();
            }, {
                passive: true
            });

            function handleSwipe() {
                const horizontalDist = touchEndX - touchStartX;
                const verticalDist = touchEndY - touchStartY;
                if (Math.abs(verticalDist) > Math.abs(horizontalDist)) return;
                if (horizontalDist < -swipeThreshold) @this.call('next');
                if (horizontalDist > swipeThreshold) @this.call('prev');
            }
        });
    </script>
</div>
