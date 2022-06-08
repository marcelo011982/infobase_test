<?php

namespace Infobase\Import\Model;

use \Magento\CustomerImportExport\Model\Import\CustomerComposite;
use \Infobase\Import\Model\Import\Customer;
use Magento\Indexer\Model\IndexerFactory;


class Import
{
    protected $customerComposite;
    protected $customer;
    protected $indexerFactory;


    public function __construct(
        CustomerComposite $customerComposite,
        Customer          $customer,
        IndexerFactory    $indexerFactory

    )
    {
        $this->indexerFactory = $indexerFactory;
        $this->customerComposite = $customerComposite;
        $this->customer = $customer;

    }

    public function execute($rows)
    {
        foreach ($rows as $row) {
            $this->customer->execute($row);
        }
        $indexer = $this->indexerFactory->create()->load('customer_grid');
        $indexer->reindexAll();
    }

}
