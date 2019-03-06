<?php

namespace App\Command;

use App\Entity\ProductCategory;
use App\Utils\ExportImport\AbastractImportEntityHelper;
use App\Utils\ExportImport\ImportHelpers\CategoryImportHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCategoriesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:import:categories';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AbastractImportEntityHelper
     */
    private $helper;

    public function __construct(CategoryImportHelper $helper, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->helper = $helper;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Exports products to specifically named file')
            ->addArgument('path', InputArgument::REQUIRED, 'path of file to import');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('path');

        $this->helper->configureImport($filePath, ProductCategory::class);
        $this->helper->importData();


        $io->success(
            sprintf('Products successfully imported from %s', $filePath)
        );
    }
}
