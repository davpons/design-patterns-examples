<?php

interface TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate;
    public function createPageTemplate(): PageTemplate;
    public function getRenderer(): TemplateRenderer;
}

class TwigTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new TwigTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new TwigPageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TemplateRenderer
    {
        return new TwigRenderer();
    }
}

class PHPTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new PHPTemplateTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new PHPTemplatePageTemplate($this->createTitleTemplate());
    }

    public function getRenderer(): TemplateRenderer
    {
        return new PHPTemplateRenderer();
    }
}

interface TitleTemplate
{
    public function getTemplateString(): string;
}

class TwigTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1>{{ title }}</h1>";
    }
}

class PHPTemplateTitleTemplate implements TitleTemplate
{
    public function getTemplateString(): string
    {
        return "<h1><?= \$title; ?></h1>"; 
    }
}

interface PageTemplate
{
    public function getTemplateString(): string;
}

abstract class BasePageTemplate implements PageTemplate
{
    protected TitleTemplate $titleTemplate;

    public function __construct(TitleTemplate $titleTemplate)
    {
        $this->titleTemplate = $titleTemplate;
    }
}

class TwigPageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();

        return "<div class=\"page\">
            $renderedTitle
            <article class=\"content\">{{ content }}</article>
        </div>";
    }    
}

class PHPTemplatePageTemplate extends BasePageTemplate
{
    public function getTemplateString(): string
    {
        $renderedTitle = $this->titleTemplate->getTemplateString();
        
        return "<div class=\"page\">
            $renderedTitle
            <article class=\"content\"><?= \$content; ?></article>
        </div>";
    }
}

interface TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string;
}

class TwigRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        return \Twig::render($templateString, $arguments);
    }    
}

class PHPTemplateRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        extract($arguments);

        ob_start();
        eval(' ?>' . $templateString . '<?php ');
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}

class Page
{
    public string $title;
    public string $content;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }    

    public function render(TemplateFactory $factory): string
    {
        $pageTemplate = $factory->createPageTemplate();
        $renderer = $factory->getRenderer();

        return $renderer->render($pageTemplate->getTemplateString(), [
            'title' => $this->title,
            'content' => $this->content,
        ]);
    }
}

$page = new Page('Sample page title', 'This is the body content.');
echo $page->render(new PHPTemplateFactory);
