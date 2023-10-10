<?php

class Book
{
    function __construct(
        private string $title,
        private string $author
    ) {}

    function getAuthor(): string
    {
        return $this->author;
    }
    function getTitle(): string
    {
        return $this->title;
    }

    function getAuthorAndTitle(): string
    {
      return $this->getTitle() . ' by ' . $this->getAuthor();
    }
}

class BookList
{
    private array $books = [];
    private int $bookCount = 0;

    public function getBookCount(): int
    {
        return $this->bookCount;
    }

    private function setBookCount(int $newCount): void
    {
        $this->bookCount = $newCount;
    }

    public function getBook(int $bookNumberToGet): ?Book
    {
        if (
            is_numeric($bookNumberToGet) &&
            $bookNumberToGet <= $this->getBookCount()
        ) {
            return $this->books[$bookNumberToGet];
        }

        return null;
    }

    public function addBooks(Book ...$books): int
    {
        foreach ($books as $book) {
            $this->addBook($book);
        }

        return $this->getBookCount();
    }

    public function addBook(Book $book): int
    {
        $this->setBookCount($this->getBookCount() + 1);
        $this->books[$this->getBookCount()] = $book;

        return $this->getBookCount();
    }

    public function removeBook(Book $book): int
    {
        $counter = 0;
        while (++$counter <= $this->getBookCount()) {
            if (
                $book->getAuthorAndTitle() ===
                    $this->books[$counter]->getAuthorAndTitle()
            ) {
                for ($x = $counter; $x < $this->getBookCount(); $x++) {
                    $this->books[$x] = $this->books[$x + 1];
                }
                $this->setBookCount($this->getBookCount() - 1);
            }
        }
    }
}

class BookListIterator
{
    protected int $currentBook = 0;

    public function __construct(
        protected BookList $bookList
    ) {}

    public function getCurrentBook(): ?Book
    {
        if (
            $this->currentBook > 0 &&
            $this->bookList->getBookCount() >= $this->currentBook
        ) {
            return $this->bookList->getBook($this->currentBook);
        }

        return null;
    }

    public function getNextBook(): ?Book
    {
        if ($this->hasNextBook()) {
            return $this->bookList->getBook(++$this->currentBook);
        }

        return null;
    }

    public function hasNextBook(): bool
    {
        return $this->bookList->getBookCount() > $this->currentBook;
    }
}

class BookListReverseIterator extends BookListIterator
{
    public function __construct(protected BookList $bookList) {
        $this->currentBook = $this->bookList->getBookCount() + 1;
    }

    public function getNextBook(): ?Book
    {
        if ($this->hasNextBook()) {
            return $this->bookList->getBook(--$this->currentBook);
        }
    }

    public function hasNextBook(): bool
    {
        return $this->currentBook > 1;
    }
}

function writeln(string $line = '') {
    echo $line . '<br>';
}

$firstBook = new Book('Core PHP Programming, Third Edition', 'Atkinson and Suraski');
$secondBook = new Book('PHP Bible', 'Converse and Park');
$thirdBook = new Book('Design Patterns', 'Gamma, Helm, Johnson, and Vlissides');
$fourthBook = new Book('PHP 8 Objects, Patterns, and Practice', 'Matt Zandstra');

$books = new BookList();
$books->addBooks(
    $firstBook,
    $secondBook,
    $thirdBook,
    $fourthBook
);

$booksIterator = new BookListIterator($books);

while ($booksIterator->hasNextBook()) {
    $book = $booksIterator->getNextBook();
    writeln($book->getAuthorAndTitle());
}

writeln();

writeln($booksIterator->getCurrentBook()->getAuthorAndTitle());

writeln();

$booksReverseIterator = new BookListReverseIterator($books);

while ($booksReverseIterator->hasNextBook()) {
    $book = $booksReverseIterator->getNextBook();
    writeln($book->getAuthorAndTitle());
}
