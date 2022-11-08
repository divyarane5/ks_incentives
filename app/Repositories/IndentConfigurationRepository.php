<?php
namespace App\Repositories;

use App\Interfaces\IndentConfigurationRepositoryInterface;
use App\Models\IndentConfiguration;
use DB;

class IndentConfigurationRepository implements IndentConfigurationRepositoryInterface
{
    public function getIndentConfigurations($userId = "")
    {
        $indentConfiguration = IndentConfiguration::select(['indent_configurations.id', 'users.name as user', 'expenses.name as expense', 'monthly_limit', 'indent_limit', 'indent_configurations.created_at'])
                        ->join('users', 'indent_configurations.user_id', '=', 'users.id')
                        ->join('expenses', 'indent_configurations.expense_id', '=', 'expenses.id');
        if ($userId != "") {
            $indentConfiguration = $indentConfiguration->where('user_id', $userId);
        }
        return $indentConfiguration;
    }
}
