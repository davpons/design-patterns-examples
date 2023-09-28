<?php

interface BookInterface
{
    public function getAuthorAndTitle(): string;
}

class SimpleBook
{
    private string $author;
    private string $title;

    public function __construct(string $author, string $title)
    {
        $this->author = $author;
        $this->title = $title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}

class BookAdapter implements BookInterface
{
    private SimpleBook $book;

    public function __construct(SimpleBook $book)
    {
        $this->book = $book;
    }

    public function getAuthorAndTitle(): string
    {
        return $this->book->getTitle() . ' by ' . $this->book->getAuthor();
    }
}

$book = new SimpleBook("Gamma, Helm, Johnson, and Vlissides", "Design Patterns");
$bookAdapter = new BookAdapter($book);

echo 'Author and Title: ' . $bookAdapter->getAuthorAndTitle();
