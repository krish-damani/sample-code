<?php

declare(strict_types=1);

namespace App\Extensions;

use InvalidArgumentException;
use Illuminate\View\FileViewFinder as CoreFileViewFinder;

/**
 * Class FileViewFinder
 * To extend the Laravel's core view finder.
 * Purpose is to change view location on run time according to company-slug.
 *
 * @package App\Illuminate\View
 */
class FileViewFinder extends CoreFileViewFinder
{
    /**
     * Find the given view in the list of paths.
     *
     * @param  string $name
     * @param  array  $paths
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function findInPaths(string $name, array $paths): string
    {
        $domain = explode('.', request()->getHttpHost());
        $companySlug = $domain[0];

        foreach ($paths as $path) {
            foreach ($this->getPossibleViewFiles($name) as $file) {
                // Check company-slug layout/files is exist.
                if ($this->files->exists($viewPath = $path.'/'. $companySlug . '/' .$file)) {
                    return $viewPath;
                }
                // Continue with the default flow.
                if ($this->files->exists($viewPath = $path.'/'.$file)) {
                    return $viewPath;
                }
            }
        }

        throw new InvalidArgumentException("View [{$name}] not found.");
    }
}
