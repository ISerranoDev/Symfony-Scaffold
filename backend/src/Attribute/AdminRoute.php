<?php

namespace App\Attribute;

use Symfony\Component\Routing\Annotation\Route;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class AdminRoute extends Route
{

    /**
     * @param array<string|\Stringable> $requirements
     * @param string[]|string           $methods
     * @param string[]|string           $schemes
     */
    public function __construct(
        string|array $path = null,
        ?string $name = null,
        array $requirements = [],
        array $options = [],
        array $defaults = [],
        ?string $host = null,
        array|string $methods = [],
        array|string $schemes = [],
        ?string $condition = null,
        ?int $priority = null,
        string $locale = null,
        string $format = null,
        bool $utf8 = null,
        bool $stateless = null,
        ?string $env = null
    ) {
        $newPath = $path;
        if (is_string($path)) {
            if (!str_starts_with($path, '/administracion')) {
                $newPath = '/administracion' . $path;
            }
        }


        parent::__construct(
            $newPath,
            $name,
            $requirements,
            $options,
            $defaults,
            $host,
            $methods,
            $schemes,
            $condition,
            $priority,
            $locale,
            $format,
            $utf8,
            $stateless,
            $env
        );

    }

}