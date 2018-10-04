<?php

namespace App\Components\Customer\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    private $customer;

    public function getAllCustomersByCompanyId(int $companyId)
    {
        return Customer::where('customers.company_id', $companyId)->get();
    }

//    public function getAllCustomersByCompanyId1(int $companyId)
//    {
//        return Customer::where('customers.company_id', $companyId);
//    }

    public function getOneCustomerById(int $id): Customer
    {
        $this->customer = Customer::where('id', $id)->first();

        return $this->customer;
    }

    public function getOneCustomerByIdUsingWith(int $id)
    {
        $this->customer = Customer::where('id', $id)->first();

        return $this->customer;
    }

    public function getCustomerRelatedDetails(int $id)
    {
        $customer = $this->getOneCustomerById($id);

        return $customer;
    }

    public function update(array $data)
    {
        $this->customer->fill($data);

        $bill_to = "";
        if($this->customer->address1 != ""){
            $bill_to .= $this->customer->address1;
            $bill_to .= "\n";
        }
        if($this->customer->address2!= ""){
            $bill_to .= $this->customer->address2;
            $bill_to .= "\n";
        }
        if($this->customer->postal_code != "" || $this->customer->city != ""){
            $bill_to .= $this->customer->postal_code . " " . $this->customer->city;
            $bill_to .= "\n";
        }
        if($this->customer->province != ""){
            $bill_to .= $this->customer->province . ",";
        }
        if($this->customer->country != ""){
            $bill_to .= $this->customer->country;
        }
        $this->customer->bill_to = $bill_to;

        $this->customer->save();
    }
}