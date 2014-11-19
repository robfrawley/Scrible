<?php

namespace Scribe\Jabiru\Extension\Textile;

use Scribe\Jabiru\Common\Text;
use Scribe\Jabiru\Extension\ExtensionInterface;
use Scribe\Jabiru\Markdown;
use Scribe\Jabiru\Renderer\RendererAwareInterface;
use Scribe\Jabiru\Renderer\RendererAwareTrait;

/**
 * [Experimental] Textile Headers
 *
 * This extension replaces Core\HeaderExtension
  */
class HeaderExtension implements ExtensionInterface, RendererAwareInterface
{

    use RendererAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $markdown->on('block', array($this, 'processHeader'), 10);
    }

    /**
     * @param Text $text
     */
    public function processHeader(Text $text)
    {
        $text->replace('{
            ^h([1-6])  #1 Level
            (|=|>)\.  #2 Align marker
            [ \t]*
            (.+)
            [ \t]*\n+
        }mx', function (Text $w, Text $level, Text $mark, Text $header) {
            $attributes = [];
            switch ((string) $mark) {
                case '>':
                    $attributes['align'] = 'right';
                    break;
                case '=':
                    $attributes['align'] = 'center';
                    break;
            }

            return $this->getRenderer()->renderHeader(
                $header,
                ['level' => (int)$level->getString(), 'attr' => $attributes]
            ) . "\n\n";
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'header';
    }

}