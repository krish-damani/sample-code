<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\Document;
use App\Models\Signee;
use App\Observers\CompanyObserver;
use App\Observers\CompanySettingsObserver;
use App\Observers\DocumentObserver;
use App\Observers\SigneeObserver;
use App\Observers\UserObserver;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class EloquentServiceProvider
 * All eloquent events/macros will be registered here.
 *
 * @package App\Providers
 */
class EloquentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Define Eloquent Observers.
        Document::observe(DocumentObserver::class);
        Signee::observe(SigneeObserver::class);
        User::observe(UserObserver::class);
        Company::observe(CompanyObserver::class);
        CompanySetting::observe(CompanySettingsObserver::class);

        // Define Eloquent Macros
        Builder::macro('attachSearch', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        Str::contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },

                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });

            return $this;
        });
    }
}
