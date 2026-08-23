@php
    $formatDate = $formatMonth ?? static function (?string $value): string {
        if (! $value) {
            return '';
        }

        return \Illuminate\Support\Carbon::createFromFormat('Y-m', $value)->format('m/Y');
    };

    $startDate = $formatDate($startDate ?? null);
    $endDate = ($isCurrent ?? false) ? 'Present' : $formatDate($endDate ?? null);
    $passingValue = trim((string) ($passingYear ?? ''));
    $passingDate = preg_match('/^\d{4}-\d{2}$/', $passingValue)
        ? $formatDate($passingValue)
        : $passingValue;
@endphp

<i class="fa-regular fa-calendar-days" aria-hidden="true"></i>
@if ($startDate !== '' || $endDate !== '')
    {{ $startDate }}@if ($startDate !== '' && $endDate !== '') - @endif{{ $endDate }}
@else
    {{ $passingDate }}
@endif
