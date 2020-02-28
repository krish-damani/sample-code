<?php

declare(strict_types=1);

namespace App\Observers;

use App\Exceptions\Documents\DeletingSignedDocumentsException;
use App\Models\Document;
use App\Services\NotificationService;

class DocumentObserver
{
    /**
     * @var NotificationService $notificationService
     */
    private $notificationService;

    /**
     * SigneeObserver constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the document "created" event.
     *
     * @param  Document $document
     * @return void
     */
    public function creating(Document $document)
    {
        $document->fillInternalData();
    }

    /**
     * Handle the document "created" event.
     *
     * @param  Document $document
     * @return void
     */
    public function created(Document $document)
    {
        // here we will send an email notification to user.
    }

    /**
     * Handle the document "updated" event.
     *
     * @param  Document $document
     * @return void
     */
    public function updated(Document $document)
    {
        if ($document->signed_at != null && $document->status == config('enum.documents.status.signed')) {
            $this->notificationService->sendSignedDocument($document);
        }
    }

    /**
     * Handle the document "deleting" event.
     *
     * @param  Document $document
     * @return void
     * @throws DeletingSignedDocumentsException
     */
    public function deleting(Document $document)
    {
        if ($document->signed_at !== null) {
            throw new DeletingSignedDocumentsException(trans('messages/exceptions.documents.deleting_signed_document'));
        }

        $document->signedBy()->delete();
    }
}
