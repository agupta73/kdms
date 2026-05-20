<?php

declare(strict_types=1);

namespace KdmsRegistration;

use Google\Cloud\Storage\StorageClient;
use Throwable;

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

    public static function stagingIdPath(string $devoteeKey): string
    {
        $key = strtoupper(preg_replace('/[^A-Z0-9]/', '', $devoteeKey) ?? '');

        return 'id-staging/' . date('Y-m-d') . '/' . $key . '.jpg';
    }

    public static function stagingSelfiePath(string $devoteeKey): string
    {
        $key = strtoupper(preg_replace('/[^A-Z0-9]/', '', $devoteeKey) ?? '');

        return 'devotee-selfies/' . date('Y-m-d') . '/' . $key . '.jpg';
    }
}
