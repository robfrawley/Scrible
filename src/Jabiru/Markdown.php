<?php

namespace Scribe\Jabiru;

use Scribe\Jabiru\Component\Collection\Collection;
use Scribe\Jabiru\Component\Collection\HashCollection;
use Scribe\Jabiru\Component\Element\ElementLiteral;
use Scribe\Jabiru\Event\EmitterAwareInterface;
use Scribe\Jabiru\Event\EmitterInterface;
use Scribe\Jabiru\Event\EmitterTrait;
use Scribe\Jabiru\Renderer\RendererInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Manages options and events
 */
class Markdown implements EmitterInterface
{

    use EmitterTrait;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var array
     */
    private $options;

    /**
     * @var HashCollection
     */
    private $hashCollection;

    /**
     * @var Collection
     */
    private $urlRegistry;

    /**
     * @var Collection
     */
    private $titleRegistry;

    /**
     * @var Text
     */
    private $rawContent;

    /**
     * Constructor
     *
     * @param Renderer\RendererInterface $renderer   A RendererInterface instance
     * @param Common\Text                $rawContent [optional] The whole content
     * @param array                      $options    [optional] An array of options
     */
    public function __construct(RendererInterface $renderer, ElementLiteral $rawContent = null, array $options = array())
    {
        if ($renderer instanceof EmitterAwareInterface) {
            $renderer->setEmitter($this);
        }

        $this->renderer       = $renderer;
        $this->options        = $this->parseOptions($options);
        $this->hashCollection = new HashCollection();
        $this->urlRegistry    = new Collection();
        $this->titleRegistry  = new Collection();
        $this->rawContent     = $rawContent;
    }

    /**
     * Returns HashCollection
     *
     * @return HashCollection
     */
    public function getHashCollection()
    {
        return $this->hashCollection;
    }

    /**
     * Returns the collection of titles
     *
     * @return Collection
     */
    public function getTitleRegistry()
    {
        return $this->titleRegistry;
    }

    /**
     * Returns the collection of urls
     *
     * @return Collection
     */
    public function getUrlRegistry()
    {
        return $this->urlRegistry;
    }

    /**
     * Returns the option
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns original text
     *
     * @return Text
     */
    public function getRawContent()
    {
        return $this->rawContent;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'tabWidth'              => 4,
            'nestedTagLevel'        => 3,
            'strict'                => false,
            'highlight-code-block'  => true,
            'highlight-code-inline' => true
        ));

        $resolver->setAllowedTypes(array(
            'tabWidth'              => 'integer',
            'nestedTagLevel'        => 'integer',
            'strict'                => 'bool',
            'highlight-code-block'  => 'bool',
            'highlight-code-inline' => 'bool'
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function buildParameters(array $parameters = array())
    {
        $parameters[] = $this->options;

        return $parameters;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function parseOptions(array $options = array())
    {
        $this->setDefaultOptions($resolver = new OptionsResolver());

        return $resolver->resolve($options);
    }

}