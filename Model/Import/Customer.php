<?php

namespace Infobase\Import\Model\Import;


use \Magento\CustomerImportExport\Model\Import\Customer as CustomerImport;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

class Customer extends CustomerImport
{

    public function __construct(
        \Magento\Framework\Stdlib\StringUtils                                            $string,
        \Magento\Framework\App\Config\ScopeConfigInterface                               $scopeConfig,
        \Magento\ImportExport\Model\ImportFactory                                        $importFactory,
        \Magento\ImportExport\Model\ResourceModel\Helper                                 $resourceHelper,
        \Magento\Framework\App\ResourceConnection                                        $resource,
        ProcessingErrorAggregatorInterface                                               $errorAggregator,
        \Magento\Store\Model\StoreManagerInterface                                       $storeManager,
        \Magento\ImportExport\Model\Export\Factory                                       $collectionFactory,
        \Magento\Eav\Model\Config                                                        $eavConfig,
        \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $storageFactory,
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory                $attrCollectionFactory,
        \Magento\Customer\Model\CustomerFactory                                          $customerFactory,
        array                                                                            $data = [],

    )
    {

        parent::__construct(
            $string, $scopeConfig, $importFactory, $resourceHelper, $resource, $errorAggregator, $storeManager, $collectionFactory, $eavConfig, $storageFactory, $attrCollectionFactory, $customerFactory, $data);
    }

    public function execute(array $data)
    {

        $this->prepareCustomerData($data);

        $processedData = $this->_prepareDataForUpdate($data);


        $entitiesToCreate[] = $processedData[self::ENTITIES_TO_CREATE_KEY];
        $entitiesToUpdate[] = $processedData[self::ENTITIES_TO_UPDATE_KEY];
        $entitiesToDelete = [];
        $attributesToSave = [];

        foreach ($processedData[self::ATTRIBUTES_TO_SAVE_KEY] as $tableName => $customerAttributes) {
            if (!isset($attributesToSave[$tableName])) {
                $attributesToSave[$tableName] = [];
            }
            $attributes = array_diff_key($attributesToSave[$tableName], $customerAttributes);
            $attributesToSave[$tableName] = $attributes + $customerAttributes;
        }

        $entitiesToCreate = array_merge([], ...$entitiesToCreate);
        $entitiesToUpdate = array_merge([], ...$entitiesToUpdate);
        $this->updateItemsCounterStats($entitiesToCreate, $entitiesToUpdate, $entitiesToDelete);


        if ($entitiesToCreate || $entitiesToUpdate) {
            $this->_saveCustomerEntities($entitiesToCreate, $entitiesToUpdate);
        }
        if ($attributesToSave) {
            $this->_saveCustomerAttributes($attributesToSave);
        }


    }

}
