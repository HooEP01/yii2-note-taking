<?php
return [
    'Core.Encoding' => 'UTF-8',
    'HTML.Doctype' =>  'XHTML 1.0 Transitional',
    'URI.AllowedSchemes' => [
        'http'      => true,
        'https'     => true,
        'mailto'    => true,
    ],
    'HTML.AllowedElements' => [
        'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'img', 'strong',
        'small', 'br', 'em', 'pre', 'a', 'del', 'b', 'q', 'span', 'iframe',
        'ul', 'ol', 'li', 'table', 'tr', 'td', 'th',
    ],
    'HTML.AllowedAttributes' => [
        'title', 'href', 'src', 'alt', 'style',
        'img.width', 'img.height', 'li.type',
        'table.border',
    ],
    'Filter.YouTube' => true,
    'HTML.SafeIframe' => true,
    'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
    'CSS.AllowedProperties' => [
        'background', 'background-color', 'background-image', 'background-position',
        'border', 'border-color', 'border-collapse', 'border-style', 'border-spacing', 'border-width',
        'color', 'font', 'font-family', 'font-size', 'font-style', 'font-weight',
        'height', 'margin', 'padding', 'line-height', 'letter-spacing',
        'max-height', 'max-width', 'min-height', 'min-width',
        'text-align', 'vertical-align', 'width', 'word-spacing', 'list-style'
    ],
];
