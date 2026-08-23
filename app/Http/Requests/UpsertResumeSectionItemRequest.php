<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\ResumeSection;
use App\Services\ResumeRichTextSanitizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class UpsertResumeSectionItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $data = $this->input('data');

        if (! is_array($data)) {
            return;
        }

        foreach (['current', 'title_bold', 'title_italic', 'title_use_accent'] as $booleanField) {
            if (array_key_exists($booleanField, $data)) {
                $data[$booleanField] = $this->boolean("data.{$booleanField}");
            }
        }

        if (array_key_exists('content_html', $data)) {
            $data['content_html'] = app(ResumeRichTextSanitizer::class)
                ->sanitize(is_string($data['content_html']) ? $data['content_html'] : null);
        }

        $this->merge(['data' => $data]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'data' => ['required', 'array', 'min:1', 'max:20'],
            'data.title' => ['nullable', 'string', 'max:160'],
            'data.name' => ['nullable', 'string', 'max:160'],
            'data.level' => ['nullable', 'string', 'max:80'],
            'data.category' => ['nullable', 'string', 'max:80'],
            'data.degree' => ['nullable', 'string', 'max:160'],
            'data.institution' => ['nullable', 'string', 'max:160'],
            'data.location' => ['nullable', 'string', 'max:160'],
            'data.start_date' => ['nullable', 'date_format:Y-m'],
            'data.end_date' => ['nullable', 'date_format:Y-m'],
            'data.passing_year' => ['nullable', 'date_format:Y-m'],
            'data.current' => ['nullable', 'boolean'],
            'data.description' => ['nullable', 'string', 'max:2000'],
            'data.company' => ['nullable', 'string', 'max:160'],
            'data.company_website' => ['nullable', 'url:http,https', 'max:255'],
            'data.role' => ['nullable', 'string', 'max:160'],
            'data.project_domain' => ['nullable', 'string', 'max:120'],
            'data.tech_stack' => ['nullable', 'string', 'max:255'],
            'data.url' => ['nullable', 'url:http,https', 'max:255'],
            'data.title_bold' => ['nullable', 'boolean'],
            'data.title_italic' => ['nullable', 'boolean'],
            'data.title_use_accent' => ['nullable', 'boolean'],
            'data.content_html' => ['nullable', 'string', 'max:5000'],
            'data.provider' => ['nullable', 'string', 'max:160'],
            'data.date' => ['nullable', 'string', 'max:40'],
            'data.organization' => ['nullable', 'string', 'max:160'],
            'data.proficiency' => ['nullable', 'string', 'max:80'],
            'data.content' => ['nullable', 'string', 'max:3000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $section = $this->route('resumeSection');
            $data = $this->input('data');

            if (! $section instanceof ResumeSection || ! is_array($data) || ! filled($data['passing_year'] ?? null)) {
                return;
            }

            if ($section->type !== 'education') {
                $validator->errors()->add('data.passing_year', 'Passing month and year are available only for education entries.');

                return;
            }

            if (filled($data['start_date'] ?? null) || filled($data['end_date'] ?? null)) {
                $validator->errors()->add('data.passing_year', 'Use either passing month and year or a start and end date range.');
            }
        });
    }
}
