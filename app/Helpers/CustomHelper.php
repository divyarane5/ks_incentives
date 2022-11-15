<?php

use App\Models\ExpenseVendorMapping;
use App\Models\Vendor;

if (!function_exists('uploadFile')) {
    function uploadFile($file, $filePath)
    {
        $name = $file->getClientOriginalName();
        $path = $file->store($filePath);
        return $path;
    }
}

if (!function_exists('getVendors')) {
    function getVendors($expenseId)
    {
        return ExpenseVendorMapping::join('vendors', 'vendors.id', '=', 'expense_vendor_mapping.vendor_id')
            ->where('expense_vendor_mapping.expense_id', $expenseId)->get();
    }
}

?>
