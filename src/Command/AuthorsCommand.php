<?php

namespace App\Command;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:authors';

    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer, AuthorRepository $authorRepository)
    {
        parent::__construct();
        $this->serializer = $serializer;
        $this->authorRepository = $authorRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Shows authors')
            ->addOption(
                'char',
                null,
                InputOption::VALUE_OPTIONAL,
                'Finds authors which surnames strats with passed letter'
            )
            ->addOption('save', null, InputOption::VALUE_OPTIONAL, 'Saves authors to a txt file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $char = $input->getOption('char');
        $save = $input->getOption('save');

        $authors = [];
        if ($char) {
            if (strlen($char) !== 1) {
                $io->error('argument should have length equal 1');
                return;
            }
            if (!ctype_alpha($char)) {
                $io->error('argument should be valid alphabet sign');
                return;
            }
            $authors = $this->authorRepository->findByFirstLetterOfSurname($char);
        } else {
            $authors = $this->authorRepository->findAll();
        }

        $authorsViewArray = $this->mapAuthorsToViewArray($authors);
        $table = new Table($output);
        $table
            ->setHeaders(['Id', 'Nazwisko', 'Imie', 'Ilość książek'])
            ->setRows($authorsViewArray);
        $table->render();

        if ($save) {
            if (strpos($save, '.') === false) {
                $io->error('Pass filename with extension');
                return;
            }

            [$name, $sufix] = explode('.', $save);
            if ($sufix !== 'txt') {
                $io->error('File should have txt extension');
                return;
            }

            while (file_exists($save)) {
                $num = [];
                $num = preg_match('/\d+/', $save, $num) ? (int) end($num) : 0;
                $num++;
                $save = $name . $num . '.' . $sufix;
            }

            $file = fopen($save, 'w');
                fwrite($file, $this->converAuthorsToFileFormat($authorsViewArray));
            fclose($file);

            $io->success('Authors succesfully saved to file');
        }

        if (count($authors) === 0) {
            $io->note('No authors found');
        }
    }

    private function mapAuthorsToViewArray($authors)
    {
        return array_map(function (Author $author) {
            $output = [];
            $output['Id'] = $author->getId();
            $output['Nazwisko'] = $author->getSurname();
            $output['Imie'] = $author->getName();
            $output['Ilosc ksiazek'] = count($author->getBooks());
            return $output;
        }, $authors);
    }

    private function converAuthorsToFileFormat($authors)
    {
        return str_replace(
            ',',
            ' ',
            preg_replace(
                '/^.*\n/',
                '',
                $this->serializer->serialize($authors, 'csv')
            )
        );
    }
}
