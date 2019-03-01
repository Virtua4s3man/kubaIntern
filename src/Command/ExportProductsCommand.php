<?php

namespace App\Command;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Utils\ExportEntityHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ExportProductsCommand extends Command
{
    protected static $defaultName = 'app:export:products';

    private $repository;
    private $exportHelper;

    public function __construct(ProductRepository $repository, ExportEntityHelper $helper)
    {
        $this->repository = $repository;
        $this->exportHelper = $helper;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('Exports products to specifically named file')
            ->addArgument('fileName', InputArgument::REQUIRED, 'export file name')
            ->addArgument('ids', InputArgument::OPTIONAL, 'product ids to export');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = $input->getArgument('fileName') . '.csv';
        $ids = $input->getArgument('ids') ?
            ['id' => explode(',', $input->getArgument('ids'))]
            : [];
        $products = $this->repository->findBy($ids);

        $this->exportHelper->setReflectionClass(Product::class);

        $f = fopen($fileName, 'w');
        fputcsv($f, $this->exportHelper->getTableHeaders());
        foreach ($products as $product) {
            fputcsv($f, $this->exportHelper->getTableRow($product));
        }
        fclose($f);

        $io->success(
            sprintf('Products successfully exported to %s', $fileName)
        );
    }
}
