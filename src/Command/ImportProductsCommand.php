<?php

namespace App\Command;

use App\Entity\Product;
use App\Utils\ExportImport\ImportProductHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportProductsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:import:products';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ImportProductHelper
     */
    private $helper;

    public function __construct(ImportProductHelper $helper, EntityManagerInterface $em)
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

        $this->helper->configureImport($filePath, Product::class);
        $io->writeln(
            var_dump($this->helper->makeEntity(['name' => 'cos', 'description' => 'cos']))
        );
//        $row = 1;
//        if (($handle = fopen($filePath, "r")) !== false) {
//
//            $io->error();
//            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
//                if ($row !== 1) {
//                    $product = new Product();
//                    $product->setName($data[1]);
//                    $product->setDescription($data[2]);
//                    $product->setCreationDate(
//                        \DateTime::createFromFormat('Y-m-d H:i:s', $data[3])
//                    );
//                    $product->setModificationDate(
//                        \DateTime::createFromFormat('Y-m-d H:i:s', $data[4])
//                    );
//
//                    $category = new ProductCategory();
//                    $category->setName($data[5]);
//                    $this->em->persist($category);
//
//                    $product->setCategory($category);
//                    $this->em->persist($product);
//                }
//                $row++;
//            }
//            $this->em->flush();
//            fclose($handle);
//        }

        $io->success(
            sprintf('Products successfully imported from %s', $filePath)
        );
    }
}
