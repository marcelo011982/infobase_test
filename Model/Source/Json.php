<?php

namespace Infobase\Import\Model\Source;

use Magento\Framework\Exception\LocalizedException;

class Json extends \Infobase\Import\Model\AbstractSource
{

    public function read($fileName)
    {
        $result = [];
        $contents = $this->readFileContent($fileName);
        $contents = json_decode($contents, true);

        foreach ($contents as $content) {

            foreach ($content as $key => $value) {
                if (!array_key_exists($key, $this->mapFields))
                    throw new LocalizedException(__("There are keys not mapped:" . $key . ", shoud be fname, lname and emailaddress"));
                $row[$this->mapFields[$key]] = $value;


            }
            $row = $this->fillDefaultFields($row);
            $result[] = $row;

        }
        return count($result) ? $result : false;
    }
}
