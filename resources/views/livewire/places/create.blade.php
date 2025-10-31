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
        'user_id' => auth()->id(), // ログインユーザーのIDを保存
    ]);

    // 登録成功したら一覧ページへ移動
    return $this->redirect('/places', navigate: true);
};

?>
<div class="p-6">
    <div class="mb-5 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-700">新しい場所を登録</h2>
        <a href="/places" wire:navigate class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            一覧に戻る
        </a>
    </div>

    <form wire:submit="save" class="p-4 border border-gray-300 rounded-lg bg-white shadow">
        <div class="mb-4">
            <label for="name" class="block text-lg font-medium text-gray-700">場所の名前:</label>
            <input type="text" id="name" wire:model="name"
                class="w-full p-2 border border-gray-300 rounded mt-1">
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="photo" class="block text-lg font-medium text-gray-700">写真:</label>
            <input type="file" id="photo" wire:model="photo" class="w-full mt-1">
            @error('photo')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        @if ($photo)
            <div class="mb-4">
                <p class="text-md font-medium text-gray-700">プレビュー:</p>
                <img src="{{ $photo->temporaryUrl() }}" class="w-48 border border-gray-300 rounded">
            </div>
        @endif

        <button type="submit" class="px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">登録する</button>
    </form>
</div>
