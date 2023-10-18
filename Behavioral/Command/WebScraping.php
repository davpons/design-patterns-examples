<?php

/**
 * La interfaz de comando declara el método de ejecución principal, así como
 * varios métodos auxiliares para recuperar los metadatos de un comando.
 */
interface Command
{
    public function execute(): void;
    public function getId(): int;
    public function getStatus(): int;
}

/**
 * El comando base de web scraping define la infraestructura de
 * descarga básica, común a todos los comandos concretos de web scraping.
 */
abstract class WebScrapingCommand implements Command
{
    private int $id = 0;
    private int $status = 0;

    public function __construct(
        public string $url
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getURL(): string
    {
        return $this->url;
    }

    /**
     * Dado que los métodos de ejecución para todos los comandos de
     * web scraping son muy similares, podemos proporcionar una implementación
     * predeterminada y permitir que las subclases los anulen si es necesario.
     * ¿Puedes detectar aquí otro patrón de comportamiento en acción?.
     */
    public function execute(): void
    {
        $html = $this->download();
        $this->parse($html);
        $this->complete();
    }

    public function download(): string
    {
        $html = file_get_contents($this->getURL());
        echo 'WebScrepingCommand: Descargado ' . $this->url . '<br>';

        return $html;
    }

    abstract function parse(string $string): void;

    public function complete(): void
    {
        $this->status = 1;
        Queue::get()->completeCommand($this);
    }
}

/**
 * El comando concreto para extraer la lista de géneros de películas.
 */
class IMDBGenresScrapingCommand extends WebScrapingCommand
{
    public function __construct()
    {
        $this->url = 'https://www.imdb.com/feature/genre/';
    }

    /**
     * Extraer todos los géneros y sus URL de búsqueda de la página:
     * https://www.imdb.com/feature/genre/
     */
    public function parse($html): void
    {
        preg_match_all("|href=\"(/search/title/\?genres=.*?)\"|", $html, $matches);
        echo 'IMDBGenresScrapingCommand: Discovered ' .
            count($matches[1]) . ' genres.<br>';

        foreach ($matches[1] as $genre) {
            Queue::get()->add(new IMDBGenrePageScrapingCommand($genre));
        }
    }
}

/**
 * El comando concreto para extraer la lista de películas
 * de un género específico
 */
class IMDBGenrePageScrapingCommand extends WebScrapingCommand
{
    private int $page;

    public function __construct(string $url, int $page = 1)
    {
        parent::__construct($url);
        $this->page = $page;
    }

    public function getURL(): string
    {
        return $this->url . '?page=' . $this->page;
    }

    /**
     * Extraer todas las películas de una página como esta:
     * https://www.imdb.com/search/title?genres=sci-fi&explore=title_type,genres
     */
    public function parse(string $html): void
    {
        preg_match_all("|href=\"(/title/.*?/)\?ref_=adv_li_tt\"|", $html, $matches);
        echo "IMDBGenrePageScrapingCommand: Discovered " . count($matches[1]) . " movies.\n";

        foreach ($matches[1] as $moviePath) {
            $url = "https://www.imdb.com" . $moviePath;
            Queue::get()->add(new IMDBMovieScrapingCommand($url));
        }

        // Parse the next page URL.
        if (preg_match("|Next &#187;</a>|", $html)) {
            Queue::get()->add(new IMDBGenrePageScrapingCommand($this->url, $this->page + 1));
        }
    }
}

/**
 * El comando concreto para extraer los detalles de la película.
 */
class IMDBMovieScrapingCommand extends WebScrapingCommand
{
    /**
     * Get the movie info from a page like this:
     * https://www.imdb.com/title/tt4154756/
     */
    public function parse(string $html): void
    {
        if (preg_match("|<h1 itemprop=\"name\" class=\"\">(.*?)</h1>|", $html, $matches)) {
            $title = $matches[1];
        }
        echo "IMDBMovieScrapingCommand: Parsed movie $title.<br>";
    }
}

/**
 * La clase Queue actúa como un Invoker.
 * Apila los objetos de comando y los ejecuta uno por uno.
 * Si la ejecución del script finaliza repentinamente, la cola y todos
 * sus comandos se pueden restaurar fácilmente y no será necesario repetir
 * todos los comandos ejecutados.
 *
 * Ten en cuenta que esta es una implementación muy primitiva de la cola
 * de comandos, que almacena comandos en una base de datos SQLite local.
 * Hay docenas de soluciones de cola sólidas disponibles para usar
 * en aplicaciones reales.
 */
class Queue
{
    private static ?self $instance = null;
    private \SQLite3 $db;

    public function __construct()
    {
        $this->db = new \SQLite3(
            __DIR__ . '/commands.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE
        );

        $this->db->query('create table if not exists "commands" (
            "id" integer primary key not null,
            "command" text,
            "status" integer
        )');
    }

    public function isEmpty(): bool
    {
        $query = 'select count("id") from "commands" where status = 0';
        return $this->db->querySingle($query) == 0;
    }

    public function add(Command $command): void
    {
        $query = 'INSERT INTO commands (command, status) VALUES (:command, :status)';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':command', base64_encode(serialize($command)));
        $statement->bindValue(':status', $command->getStatus());
        $statement->execute();
    }

    public function getCommand(): Command
    {
        $query = 'SELECT * FROM "commands" WHERE "status" = 0 LIMIT 1';
        $record = $this->db->querySingle($query, true);
        $command = unserialize(base64_decode($record['command']));
        $command->id = $record['id'];

        return $command;
    }

    public function completeCommand(Command $command): void
    {
        $query = 'UPDATE commands SET status = :status WHERE id = :id';
        $statement = $this->db->prepare($query);
        $statement->bindValue(':status', $command->getStatus());
        $statement->bindValue(':id', $command->getId());
        $statement->execute();
    }

    public function work(): void
    {
        while (!$this->isEmpty()) {
            $command = $this->getCommand();
            $command->execute();
        }
    }

    /**
     * Por comodidad, el objeto Queue es un Singleton.
     */
    public static function get(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

/**
 * Cliente...
 */
$queue = Queue::get();

if ($queue->isEmpty()) {
    $queue->add(new IMDBGenresScrapingCommand());
}

$queue->work();
