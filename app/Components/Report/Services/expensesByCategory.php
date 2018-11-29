<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/28
 * Time: 5:18 PM
 */

namespace App\Components\Report\Services;


use App\ChartOfAccount;
use App\Expense;

class expensesByCategory
{
    public function getExpensesByCategory(){
        $date_start 	 = date("Y-01-01");
        $date_end 		 = date("Y-m-t");
        $company_id 	 = return_company_id();

        $categories 	 = ChartOfAccount::where('company_id', $company_id)
            ->where('type', 'Expense')
            ->orderBy('name')
            ->get();
        $expenses   = Expense::where('company_id',$company_id)
            ->where('amount_conv',0)
            ->get();

        $category_ids = ChartOfAccount::where('company_id', return_company_id())
            ->where('type','Expense')
            ->pluck('id');
        $expense_total = Expense::where('company_id', return_company_id())
            ->where('date_created','>=',$date_start)
            ->where('date_created','<=',$date_end)
            ->whereIn('account_id', $category_ids)
            ->sum('amount_conv');
        foreach($categories as $category){
            $expenses_category = Expense::where('account_id',$category->id)
                ->where('company_id', return_company_id())
                ->where('date_created','>=',$date_start)
                ->where('date_created','<=',$date_end)
                ->whereIn('account_id', $category_ids)
                ->get();
            $category_total = Expense::where('account_id',$category->id)
                ->where('company_id', return_company_id())
                ->where('date_created','>=',$date_start)
                ->where('date_created','<=',$date_end)
                ->whereIn('account_id', $category_ids)
                ->sum('amount_conv');
        }
        return compact('categories','date_start','date_end','company_id','expenses','category_ids','expense_total','category_total','expenses_category');
    }
}