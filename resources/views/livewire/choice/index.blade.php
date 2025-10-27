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

// 「選ばれた」カードのIDを保存する変数
state(['selectedPlaceId' => null]);

// ページが読み込まれた時に、全カード情報を取得
mount(function () {
    $this->allPlaces = Place::latest()->get(); // ★ ここで全カードを取得
    $this->totalPlaces = $this->allPlaces->count();
});

// 「つぎへ」ボタン（または右スワイプ）の処理
$next = function () {
    if ($this->totalPlaces > 0) {
        $this->currentIndex = ($this->currentIndex + 1) % $this->totalPlaces;
        $this->selectedPlaceId = null; // 次/前に移動したら選択状態をリセット
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
        $this->selectedPlaceId = null; // 次/前に移動したら選択状態をリセット
    }
};

// カードがクリックされた時の処理
$selectCurrentPlace = function () {
    if ($this->currentPlace) {
        $this->selectedPlaceId = $this->currentPlace->id;
    }
};

// 表示すべき「前の」カードを計算
$prevPlace = computed(function () {
    // allPlaces が Collection インスタンスであることを確認
    if (!$this->allPlaces instanceof Collection || $this->totalPlaces < 2) {
        return null;
    }
    $prevIndex = $this->currentIndex - 1;
    if ($prevIndex < 0) {
        $prevIndex = $this->totalPlaces - 1; // 0 -> 最後尾へ
    }
    // 配列のキーが存在するか確認
    return isset($this->allPlaces[$prevIndex]) ? $this->allPlaces[$prevIndex] : null;
});

// 表示すべき「現在の」カードを計算
$currentPlace = computed(function () {
    // allPlaces が Collection インスタンスであることを確認
    if ($this->allPlaces instanceof Collection && $this->totalPlaces > 0 && isset($this->allPlaces[$this->currentIndex])) {
        return $this->allPlaces[$this->currentIndex];
    }
    return null; // 表示するカードがない場合
});

// 表示すべき「次の」カードを計算
$nextPlace = computed(function () {
    // allPlaces が Collection インスタンスであることを確認
    if (!$this->allPlaces instanceof Collection || $this->totalPlaces < 2) {
        return null;
    }
    $nextIndex = ($this->currentIndex + 1) % $this->totalPlaces; // 最後尾 -> 0へ
    // 配列のキーが存在するか確認
    return isset($this->allPlaces[$nextIndex]) ? $this->allPlaces[$nextIndex] : null;
});

?>
<div class="p-5 md:p-8 flex flex-col items-center justify-between min-h-[90vh]">

    {{-- 1. プレビューエリア --}}
    <div class="w-full max-w-lg flex justify-center items-center gap-4 mb-4">
        <div class="w-24 h-24">
            @if ($this->prevPlace)
                <img src="{{ asset('storage/' . $this->prevPlace->image_path) }}"
                    class="w-full h-full object-cover rounded-lg opacity-50"
                    onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;">
            @endif
        </div>
        <div class="w-32 h-32">
            @if ($this->currentPlace)
                <img src="{{ asset('storage/' . $this->currentPlace->image_path) }}"
                    class="w-full h-full object-cover rounded-2xl border-4 border-blue-400 shadow-lg"
                    onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;">
            @endif
        </div>
        <div class="w-24 h-24">
            @if ($this->nextPlace)
                <img src="{{ asset('storage/' . $this->nextPlace->image_path) }}"
                    class="w-full h-full object-cover rounded-lg opacity-50"
                    onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;">
            @endif
        </div>
    </div>

    {{-- 2. メインのカード表示エリア --}}
    <div id="swipe-area" class="w-full text-center">
        @if ($this->currentPlace)
            <h1 class="text-3xl md:text-4xl font-bold text-gray-700 mb-6">
                {{ $this->currentPlace->name }}
            </h1>
            {{-- 大きなカード (wire:click を追加) --}}
            <div class="flex justify-center">
                {{-- selectedPlaceId と currentPlace->id が一致したら 'selected-card' クラスを追加 --}}
                <div wire:click="selectCurrentPlace" role="button" tabindex="0"
                    aria-label="{{ $this->currentPlace->name }}を選択"
                    aria-pressed="{{ $selectedPlaceId === $this->currentPlace->id ? 'true' : 'false' }}"
                    class="card w-full max-w-lg cursor-pointer relative {{ $selectedPlaceId === $this->currentPlace->id ? 'selected-card' : '' }}"
                    onkeypress="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.click(); }">

                    <img src="{{ asset('storage/' . $this->currentPlace->image_path) }}"
                        alt="{{ $this->currentPlace->name }}" class="card-image h-80 md:h-96"
                        onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;">

                    {{-- ★ 「選ばれた」時にキラキラエフェクトを表示する要素 --}}
                    @if ($selectedPlaceId === $this->currentPlace->id)
                        <div class="sparkle-effect"></div> {{-- ← チェックマークから変更 --}}
                    @endif
                </div>
            </div>
        @else
            {{-- カードがない場合の表示 --}}
            <h1 class="text-3xl md:text-4xl font-bold text-gray-700 mb-6">カードがありません</h1>
            <p class="text-xl text-gray-500 mb-6">まず管理画面からカードを登録してください。</p>
            <a href="/places" wire:navigate
                class="inline-block px-8 py-4 bg-blue-500 text-white rounded-2xl text-xl font-bold hover:bg-blue-600 transition shadow-lg">
                管理画面へ
            </a>
        @endif
    </div>

    {{-- 3. 操作ボタンのエリア (常時表示) --}}
    <div class="choice-buttons w-full max-w-lg flex justify-between mt-8">
        <button wire:click="prev" {{ $this->totalPlaces < 2 ? 'disabled' : '' }} aria-label="前のカード"
            class="inline-block px-10 py-5 bg-blue-200 text-gray-700 rounded-2xl text-4xl font-bold hover:bg-blue-300 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed min-h-[48px] min-w-[48px]">
            &lt;
        </button>
        <button wire:click="next" {{ $this->totalPlaces < 2 ? 'disabled' : '' }} aria-label="次のカード"
            class="inline-block px-10 py-5 bg-green-200 text-gray-700 rounded-2xl text-4xl font-bold hover:bg-green-300 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed min-h-[48px] min-w-[48px]">
            &gt;
        </button>
    </div>

    {{-- 管理画面に戻るリンク --}}
    <div class="text-center mt-8">
        <a href="/places" wire:navigate
            class="inline-block px-2 py-1 bg-gray-400 text-white rounded text-xs hover:bg-gray-500 transition duration-150 opacity-75 hover:opacity-100">
            メニュー
        </a>
    </div>

    {{-- スワイプ用JavaScript --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            const swipeArea = document.getElementById('swipe-area');
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
