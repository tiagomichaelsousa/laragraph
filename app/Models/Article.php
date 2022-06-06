<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory;

    /**
     * The URL for the default thumbnail.
     *
     * @var string
     */
    public const DEFAULT_THUMBNAIL_PATH = 'https://placeimg.com/640/380/tech';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'body',
        'slug',
        'thumbnail',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Interact with the article thumbnail
     *
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Storage::url($value) : self::DEFAULT_THUMBNAIL_PATH
        );
    }
}
