<?php

declare(strict_types=1);

/**
 * @param string $photoB64 base64 JPEG (no data: prefix)
 */
function kdms_render_card_photo_html(string $photoB64, int $height = 80, int $width = 80): void
{
    if ($photoB64 === '') {
        echo '<img src="../assets/img/faces/devotee.ico" alt="Devotee Image" height="', $height, 'px" width="', $width, 'px">';
    } else {
        echo '<img src="data:image/jpeg;base64,', htmlspecialchars($photoB64, ENT_QUOTES, 'UTF-8'), '" alt="Devotee Image" height="', $height, 'px" width="', $width, 'px">';
    }
}
