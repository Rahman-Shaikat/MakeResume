<?php

declare(strict_types=1);

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

final class ResumeRichTextSanitizer
{
    public function sanitize(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        if (trim($html) === '') {
            return '';
        }

        $previousErrors = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument('1.0', 'UTF-8');
            $document->loadHTML(
                '<?xml encoding="UTF-8"><div id="resume-rich-text">'.$html.'</div>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
            );

            $root = $document->getElementsByTagName('div')->item(0);

            return $root instanceof DOMElement
                ? preg_replace('/(?:<br>)+$/', '', $this->childrenOf($root)) ?? ''
                : $this->escape($html);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrors);
        }
    }

    private function childrenOf(DOMNode $node): string
    {
        $content = '';

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $content .= $this->escape($child->nodeValue ?? '');

                continue;
            }

            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if ($tag === 'br') {
                $content .= '<br>';

                continue;
            }

            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math'], true)) {
                continue;
            }

            $inner = $this->childrenOf($child);

            if (in_array($tag, ['strong', 'b'], true)) {
                $content .= '<strong>'.$inner.'</strong>';

                continue;
            }

            if (in_array($tag, ['em', 'i'], true)) {
                $content .= '<em>'.$inner.'</em>';

                continue;
            }

            if (in_array($tag, ['div', 'p'], true) && $inner !== '') {
                if ($content !== '' && ! str_ends_with($content, '<br>')) {
                    $content .= '<br>';
                }

                $content .= $inner.'<br>';

                continue;
            }

            $content .= $inner;
        }

        return $content;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
