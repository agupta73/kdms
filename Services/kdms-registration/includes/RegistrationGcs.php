<?php

declare(strict_types=1);

namespace KdmsRegistration;

use Google\Cloud\Storage\StorageClient;
use Throwable;

/**
 * GCS paths align with includes/PhotoStorage.php (kdms-api staff UI / migration).
 */
final class RegistrationGcs
{
    private const DEFAULT_BUCKET = 'kdms-photos';

    public static function bucketName(): string
    {
        $name = getenv('KDMS_GCS_PHOTOS_BUCKET');

        return is_string($name) && trim($name) !== '' ? trim($name) : self::DEFAULT_BUCKET;
    }

    public static function uploadBytes(string $objectPath, string $bytes, string $contentType = 'image/jpeg'): ?string
    {
        $objectPath = ltrim($objectPath, '/');
        if ($objectPath === '' || !class_exists(StorageClient::class)) {
            return null;
        }

        try {
            $storage = new StorageClient();
            $storage->bucket(self::bucketName())->upload($bytes, [
                'name' => $objectPath,
                'metadata' => ['contentType' => $contentType],
            ]);

            return $objectPath;
        } catch (Throwable $e) {
            kdms_log('ERROR', 'GCS upload failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @return array{upload_url: string, selfie_gcs_path: string}|null
     */
    public static function signedPutUrl(string $objectPath, int $ttlSeconds = 900): ?array
    {
        $objectPath = ltrim($objectPath, '/');
        if ($objectPath === '' || !class_exists(StorageClient::class)) {
            return null;
        }

        try {
            $storage = new StorageClient();
            $object = $storage->bucket(self::bucketName())->object($objectPath);
            $url = $object->signedUrl(
                new \DateTimeImmutable('+' . $ttlSeconds . ' seconds'),
                [
                    'version' => 'v4',
                    'method' => 'PUT',
                    'contentType' => 'image/jpeg',
                ]
            );

            return ['upload_url' => $url, 'selfie_gcs_path' => $objectPath];
        } catch (Throwable $e) {
            kdms_log('ERROR', 'GCS signed URL failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /** Permanent ID image path (same as PhotoStorage::objectPathForIdImage). */
    public static function idImagePath(string $devoteeKey): string
    {
        return 'devotee/' . self::normalizeKey($devoteeKey) . '/id.jpg';
    }

    /** Permanent devotee photo path (same as PhotoStorage::objectPathForPhoto). */
    public static function photoPath(string $devoteeKey): string
    {
        return 'devotee/' . self::normalizeKey($devoteeKey) . '/photo.jpg';
    }

    public static function normalizeKey(string $devoteeKey): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', $devoteeKey) ?? '');
    }

    public static function isAllowedPath(string $path, string $devoteeKey): bool
    {
        $path = trim($path);
        if ($path === '' || str_contains($path, '..')) {
            return false;
        }
        $key = preg_quote(self::normalizeKey($devoteeKey), '#');

        return (bool) preg_match('#^devotee/' . $key . '/(id|photo)\.jpg$#i', $path);
    }

    public static function deleteObject(string $objectPath): bool
    {
        $objectPath = ltrim($objectPath, '/');
        if ($objectPath === '' || !class_exists(StorageClient::class)) {
            return false;
        }

        try {
            $storage = new StorageClient();
            $object = $storage->bucket(self::bucketName())->object($objectPath);
            if (!$object->exists()) {
                return true;
            }
            $object->delete();

            return true;
        } catch (Throwable $e) {
            kdms_log('ERROR', 'GCS delete failed', ['error' => $e->getMessage(), 'path' => $objectPath]);

            return false;
        }
    }
}
