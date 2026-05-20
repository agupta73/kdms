<?php

declare(strict_types=1);

namespace KdmsRegistration;

final class ImageResize
{
    /** Max longest edge before resize (keeps phone photos under PHP upload limits). */
    public const MAX_EDGE = 1600;

    /** Re-encode JPEG above this size (bytes). */
    public const JPEG_REENCODE_ABOVE_BYTES = 900_000;

    /**
     * @return array{bytes: string, mime: string}|null
     */
    public static function normalizeForOcr(string $bytes, string $mime): ?array
    {
        if (!function_exists('imagecreatefromstring')) {
            return ['bytes' => $bytes, 'mime' => $mime];
        }

        $img = @imagecreatefromstring($bytes);
        if ($img === false) {
            return ['bytes' => $bytes, 'mime' => $mime];
        }

        $w = imagesx($img);
        $h = imagesy($img);
        $maxEdge = max($w, $h);
        $needsResize = $maxEdge > self::MAX_EDGE;
        $needsReencode = $mime === 'image/jpeg' && strlen($bytes) > self::JPEG_REENCODE_ABOVE_BYTES;

        if (!$needsResize && !$needsReencode) {
            imagedestroy($img);

            return ['bytes' => $bytes, 'mime' => $mime];
        }

        if ($needsResize) {
            $scale = self::MAX_EDGE / $maxEdge;
            $nw = (int) round($w * $scale);
            $nh = (int) round($h * $scale);
            $resized = imagecreatetruecolor($nw, $nh);
            if ($mime === 'image/png') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagedestroy($img);
            $img = $resized;
        }

        ob_start();
        if ($mime === 'image/png') {
            imagepng($img, null, 6);
            $outMime = 'image/png';
        } else {
            imagejpeg($img, null, 82);
            $outMime = 'image/jpeg';
        }
        $out = ob_get_clean();
        imagedestroy($img);

        if (!is_string($out) || $out === '') {
            return null;
        }

        return ['bytes' => $out, 'mime' => $outMime];
    }
}
