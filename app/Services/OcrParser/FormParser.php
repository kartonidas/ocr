<?php

namespace App\Services\OcrParser;

class FormParser extends OcrParserAbstract
{
    public static function parse($ocrResponse): array
    {
        $kvMap = self::getKvMap($ocrResponse);

        return self::getKvRelationship($kvMap['key'], $kvMap['value'], $kvMap['block']);
    }

    private static function getKvMap($ocrResponse): array
    {
        $keyMap = $valueMap = $blockMap = [];

        foreach ($ocrResponse['Blocks'] as $block) {
            $blockId = $block['Id'];
            $blockMap[$blockId] = $block;
            if ($block['BlockType'] == 'KEY_VALUE_SET') {
                if (in_array('KEY', $block['EntityTypes'])) {
                    $keyMap[$blockId] = $block;
                }
                else {
                    $valueMap[$blockId] = $block;
                }
            }
        }

        return [
            'key' => $keyMap,
            'value' => $valueMap,
            'block' => $blockMap,
        ];
    }

    private static function getKvRelationship($keyMap, $valueMap, $blockMap): array
    {
        $kvs = [];

        foreach ($keyMap as $keyBlock) {
            $valueBlock = self::findValueBlock($keyBlock, $valueMap);

            $key = self::getText($keyBlock, $blockMap);
            $val = self::getText($valueBlock, $blockMap);

            $kvs[$key][] = $val;
        }

        return $kvs;
    }

    private static function findValueBlock($keyBlock, $valueMap): array
    {
        $valueBlock = '';

        foreach ($keyBlock['Relationships'] as $relationship) {
            if ($relationship['Type'] == 'VALUE') {
                foreach ($relationship['Ids'] as $valueId) {
                    $valueBlock = $valueMap[$valueId];
                }
            }
        }

        return $valueBlock;
    }
}
