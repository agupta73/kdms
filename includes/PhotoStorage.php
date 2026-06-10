<?php

declare(strict_types=1);

$photoStorageAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($photoStorageAutoload)) {
    require_once $photoStorageAutoload;
}

/**
 * Dual-read photo storage: GCS path (preferred) or MySQL LONGBLOB fallback.
 * Bucket-relative paths stored in DB, e.g. devotee/{Devotee_Key}/photo.jpg
 */
final class PhotoStorage
{
    private const DEFAULT_BUCKET = 'kdms-photos';

    public static function bucketName(): string
    {
        $name = getenv('KDMS_GCS_PHOTOS_BUCKET');
        if (is_string($name) && trim($name) !== '') {
            return trim($name);
        }

        return self::DEFAULT_BUCKET;
    }

    /**
     * @return array{bytes: string, source: string}|null
     */
    public static function readDevoteePhoto(PDO $db, string $devoteeKey): ?array
    {
        $stmt = $db->prepare(
            'SELECT Devotee_Photo_Gcs_Path, Devotee_Photo FROM devotee_photo WHERE Devotee_Key = :key'
        );
        $stmt->execute(['key' => $devoteeKey]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rows === []) {
            return null;
        }

        return self::resolveBestChildRow($rows, 'Devotee_Photo_Gcs_Path', 'Devotee_Photo', self::objectPathForPhoto($devoteeKey));
    }

    /**
     * @return array{bytes: string, source: string}|null
     */
    public static function readDevoteeIdImage(PDO $db, string $devoteeKey): ?array
    {
        $stmt = $db->prepare(
            'SELECT Devotee_ID_Image_Gcs_Path, Devotee_ID_Image FROM devotee_id WHERE Devotee_Key = :key'
        );
        $stmt->execute(['key' => $devoteeKey]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rows === []) {
            return null;
        }

        return self::resolveBestChildRow($rows, 'Devotee_ID_Image_Gcs_Path', 'Devotee_ID_Image', self::objectPathForIdImage($devoteeKey));
    }

    public static function objectPathForPhoto(string $devoteeKey): string
    {
        return 'devotee/' . $devoteeKey . '/photo.jpg';
    }

    public static function objectPathForIdImage(string $devoteeKey): string
    {
        return 'devotee/' . $devoteeKey . '/id.jpg';
    }

    /**
     * When multiple devotee_id / devotee_photo rows exist per key (legacy merge repoint),
     * pick the row most likely to represent the latest staff upload.
     *
     * @param list<array<string, mixed>> $rows
     * @return array{bytes: string, source: string}|null
     */
    private static function resolveBestChildRow(
        array $rows,
        string $pathColumn,
        string $blobColumn,
        string $canonicalPath
    ): ?array {
        $canonicalPath = ltrim($canonicalPath, '/');
        $best = null;
        $bestScore = -1;

        foreach ($rows as $row) {
            $gcsPath = isset($row[$pathColumn]) ? trim((string) $row[$pathColumn]) : '';
            $blobLen = 0;
            if (!empty($row[$blobColumn])) {
                $blob = $row[$blobColumn];
                if (is_resource($blob)) {
                    $blob = stream_get_contents($blob);
                }
                if (is_string($blob)) {
                    $blobLen = strlen($blob);
                }
            }

            $score = 0;
            if ($gcsPath !== '') {
                $score += 40;
                if ($gcsPath === $canonicalPath) {
                    $score += 60;
                }
            }
            if ($blobLen > 64) {
                $score += 30 + (int) min($blobLen / 4096, 40);
            }
            if ($gcsPath !== '' && $blobLen > 64) {
                $score += 20;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $row;
            }
        }

        if ($best === null) {
            return null;
        }

        return self::resolveRow($best, $pathColumn, $blobColumn);
    }

    /**
     * @param array<string, mixed> $row
     * @return array{bytes: string, source: string}|null
     */
    private static function resolveRow(array $row, string $pathColumn, string $blobColumn): ?array
    {
        $gcsPath = isset($row[$pathColumn]) ? trim((string) $row[$pathColumn]) : '';
        if ($gcsPath !== '') {
            $bytes = self::readGcsObject($gcsPath);
            if ($bytes !== null) {
                return ['bytes' => $bytes, 'source' => 'gcs'];
            }
        }

        if (!empty($row[$blobColumn])) {
            $blob = $row[$blobColumn];
            if (is_resource($blob)) {
                $blob = stream_get_contents($blob);
            }
            if (is_string($blob) && $blob !== '') {
                return ['bytes' => $blob, 'source' => 'blob'];
            }
        }

        return null;
    }

    /**
     * Base64 for legacy JSON/UI consumers (search grid, card print PCD, addDevoteeI).
     * Always resolves from devotee_photo row (GCS path preferred over stale JOIN BLOB).
     */
    public static function legacyBase64Photo(PDO $db, string $devoteeKey, mixed $blobFromQuery): string
    {
        $read = self::readDevoteePhoto($db, $devoteeKey);
        if ($read !== null) {
            return base64_encode($read['bytes']);
        }

        $blob = self::normalizeBlobValue($blobFromQuery);

        return $blob !== '' ? base64_encode($blob) : '';
    }

    /**
     * Base64 for legacy JSON/UI consumers (search grid, addDevoteeI).
     * GCS path preferred over stale JOIN BLOB.
     */
    public static function legacyBase64IdImage(PDO $db, string $devoteeKey, mixed $blobFromQuery): string
    {
        $read = self::readDevoteeIdImage($db, $devoteeKey);
        if ($read !== null) {
            return base64_encode($read['bytes']);
        }

        $blob = self::normalizeBlobValue($blobFromQuery);

        return $blob !== '' ? base64_encode($blob) : '';
    }

    private static function normalizeBlobValue(mixed $blob): string
    {
        if (is_resource($blob)) {
            $blob = stream_get_contents($blob);
        }

        return is_string($blob) ? $blob : '';
    }

    public static function canSignUrls(): bool
    {
        if (!class_exists(\Google\Cloud\Storage\StorageClient::class)) {
            return false;
        }

        $creds = getenv('GOOGLE_APPLICATION_CREDENTIALS');
        if (is_string($creds) && trim($creds) !== '' && is_file($creds)) {
            return true;
        }

        // Cloud Run / GCE workload identity (no key file required).
        if (getenv('K_SERVICE') !== false || getenv('GOOGLE_CLOUD_PROJECT') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Time-limited read-only URL for a private GCS object (grid/report lazy load).
     */
    /**
     * GCS object path for lazy redirect (photo or id). Null when only BLOB / missing.
     */
    public static function resolveGcsObjectPath(PDO $db, string $devoteeKey, string $type): ?string
    {
        $type = strtolower(trim($type));
        if ($type === 'photo') {
            $sql = 'SELECT Devotee_Photo_Gcs_Path, Devotee_Photo FROM devotee_photo WHERE Devotee_Key = :key';
            $pathColumn = 'Devotee_Photo_Gcs_Path';
            $blobColumn = 'Devotee_Photo';
            $canonicalPath = self::objectPathForPhoto($devoteeKey);
        } elseif ($type === 'id') {
            $sql = 'SELECT Devotee_ID_Image_Gcs_Path, Devotee_ID_Image FROM devotee_id WHERE Devotee_Key = :key';
            $pathColumn = 'Devotee_ID_Image_Gcs_Path';
            $blobColumn = 'Devotee_ID_Image';
            $canonicalPath = self::objectPathForIdImage($devoteeKey);
        } else {
            return null;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute(['key' => $devoteeKey]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rows === []) {
            return null;
        }

        $best = self::pickBestChildRow($rows, $pathColumn, $blobColumn, $canonicalPath);
        if ($best === null) {
            return null;
        }

        $gcsPath = isset($best[$pathColumn]) ? trim((string) $best[$pathColumn]) : '';

        return $gcsPath !== '' ? $gcsPath : null;
    }

    public static function signedUrl(string $objectPath, int $ttlSeconds = 1800): ?string
    {
        $objectPath = ltrim($objectPath, '/');
        if ($objectPath === '' || !self::canSignUrls()) {
            return null;
        }

        if (!class_exists(\Google\Cloud\Storage\StorageClient::class)) {
            return null;
        }

        try {
            $storage = self::storageClient();
            $object = $storage->bucket(self::bucketName())->object($objectPath);

            return $object->signedUrl(
                new \DateTimeImmutable('+' . $ttlSeconds . ' seconds'),
                [
                    'version' => 'v4',
                    'method' => 'GET',
                ]
            );
        } catch (Throwable $e) {
            kdms_log('ERROR', 'PhotoStorage: signed URL failed', [
                'bucket' => self::bucketName(),
                'object' => $objectPath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private static function storageClient(): \Google\Cloud\Storage\StorageClient
    {
        static $client = null;
        if ($client === null) {
            $client = new \Google\Cloud\Storage\StorageClient();
        }

        return $client;
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return array<string, mixed>|null
     */
    private static function pickBestChildRow(
        array $rows,
        string $pathColumn,
        string $blobColumn,
        string $canonicalPath
    ): ?array {
        $canonicalPath = ltrim($canonicalPath, '/');
        $best = null;
        $bestScore = -1;

        foreach ($rows as $row) {
            $gcsPath = isset($row[$pathColumn]) ? trim((string) $row[$pathColumn]) : '';
            $blobLen = 0;
            if (!empty($row[$blobColumn])) {
                $blob = $row[$blobColumn];
                if (is_resource($blob)) {
                    $blob = stream_get_contents($blob);
                }
                if (is_string($blob)) {
                    $blobLen = strlen($blob);
                }
            }

            $score = 0;
            if ($gcsPath !== '') {
                $score += 40;
                if ($gcsPath === $canonicalPath) {
                    $score += 60;
                }
            }
            if ($blobLen > 64) {
                $score += 30 + (int) min($blobLen / 4096, 40);
            }
            if ($gcsPath !== '' && $blobLen > 64) {
                $score += 20;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $row;
            }
        }

        return $best;
    }

    public static function readGcsObject(string $objectPath): ?string
    {
        $objectPath = ltrim($objectPath, '/');
        if ($objectPath === '') {
            return null;
        }

        if (!class_exists(\Google\Cloud\Storage\StorageClient::class)) {
            kdms_log('ERROR', 'PhotoStorage: google/cloud-storage not installed');

            return null;
        }

        try {
            $storage = new \Google\Cloud\Storage\StorageClient();
            $object = $storage->bucket(self::bucketName())->object($objectPath);
            if (!$object->exists()) {
                kdms_log('ERROR', 'PhotoStorage: GCS object not found', [
                    'bucket' => self::bucketName(),
                    'object' => $objectPath,
                ]);

                return null;
            }

            return $object->downloadAsString();
        } catch (Throwable $e) {
            kdms_log('ERROR', 'PhotoStorage: GCS read failed', [
                'bucket' => self::bucketName(),
                'object' => $objectPath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @return array{written: bool, path: string}|null null on failure
     */
    public static function writeGcsObject(string $objectPath, string $bytes, string $contentType = 'image/jpeg'): ?array
    {
        $objectPath = ltrim($objectPath, '/');
        if ($objectPath === '' || !class_exists(\Google\Cloud\Storage\StorageClient::class)) {
            return null;
        }

        try {
            $storage = new \Google\Cloud\Storage\StorageClient();
            $storage->bucket(self::bucketName())->upload($bytes, [
                'name' => $objectPath,
                'metadata' => ['contentType' => $contentType],
            ]);

            return ['written' => true, 'path' => $objectPath];
        } catch (Throwable $e) {
            kdms_log('ERROR', 'PhotoStorage: GCS write failed', [
                'bucket' => self::bucketName(),
                'object' => $objectPath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
