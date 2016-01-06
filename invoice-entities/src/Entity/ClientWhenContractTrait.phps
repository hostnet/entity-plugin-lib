<?php
namespace Hostnet\Invoice\Entity;

trait ClientWhenContractTrait
{
    abstract public function getContracts();

    public function getInvoicedContracts() {
        $contracts = [];
        foreach ($this->getContracts() as $contract) {
            if (! empty($contract->getInvoices())) {
                $contracts[] = $contract;
            }
        }
        return $contracts;
    }
}