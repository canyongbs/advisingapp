<?php

namespace AdvisingApp\Engagement\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class GenerateEngagementSubjectContent
{
    // public function __invoke(string|array $content, array $mergeData, Model $record, string $recordAttribute): HtmlString
    public function __invoke(string|array $content, array $mergeData, Model $record, string $recordAttribute): string
    {
        // Step 1: Convert Tiptap JSON to HTML with merge tag substitution
        $html = tiptap_converter()
            ->mergeTagsMap($mergeData)
            ->record($record, $recordAttribute)
            ->asHTML($content);

        // Step 2: Strip all HTML tags
        $text = strip_tags($html);

        // Step 3: Decode any HTML entities (e.g., &#64; => @)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Step 4: Normalize whitespace
        $text = trim(preg_replace('/\s+/u', ' ', $text ?? ''));

        // Step 5: Truncate to 988 characters
        return Str::limit($text, 988, '');
    }
}
