<?php

namespace App\Models;

use App\Enums\DocumentStatusEnum;
use App\Enums\DocumentTypeEnum;
use App\Notifications\Notification;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'claim_user_id',
        'notification_id',
        'category_id',
        'document_number',
        'location',
        'status',
        'type',
    ];

    protected $casts = [
        'status' => DocumentStatusEnum::class,
        'type' => DocumentTypeEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function notification(): HasOne
    {
        return $this->hasOne(Notification::class);
    }

    public function claimUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claim_user_id');
    }
}
