<?php
/**
 * Created by PhpStorm.
 * User: virtua
 * Date: 2019-03-08
 * Time: 10:46
 */

namespace App\Utils;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RandomBookService
{
    private $logger;

    private $session;

    public function __construct(LoggerInterface $logger, SessionInterface $session)
    {
        $this->logger = $logger;
        $this->session = $session;
    }

    public function log(int $bookId)
    {
        $this->logger->info('Drawn book with id = ' . $bookId);
    }

    public function save(int $bookId)
    {
        $this->session->set('randomBookService_book', $bookId);
    }

    public function get()
    {
        return $this->session->get('randomBookService_book');
    }

}