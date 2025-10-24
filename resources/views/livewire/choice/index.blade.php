<?php

use function Livewire\Volt\{state, mount, computed};
use App\Models\Place;

// 登録されている全てのカード
state(['allPlaces' => []]);
// 支援者が選択したカードのID（最大2つまで）
state(['selectedIds' => []]);

// ページが読み込まれた時に、全カード情報を取得
mount(function () {
    $this->allPlaces = Place::latest()->get();
});

// ★ 選択されたID（selectedIds）が変わるたびに、この計算が自動で走る
// 選択されたカード情報（最大2つ）を取得する
$selectedPlaces = computed(function () {
    // selectedIds配列が空なら、空のコレクションを返す
    if (empty($this->selectedIds)) {
        return collect();
    }
    // selectedIds配列にあるIDだけをデータベースから探して返す
    return Place::whereIn('id', $this->selectedIds)->get();
});

// カードがクリックされた時の処理
$toggleSelection = function ($id) {
    // 1. すでに選ばれているかチェック
    $index = array_search($id, $this->selectedIds);

    if ($index !== false) {
        // 2. すでに選ばれていたら、選択解除（配列から削除）
        unset($this->selectedIds[$index]);
    } else {
        // 3. まだ選ばれていなくて、選択数が2未満なら
        if (count($this->selectedIds) < 2) {
            // 選択に追加（配列に追加）
            $this->selectedIds[] = $id;
        }
    }
    // 配列のキーを整理する（例：[0 => 1, 2 => 3] を [0 => 1, 1 => 3] にする）
    $this->selectedIds = array_values($this->selectedIds);
};

?>

<div style="padding: 20px;">
    <h1 style="font-size: 2rem; text-align: center;">カードを2まい えらんでください</h1>

    {{-- ★ カードを選ぶエリア --}}
    <div
        style="display: flex; flex-wrap: wrap; gap: 10px; padding: 15px; border: 2px dashed #ccc; border-radius: 10px; margin-bottom: 20px;">
        @foreach ($allPlaces as $place)
            {{-- ★ $selectedIds配列にこのカードのIDが含まれているかチェック --}}
            @php
                $isSelected = in_array($place->id, $this->selectedIds);
            @endphp

            {{-- ★ クリックで $toggleSelection を呼び出す --}}
            <div wire:click="toggleSelection({{ $place->id }})"
                style="border: 4px solid {{ $isSelected ? '#007bff' : '#ddd' }}; {{-- 選ばれていたら枠の色を変える --}}
                        border-radius: 10px; 
                        padding: 5px; 
                        cursor: pointer;
                        opacity: {{ $isSelected || count($this->selectedIds) < 2 ? '1' : '0.5' }}; {{-- 2枚選ばれたら他を半透明に --}}
                        text-align: center;
                        width: 150px;">
                <img src="{{ asset('storage/' . $place->image_path) }}" alt="{{ $place->name }}"
                    style="width: 100%; height: 100px; object-fit: cover; border-radius: 5px;">
                <p style="font-size: 1rem; font-weight: bold; margin: 5px 0 0 0;">{{ $place->name }}</p>
            </div>
        @endforeach
    </div>

    <hr style="border: 1px solid #eee;">

    {{-- ★ 選んだカードを大きく表示するエリア --}}
    <h1 style="font-size: 2.5rem; text-align: center; margin-top: 20px;">
        {{-- ★ computedプロパティ（$this->selectedPlaces）を使う --}}
        @if (count($this->selectedPlaces) == 2)
            どっちがいい？
        @elseif (count($this->selectedPlaces) == 1)
            これがいい？
        @else
            （ここに えらんだ カードが でます）
        @endif
    </h1>

    <div style="display: flex; justify-content: center; align-items: stretch; gap: 30px; margin-top: 20px;">
        {{-- ★ computedプロパティ（$this->selectedPlaces）をループ --}}
        @foreach ($this->selectedPlaces as $place)
            <div
                style="border: 5px solid {{ $loop->first ? '#007bff' : '#dc3545' }}; {{-- 1枚目と2枚目で枠色を変える --}}
                        border-radius: 15px; 
                        width: 40%; 
                        padding: 20px; 
                        background-color: #f8f9fa; 
                        display: flex; 
                        flex-direction: column; 
                        justify-content: space-between;">
                <img src="{{ asset('storage/' . $place->image_path) }}" alt="{{ $place->name }}"
                    style="width: 100%; height: 300px; object-fit: cover; border-radius: 10px;">
                <p style="font-size: 2.5rem; font-weight: bold; margin-top: 15px;">{{ $place->name }}</p>
            </div>
        @endforeach
    </div>

    {{-- 管理画面に戻るリンク --}}
    <div style="text-align: center; margin-top: 40px;">
        <a href="/places" wire:navigate
            style="display: inline-block; padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-size: 1rem;">
            管理画面にもどる
        </a>
    </div>
</div>
