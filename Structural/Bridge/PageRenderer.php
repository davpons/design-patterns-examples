<?php

/**
 * La abstracción.
 */
abstract class Page
{
    /**
     * La abstracción generalmente se inicializa
     * con uno de los objetos de implementación.
     */
    public function __construct(
        protected Renderer $renderer
    ) {}

    /**
     * El patrón Bridge permite reemplazar
     * dinámicamente el objeto de implementación adjunto.
     */
    public function changeRenderer(Renderer $renderer): void
    {
        $this->renderer = $renderer;
    }

    /**
     * El comportamiento de "view" permanece abstracto
     * ya que solo lo pueden proporcionar clases de abstracción concreta.
     */
    abstract public function view(): string;
}

/**
 * Esta abstracción concreta representa una página simple.
 */
class SimplePage extends Page
{
    public function __construct(
        Renderer $renderer,
        protected string $title,
        protected string $content
    ) {
        parent::__construct($renderer);
    }

    public function view(): string
    {
        return $this->renderer->renderParts([
            $this->renderer->renderHeader(),
            $this->renderer->renderTitle($this->title),
            $this->renderer->renderTextBlock($this->content),
            $this->renderer->renderFooter()
        ]);
    }
}

/**
 * Esta abstracción concreta representa una página más compleja.
 */
class ProductPage extends Page
{
    public function __construct(
        Renderer $renderer,
        protected Product $product
    ) {
        parent::__construct($renderer);
    }

    public function view(): string
    {
        return $this->renderer->renderParts([
            $this->renderer->renderHeader(),
            $this->renderer->renderTitle($this->product->getTitle()),
            $this->renderer->renderTextBlock($this->product->getDescription()),
            $this->renderer->renderImage($this->product->getImage()),
            $this->renderer->renderLink("/cart/add/" . $this->product->getId(), "Add to cart"),
            $this->renderer->renderFooter()
        ]);
    }
}

/**
 * Una clase auxiliar para la clase ProductPage.
 */
class Product
{
    public function __construct(
        private string $id,
        private string $title,
        private string $description,
        private string $image,
        private float $price
    ) {}


    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}

/**
 * La implementación declara un conjunto de métodos
 * "reales", "ocultos" y de "plataforma".
 *
 * En este caso, la implementación
 * enumera métodos de representación que se pueden utilizar para componer
 * cualquier página web. Diferentes abstracciones pueden utilizar
 * diferentes métodos de implementación.
 */
interface Renderer
{
    public function renderTitle(string $title): string;
    public function renderTextBlock(string $text): string;
    public function renderImage(string $url): string;
    public function renderLink(string $url, string $title): string;
    public function renderHeader(): string;
    public function renderFooter(): string;
    public function renderParts(array $parts): string;
}

/**
 * Esta implementación concreta representa una página web como HTML.
 */
class HTMLRenderer implements Renderer
{
    public function renderTitle(string $title): string
    {
        return '<h1>' . $title . '</h1>';
    }

    public function renderTextBlock(string $text): string
    {
        return '<div class="text">' . $text . '</div>';
    }

    public function renderImage(string $url): string
    {
        return '<img src="' . $url . '">';
    }

    public function renderLink(string $url, string $title): string
    {
        return "<a href='$url'>$title</a>";
    }

    public function renderHeader(): string
    {
        return "<html><body>";
    }

    public function renderFooter(): string
    {
        return "</body></html>";
    }

    public function renderParts(array $parts): string
    {
        return implode("\n", $parts);
    }
}

/**
 * Esta implementación concreta representa una página web como cadenas JSON.
 */
class JSONRenderer implements Renderer
{
    public function renderTitle(string $title): string
    {
        return '"title": "' . $title . '"';
    }

    public function renderTextBlock(string $text): string
    {
        return '"text": "' . $text . '"';
    }

    public function renderImage(string $url): string
    {
        return '"img": "' . $url . '"';
    }

    public function renderLink(string $url, string $title): string
    {
        return '"link": {"href": "' . $url . '", "title": "' . $title . '"}';
    }

    public function renderHeader(): string
    {
        return '';
    }

    public function renderFooter(): string
    {
        return '';
    }

    public function renderParts(array $parts): string
    {
        return "{\n" . implode(",\n", array_filter($parts)) . "\n}";
    }
}

/**
 * El código del cliente normalmente trata
 * sólo con los objetos de abstracción.
 */
function clientCode(Page $page)
{
    echo $page->view();
}

/**
 * El código del cliente se puede ejecutar con cualquier combinación 
 * preconfigurada de Abstracción+Implementación.
 */
$HTMLRenderer = new HTMLRenderer();
$JSONRenderer = new JSONRenderer();

$page = new SimplePage($HTMLRenderer, 'Home', 'Welcome to our website!');
echo 'Vista HTML de una página simple:<br>';
clientCode($page);
echo '<br><br>';

// La abstracción puede cambiar la implementación vinculada 
// en tiempo de ejecución si es necesario.
$page->changeRenderer($JSONRenderer);
echo 'Vista JSON de una página simple, representada con el mismo código de cliente:<br>';
clientCode($page);
echo '<br><br>';

$product = new Product(
    "123",
    "Star Wars, episode1",
    "A long time ago in a galaxy far, far away...",
    "https://fakeimg.pl/80x80/",
    39.95
);

$page = new ProductPage($HTMLRenderer, $product);
echo 'Vista HTML de la página de un producto, mismo código de cliente:<br>';
clientCode($page);
echo '<br><br>';

$page->changeRenderer($JSONRenderer);
echo 'Vista JSON de una página de producto, con el mismo código de cliente:<br>';
clientCode($page);
