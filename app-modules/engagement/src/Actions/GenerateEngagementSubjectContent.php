<?php

namespace AdvisingApp\Engagement\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class GenerateEngagementSubjectContent
{
    /**
     * @param string|array<string|int, string>|null $content
     * @param array<string, mixed> $mergeData
     *
     * @todo RefactorEngagementCampaignSubjectToJsonb: Once migration to JSONB subject is complete and all usages are updated,
     *       - make $content non-nullable (string|array)
     *       - remove the blank($content) check
     */
    public function __invoke(string|array|null $content, array $mergeData, Model $record, string $recordAttribute): HtmlString
    {
        if (blank($content)) {
            return new HtmlString('');
        }

        $html = tiptap_converter()
            ->mergeTagsMap($mergeData)
            ->record($record, $recordAttribute)
            ->asHTML($content);

        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        $text = Str::limit($text, 988, '');

        return new HtmlString($text);
    }
}
