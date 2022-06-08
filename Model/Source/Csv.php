<?php

namespace Infobase\Import\Model\Source;


class Csv extends \Infobase\Import\Model\AbstractSource
{
    public function read($fileName) {
        $result = [];
        $contents = $this->readFileContent($fileName);

        $contents = explode("\n", $contents);
        array_pop($contents);


        foreach ($contents as $key => $value) {
            $contents[$key] = explode(",", $value);
        }
        $headers = array_shift($contents);
        foreach ($headers as $key => $value) {
            if (!array_key_exists($value, $this->mapFields)) {
                return false;
            }
            $headers[$key] = $this->mapFields[$value];
        }

        foreach ($contents as $content) {

            $row = array_combine($headers, $content);
            $row = $this->fillDefaultFields($row);
            $result[] = $row;
        }
        return count($result) ? $result : false;
    }


}
