<?php

namespace App\Support;

use App\Models\Assessment;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

final class QuizSchedule
{
    // HTML datetime-local is interpreted as WIB; the database stores UTC.
    public const TIMEZONE = 'Asia/Jakarta';

    public static function localToUtc(?string $value): ?CarbonImmutable
    {
        return $value
            ? CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $value, self::TIMEZONE)->utc()
            : null;
    }

    public static function validateDates(array $data): array
    {
        $starts = self::localToUtc($data['starts_at'] ?? null);
        $due = self::localToUtc($data['due_at'] ?? null);
        if ($starts && $due && $starts->greaterThanOrEqualTo($due)) {
            throw ValidationException::withMessages(['due_at' => 'Deadline harus setelah waktu mulai (WIB).']);
        }
        if ($due && $due->lessThanOrEqualTo(now())) {
            throw ValidationException::withMessages(['due_at' => 'Deadline harus berada di masa depan.']);
        }
        return ['starts_at' => $starts, 'due_at' => $due];
    }

    public static function canStart(Assessment $quiz): bool
    {
        return (bool) $quiz->is_published
            && $quiz->archived_at === null
            && (!$quiz->starts_at || $quiz->starts_at->lessThanOrEqualTo(now()))
            && (!$quiz->due_at || $quiz->due_at->greaterThan(now()));
    }

    public static function assertOpen(Assessment $quiz): void
    {
        abort_unless(self::canStart($quiz), 403, 'Kuis belum dimulai, sudah berakhir, atau diarsipkan.');
    }

    public static function archiveExpired(): int
    {
        // Safe to run every minute and on page requests; no student grade is deleted.
        return Assessment::query()
            ->whereNull('archived_at')
            ->where('is_published', true)
            ->whereNotNull('due_at')
            ->where('due_at', '<=', now())
            ->update(['archived_at' => now(), 'is_published' => false, 'updated_at' => now()]);
    }
}
