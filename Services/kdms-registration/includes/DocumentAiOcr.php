<?php

declare(strict_types=1);

namespace KdmsRegistration;

use Google\Cloud\DocumentAI\V1\DocumentProcessorServiceClient;
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
            $client = new DocumentProcessorServiceClient(self::clientConfigForProcessor($processor));
            $raw = (new RawDocument())
                ->setContent($imageBytes)
                ->setMimeType($mimeType);

            $processorName = self::resolveProcessorResourceName(trim($processor));

            // Client API: processDocument(string $name, array{rawDocument?: RawDocument} $optionalArgs)
            $response = $client->processDocument($processorName, [
                'rawDocument' => $raw,
            ]);
            $document = $response->getDocument();
            $mapped = $empty;

            $entityCount = 0;
            foreach ($document->getEntities() as $entity) {
                $entityCount++;
                $type = strtolower((string) $entity->getType());
                $text = self::entityText($entity);
                $conf = (float) $entity->getConfidence();
                if ($text === '') {
                    continue;
                }

                switch ($type) {
                    case 'given_name':
                    case 'first_name':
                    case 'devotee_first_name':
                        self::setField($mapped, 'Devotee_First_Name', RegistrationFields::sanitizeName($text), $conf);
                        break;
                    case 'family_name':
                    case 'last_name':
                    case 'surname':
                    case 'devotee_last_name':
                        self::setField($mapped, 'Devotee_Last_Name', RegistrationFields::sanitizeName($text), $conf);
                        break;
                    case 'document_id':
                    case 'id_number':
                    case 'devotee_id_number':
                        self::setField($mapped, 'Devotee_ID_Number', $text, $conf);
                        break;
                    case 'birth_date':
                    case 'date_of_birth':
                    case 'dob':
                    case 'devotee_dob':
                        $iso = RegistrationFields::parseDate($text);
                        self::setField(
                            $mapped,
                            'Devotee_DOB',
                            $iso !== '' ? RegistrationFields::formatDobDisplay($iso) : null,
                            $conf
                        );
                        break;
                    case 'address':
                    case 'full_address':
                    case 'address_line_1':
                    case 'devotee_address_1':
                        self::setField($mapped, 'Devotee_Address_1', RegistrationFields::sanitizeShort($text, 100), $conf);
                        break;
                    case 'address_line_2':
                    case 'devotee_address_2':
                        self::setField($mapped, 'Devotee_Address_2', RegistrationFields::sanitizeShort($text, 100), $conf);
                        break;
                    case 'city':
                    case 'devotee_station':
                        self::setField($mapped, 'Devotee_Station', RegistrationFields::sanitizeShort($text, 50), $conf);
                        break;
                    case 'state':
                    case 'devotee_state':
                        self::setField($mapped, 'Devotee_State', RegistrationFields::sanitizeShort($text, 25), $conf);
                        break;
                    case 'zip_code':
                    case 'postal_code':
                    case 'devotee_zip':
                        self::setField($mapped, 'Devotee_Zip', RegistrationFields::sanitizeZip($text), $conf);
                        break;
                    case 'gender':
                    case 'sex':
                        self::setField($mapped, 'Devotee_Gender', RegistrationFields::sanitizeGender($text), $conf);
                        break;
                    case 'email':
                    case 'email_address':
                        self::setField($mapped, 'Devotee_Email', RegistrationFields::sanitizeEmail($text), $conf);
                        break;
                    default:
                        break;
                }
            }

            if ($entityCount === 0) {
                $processorUsed = self::resolveProcessorResourceName(trim($processor));
                kdms_log('WARNING', 'Document AI returned no entities', [
                    'processor' => $processorUsed,
                    'entity_count' => 0,
                ]);
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
            'Devotee_Address_2' => ['value' => null, 'confidence' => 0],
            'Devotee_Station' => ['value' => null, 'confidence' => 0],
            'Devotee_State' => ['value' => null, 'confidence' => 0],
            'Devotee_Zip' => ['value' => null, 'confidence' => 0],
            'Devotee_Gender' => ['value' => null, 'confidence' => 0],
            'Devotee_Email' => ['value' => null, 'confidence' => 0],
        ];
    }

    /**
     * @param array<string, array{value: ?string, confidence: float}> $mapped
     */
    private static function setField(array &$mapped, string $key, ?string $value, float $conf): void
    {
        if ($value === null || $value === '') {
            return;
        }
        $mapped[$key] = ['value' => $value, 'confidence' => $conf];
    }

    private static function resolveProcessorResourceName(string $processor): string
    {
        $processor = trim($processor);
        $version = getenv('DOCUMENT_AI_PROCESSOR_VERSION');
        if (!is_string($version) || trim($version) === '') {
            return $processor;
        }
        $version = trim($version);
        if (str_contains($processor, '/processorVersions/')) {
            return $processor;
        }

        return rtrim($processor, '/') . '/processorVersions/' . $version;
    }

    /**
     * @param object $entity Document AI Entity message
     */
    private static function entityText(object $entity): string
    {
        $text = trim((string) $entity->getMentionText());
        if ($text !== '') {
            return $text;
        }
        if (!method_exists($entity, 'getNormalizedValue')) {
            return '';
        }
        $normalized = $entity->getNormalizedValue();
        if ($normalized === null) {
            return '';
        }
        if (method_exists($normalized, 'getText')) {
            $text = trim((string) $normalized->getText());
            if ($text !== '') {
                return $text;
            }
        }
        if (method_exists($normalized, 'getDateValue')) {
            $date = $normalized->getDateValue();
            if ($date !== null && method_exists($date, 'getYear')) {
                $y = (int) $date->getYear();
                $m = (int) $date->getMonth();
                $d = (int) $date->getDay();
                if ($y > 0 && $m > 0 && $d > 0) {
                    return sprintf('%04d-%02d-%02d', $y, $m, $d);
                }
            }
        }

        return '';
    }

    /**
     * @return array<string, string>
     */
    private static function clientConfigForProcessor(string $processorName): array
    {
        $location = getenv('DOCUMENT_AI_LOCATION');
        if (!is_string($location) || trim($location) === '') {
            if (preg_match('#/locations/([a-z0-9-]+)/processors/#', $processorName, $m)) {
                $location = $m[1];
            } else {
                $location = 'us';
            }
        }
        $location = trim($location);
        if ($location === 'us') {
            return [];
        }

        return ['apiEndpoint' => $location . '-documentai.googleapis.com'];
    }

}
