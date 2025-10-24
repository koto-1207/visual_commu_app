<?php

use function Livewire\Volt\{state, on, mount};
use App\Models\Place;
use Illuminate\Support\Facades\Session;

// 編集中のカードIDと新しい名前を保存する変数を準備
state(['editingPlaceId' => null]);
state(['editingPlaceName' => '']);

// 今日の予定リスト用の変数
state(['scheduledIds' => []]);

// ページ読み込み時に、セッションから予定リストを読み込む
mount(function () {
    $this->scheduledIds = session('schedule_list', []);
});

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
};

// 予定リストを空にする処理
$clearSchedule = function () {
    $this->scheduledIds = [];
    session(['schedule_list' => []]);
};

// 削除ボタンが押された時の処理
$delete = function (Place $place) {
    $place->delete();
    $this->dispatch('$refresh');
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
    $this->dispatch('$refresh');
};

// 編集中のキャンセルボタンが押された時の処理
$cancelEdit = function () {
    $this->reset('editingPlaceId', 'editingPlaceName');
};

// データベースから「すべての」場所のリストを取得する
$places = fn () => Place::latest()->get();

?>
<div>
    {{-- ★ 管理者用ナビゲーション --}}
    <div style="margin-bottom: 20px; padding: 10px; background-color: #f4f4f4; border-radius: 8px;">
        <h2 style="margin: 0; display: inline-block;">管理メニュー</h2>
        
        <div style="display: inline-flex; gap: 10px; margin-left: 20px;">
            <a href="/schedule" wire:navigate style="display: inline-block; padding: 10px 15px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 5px;" target="_blank">
                利用者画面 (スケジュール)
            </a>
            <a href="/choice" wire:navigate style="display: inline-block; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;" target="_blank">
                利用者画面 (選択)
            </a>
            <a href="/places/create" wire:navigate style="display: inline-block; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
                新しい場所を登録
            </a>
            
            <button wire:click="clearSchedule" style="padding: 10px 15px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                予定をクリア
            </button>
        </div>
    </div>


    {{-- カード一覧表示 --}}
    <h2>登録された場所（全カード）</h2>
    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
        @foreach ($this->places() as $place)
            {{-- 予定に追加されているかチェック --}}
            @php
                $isOnSchedule = in_array($place->id, $this->scheduledIds);
            @endphp

            {{-- 予定に追加されているかで枠の色を変更 --}}
            <div style="border: 5px solid {{ $isOnSchedule ? '#28a745' : '#ccc' }}; padding: 10px; border-radius: 8px; text-align: center;">

                @if ($editingPlaceId === $place->id)
                    {{-- 編集中の表示 --}}
                    <input type="text" wire:model="editingPlaceName" style="width: 100%; padding: 8px; margin-bottom: 5px;">
                    @error('editForm.editingPlaceName') <span style="color: red; display: block; margin-bottom: 5px;">{{ $message }}</span> @enderror
                    <button wire:click="update">保存</button>
                    <button wire:click="cancelEdit">キャンセル</button>
                @else
                    {{-- 通常の表示 --}}
                    <p>{{ $place->name }}</p>
                    <img src="{{ asset('storage/' . $place->image_path) }}" alt="{{ $place->name }}" style="width: 200px; height: 150px; object-fit: cover;">
                    <div style="margin-top: 10px;">
                        
                        <button wire:click="toggleSchedule({{ $place->id }})" 
                                style="background-color: {{ $isOnSchedule ? '#ffc107' : '#28a745' }}; color: {{ $isOnSchedule ? 'black' : 'white' }}; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
                            {{ $isOnSchedule ? '✅ 外す' : '✅ 予定に追加' }}
                        </button>

                        <button wire:click="edit({{ $place->id }})">編集</button>
                        <button wire:click="delete({{ $place->id }})" wire:confirm="本当に削除しますか？">削除</button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
