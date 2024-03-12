<?php

namespace App\Notifications;

use Filament\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification
{
    protected string $documentId = '';
    protected string $userId = '';

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'document_id' => $this->getDocumentId(),
            'user_id' => $this->getUserId(),
        ];
    }

    public static function fromArray(array $data): static
    {
        return parent::fromArray($data)
            ->documentId($data['document_id'])
            ->userId($data['user_id']);
    }

    public function documentId(string $documentId): static
    {
        $this->documentId = $documentId;

        return $this;
    }

    public function userId(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getDocumentId(): string
    {
        return $this->documentId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
