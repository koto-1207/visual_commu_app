<?php

use function Livewire\Volt\{state, on, mount};
use App\Models\Place;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Collection;

// 編集中のカードIDと新しい名前を保存する変数を準備
state(['editingPlaceId' => null]);
state(['editingPlaceName' => '']);

// 今日の予定リスト用の変数
state(['scheduledIds' => []]);

// 並び替え用の、予定リストに入ったカードコレクション
state(['scheduledPlaces' => null]);

// ページ読み込み時に、セッションから予定リストを読み込む
mount(function () {
    $this->scheduledIds = session('schedule_list', []);
    $this->updateScheduledPlaces(); // 関数呼び出しに変更
});

// 並び替えが完了したときに呼ばれる関数
$updateSortOrder = function ($items) {
    // $items には並び替え後のIDの順番がキー/バリューで入ってくる
    // IDだけを順番通りに抽出
    $this->scheduledIds = array_column($items, 'value');
    // セッションに保存
    session(['schedule_list' => $this->scheduledIds]);
    // 画面上のリストも更新
    $this->updateScheduledPlaces();
};

// 予定リストのカード情報を更新する補助関数
$updateScheduledPlaces = function () {
    if (empty($this->scheduledIds)) {
        $this->scheduledPlaces = collect(); // 空のコレクション
        return;
    }
    // DBからIDリストにあるカードを取得
    $placesCollection = Place::whereIn('id', $this->scheduledIds)->get();
    // セッションのID順に並び替える
    $this->scheduledPlaces = $placesCollection
        ->sortBy(function ($place) {
            return array_search($place->id, $this->scheduledIds);
        })
        ->values(); // キーを0から振り直す
};

// 予定リストに追加/削除する処理
$toggleSchedule = function ($id) {
    $index = array_search($id, $this->scheduledIds);

    if ($index !== false) {
        unset($this->scheduledIds[$index]);
    } else {
        $this->scheduledIds[] = $id;
    }

    $this->scheduledIds = array_values($this->scheduledIds);
    session(['schedule_list' => $this->scheduledIds]);

    // 画面上のリストも更新
    $this->updateScheduledPlaces();
};

// 予定リストを空にする処理
$clearSchedule = function () {
    $this->scheduledIds = [];
    session(['schedule_list' => []]);

    // 画面上のリストも更新
    $this->updateScheduledPlaces();
};

// 削除ボタンが押された時の処理
$delete = function (Place $place) {
    $place->delete();
    // セッションからも削除（もしあれば）
    $index = array_search($place->id, $this->scheduledIds);
    if ($index !== false) {
        unset($this->scheduledIds[$index]);
        $this->scheduledIds = array_values($this->scheduledIds);
        session(['schedule_list' => $this->scheduledIds]);
        $this->updateScheduledPlaces(); // リスト更新
    }
    $this->dispatch('$refresh'); // 全カードリスト更新
};

// 編集ボタンが押された時の処理
$edit = function (Place $place) {
    $this->editingPlaceId = $place->id;
    $this->editingPlaceName = $place->name;
};

// 編集中の保存ボタンが押された時の処理
$update = function () {
    // バリデーションルールを、この場所で直接定義
    $validatedData = $this->validate([
        'editingPlaceName' => 'required|string|max:255',
    ]);

    $place = Place::find($this->editingPlaceId);
    $place->update([
        'name' => $validatedData['editingPlaceName'],
    ]);

    $this->cancelEdit();
    $this->updateScheduledPlaces(); // 予定リストも更新される可能性があるため
    $this->dispatch('$refresh'); // 全カードリスト更新
};

// 編集中のキャンセルボタンが押された時の処理
$cancelEdit = function () {
    $this->reset('editingPlaceId', 'editingPlaceName');
};

// データベースから「すべての」場所のリストを取得する
$places = fn() => Place::latest()->get();

?>
<div class="p-6">
    {{-- 管理者用ナビゲーション --}}
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

    {{-- 今日の予定リスト (並び替え可能エリア) --}}
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
                        class="w-40 h-28 object-cover rounded my-2">

                    <button wire:click="toggleSchedule({{ $place->id }})"
                        class="px-3 py-1 bg-yellow-400 text-black rounded text-xs">
                        ✅ 予定から外す
                    </button>
                </div>
            @empty
                <p class="text-gray-500">下の「全カード」から「✅ 予定に追加」ボタンを押して、今日の予定を追加してください。</p>
            @endforelse
        </div>
    </div>

    {{-- カード一覧表示 --}}
    <h2 class="text-lg font-semibold text-gray-800 mb-3">登録された場所（全カード）</h2>
    <div class="flex flex-wrap gap-4">
        @foreach ($this->places() as $place)
            @php
                $isOnSchedule = in_array($place->id, $this->scheduledIds);
            @endphp

            <div
                class="border-4 {{ $isOnSchedule ? 'border-green-500' : 'border-gray-300' }} p-3 rounded-lg text-center bg-white shadow">
                @if ($editingPlaceId === $place->id)
                    {{-- 編集中の表示 --}}
                    <input type="text" wire:model="editingPlaceName"
                        class="w-full p-2 border border-gray-300 rounded mb-2">
                    {{-- エラー表示は name 属性ではなく $editingPlaceName を参照 --}}
                    @error('editingPlaceName')
                        <span class="text-red-500 text-sm block mb-2">{{ $message }}</span>
                    @enderror
                    <button wire:click="update" class="px-3 py-1 bg-blue-500 text-white rounded">保存</button>
                    <button wire:click="cancelEdit" class="px-3 py-1 bg-gray-500 text-white rounded">キャンセル</button>
                @else
                    {{-- 通常の表示 --}}
                    <p class="text-lg font-bold text-gray-700">{{ $place->name }}</p>
                    <img src="{{ asset('storage/' . $place->image_path) }}" alt="{{ $place->name }}"
                        class="w-52 h-36 object-cover rounded my-2">
                    <div class="flex flex-col gap-1">
                        <button wire:click="toggleSchedule({{ $place->id }})"
                            class="px-3 py-1 {{ $isOnSchedule ? 'bg-yellow-400 text-black' : 'bg-green-500 text-white' }} rounded text-sm">
                            {{ $isOnSchedule ? '✅ 予定から外す' : '✅ 予定に追加' }}
                        </button>
                        <button wire:click="edit({{ $place->id }})"
                            class="px-3 py-1 bg-gray-300 text-black rounded text-sm">編集</button>
                        <button wire:click="delete({{ $place->id }})" wire:confirm="本当に削除しますか？"
                            class="px-3 py-1 bg-red-500 text-white rounded text-sm">削除</button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
