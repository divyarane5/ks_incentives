<?php

namespace App\Helpers;

use Maatwebsite\Excel\HeadingRowImport;

class ExcelSanitizer
{
    /**
     * Sanitize headers: lowercase, replace spaces/special chars with underscores
     */
    public static function sanitizeHeaders($filePath): array
    {
        $headings = (new HeadingRowImport)->toArray($filePath)[0][0] ?? [];

        $cleanHeaders = [];
        foreach ($headings as $header) {
            $h = strtolower(trim($header));
            $h = preg_replace('/[^a-z0-9_]/', '_', $h);
            $h = preg_replace('/_+/', '_', $h); // collapse multiple underscores
            $cleanHeaders[] = $h;
        }

        return $cleanHeaders;
    }

    /**
     * Optional: sanitize a row of data
     */
    public static function sanitizeRow(array $row): array
    {
        return array_map(function($value) {
            if (is_string($value)) {
                $value = trim($value);
                $value = preg_replace('/\x{200B}/u', '', $value); // remove zero-width spaces
            }
            return $value;
        }, $row);
    }
}
