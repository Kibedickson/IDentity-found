<?php

namespace App\Models;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'document_number',
        'document_name',
        'location',
        'type',
        'status',
    ];

    protected $casts = [
        'type' => DocumentTypeEnum::class,
        'status' => DocumentStatusEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function claimUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claim_user_id');
    }
}
