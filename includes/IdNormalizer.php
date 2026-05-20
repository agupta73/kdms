<?php

declare(strict_types=1);

/**
 * Shared ID normalization for registration and deduplication (align with devotee.Devotee_ID_Unique_Key).
 */
final class IdNormalizer
{
    public static function normalize(string $idType, string $idNumber): string
    {
        $type = trim($idType);
        $value = trim($idNumber);

        switch ($type) {
            case 'Aadhaar':
                $value = preg_replace('/[\s\-]+/', '', $value) ?? $value;

                return $value;
            case 'PAN Card':
            case 'PAN':
                return strtoupper(preg_replace('/\s+/', '', $value) ?? $value);
            case 'Voter ID':
            case 'Passport':
            case 'Driving License':
                return strtoupper(preg_replace('/\s+/', '', $value) ?? $value);
            default:
                return strtoupper(trim($value));
        }
    }

    /**
     * Build stored unique key (NULL when invalid / placeholder — matches Phase 1a generated column rules).
     */
    public static function uniqueKey(string $idType, string $idNumber): ?string
    {
        $type = trim($idType);
        $number = self::normalize($type, $idNumber);
        if ($type === '' || $number === '') {
            return null;
        }
        $placeholders = ['-', 'N/A', 'NA', 'n/a', 'None', 'NONE', 'null', 'NULL'];
        if (in_array($number, $placeholders, true)) {
            return null;
        }
        $digitsOnly = preg_replace('/[\s\-]+/', '', $number) ?? '';
        if ($digitsOnly !== '' && preg_match('/^0+$/', $digitsOnly)) {
            return null;
        }
        if (strlen($digitsOnly) < 4) {
            return null;
        }
        $typeUpper = strtoupper($type);
        if (in_array($typeUpper, ['AADHAAR', 'AADHAR'], true)) {
            if (!preg_match('/^\d{12}$/', $digitsOnly)) {
                return null;
            }

            return 'AADHAAR|' . $digitsOnly;
        }

        return $typeUpper . '|' . str_replace([' ', '-'], '', $number);
    }
}
