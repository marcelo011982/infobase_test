<?php

namespace Infobase\Import\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Infobase\Import\Model\Import as ImportCustomer;
use \Infobase\Import\Model\Source\Csv;
use \Infobase\Import\Model\Source\Json;


class Import extends Command
{
    private $state;
    protected $typeImport;
    private $fileToImport;
    private $importCustomer;

    private $json;
    private $csv;


    const PROFILE_CSV_TYPE = "sample-csv";
    const PROFILE_JSON_TYPE = "sample-json";
    const COMMAND_OPTIONS = "options";

    public function __construct(State $state, ImportCustomer $importCustomer, Csv $csv, Json $json, string $name = null)
    {
        $this->state = $state;
        $this->importCustomer = $importCustomer;
        $this->csv = $csv;
        $this->json = $json;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('customer:import');
        $this->setDescription('Customer Import');
        $this->setDefinition($this->getInputList());
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode(Area::AREA_GLOBAL);
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {

        }
        $options = $input->getArgument(self::COMMAND_OPTIONS);

        if (!$this->validateOptions($options)) {
            $output->writeln('<error> Options should have two parameters: ' . self::PROFILE_JSON_TYPE . ' or ' . self::PROFILE_CSV_TYPE . ' followed by the name of the file</error>');
            return;
        }
        try {
            switch ($this->typeImport):
                case self::PROFILE_CSV_TYPE :
                    $customerData = $this->csv->read($this->fileToImport);
                    break;
                case self::PROFILE_JSON_TYPE :
                    $customerData = $this->json->read($this->fileToImport);
                    break;
            endswitch;
        } catch (\Magento\Framework\Exception\FileSystemException $fileSystemException) {
            $output->writeln('<error> file could not be loaded, checkf if it exist at root folder </error>');
            return;
        } catch (Magento\Framework\Exception\LocalizedException $e) {
            $output->writeln('<error>  File could not be parsed, check file content to check the content </error>');
        }

        if (!$customerData) {
            $output->writeln('<error> File could not be parsed, check file content to check the content</error>');
            return;
        }


        try {
            $this->importCustomer->execute($customerData);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }


    }


    private function validateOptions($options)
    {
        if (count($options) == 2) {
            switch ($options[0]) :
                case self::PROFILE_CSV_TYPE:
                case self::PROFILE_JSON_TYPE:
                    $this->typeImport = $options[0];
                    break;
                default:
                    return false;

            endswitch;


            $this->fileToImport = $options[1];
            return true;
        }
        return true;
    }

    private function validateFile()
    {

    }


    public function getInputList()
    {
        return [
            new InputArgument(
                self::COMMAND_OPTIONS,
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'Import command '
            ),
        ];
    }
}
