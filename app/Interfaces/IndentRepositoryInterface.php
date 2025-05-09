<?php
namespace App\Interfaces;

interface IndentRepositoryInterface
{
    public function getIndentApproval($limit = "", $indentRequest = []);

    //creation
    public function addIndent($indentDetails);
    public function addIndentItems($indentDetails, $indent);
    public function addIndentPayments($indentPaymentDetails, $indent);
    public function updateIndentStatus($indent);
    public function addIndentAttachments($files, $attachmentNames, $indent);

    //updation
    public function updateIndent($indentDetails, $id);
    public function updateIndentItems($indentItemDetails, $indent);
    public function updateIndentPayments($indentPaymentDetails, $indent, $addingFlag = false);
    public function updateIndentAttachments($files, $attachmentNames, $attachmentIds, $indent);
    public function updateIndentItemStatus($status, $indentItem, $desc = "");
    public function updateIndentItemToNextApproval($status, $indentItem);

    //dashboard
    public function IndentCountByStatus();
    public function getWeeklyIndentExpense();
    public function getTotalIndentExpense();
}
