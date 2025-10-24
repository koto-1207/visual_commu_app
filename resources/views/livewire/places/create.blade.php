<?php

use function Livewire\Volt\{state, rules, uses};
use App\Models\Place;
use Livewire\WithFileUploads;

uses(WithFileUploads::class);

state(['name' => '']);
state(['photo' => null]);

rules([
    'name' => 'required|string|max:255',
    'photo' => 'required|image|max:2048',
]);

$save = function () {
    $validated = $this->validate();
    $path = $this->photo->store('places', 'public');
    Place::create([
        'name' => $validated['name'],
        'image_path' => $path,
    ]);

    // ★ 登録が成功したら、一覧ページ（/places）へ移動する
    return $this->redirect('/places', navigate: true);
};

?>

<div>
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h2>新しい場所を登録</h2>
        <a href="/places" wire:navigate
            style="display: inline-block; padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px;">
            一覧に戻る
        </a>
    </div>

    <form wire:submit="save" style="border: 1px solid #ccc; padding: 15px; border-radius: 8px;">
        <div style="margin-bottom: 10px;">
            <label for="name">場所の名前:</label><br>
            <input type="text" id="name" wire:model="name" style="width: 100%; padding: 8px;">
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div style="margin-bottom: 10px;">
            <label for="photo">写真:</label><br>
            <input type="file" id="photo" wire:model="photo">
            @error('photo')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        @if ($photo)
            <div style="margin-bottom: 10px;">
                <p>プレビュー:</p>
                <img src="{{ $photo->temporaryUrl() }}" style="width: 200px; border: 1px solid #ddd;">
            </div>
        @endif

        <button type="submit">登録する</button>
    </form>
</div>
