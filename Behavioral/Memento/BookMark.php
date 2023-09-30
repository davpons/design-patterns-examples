<?php

class BookReader
{
    private string $title;
    private string $page;

    public function __construct(string $title, string $page)
    {
        $this->setTitle($title);
        $this->setPage($page);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function setPage(string $page): void
    {
        $this->page = $page;
    }
}

class BookMark
{
    private string $title;
    private string $page;

    public function __construct(BookReader $bookReader)
    {
        $this->setPage($bookReader);
        $this->setTitle($bookReader);
    }

    public function getPage(BookReader $bookReader): void
    {
        $bookReader->setPage($this->page);
    }

    public function setPage(BookReader $bookReader): void
    {
        $this->page = $bookReader->getPage();
    }

    public function getTitle(BookReader $bookReader): void
    {
        $bookReader->setTitle($this->title);
    }

    public function setTitle(BookReader $bookReader): void
    {
        $this->title = $bookReader->getTitle();
    }
}

class ClientCaretaker
{
    private BookReader $bookReader;
    private BookMark $bookMark;

    public function __construct()
    {
        $this->writeln('Empezamos a probar el patrón Memento...');

        $this->bookReader = new BookReader('Core PHP Programming, Third Edition', '1');
        $this->bookMark = new BookMark($this->bookReader);
    }

    public function start(): void
    {
        $this->writeln();
        $this->writeln('(al principio) bookReader title: '.$this->bookReader->getTitle());
        $this->writeln('(al principio) bookReader page: '.$this->bookReader->getPage());
        $this->writeln();        
    }

    public function turnPages(): void
    {
        $this->writeln('Asignamos página 104 al BookReader...');
        $this->bookReader->setPage("104");
        $this->writeln('Asignamos página 104 al BookMark...');
        $this->bookMark->setPage($this->bookReader);
        $this->writeln('(una página después) BookReader page: ' . $this->bookReader->getPage());  

        $this->writeln('Asignamos por error la página 2005 al BookReader...');
        $this->bookReader->setPage('2005');
        $this->writeln('(tras el error) BookReader page: ' . $this->bookReader->getPage());    
        
        $this->writeln('Recuperamos la página anterior desde el BookMark');
        $this->bookMark->getPage($this->bookReader);
        $this->writeln('(volver a la página anterior) BookReader page: ' . $this->bookReader->getPage());    
        $this->writeln();
    }

    private function writeln(string $line = ''): void
    {
        echo $line . '<br>';
    }
}

$client = new ClientCaretaker();
$client->start();
$client->turnPages();
