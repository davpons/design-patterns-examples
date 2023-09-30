<?php

abstract class BookTemplatePresenter {
    // El método plantilla configura un algoritmo general para toda la clase 
    public final function showBookTitleInfo(Book $book): string {
        $title = $book->getTitle();
        $author = $book->getAuthor();
        $processedTitle = $this->processTitle($title);
        $processedAuthor = $this->processAuthor($author);

        $processed_info = $processedTitle;

        if (null !== $processedAuthor) {
            $processed_info .= ' by ' . $processedAuthor;
        }

        return $processed_info;
    }

    // Una operación primitiva.
    // Deberá sobreescribirse obligatoriamente.
    abstract function processTitle(string $title): string;

    // Un hook
    // Esta operación de enlace puede anularse,
    // simplemente devolverá null si no lo es.
    function processAuthor(string $author): ?string {
        return null;
    } 
}

class BookPresenterExclamations extends BookTemplatePresenter
{
    public function processTitle(string $title): string
    {
        return str_replace(' ', '!!!', $title);
    }

    public function processAuthor(string $author): string
    {
        return str_replace(' ', '!!!', $author);
    }
}

class BookPresenterStars extends BookTemplatePresenter
{
    public function processTitle(string $title): string
    {
        return str_replace(' ', '***', $title);
    }
}

class Book
{
    private string $author;
    private string $title;

    function __construct(string $title, string $author) {
        $this->author = $author;
        $this->title  = $title;
    }

    function getAuthor() {
        return $this->author;
    }

    function getTitle() {
        return $this->title;
    }

    function getAuthorAndTitle() {
        return $this->getTitle() . ' by ' . $this->getAuthor();
    }    
}

class Client
{
    private Book $book;

    public function __construct()
    {
        $this->book = new Book('PHP for Cats', 'Larry Truett');
    }

    public function exclaim()
    {
        $exclaimTemplate = new BookPresenterExclamations();
        
        $this->writeln('test 1 - mostrar libro con exclamaciones');
        $this->writeln($exclaimTemplate->showBookTitleInfo($this->book));
        $this->writeln();
    }

    public function stars()
    {
        $starTemplate = new BookPresenterStars();
        
        $this->writeln('test 2 - mostrar libro con estrellas');
        $this->writeln($starTemplate->showBookTitleInfo($this->book));
        $this->writeln();
    }    

    private function writeln($line = '') {
        echo $line . '<br/>';
    }
}

$client = new Client();

$client->exclaim();
$client->stars();
