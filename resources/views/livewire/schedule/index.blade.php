<?php

use function Livewire\Volt\{state, mount, layout, computed};
use App\Models\Place;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Collection;

layout('components.layouts.user');

state(['scheduledIds' => []]);
state(['currentIndex' => 0]);
state(['totalScheduled' => 0]);
state(['scheduledPlaces' => null]);
mount(function () {
    $this->scheduledIds = session('schedule_list', []);
    $this->totalScheduled = count($this->scheduledIds);
    if ($this->totalScheduled > 0) {
        $placesCollection = Place::where('user_id', auth()->id())
            ->whereIn('id', $this->scheduledIds)
            ->get();
        $this->scheduledPlaces = $placesCollection
            ->sortBy(function ($place) {
                return array_search($place->id, $this->scheduledIds);
            })
            ->values();
    } else {
        $this->scheduledPlaces = collect();
    }
});

$next = function () {
    if ($this->totalScheduled > 0) {
        $this->currentIndex = ($this->currentIndex + 1) % $this->totalScheduled;
    }
};

$goToFirst = function () {
    $this->currentIndex = 0;
};

$currentScheduledPlace = computed(function () {
    if ($this->scheduledPlaces instanceof Collection && $this->totalScheduled > 0 && isset($this->scheduledPlaces[$this->currentIndex])) {
        return $this->scheduledPlaces[$this->currentIndex];
    }
    return null;
});

?>
<div class="p-5 md:p-8 flex flex-col items-center justify-between min-h-[90vh]">

    <div class="w-full max-w-lg mb-4 flex justify-between items-center pb-4 border-b border-gray-200">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-700">きょうのよてい</h1>
        <a href="/places" wire:navigate
            class="inline-block px-2 py-1 bg-gray-400 text-white rounded text-xs hover:bg-gray-500 transition duration-150 opacity-75 hover:opacity-100">
            メニュー
        </a>
    </div>

    <div id="swipe-area" class="w-full text-center">
        @if ($this->currentScheduledPlace)
            <h2 class="text-3xl md:text-4xl font-bold text-gray-700 mb-6">
                {{ $this->currentScheduledPlace->name }}
            </h2>
            <div class="flex justify-center">
                <div class="card w-full max-w-lg">
                    <img src="{{ asset('storage/' . $this->currentScheduledPlace->image_path) }}"
                        alt="{{ $this->currentScheduledPlace->name }}" class="card-image h-80 md:h-96"
                        onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;"
                        loading="lazy">
                </div>
            </div>
        @else
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

    <div class="choice-buttons w-full max-w-lg flex justify-between mt-8">
        <button wire:click="goToFirst" {{ $this->totalScheduled < 2 ? 'disabled' : '' }} aria-label="最初に戻る"
            class="inline-block px-8 py-5 text-gray-700 rounded-2xl text-2xl font-bold transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed min-h-[48px] min-w-[48px]
                    {{ $this->currentIndex === 0 ? 'bg-blue-100 opacity-75 cursor-default' : 'bg-blue-300 hover:bg-blue-400' }}">
            はじめ
        </button>

        <button wire:click="next"
            {{ $this->totalScheduled < 2 || $this->currentIndex === $this->totalScheduled - 1 ? 'disabled' : '' }}
            aria-label="次へ進む"
            class="inline-block px-10 py-5 text-gray-700 rounded-2xl text-4xl font-bold transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed min-h-[48px] min-w-[48px]
                    {{ $this->currentIndex === $this->totalScheduled - 1 ? 'bg-green-100 opacity-75 cursor-default' : 'bg-green-300 hover:bg-green-400' }}">
            👉️
        </button>
    </div>
</div>

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

            if (horizontalDist < -swipeThreshold && @this.currentIndex < @this.totalScheduled - 1) {
                @this.call('next');
            }
            if (horizontalDist > swipeThreshold) {
                @this.call('goToFirst');
            }
        }
    });
</script>
</div>
