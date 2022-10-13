<?php

if (!function_exists('uploadFile')) {
    function uploadFile($file, $filePath)
    {
        $name = $file->getClientOriginalName();
        $path = $file->store($filePath);
        return $path;
    }
}

?>
