<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-08
 * Time: 10:46
 */

namespace App\Utils;

use App\Entity\Book;
use App\Repository\BookRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RandomBookService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var BookRepository
     */
    private $bookRepository;

    public function __construct(
        LoggerInterface $logger,
        SessionInterface $session,
        BookRepository $bookRepository
    ) {
        $this->logger = $logger;
        $this->session = $session;
        $this->bookRepository = $bookRepository;
    }

    public function getRandomBook(): ?Book
    {
        $book = $this->bookRepository->getRandomBook();
        if ($book instanceof Book) {
            $id = $book->getId();
            $this->log($id);
            $this->saveToSession($id);
        }

        return $book;
    }

    public function log(int $bookId)
    {
        $this->logger->info('Drawn book with id = ' . $bookId);
    }

    public function saveToSession(int $bookId)
    {
        $this->session->set('randomBookService_last_drawn_book', $bookId);
    }

    public function getLastBook(): ?string
    {
        return $this->session->get('randomBookService_last_drawn_book');
    }
}
