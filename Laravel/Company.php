<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BaseModelTrait;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
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
        'name',
        'coc_number',
        'vat_number',
        'subdomain',
        'logo',
        'theme_color',
        'description',
        'address',
        'timezone',
        'updated_by',
        'theme_id',
    ];

    /**
     * ----------------------------------------
     * Internal methods
     * ----------------------------------------
     */

    public function setUpdatedBy()
    {
        $this->updated_by = Auth::id();
    }

    /**
     * ----------------------------------------
     * Models relationships
     * ----------------------------------------
     */

    /**
     * Find companies documents.
     *
     * @return HasMany
     */
    public function companyDocuments(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * @return HasMany
     */
    public function companyUsers(): HasMany
    {
        return $this->hasMany(CompanyUser::class, 'company_id')->with('company', 'user');
    }

    /**
     * Get company user by email.
     *
     * @param string $email
     * @return Collection
     */
    public function companyUser(string $email): Collection
    {
        return $this->hasMany(CompanyUser::class, 'company_id')->where('email', '=', $email)->get();
    }

    /**
     * Return company settings.
     *
     * @return HasOne
     */
    public function companySettings(): HasOne
    {
        return $this->hasOne(CompanySetting::class);
    }

    /**
     * Return company admin
     *
     * @return BelongsToMany
     */
    public function companyAdmin(): BelongsToMany
    {
        return $this->BelongsToMany(User::class, 'company_users')->where('is_company_admin', 1);
    }

    /**
     * return company theme if selected by company admin.
     *
     * @return BelongsTo
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class, 'theme_id');
    }
}
