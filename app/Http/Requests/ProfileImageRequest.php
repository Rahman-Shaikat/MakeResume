<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ProfileImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'profile_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:min_width=160,min_height=160,max_width=4000,max_height=4000',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'profile_image.required' => 'Choose a profile photo to upload.',
            'profile_image.mimes' => 'The profile photo must be a JPG, PNG, or WebP image.',
            'profile_image.max' => 'The profile photo may not be larger than 2 MB.',
            'profile_image.dimensions' => 'Use an image between 160 x 160 and 4000 x 4000 pixels.',
        ];
    }
}
