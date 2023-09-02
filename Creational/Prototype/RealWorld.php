<?php

class Page
{
    private string $title;
    private string $body;
    private Author $author;
    private array $comments = [];
    private \DateTime $date;

    public function __construct(string $title, string $body, Author $author)
    {
        $this->title = $title;
        $this->body = $body;
        $this->author = $author;
        $this->author->addToPage($this);
        $this->date = new \DateTime();
    }

    public function addComment(string $comment): void
    {
        $this->comments[] = $comment;
    }

    public function __clone(): void
    {
        $this->title = "Copy of " . $this->title;
        $this->author->addToPage($this);
        $this->comments = [];
        $this->date = new \DateTime();
    }    

    public function __toString(): string
    {
        return sprintf(
            'Página: "%s" del autor: "%s" con los siguientes comentarios: %s',
            $this->title,
            $this->author,
            implode(',', $this->comments)
        );
    }
}

class Author
{
    private string $name;
    private array $pages = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addToPage(Page $page): void
    {
        $this->pages[] = $page;
    }  

    public function __toString(): string
    {
        return $this->name;
    }  
}

$author = new Author('Alice Smith');
$pagePrototype = new Page('Design patterns', 'Hello my friend.', $author);
$pagePrototype->addComment('Nice page, thanks!');

echo $pagePrototype;
echo '<br><br>';

$anotherPage = clone $pagePrototype;
echo $anotherPage;
