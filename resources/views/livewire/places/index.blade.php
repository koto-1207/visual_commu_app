<?php

use function Livewire\Volt\{state, on, mount};
use App\Models\Place;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

state(['editingPlaceId' => null]);
state(['editingPlaceName' => '']);
state(['editingPlaceImage' => '']);
state(['scheduledIds' => []]);
state(['scheduledPlaces' => null]);
mount(function () {
    $this->scheduledIds = session('schedule_list', []);
    $this->updateScheduledPlaces();
});

$updateSortOrder = function ($items) {
    $this->scheduledIds = array_column($items, 'value');
    session(['schedule_list' => $this->scheduledIds]);
    $this->updateScheduledPlaces();
};

$updateScheduledPlaces = function () {
    if (empty($this->scheduledIds)) {
        $this->scheduledPlaces = collect();
        return;
    }
    $placesCollection = Place::whereIn('id', $this->scheduledIds)->get();
    $this->scheduledPlaces = $placesCollection
        ->sortBy(function ($place) {
            return array_search($place->id, $this->scheduledIds);
        })
        ->values();
};

$toggleSchedule = function ($id) {
    $index = array_search($id, $this->scheduledIds);
    if ($index !== false) {
        unset($this->scheduledIds[$index]);
    } else {
        $this->scheduledIds[] = $id;
    }
    $this->scheduledIds = array_values($this->scheduledIds);
    session(['schedule_list' => $this->scheduledIds]);
    $this->updateScheduledPlaces();
};

$clearSchedule = function () {
    $this->scheduledIds = [];
    session(['schedule_list' => []]);
    $this->updateScheduledPlaces();
};

$delete = function (Place $place) {
    if ($place->image_path && Storage::disk('public')->exists($place->image_path)) {
        Storage::disk('public')->delete($place->image_path);
    }
    $place->delete();

    $index = array_search($place->id, $this->scheduledIds);
    if ($index !== false) {
        unset($this->scheduledIds[$index]);
        $this->scheduledIds = array_values($this->scheduledIds);
        session(['schedule_list' => $this->scheduledIds]);
        $this->updateScheduledPlaces();
    }
    $this->dispatch('$refresh');
};

$edit = function (Place $place) {
    $this->editingPlaceId = $place->id;
    $this->editingPlaceName = $place->name;
    $this->editingPlaceImage = $place->image_path;
};

$update = function () {
    $validatedData = $this->validate([
        'editingPlaceName' => 'required|string|max:255',
    ]);
    $place = Place::find($this->editingPlaceId);
    $place->update(['name' => $validatedData['editingPlaceName']]);
    $this->cancelEdit();
    $this->updateScheduledPlaces();
    $this->dispatch('$refresh');
};

$cancelEdit = function () {
    $this->reset('editingPlaceId', 'editingPlaceName', 'editingPlaceImage');
};

$places = fn() => Place::where('user_id', auth()->id())
    ->latest()
    ->get();

$logout = function (Logout $logout) {
    $logout();
    $this->redirect('/', navigate: true);
};

?>
<div class="p-6">
    <div wire:loading.delay wire:target="delete,clearSchedule,updateSortOrder"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <p class="text-xl font-bold text-gray-700">処理中...</p>
        </div>
    </div>

    <div class="mb-5 p-3 md:p-4 bg-gradient-to-r from-pic-bg to-white rounded-xl shadow-sm border border-pic-mint/20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-3">
            <h2 class="text-lg md:text-xl font-bold text-gray-700">管理メニュー</h2>

            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('profile') }}" wire:navigate
                    class="flex-1 sm:flex-none px-3 md:px-4 py-2 bg-pic-pink text-white rounded-lg hover:bg-opacity-80 flex items-center justify-center gap-2 text-sm md:text-base shadow-sm transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="hidden sm:inline">プロフィール</span>
                    <span class="sm:hidden">アカウント</span>
                </a>
                <button wire:click="logout"
                    class="flex-1 sm:flex-none px-3 md:px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center justify-center gap-2 text-sm md:text-base shadow-sm transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                            clip-rule="evenodd" />
                    </svg>
                    ログアウト
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            <a href="/schedule" wire:navigate
                class="px-4 py-2 bg-pic-mint text-white rounded-lg hover:bg-opacity-80 text-center text-base md:text-lg shadow-sm transition duration-150 font-semibold"
                target="_blank">
                📅 スケジュール画面
            </a>
            <a href="/choice" wire:navigate
                class="px-4 py-2 bg-pic-pink text-white rounded-lg hover:bg-opacity-80 text-center text-base md:text-lg shadow-sm transition duration-150 font-semibold"
                target="_blank">
                👆️ 選択画面
            </a>
            <a href="/places/create" wire:navigate
                class="px-4 py-2 bg-pic-mint text-white rounded-lg hover:bg-opacity-80 text-center text-base md:text-lg shadow-sm transition duration-150 font-semibold">
                ➕ 新しい場所を登録
            </a>
        </div>
    </div>

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-3">
            <h2 class="text-base md:text-lg font-semibold text-gray-800">今日の予定</h2>
            <button wire:click="clearSchedule"
                class="w-full sm:w-auto px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm md:text-base flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                予定をクリア
            </button>
        </div>

        <div wire:sortable="updateSortOrder"
            class="flex flex-wrap gap-3 md:gap-4 p-3 md:p-4 bg-blue-50 border border-blue-200 rounded-lg min-h-[100px]">
            @forelse ($this->scheduledPlaces as $place)
                <div wire:key="schedule-{{ $place->id }}" wire:sortable.item="{{ $place->id }}"
                    class="border-2 border-green-500 p-2 md:p-3 rounded-lg text-center bg-white shadow-md relative w-32 sm:w-36 md:w-40">

                    <div wire:sortable.handle class="absolute top-1 left-1 cursor-move text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>

                    <p class="text-sm md:text-md font-bold text-gray-700 mb-1">{{ $place->name }}</p>
                    <img src="{{ asset('storage/' . $place->image_path) }}" alt="{{ $place->name }}"
                        class="w-full h-20 sm:h-24 md:h-28 object-cover rounded my-2"
                        onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;"
                        loading="lazy">

                    <button wire:click="toggleSchedule({{ $place->id }})" wire:loading.attr="disabled"
                        wire:target="toggleSchedule({{ $place->id }})"
                        class="w-full px-2 py-1 bg-yellow-400 text-black rounded text-xs disabled:opacity-50">
                        <span wire:loading.remove wire:target="toggleSchedule({{ $place->id }})">✅ 予定から外す</span>
                        <span wire:loading wire:target="toggleSchedule({{ $place->id }})">処理中...</span>
                    </button>
                </div>
            @empty
                <p class="text-base md:text-lg text-gray-500">下の「全カード」から「✅ 予定に追加」ボタンを押して、今日の予定を追加してください。</p>
            @endforelse
        </div>
    </div>

    <h2 class="text-base md:text-lg font-semibold text-gray-800 mb-3">登録された場所（全カード）</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4">
        @foreach ($this->places() as $place)
            @php
                $isOnSchedule = in_array($place->id, $this->scheduledIds);
            @endphp

            <div
                class="border-4 {{ $isOnSchedule ? 'border-green-500' : 'border-gray-300' }} p-2 md:p-3 rounded-lg text-center bg-white shadow">
                @if ($editingPlaceId === $place->id)
                    @if ($editingPlaceImage)
                        <img src="{{ asset('storage/' . $editingPlaceImage) }}" alt="{{ $editingPlaceName }}"
                            class="w-full aspect-[4/3] object-cover rounded my-2"
                            onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;"
                            loading="lazy">
                    @endif
                    <input type="text" wire:model="editingPlaceName"
                        class="w-full p-2 border border-gray-300 rounded mb-2 text-sm">
                    @error('editingPlaceName')
                        <span class="text-red-500 text-sm block mb-2">{{ $message }}</span>
                    @enderror
                    <button wire:click="update" wire:loading.attr="disabled" wire:target="update"
                        class="w-full px-2 py-1 bg-blue-500 text-white rounded text-xs mb-1 disabled:opacity-50">
                        <span wire:loading.remove wire:target="update">保存</span>
                        <span wire:loading wire:target="update">保存中...</span>
                    </button>
                    <button wire:click="cancelEdit"
                        class="w-full px-2 py-1 bg-gray-500 text-white rounded text-xs">キャンセル</button>
                @else
                    <p class="text-sm md:text-base font-bold text-gray-700 mb-2 truncate">{{ $place->name }}</p>
                    <img src="{{ asset('storage/' . $place->image_path) }}" alt="{{ $place->name }}"
                        class="w-full aspect-[4/3] object-cover rounded mb-2"
                        onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;"
                        loading="lazy">
                    <div class="flex flex-col gap-1">
                        <button wire:click="toggleSchedule({{ $place->id }})" wire:loading.attr="disabled"
                            wire:target="toggleSchedule({{ $place->id }})"
                            class="w-full px-2 py-1 {{ $isOnSchedule ? 'bg-yellow-400 text-black' : 'bg-green-500 text-white' }} rounded text-xs disabled:opacity-50">
                            <span wire:loading.remove
                                wire:target="toggleSchedule({{ $place->id }})">{{ $isOnSchedule ? '✅ 外す' : '✅ 追加' }}</span>
                            <span wire:loading wire:target="toggleSchedule({{ $place->id }})">...</span>
                        </button>
                        <button wire:click="edit({{ $place->id }})"
                            class="w-full px-2 py-1 bg-gray-300 text-black rounded text-xs">編集</button>
                        <button wire:click="delete({{ $place->id }})" wire:confirm="本当に削除しますか？"
                            wire:loading.attr="disabled" wire:target="delete({{ $place->id }})"
                            class="w-full px-2 py-1 bg-red-500 text-white rounded text-xs disabled:opacity-50">
                            <span wire:loading.remove wire:target="delete({{ $place->id }})">削除</span>
                            <span wire:loading wire:target="delete({{ $place->id }})">...</span>
                        </button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
