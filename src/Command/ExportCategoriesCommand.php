<?php

namespace App\Command;

use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use App\Utils\ExportImport\ExportHelpers\ExportCategoryHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportCategoriesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:export:categories';

    /**
     * @var ProductCategoryRepository
     */
    private $repository;

    /**
     * @var ExportCategoryHelper
     */
    private $exportHelper;

    public function __construct(ProductCategoryRepository $repository, ExportCategoryHelper $helper)
    {
        $this->repository = $repository;
        $this->exportHelper = $helper;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Exports categories to specifically named file')
            ->addArgument('fileName', InputArgument::REQUIRED, 'export file name')
            ->addArgument('ids', InputArgument::OPTIONAL, 'category ids to export');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = $input->getArgument('fileName') . '.csv';
        $ids = $input->getArgument('ids') ?
            ['id' => explode(',', $input->getArgument('ids'))]
            : [];
        $categories = $this->repository->findBy($ids);

        $this->exportHelper->setReflectionClass(ProductCategory::class);

        $f = fopen($fileName, 'w');
        fputcsv($f, $this->exportHelper->getTableHeaders());
        foreach ($categories as $category) {
            fputcsv($f, $this->exportHelper->getTableRow($category));
        }
        fclose($f);

        $io->success(
            sprintf('Categories successfully exported to %s', $fileName)
        );
    }
}
