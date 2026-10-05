<?php

namespace App\Models;

use App\Enums\DocumentCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'documentable_type', 'documentable_id', 'client_id', 'title', 'category', 'file_path', 'file_name',
        'mime_type', 'size', 'document_date', 'visible_to_client', 'uploaded_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'category' => DocumentCategory::class,
            'document_date' => 'date',
            'visible_to_client' => 'boolean',
            'size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Document $document): void {
            $document->uploaded_by ??= auth()->id();

            if ($document->file_path && ! $document->file_name) {
                $document->file_name = basename($document->file_path);
            }

            if ($document->file_path && ! $document->size) {
                $disk = Storage::disk(config('firm.documents_disk'));

                if ($disk->exists($document->file_path)) {
                    $document->size = $disk->size($document->file_path);
                    $document->mime_type = $disk->mimeType($document->file_path) ?: null;
                }
            }

            if ($document->client_id === null) {
                $document->client_id = $document->resolveClientId();
            }
        });

        static::deleted(function (Document $document): void {
            if ($document->isForceDeleting() && $document->file_path) {
                Storage::disk(config('firm.documents_disk'))->delete($document->file_path);
            }
        });
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function resolveClientId(): ?int
    {
        $owner = $this->documentable;

        return match (true) {
            $owner instanceof Client => $owner->id,
            $owner instanceof CompanyProcedure => $owner->company?->client_id,
            $owner !== null => $owner->client_id ?? null,
            default => null,
        };
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
