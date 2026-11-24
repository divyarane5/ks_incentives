<?php

namespace App\Support;

use OpenSpout\Reader\CSV\Options as CsvOptions;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\XLSX\Options as XlsxOptions;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

/**
 * Streams rows from an .xlsx or .csv file without loading the whole file into memory,
 * so a 100,000-row Leadrat export reads as easily as a 20-row sheet.
 */
class Spreadsheet
{
    /**
     * Rows of the first sheet, header row first. Each row is a list of cell values
     * (string, int, float, bool, DateTimeInterface or null).
     *
     * @return \Generator<int, array<int, mixed>>
     */
    public static function rows(string $path): \Generator
    {
        $reader = self::reader($path);
        $reader->open($path);

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    yield $row->toArray();
                }
                break; // first sheet only
            }
        } finally {
            $reader->close();
        }
    }

    private static function reader(string $path): ReaderInterface
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($ext === 'csv' || $ext === 'txt') {
            $options = new CsvOptions;
            $options->SHOULD_PRESERVE_EMPTY_ROWS = true; // keep row numbers the same as in Excel
            $first = (string) @file_get_contents($path, false, null, 0, 4096);
            // Excel in some regions saves CSV with semicolons
            if (substr_count($first, ';') > substr_count($first, ',')) {
                $options->FIELD_DELIMITER = ';';
            }

            return new CsvReader($options);
        }

        $options = new XlsxOptions;
        $options->SHOULD_PRESERVE_EMPTY_ROWS = true;

        return new XlsxReader($options);
    }

    public static function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && trim((string) ($cell instanceof \DateTimeInterface ? 'x' : $cell)) !== '') {
                return false;
            }
        }

        return true;
    }
}
