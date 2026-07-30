<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\HomepageHero;
use Illuminate\Validation\Validator;

final class UpdateHomepageHeroRequest extends StoreHomepageHeroRequest
{
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validatePreviewImage($validator);

            /** @var HomepageHero $hero */
            $hero = $this->route('homepageHero');

            if (
                (int) $this->input('status') === 1
                && ! $this->input('resume_template_id')
                && ! $this->hasFile('preview_image')
                && (! $hero->preview_image_path || $this->boolean('remove_preview_image'))
            ) {
                $validator->errors()->add('resume_template_id', 'Choose a template or upload a preview image before publishing this hero.');
            }
        });
    }
}
