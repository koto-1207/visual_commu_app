<?php

use function Livewire\Volt\{state, mount, layout, computed};
use App\Models\Place;
use Illuminate\Database\Eloquent\Collection;

layout('components.layouts.user');

// カード管理
state(['allPlaces' => null]);
state(['currentIndex' => 0]);
state(['totalPlaces' => 0]);

// 初期化処理
mount(function () {
    $this->allPlaces = Place::latest()->get();
    $this->totalPlaces = $this->allPlaces->count();
});

// 次のカードへ
$next = function () {
    if ($this->totalPlaces > 0) {
        $this->currentIndex = ($this->currentIndex + 1) % $this->totalPlaces;
    }
};

// 前のカードへ
$prev = function () {
    if ($this->totalPlaces > 0) {
        $newIndex = $this->currentIndex - 1;
        if ($newIndex < 0) {
            $newIndex = $this->totalPlaces - 1;
        }
        $this->currentIndex = $newIndex;
    }
};

// プレビュー用：前のカード
$prevPlace = computed(function () {
    if (!$this->allPlaces instanceof Collection || $this->totalPlaces < 2) {
        return null;
    }
    $prevIndex = $this->currentIndex - 1;
    if ($prevIndex < 0) {
        $prevIndex = $this->totalPlaces - 1;
    }
    return isset($this->allPlaces[$prevIndex]) ? $this->allPlaces[$prevIndex] : null;
});

// メイン表示：現在のカード
$currentPlace = computed(function () {
    if ($this->allPlaces instanceof Collection && $this->totalPlaces > 0 && isset($this->allPlaces[$this->currentIndex])) {
        return $this->allPlaces[$this->currentIndex];
    }
    return null;
});

// プレビュー用：次のカード
$nextPlace = computed(function () {
    if (!$this->allPlaces instanceof Collection || $this->totalPlaces < 2) {
        return null;
    }
    $nextIndex = ($this->currentIndex + 1) % $this->totalPlaces;
    return isset($this->allPlaces[$nextIndex]) ? $this->allPlaces[$nextIndex] : null;
});

?>
<div class="p-5 md:p-8 flex flex-col items-center justify-between min-h-[90vh]">

    {{-- プレビューエリア --}}
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
                    class="w-full h-full object-cover rounded-2xl border-4 preview-highlight shadow-lg"
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

    {{-- カード表示エリア --}}
    <div id="swipe-area" class="w-full text-center">
        @if ($this->currentPlace)
            <h1 class="text-3xl md:text-4xl font-bold text-gray-700 mb-6">
                {{ $this->currentPlace->name }}
            </h1>
            <div class="flex justify-center">
                <div id="card-main" role="button" tabindex="0" aria-label="{{ $this->currentPlace->name }}を選択"
                    aria-pressed="false" class="card w-full max-w-lg cursor-pointer relative"
                    onclick="toggleCardSelection(this)"
                    onkeypress="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); toggleCardSelection(this); }">
                    <img src="{{ asset('storage/' . $this->currentPlace->image_path) }}"
                        alt="{{ $this->currentPlace->name }}" class="card-image h-80 md:h-96"
                        onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;">
                    <div class="sparkle-effect" style="display: none;"></div>
                </div>
            </div>
        @else
            <h1 class="text-3xl md:text-4xl font-bold text-gray-700 mb-6">カードがありません</h1>
            <p class="text-xl text-gray-500 mb-6">まず管理画面からカードを登録してください。</p>
            <a href="/places" wire:navigate
                class="inline-block px-8 py-4 bg-blue-500 text-white rounded-2xl text-xl font-bold hover:bg-blue-600 transition shadow-lg">
                管理画面へ
            </a>
        @endif
    </div>

    {{-- 操作ボタン --}}
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

    {{-- メニューリンク --}}
    <div class="text-center mt-8">
        <a href="/places" wire:navigate
            class="inline-block px-2 py-1 bg-gray-400 text-white rounded text-xs hover:bg-gray-500 transition duration-150 opacity-75 hover:opacity-100">
            メニュー
        </a>
    </div>

    {{-- カード選択とスワイプ機能 --}}
    <script>
        function toggleCardSelection(cardElement) {
            const sparkleEffect = cardElement.querySelector('.sparkle-effect');
            const isSelected = cardElement.classList.contains('selected-card');

            if (isSelected) {
                cardElement.classList.remove('selected-card');
                cardElement.setAttribute('aria-pressed', 'false');
                if (sparkleEffect) sparkleEffect.style.display = 'none';
            } else {
                cardElement.classList.add('selected-card');
                cardElement.setAttribute('aria-pressed', 'true');
                if (sparkleEffect) sparkleEffect.style.display = 'block';
            }
        }

        function resetCardSelection() {
            const cardElement = document.getElementById('card-main');
            if (cardElement) {
                cardElement.classList.remove('selected-card');
                cardElement.setAttribute('aria-pressed', 'false');
                const sparkleEffect = cardElement.querySelector('.sparkle-effect');
                if (sparkleEffect) sparkleEffect.style.display = 'none';
            }
        }

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

                if (horizontalDist < -swipeThreshold) {
                    resetCardSelection();
                    @this.call('next');
                }
                if (horizontalDist > swipeThreshold) {
                    resetCardSelection();
                    @this.call('prev');
                }
            }
        });

        document.addEventListener('livewire:navigated', () => {
            const prevBtn = document.querySelector('button[wire\\:click="prev"]');
            const nextBtn = document.querySelector('button[wire\\:click="next"]');

            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    setTimeout(resetCardSelection, 100);
                });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    setTimeout(resetCardSelection, 100);
                });
            }
        });
    </script>
</div>
