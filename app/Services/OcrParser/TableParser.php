<?php

namespace App\Services\OcrParser;

class TableParser extends OcrParserAbstract
{
    public static function parse($ocrResponse): array
    {
        $blocksMap = [];
        $blockTables = [];

        foreach ($ocrResponse['Blocks'] as $block) {
            $blocksMap[$block['Id']] = $block;
            if ($block['BlockType'] == "TABLE") {
                $blockTables[] = $block;
            }
        }

        $tables = [];

        if (! empty($blockTables)) {
            foreach ($blockTables as $blockTable) {
                $tables[] = self::getTable($blockTable, $blocksMap);
            }
        }

        return $tables;
    }

    private static function getTable($blockTable, $blocksMap): array
    {
        $rows = $scores = [];

        foreach ($blockTable['Relationships'] as $relationship) {
            if ($relationship['Type'] == 'CHILD') {
                foreach ($relationship['Ids'] as $childId) {
                    $cell = $blocksMap[$childId];
                    if ($cell['BlockType'] == 'CELL') {
                        $rowIndex = $cell['RowIndex'];
                        $colIndex = $cell['ColumnIndex'];

                        if (! isset($rows[$rowIndex])) {
                            $rows[$rowIndex] = [];
                        }

                        $scores[$rowIndex][$colIndex] = $cell['Confidence'];

                        $rows[$rowIndex][$colIndex] = self::getText($cell, $blocksMap);
                    }
                }
            }
        }

        return [
            'rows' => $rows,
            'scores' => $scores,
        ];
    }
}
