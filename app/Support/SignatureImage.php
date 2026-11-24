<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Saves a signature or stamp photo as a clean PNG: paper-white background made see-through, edges trimmed. */
class SignatureImage
{
    public static function store(UploadedFile $file, string $dir = 'letter-signatures'): string
    {
        $png = function_exists('imagecreatefromstring') ? self::clean((string) file_get_contents($file->getRealPath())) : null;
        if ($png === null) {
            return $file->store($dir, 'public'); // no GD: keep the file as uploaded
        }
        $path = $dir.'/'.Str::random(24).'.png';
        Storage::disk('public')->put($path, $png);

        return $path;
    }

    /** White/light paper -> transparent; keeps ink colour. Returns PNG bytes, or null if the image can't be read. */
    public static function clean(string $bytes): ?string
    {
        $src = @imagecreatefromstring($bytes);
        if (! $src) {
            return null;
        }
        $w = imagesx($src);
        $h = imagesy($src);
        $scale = min(1, 900 / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));
        $img = imagecreatetruecolor($nw, $nh);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        imagecopyresampled($img, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($src);

        // Paper level = a bright corner-ish sample, so off-white scans also clean up.
        $paper = 0;
        foreach ([[0, 0], [$nw - 1, 0], [0, $nh - 1], [$nw - 1, $nh - 1]] as [$x, $y]) {
            $c = imagecolorsforindex($img, imagecolorat($img, $x, $y));
            $paper = max($paper, min($c['red'], $c['green'], $c['blue']));
        }
        $paper = max(170, $paper);
        $minX = $nw; $minY = $nh; $maxX = -1; $maxY = -1;
        for ($y = 0; $y < $nh; $y++) {
            for ($x = 0; $x < $nw; $x++) {
                $c = imagecolorsforindex($img, imagecolorat($img, $x, $y));
                if ($c['alpha'] >= 120) {
                    continue; // already transparent
                }
                $light = min($c['red'], $c['green'], $c['blue']);
                $ink = max(0, min(1, ($paper - $light) / max(1, $paper - 40))); // 0 = paper, 1 = full ink
                if ($ink < 0.08) {
                    imagesetpixel($img, $x, $y, imagecolorallocatealpha($img, 255, 255, 255, 127));
                    continue;
                }
                $a = (int) round(127 * (1 - min(1, $ink * 1.4)));
                imagesetpixel($img, $x, $y, imagecolorallocatealpha($img, $c['red'], $c['green'], $c['blue'], $a));
                $minX = min($minX, $x); $minY = min($minY, $y); $maxX = max($maxX, $x); $maxY = max($maxY, $y);
            }
        }
        if ($maxX >= $minX && $maxY >= $minY) {
            $pad = 6;
            $minX = max(0, $minX - $pad); $minY = max(0, $minY - $pad);
            $maxX = min($nw - 1, $maxX + $pad); $maxY = min($nh - 1, $maxY + $pad);
            $cut = imagecreatetruecolor($maxX - $minX + 1, $maxY - $minY + 1);
            imagealphablending($cut, false);
            imagesavealpha($cut, true);
            imagecopy($cut, $img, 0, 0, $minX, $minY, $maxX - $minX + 1, $maxY - $minY + 1);
            imagedestroy($img);
            $img = $cut;
        }
        ob_start();
        imagepng($img, null, 6);
        imagedestroy($img);

        return (string) ob_get_clean();
    }
}
