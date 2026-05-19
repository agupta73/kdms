<?php

declare(strict_types=1);

namespace KdmsRegistration;

use Google\Cloud\DocumentAI\V1\DocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\ProcessRequest;
use Google\Cloud\DocumentAI\V1\RawDocument;
use Throwable;

final class DocumentAiOcr
{
    /**
     * @return array<string, array{value: ?string, confidence: float}>
     */
    public static function extract(string $imageBytes, string $mimeType): array
    {
        $empty = self::emptyFields();

        $processor = getenv('DOCUMENT_AI_PROCESSOR_ID');
        if (!is_string($processor) || trim($processor) === '' || strtolower(trim($processor)) === 'mock') {
            return MockOcrService::extract();
        }

        if (!class_exists(DocumentProcessorServiceClient::class)) {
            kdms_log('WARNING', 'Document AI client not available');

            return $empty;
        }

        try {
            $client = new DocumentProcessorServiceClient();
            $raw = (new RawDocument())
                ->setContent($imageBytes)
                ->setMimeType($mimeType);

            $request = (new ProcessRequest())
                ->setName($processor)
                ->setRawDocument($raw);

            $response = $client->processDocument($request);
            $document = $response->getDocument();
            $mapped = $empty;

            foreach ($document->getEntities() as $entity) {
                $type = strtolower((string) $entity->getType());
                $text = trim((string) $entity->getMentionText());
                $conf = (float) $entity->getConfidence();

                switch ($type) {
                    case 'given_name':
                    case 'first_name':
                        $mapped['Devotee_First_Name'] = ['value' => $text, 'confidence' => $conf];
                        break;
                    case 'family_name':
                    case 'last_name':
                    case 'surname':
                        $mapped['Devotee_Last_Name'] = ['value' => $text, 'confidence' => $conf];
                        break;
                    case 'document_id':
                    case 'id_number':
                        $mapped['Devotee_ID_Number'] = ['value' => $text, 'confidence' => $conf];
                        break;
                    case 'birth_date':
                    case 'date_of_birth':
                        $mapped['Devotee_DOB'] = [
                            'value' => self::normalizeDate($text),
                            'confidence' => $conf,
                        ];
                        break;
                    case 'address':
                        $mapped['Devotee_Address_1'] = ['value' => $text, 'confidence' => $conf];
                        break;
                    default:
                        break;
                }
            }

            return $mapped;
        } catch (Throwable $e) {
            kdms_log('WARNING', 'Document AI OCR failed', ['error' => $e->getMessage()]);

            return $empty;
        }
    }

    /**
     * @return array<string, array{value: null, confidence: 0}>
     */
    public static function emptyFields(): array
    {
        return [
            'Devotee_First_Name' => ['value' => null, 'confidence' => 0],
            'Devotee_Last_Name' => ['value' => null, 'confidence' => 0],
            'Devotee_ID_Number' => ['value' => null, 'confidence' => 0],
            'Devotee_DOB' => ['value' => null, 'confidence' => 0],
            'Devotee_Address_1' => ['value' => null, 'confidence' => 0],
        ];
    }

    private static function normalizeDate(string $text): ?string
    {
        $text = trim($text);
        if ($text === '') {
            return null;
        }
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $fmt) {
            $d = \DateTime::createFromFormat($fmt, $text);
            if ($d && $d->format($fmt) === $text) {
                return $d->format('Y-m-d');
            }
        }
        $ts = strtotime($text);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }

        return null;
    }
}
