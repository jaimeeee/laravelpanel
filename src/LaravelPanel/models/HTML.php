<?php

namespace Jaimeeee\Panel;

class HTML
{
    private static $voidElements = [
        'area', 'base', 'br', 'col', 'command', 'embed', 'hr',
        'img', 'input', 'link', 'meta', 'param', 'source',
    ];
    
    /**
     * Generate the HTML code for an element
     * @param  string $tag  <tag>
     * @param  array  $attr The tag attributes
     * @param  string $html The tag HTML content
     * @return string       HTML code of the tag
     */
    public static function tag($tag, $attr = [], $html = '')
    {
        // Build attributes
        $attributes = [];
        foreach ($attr as $name => $value)
        {
            $attributes[] = $name . '="' . $value . '"';
        }
        
        if (in_array($tag, HTML::$voidElements))
            return '<' . $tag . (!empty($attributes) ? ' ' . implode(' ', $attributes) : '') . '>';
        else
            return '<' . $tag . (!empty($attributes) ? ' ' . implode(' ', $attributes) : '') . '>' . $html . '</' . $tag . '>';
    }
}
