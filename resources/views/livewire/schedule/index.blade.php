<?php

use function Livewire\Volt\{state, mount, layout, computed};
use App\Models\Place;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Collection;

// 利用者画面用のレイアウト('user.blade.php')を使う
layout('components.layouts.user');

// セッションから読み込んだ予定リストのID
state(['scheduledIds' => []]);
// 現在表示しているカードのインデックス（順番）
state(['currentIndex' => 0]);
// 予定リストに入っているカードの総数
state(['totalScheduled' => 0]);
// 予定リストのカード情報（並び替え済み）
state(['scheduledPlaces' => null]);

// ページ読み込み時に、セッションから予定リストを読み込み、カード情報を取得・並び替え
mount(function () {
    $this->scheduledIds = session('schedule_list', []);
    $this->totalScheduled = count($this->scheduledIds);
    if ($this->totalScheduled > 0) {
        $placesCollection = Place::whereIn('id', $this->scheduledIds)->get();
        // セッションのID順に並び替えて保存
        $this->scheduledPlaces = $placesCollection
            ->sortBy(function ($place) {
                return array_search($place->id, $this->scheduledIds);
            })
            ->values(); // キーを0から振り直す
    } else {
        $this->scheduledPlaces = collect(); // 空のコレクション
    }
});

// 「つぎへ」ボタン（または右スワイプ）の処理
$next = function () {
    if ($this->totalScheduled > 0) {
        $this->currentIndex = ($this->currentIndex + 1) % $this->totalScheduled;
    }
};

// 「最初から」ボタン（または左スワイプ）の処理
$goToFirst = function () {
    $this->currentIndex = 0; // インデックスを0に戻す
};

// 表示すべき現在のカード情報を計算して取得する
$currentScheduledPlace = computed(function () {
    // scheduledPlaces が Collection インスタンスであることを確認
    if ($this->scheduledPlaces instanceof Collection && $this->totalScheduled > 0 && isset($this->scheduledPlaces[$this->currentIndex])) {
        return $this->scheduledPlaces[$this->currentIndex];
    }
    return null; // 表示するカードがない場合
});

?>
<div class="p-5 md:p-8 flex flex-col items-center justify-between min-h-[90vh]">

    {{-- 1. ヘッダーエリア --}}
    <div class="w-full max-w-lg mb-4 flex justify-between items-center pb-4 border-b border-gray-200">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-700">きょうのよてい</h1>
        <a href="/places" wire:navigate
            class="inline-block px-2 py-1 bg-gray-400 text-white rounded text-xs hover:bg-gray-500 transition duration-150 opacity-75 hover:opacity-100">
            {{-- ★ クラスを変更 --}}
            メニュー
        </a>
    </div>

    {{-- 2. メインのカード表示エリア --}}
    <div id="swipe-area-schedule" class="w-full text-center flex-grow flex flex-col justify-center">
        @if ($this->currentScheduledPlace)
            {{-- タイトル --}}
            <h2 class="text-3xl md:text-4xl font-bold text-gray-700 mb-6">
                {{ $this->currentScheduledPlace->name }}
            </h2>
            {{-- 大きなカード --}}
            <div class="flex justify-center">
                <div class="card w-full max-w-lg">
                    <img src="{{ asset('storage/' . $this->currentScheduledPlace->image_path) }}"
                        alt="{{ $this->currentScheduledPlace->name }}" class="card-image h-80 md:h-96"
                        onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;"
                        loading="lazy">
                </div>
            </div>
        @else
            {{-- 予定が登録されていない場合 --}}
            <h2 class="text-3xl md:text-4xl font-bold text-gray-700 mb-6">
                よていはまだありません
            </h2>
            <p class="text-xl text-gray-500 mb-6">
                管理画面から「今日の予定」を追加してください。
            </p>
            <a href="/places" wire:navigate
                class="inline-block px-8 py-4 bg-blue-500 text-white rounded-2xl text-xl font-bold hover:bg-blue-600 transition shadow-lg">
                管理画面へ
            </a>
        @endif
    </div>

    {{-- 3. 操作ボタンのエリア (常時表示) --}}
    <div class="choice-buttons w-full max-w-lg flex justify-between mt-8">
        {{-- ★ 「最初から」ボタン --}}
        {{-- 現在が最初のカード($currentIndex === 0)なら色を薄く(bg-blue-100 opacity-75)、そうでなければ濃く(bg-blue-300) --}}
        <button wire:click="goToFirst" {{ $this->totalScheduled < 2 ? 'disabled' : '' }} aria-label="最初に戻る"
            class="inline-block px-8 py-5 text-gray-700 rounded-2xl text-2xl font-bold transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed min-h-[48px] min-w-[48px]
                    {{ $this->currentIndex === 0 ? 'bg-blue-100 opacity-75 cursor-default' : 'bg-blue-300 hover:bg-blue-400' }}">
            はじめ
        </button>

        {{-- ★ つぎへボタン --}}
        {{-- 現在が最後のカードなら色を薄く(bg-green-100 opacity-75)、そうでなければ濃く(bg-green-300) --}}
        <button wire:click="next"
            {{ $this->totalScheduled < 2 || $this->currentIndex === $this->totalScheduled - 1 ? 'disabled' : '' }}
            aria-label="次へ進む"
            class="inline-block px-10 py-5 text-gray-700 rounded-2xl text-4xl font-bold transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed min-h-[48px] min-w-[48px]
                    {{ $this->currentIndex === $this->totalScheduled - 1 ? 'bg-green-100 opacity-75 cursor-default' : 'bg-green-300 hover:bg-green-400' }}">
            👉️
        </button>
    </div>
</div>

{{-- スワイプ用JavaScript --}}
<script>
    document.addEventListener('livewire:navigated', () => {
        const swipeArea = document.getElementById('swipe-area-schedule');
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

            // 右->左 スワイプで「つぎへ」
            // ★ 最後のカードでない場合のみ実行する
            if (horizontalDist < -swipeThreshold && @this.currentIndex < @this.totalScheduled - 1) {
                @this.call('next');
            }
            // 左->右 スワイプで「最初から」
            if (horizontalDist > swipeThreshold) {
                @this.call('goToFirst');
            }
        }
    });
</script>
</div>
