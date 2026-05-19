<?php

declare(strict_types=1);

namespace KdmsRegistration;

final class MockOcrService
{
    /**
     * @return array<string, array{value: null, confidence: 0}>
     */
    public static function extract(): array
    {
        return DocumentAiOcr::emptyFields();
    }
}
