<?php

namespace App\Services\OcrParser;

abstract class OcrParserAbstract
{
    abstract protected static function parse(string $ocrResponse): array;

    protected static function getText($result, $blockMap): string {
        $text = '';

        if (! empty($result['Relationships'])) {
            foreach ($result['Relationships'] as $relationship) {
                if ($relationship['Type'] == 'CHILD') {
                    foreach ($relationship['Ids'] as $childId) {
                        $word = $blockMap[$childId] ;
                        if ($word['BlockType'] == 'WORD') {
                            $text .= $word['Text'] . ' ';
                        }
                    }
                }
            }
        }

        return $text;
    }
}
