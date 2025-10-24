<?php

use function Livewire\Volt\{state, mount, computed}; // ★ computed を追加
use App\Models\Place;
use Illuminate\Support\Facades\Session; // ★ Session を追加

// セッションから読み込んだIDリストを保存する変数
state(['scheduledIds' => []]);

// ページ読み込み時に、セッションから予定リストを読み込む
mount(function () {
    $this->scheduledIds = session('schedule_list', []);
});

// ★ 変更部分：セッションのIDリスト（$scheduledIds）から、正しい順番でカード情報を取得する
$places = computed(function () {
    if (empty($this->scheduledIds)) {
        return collect(); // リストが空なら、何も返さない
    }

    // IDのリストから、カード情報を取得
    $placesCollection = Place::whereIn('id', $this->scheduledIds)->get();

    // セッションに保存された順番（$scheduledIds の順番）に並び替える
    return $placesCollection->sortBy(function ($place) {
        return array_search($place->id, $this->scheduledIds);
    });
});

?>

<div style="padding: 20px;">

    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 2.5rem;">きょうのよてい</h1>

        <a href="/places" wire:navigate
            style="display: inline-block; padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-size: 1rem;">
            かんりしゃメニュー
        </a>
    </div>

    {{-- ★ 変更部分：computed プロパティ（$this->places）を使う --}}
    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        @if ($this->places->isEmpty())
            <p style="font-size: 1.5rem; color: #6c757d;">まだ「きょうのよてい」が とうろくされていません。</p>
        @else
            @foreach ($this->places as $place)
                <div
                    style="border: 3px solid #007bff; border-radius: 15px; text-align: center; width: 300px; padding: 15px; background-color: #f8f9fa;">

                    <img src="{{ asset('storage/' . $place->image_path) }}" alt="{{ $place->name }}"
                        style="width: 100%; height: 220px; object-fit: cover; border-radius: 10px;">

                    <p style="font-size: 2rem; font-weight: bold; margin: 10px 0 0 0;">{{ $place->name }}</p>

                </div>
            @endforeach
        @endif
    </div>
</div>
