<?php

use function Livewire\Volt\{state, on, mount};
use App\Models\Place;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

// 編集中のカード情報
state(['editingPlaceId' => null]);
state(['editingPlaceName' => '']);
state(['editingPlaceImage' => '']);

// 予定リスト管理
state(['scheduledIds' => []]);
state(['scheduledPlaces' => null]);

// 初期化処理
mount(function () {
    $this->scheduledIds = session('schedule_list', []);
    $this->updateScheduledPlaces();
});

// 予定リストの並び替え処理
$updateSortOrder = function ($items) {
    $this->scheduledIds = array_column($items, 'value');
    session(['schedule_list' => $this->scheduledIds]);
    $this->updateScheduledPlaces();
};

// 予定リストの更新処理
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

// 予定リストの追加・削除
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

// 予定リストのクリア
$clearSchedule = function () {
    $this->scheduledIds = [];
    session(['schedule_list' => []]);
    $this->updateScheduledPlaces();
};

// カードの削除
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

// カードの編集開始
$edit = function (Place $place) {
    $this->editingPlaceId = $place->id;
    $this->editingPlaceName = $place->name;
    $this->editingPlaceImage = $place->image_path;
};

// カードの更新保存
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

// 編集のキャンセル
$cancelEdit = function () {
    $this->reset('editingPlaceId', 'editingPlaceName', 'editingPlaceImage');
};

// 全カード取得
$places = fn() => Place::latest()->get();

?>
<div class="p-6">
    {{-- 処理中オーバーレイ --}}
    <div wire:loading.delay wire:target="delete,clearSchedule,updateSortOrder"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <p class="text-xl font-bold text-gray-700">処理中...</p>
        </div>
    </div>

    {{-- ヘッダーメニュー --}}
    <div class="mb-5 p-4 bg-gray-100 rounded-lg flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-700">管理メニュー</h2>

        <div class="flex gap-2">
            <a href="/schedule" wire:navigate class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600"
                target="_blank">
                利用者画面 (スケジュール)
            </a>
            <a href="/choice" wire:navigate class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600"
                target="_blank">
                利用者画面 (選択)
            </a>
            <a href="/places/create" wire:navigate
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                新しい場所を登録
            </a>
            <button wire:click="clearSchedule" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                予定をクリア
            </button>
        </div>
    </div>

    {{-- 今日の予定リスト --}}
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">今日の予定（ドラッグで並び替え）</h2>

        <div wire:sortable="updateSortOrder"
            class="flex flex-wrap gap-4 p-4 bg-blue-50 border border-blue-200 rounded-lg min-h-[100px]">
            @forelse ($this->scheduledPlaces as $place)
                <div wire:key="schedule-{{ $place->id }}" wire:sortable.item="{{ $place->id }}"
                    class="border-2 border-green-500 p-3 rounded-lg text-center bg-white shadow-md relative">

                    <div wire:sortable.handle class="absolute top-1 left-1 cursor-move text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>

                    <p class="text-md font-bold text-gray-700">{{ $place->name }}</p>
                    <img src="{{ asset('storage/' . $place->image_path) }}" alt="{{ $place->name }}"
                        class="w-40 h-28 object-cover rounded my-2"
                        onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;"
                        loading="lazy">

                    <button wire:click="toggleSchedule({{ $place->id }})" wire:loading.attr="disabled"
                        wire:target="toggleSchedule({{ $place->id }})"
                        class="px-3 py-1 bg-yellow-400 text-black rounded text-xs disabled:opacity-50">
                        <span wire:loading.remove wire:target="toggleSchedule({{ $place->id }})">✅ 予定から外す</span>
                        <span wire:loading wire:target="toggleSchedule({{ $place->id }})">処理中...</span>
                    </button>
                </div>
            @empty
                <p class="text-gray-500">下の「全カード」から「✅ 予定に追加」ボタンを押して、今日の予定を追加してください。</p>
            @endforelse
        </div>
    </div>

    {{-- 全カード一覧 --}}
    <h2 class="text-lg font-semibold text-gray-800 mb-3">登録された場所（全カード）</h2>
    <div class="flex flex-wrap gap-4">
        @foreach ($this->places() as $place)
            @php
                $isOnSchedule = in_array($place->id, $this->scheduledIds);
            @endphp

            <div
                class="border-4 {{ $isOnSchedule ? 'border-green-500' : 'border-gray-300' }} p-3 rounded-lg text-center bg-white shadow">
                @if ($editingPlaceId === $place->id)
                    {{-- 編集モード --}}
                    @if ($editingPlaceImage)
                        <img src="{{ asset('storage/' . $editingPlaceImage) }}" alt="{{ $editingPlaceName }}"
                            class="w-52 h-36 object-cover rounded my-2"
                            onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;"
                            loading="lazy">
                    @endif
                    <input type="text" wire:model="editingPlaceName"
                        class="w-full p-2 border border-gray-300 rounded mb-2">
                    @error('editingPlaceName')
                        <span class="text-red-500 text-sm block mb-2">{{ $message }}</span>
                    @enderror
                    <button wire:click="update" wire:loading.attr="disabled" wire:target="update"
                        class="px-3 py-1 bg-blue-500 text-white rounded disabled:opacity-50">
                        <span wire:loading.remove wire:target="update">保存</span>
                        <span wire:loading wire:target="update">保存中...</span>
                    </button>
                    <button wire:click="cancelEdit" class="px-3 py-1 bg-gray-500 text-white rounded">キャンセル</button>
                @else
                    {{-- 通常モード --}}
                    <p class="text-lg font-bold text-gray-700">{{ $place->name }}</p>
                    <img src="{{ asset('storage/' . $place->image_path) }}" alt="{{ $place->name }}"
                        class="w-52 h-36 object-cover rounded my-2"
                        onerror="this.src='{{ asset('storage/places/placeholder.png') }}'; this.onerror=null;"
                        loading="lazy">
                    <div class="flex flex-col gap-1">
                        <button wire:click="toggleSchedule({{ $place->id }})" wire:loading.attr="disabled"
                            wire:target="toggleSchedule({{ $place->id }})"
                            class="px-3 py-1 {{ $isOnSchedule ? 'bg-yellow-400 text-black' : 'bg-green-500 text-white' }} rounded text-sm disabled:opacity-50">
                            <span wire:loading.remove
                                wire:target="toggleSchedule({{ $place->id }})">{{ $isOnSchedule ? '✅ 予定から外す' : '✅ 予定に追加' }}</span>
                            <span wire:loading wire:target="toggleSchedule({{ $place->id }})">処理中...</span>
                        </button>
                        <button wire:click="edit({{ $place->id }})"
                            class="px-3 py-1 bg-gray-300 text-black rounded text-sm">編集</button>
                        <button wire:click="delete({{ $place->id }})" wire:confirm="本当に削除しますか？"
                            wire:loading.attr="disabled" wire:target="delete({{ $place->id }})"
                            class="px-3 py-1 bg-red-500 text-white rounded text-sm disabled:opacity-50">
                            <span wire:loading.remove wire:target="delete({{ $place->id }})">削除</span>
                            <span wire:loading wire:target="delete({{ $place->id }})">削除中...</span>
                        </button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
