<?php

declare(strict_types=1);

namespace App\Models;

use App\Presenters\DocumentPresenter;
use App\Scopes\DocumentScope;
use App\Traits\BaseModelTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Robbo\Presenter\PresentableInterface;
use Robbo\Presenter\Presenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends Model implements PresentableInterface
{
    use BaseModelTrait, SoftDeletes;

    /**
     * To disable auto-increment
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'webhook_url',
        'signature_field',
        'original_path',
        'path',
        'image_path',
        'path_with_signature',
        'image_path_with_signature',
        'signed_at',
        'status',
        'document_type',
        'is_draft',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new DocumentScope);
    }

    /*
     * ==========================================================================================================
     *  Model Level Operations via methods.
     * ==========================================================================================================
     */

    /**
     * Method to confirm the document.
     *
     * @return bool
     */
    public function confirmDocument(): bool
    {
        $this->confirmed_at = Carbon::now();
        $this->confirmed_by = Auth::id();

        return $this->save();
    }

    /**
     * Return a created presenter, MODEL PRESENTER.
     *
     * @return Presenter
     */
    public function getPresenter(): Presenter
    {
        return new DocumentPresenter($this);
    }

    /*
     * ==========================================================================================================
     *  Model relationships
     * ==========================================================================================================
     */

    /**
     * Find document company.
     *
     * @return BelongsTo
     */
    public function documentCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Find document created by user.
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Find the document confirmed by user.
     *
     * @return BelongsTo
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Find the document signee.
     *
     * @return HasOne
     */
    public function signedBy(): HasOne
    {
        return $this->hasOne(Signee::class);
    }

    /**
     * Get document signature.
     *
     * @return HasOne
     */
    public function signature(): HasOne
    {
        return $this->hasOne(Signature::class);
    }

    /**
     * Find the document signee.
     *
     * @return HasOne
     */
    public function signee(): HasOne
    {
        return $this->hasOne(Signee::class);
    }

    /**
     * Get document type.
     *
     * @return BelongsTo
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type');
    }
}
