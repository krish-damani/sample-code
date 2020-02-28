<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Document;

class DocumentRepository extends Repository
{
    /**
     * @var array $filters
     */
    private $filters = [];

    /**
     * Allowed fields for searching.
     * #NOTE: Example for relational searchable field ==> 'relation_name.field_name'
     *
     * @var array $searchableFields
     */
    private $searchableFields = [
        'created_at',
        'signed_at',
        'createdBy.name',
        'createdBy.email',
        'signedBy.name',
        'signedBy.email',
    ];

    /**
     * To initialize class objects/variables.
     *
     * @param Document $model
     */
    public function __construct(Document $model)
    {
        $this->model = $model;
    }

    /**
     * Fetch all the documents with its dependencies.
     *
     * @return mixed
     */
    public function fetchAllDocuments()
    {
        $query = $this->model->orderBy('documents.created_at', 'desc');

        $this->attachCommonFilters($query);
        $this->attachDocumentTypeFilters($query);
        $this->attachSearchResult($query, $this->searchableFields);

        $query->with('documentCompany', 'createdBy', 'signedBy', 'documentType');

        return $query->paginate(10);
    }

    /**
     * Method to apply some common filters like invoice status & type.
     *
     * @param  $query
     * @return mixed
     */
    public function attachCommonFilters(&$query)
    {
        if (request()->has('status')) {
            $value = (trim(request()->get('status')) == 'signed') ? 1 : 0;
            $query->where('status', $value);
        }

        return $query;
    }

    /**
     * Attach filters.
     *
     * @param $query
     * @return mixed
     */
    public function attachDocumentTypeFilters(&$query)
    {
        if (request()->has('type')) {
            $value = trim(request()->get('type'));
            if (! empty($value)) {
                ($value == 'draft') ?
                    $query->where('documents.is_draft', 1) :
                    $query->attachSearch(['documentType.name'], $value);
            }
        }

        return $query;
    }

    /**
     * Method to get the full document details.
     *
     * @param int $id
     * @return mixed
     */
    public function getDocumentDetails(int $id)
    {
        $query = $this->model->where('id', $id)->with('createdBy', 'confirmedBy', 'signee', 'signature', 'documentType');

        return $query->first();
    }
}
