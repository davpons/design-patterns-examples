<?php

/**
 * La clase base Component declara una interfaz para todos los componentes concretos,
 * tanto simples como complejos. En nuestro ejemplo, nos centraremos en el comportamiento
 * de renderizado de los elementos DOM.
 */
abstract class FormElement
{
    /**
     * Podemos anticipar que todos los elementos DOM requieren estos 3 campos.
     */
    protected string $name;
    protected string $title;
    protected $data;

    public function __construct(string $name, string $title)
    {
        $this->name = $name;
        $this->title = $title;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Cada elemento DOM concreto debe proporcionar su implementación de representación,
     * pero podemos asumir con seguridad que todos devuelven cadenas.
     */
    abstract public function render(): string;
}

/**
 * Este es un componente Leaf.
 * Como todas las Hojas, no puede tener hijos.
 */
class Input extends FormElement
{
    private string $type;

    public function __construct(string $name, string $title, string $type)
    {
        parent::__construct($name, $title);
        $this->type = $type;
    }

    /**
     * Dado que los componentes Leaf no tienen hijos que puedan realizar la mayor parte
     * del trabajo por ellos, generalmente son las propias hojas quienes realizan este trabajo
     * dentro del patrón Composite.
     */
    public function render(): string
    {
        return "<label for=\"{$this->name}\">{$this->title}</label>\n" .
               "<input name=\"{$this->name}\" type=\"{$this->type}\" value=\"{$this->data}\">\n";
    }
}

/**
 * La clase base Composite implementa la infraestructura para administrar objetos secundarios,
 * reutilizados por todos los Concrete Composites.
 */
abstract class FieldComposite extends FormElement
{
    /**
     * @var FormElement[]
     */    
    protected array $fields = [];

    /**
     * Los métodos para agregar/eliminar subobjetos.
     */
    public function add(FormElement $field): void
    {
        $name = $field->getName();
        $this->fields[$name] = $field;
    }

    /**
     * Mientras que el método de Leaf simplemente hace el trabajo,
     * el método de Composite casi siempre tiene que tener en cuenta sus subobjetos.
     * En este caso, el compuesto puede aceptar datos estructurados.
     */
    public function setData($data): void
    {
        foreach ($this->fields as $name => $field) {
            if (isset($data[$name])) {
                $field->setData($data[$name]);
            }
        }
    }

    /**
     * La misma lógica se aplica al getter.
     * Devuelve los datos estructurados del propio compuesto (si lo hay)
     * y todos los datos secundarios.
     */
    public function getData(): array
    {
        $data = [];

        foreach ($this->fields as $name => $field) {
            $data[$name] = $field->getData();
        }

        return $data;
    }

    /**
     * La implementación básica del renderizado del Composite simplemente combina los resultados
     * de todos los elementos secundarios. Concrete Composites podrá reutilizar esta implementación
     * en sus implementaciones de renderizado reales.
     */
    public function render(): string
    {
        $output = '';

        foreach ($this->fields as $name => $field) {
            $output .= $field->render();
        }

        return $output;
    }
}


/**
 * El fieldset es un Concrete Composite.
 */
class Fieldset extends FieldComposite
{
    public function render(): string
    {
        // Observa cómo el resultado de la representación combinada de los hijos
        // se incorpora a la etiqueta del conjunto de campos.
        $output = parent::render();

        return "<fieldset><legend>{$this->title}</legend>\n$output</fieldset>\n";
    }
}

/**
 * El elemento formulario
 */
class Form extends FieldComposite
{
    protected string $url;

    public function __construct(string $name, string $title, string $url)
    {
        parent::__construct($name, $title);
        $this->url = $url;
    }

    public function render(): string
    {
        $output = parent::render();
        return "<form action=\"{$this->url}\">\n<h3>{$this->title}</h3>\n$output</form>\n";
    }
}

class ClientProductForm
{
    private FormElement $form;

    public function __construct()
    {
        $this->form = new Form('product', 'Add product', 'product/add');
        $this->form->add(new Input('name', 'Name', 'text'));
        $this->form->add(new Input('description', 'Description', 'text'));

        $picture = new Fieldset('photo', 'Product Photo');
        $picture->add(new Input('caption', 'Caption', 'text'));
        $picture->add(new Input('image', 'Image', 'text'));
        $this->form->add($picture);

        $this->loadProductData();
    }

    private function loadProductData(): void{
        $data = [
            'name' => 'Apple MacBook',
            'description' => 'A decent laptop.',
            'photo' => [
                'caption' => 'Front photo.',
                'image' => 'photo1.png',
            ],
        ]; 

        $this->form->setData($data);       
    }

    public function render(): void
    {
        echo $this->form->render();
    }
}

$productForm = new ClientProductForm();
$productForm->render();
