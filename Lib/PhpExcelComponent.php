<?php

/**
 * XLSX export (PhpSpreadsheet). Replaces legacy PHPExcel-based implementation.
 * Only the methods used by the application are implemented.
 */
class PhpExcelComponent
{
    /** @var \PhpOffice\PhpSpreadsheet\Spreadsheet|null */
    private $spreadsheet;

    /** @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet|null */
    private $sheet;

    public function createExcel(): void
    {
        $this->loadAutoloader();
        $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    private function loadAutoloader(): void
    {
        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class, false)) {
            return;
        }
        $base = dirname(__DIR__);
        $autoload = $base . '/vendor/autoload.php';
        if (is_file($autoload)) {
            require_once $autoload;
            return;
        }
        throw new RuntimeException(
            'Composer autoload not found. Run: composer install (expected at ' . $autoload . ')'
        );
    }

    public function writeCellValue(string $cell, $value): void
    {
        $this->sheet->setCellValue($cell, $value);
    }

    public function fillCellColour(string $cell, string $hexRgb): void
    {
        $color = ltrim($hexRgb, '#');
        if (strlen($color) !== 6) {
            return;
        }
        $this->sheet->getStyle($cell)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FF' . $color);
    }

    public function downloadFile(string $filename = 'export.xlsx'): void
    {
        $filename = preg_replace('/[^-a-zA-Z0-9_.]/', '_', $filename) ?: 'export.xlsx';
        if (strtolower(substr($filename, -5)) !== '.xlsx') {
            $filename .= '.xlsx';
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
