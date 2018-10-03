<?php

namespace App\Components\Customer\Services;

use App\Components\Exceptions\StatusChangeDeniedException;
use App\Components\Customer\Repositories\CustomerRepository;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerContact;
use App\Models\PaymentTerm;
use App\Models\Taxcode;
use App\Models\User;
use App\Models\ValueList;
use App\Shared\Repositories\CustomerGroupRepository;

class CustomerService
{
    private $customerRepository;
    private $customerGroupRepository;

    public function __construct(CustomerRepository $customerRepository, CustomerGroupRepository $customerGroupRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->customerGroupRepository = $customerGroupRepository;
    }

    public function getAllCustomersByCompanyId(int $customerId): array
    {
        $customers = $this->customerRepository->getAllCustomersByCompanyId($customerId);

        return [
            'customers' => $customers,
            'outstanding_balance_currency_code' => 'USD',
            'outstanding_balance_amount' => 0,
        ];
    }

    /**
     * @param int $id
     * @return array
     */
    public function getOneCustomerById(int $id)
    {
        $currentUserId = currentUserId();
        $currentUserCompanyId = currentUserCompanyId();

        $customer = $this->customerRepository->getOneCustomerByIdUsingWith($id);

        $select_contacts = CustomerContact::where('customer_id', $customer->id)->pluck('contact_name', 'id');

        if ($customer->company_id != $currentUserCompanyId) {
            die("Access violation. Click <a href='/'>here</a> to get back.");
        }

        $overdue_currency = $outstanding_currency = User::Leftjoin('companies', 'users.company_id', '=', 'companies.id')->where('users.id', $currentUserId)->pluck('companies.currency_code');

        $outstandings 		   = $customer->getOutstandingMoney($outstanding_currency[0]);
        $overdue 		= $customer->getOverdueMoney($outstanding_currency[0]);

        $created_by_user = User::find($customer->created_by)->username;
        $updated_by_user = User::find($customer->updated_by)->username;;

        return array_merge(
            [
                'select_contacts' => $select_contacts,
                'customer' => $customer,
                'overdue_currency' => $overdue_currency,
                'outstandings' => $outstandings,
                'overdue' => $overdue,
                'created_by_user' => $created_by_user,
                'updated_by_user' => $updated_by_user,
                'outstanding_currency' => $outstanding_currency
            ],
            $this->getProductRelated($currentUserId, $currentUserCompanyId));
    }

    public function update(int $id, array $data, string $status)
    {
        $customer = $this->customerRepository->getOneCustomerById($id);

        if ($customer->status != $status && !has_role('customers_change_status')) {
            throw new StatusChangeDeniedException();
        }

        $this->customerRepository->update($data);
    }

    public function getContactById(int $id)
    {
        $customer_contact = CustomerContact::findOrFail($id);
        $customer = Customer::findOrFail($customer_contact->customer_id);

        $select_payment_terms = ValueList::where('uid', '=', 'PAYMENT_TERMS')->orderBy('name', 'asc')->pluck('name', 'name');
        $select_currency_codes = ValueList::where('uid', '=', 'CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name', 'name');

        return [
            'customer_contact' => $customer_contact,
            'customer' => $customer,
            'select_payment_terms' => $select_payment_terms,
            'select_currency_codes' => $select_currency_codes,
        ];
    }

    public function updateContactById(int $id, array $data): int
    {
        $customer_contact = CustomerContact::findOrFail($id);
        $customer = Customer::findOrFail($customer_contact->customer_id);

        $new_password = "";
        if ($data['reset_password']) {
            $new_password = $data['reset_password'];
            if ($new_password != "") {
                $new_password = Hash::make($new_password);
            }
        }
        unset($data['reset_password']);

        $customer_contact->fill($data);
        if ($new_password != "") {
            $customer_contact->password = $new_password;
        }
        $customer_contact->save();

        return $customer->id;
    }

    public function getAddressById(int $id)
    {
        $customer_address = CustomerAddress::findOrFail($id);
        $customer = Customer::findOrFail($customer_address->customer_id);

        $select_payment_terms = ValueList::where('uid', '=', 'PAYMENT_TERMS')->orderBy('name', 'asc')->pluck('name', 'name');
        $select_currency_codes = ValueList::where('uid', '=', 'CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name', 'name');

        return [
            'customer' => $customer,
            'customer_address' => $customer_address,
            'select_currency_codes' => $select_currency_codes,
            'select_payment_terms' => $select_payment_terms,
        ];
    }

    public function updateAddressById(int $id, array $data): int
    {
        $customer_address = CustomerAddress::findOrFail($id);
        $customer = Customer::findOrFail($customer_address->customer_id);
        $customer_address->fill($data);
        $customer_address->save();

        return $customer->id;
    }

    public function getProductRelated(int $currentUserId, int $companyId)
    {
        $selectGroups = $this->customerGroupRepository->getGroupAndId($companyId);

        $select_users = User::where('company_id', $companyId)->pluck('username', 'id');

        $select_payment_terms = PaymentTerm::orderBy('name', 'asc')->pluck('name', 'name');
        $select_currency_codes = ValueList::where('uid', '=', 'CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name', 'name');
        $select_taxcodes = Taxcode::orderBy('sort_no', 'asc')->pluck('name', 'id');

        $overdue_currency = $outstanding_currency = User::Leftjoin('companies', 'users.company_id', '=', 'companies.id')->where('users.id', $currentUserId)->pluck('companies.currency_code');

        return [
            'select_currency_codes' => $select_currency_codes,
            'select_payment_terms' => $select_payment_terms,
            'select_taxcodes' => $select_taxcodes,
            'select_groups' => $selectGroups,
            'select_users' => $select_users,
            'overdue_currency' => $overdue_currency,
            'outstanding_currency' => $outstanding_currency
        ];
    }
}