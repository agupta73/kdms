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

    public static function stagingIdPath(): string
    {
        return 'id-staging/' . date('Y-m-d') . '/' . self::uuid() . '.jpg';
    }

    public static function stagingSelfiePath(): string
    {
        return 'devotee-selfies/' . date('Y-m-d') . '/' . self::uuid() . '.jpg';
    }

    private static function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
