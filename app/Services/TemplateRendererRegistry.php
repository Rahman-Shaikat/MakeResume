<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

final class TemplateRendererRegistry
{
    /**
     * @return array<string, array{
     *     view: string,
     *     sample: array<string, mixed>,
     *     default_accent_color: string,
     *     supports_profile_photo: bool
     * }>
     */
    public function all(): array
    {
        return config('resume_templates.renderers', []);
    }

    public function has(string $rendererKey): bool
    {
        return array_key_exists($rendererKey, $this->all());
    }

    /**
     * @return array{
     *     view: string,
     *     sample: array<string, mixed>,
     *     default_accent_color: string,
     *     supports_profile_photo: bool
     * }
     */
    public function get(string $rendererKey): array
    {
        $definition = $this->all()[$rendererKey] ?? null;

        if (! is_array($definition)) {
            throw new InvalidArgumentException("Unknown resume template renderer [{$rendererKey}].");
        }

        return $definition;
    }

    public function view(string $rendererKey): string
    {
        return $this->get($rendererKey)['view'];
    }

    /**
     * @return array<string, mixed>
     */
    public function sample(string $rendererKey): array
    {
        return $this->get($rendererKey)['sample'];
    }

    /**
     * @return array<string, string>
     */
    public function options(): array
    {
        return collect($this->all())
            ->mapWithKeys(fn (array $definition, string $key): array => [
                $key => str($key)->replace('-', ' ')->title()->toString(),
            ])
            ->all();
    }
}
