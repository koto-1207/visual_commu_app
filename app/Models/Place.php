<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Place extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'image_path', 'user_id'];

    /**
     * このPlaceが属するUserを取得
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
