<?php

namespace Infobase\Import\Model;

use Magento\Framework\Filesystem;
 use Magento\Framework\App\Filesystem\DirectoryList;

abstract class AbstractSource
{
    protected $fileSystem;
    protected $stream;
    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;


    }

    protected $mapFields = [
        "fname" => "firstname",
        "lname" => "lastname",
        "emailaddress" => "email"
    ];

    protected function readFileContent($fileName)
    {
        $directory = $this->fileSystem->getDirectoryRead(DirectoryList::ROOT);
        $stream = $directory->openFile($fileName);
        return $stream->readAll();
    }
    public abstract function read($fileName);
    protected function fillDefaultFields($customFields)
    {

        $defaultFields = [
            '_website' => 'base',
            '_store' => 'default',
            'group_id' => 1,
            'store_id' => 1,
            'website_id' => 1
        ];
        return array_merge($customFields, $defaultFields);


    }
}
