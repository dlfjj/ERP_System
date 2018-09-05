<?php

namespace App\Shared\Repositories;

use App\Models\CustomerGroup;

class CustomerGroupRepository
{
    public function getGroupAndId(int $companyId)
    {
        return CustomerGroup::where('company_id', $companyId)
            ->pluck('group','id');
    }
}