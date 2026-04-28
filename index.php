<?php

/**
 * Document root has no landing page at `/`; UI lives under `/UI/`.
 * Redirect avoids Apache 403 on `/` when DirectoryIndex is missing.
 */
header('Location: /UI/login.php', true, 302);
exit;
