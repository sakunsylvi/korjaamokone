<?php
/**
 * Minimal Markdown to HTML converter
 *
 * Supports: paragraphs, **bold**, *italic*, [links](url), line breaks
 */

/**
 * Convert a subset of Markdown to safe HTML.
 *
 * Input is escaped first (htmlspecialchars), then Markdown syntax is
 * converted to HTML tags, so the output is safe from XSS.
 */
function markdown_to_html(string $text): string
{
    // Normalize line endings
    $text = str_replace("\r\n", "\n", $text);
    $text = str_replace("\r", "\n", $text);

    // Escape HTML entities first (XSS protection)
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    // Bold: **text**
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);

    // Italic: *text*  (but not inside <strong> tags' asterisks, already consumed above)
    $text = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $text);

    // Links: [text](url) - URL was already escaped, unescape &amp; back for href
    $text = preg_replace_callback(
        '/\[(.+?)\]\((.+?)\)/',
        function ($matches) {
            $label = $matches[1];
            $url = html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8');
            $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            return '<a href="' . $url . '" target="_blank" rel="noopener">' . $label . '</a>';
        },
        $text
    );

    // Split into paragraphs on double newlines
    $blocks = preg_split('/\n{2,}/', trim($text));

    $html = '';
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        // Single newlines within a paragraph become <br>
        $block = str_replace("\n", "<br>\n", $block);
        $html .= '<p>' . $block . "</p>\n";
    }

    return $html;
}
