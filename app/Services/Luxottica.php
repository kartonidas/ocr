<?php

namespace App\Services;

use App\Enums\OcrDocumentType;
use App\Exceptions\OcrParseException;
use App\Models\OcrDocument;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Luxottica
{
    private OcrDocument $ocrDocument;
    private array $forms;
    private array $tables;
    private array $products;

    private const SUPPLIER_ID = 18;

    public function __construct(OcrDocument $ocrDocument)
    {
        $this->ocrDocument = $ocrDocument;

        $this->forms = $ocrDocument->texts()->where('type', OcrDocumentType::FORMS)->get()->toArray();
        $this->tables = $ocrDocument->getTables();

        $this->products = $this->getProductsTable();
    }

    public function createDocument()
    {
        $params = [
            'type' => $this->getDocumentType(),
            'stockId' => $this->getWarehouse(),
            'note' => $this->getDocumentNote(),
            'delivererId' => self::SUPPLIER_ID,
            'stockDocumentNumber' => $this->getInvoiceNo(),
            'saleDocumentCreationDate' => Carbon::parse($this->getDocumentDate())->format('Y-m-d'),
            'priceType' => 'netto',
            'productsInPreorder' => 'n',
            'wnt' => 'national_VAT_invoice',
            'currencyForPurchasePrice' => 'PLN',
        ];

        if (config('app.env') == 'local') {
            $params['stockId'] = 1;
        }

        $this->prepareProductsToIdosell();

        dump($params);


        //Idosell::createStockDocument($params);
    }

    private function getDocumentType(): string
    {
        foreach ($this->products as $product) {
            if (! empty($product['_glasses_set'])) {
                foreach ($product['_glasses_set'] as $glassesSet) {
                    if (self::checkPhraseOccurs($glassesSet['opis'], 'soczewki korekcyjne OTD')) {
                        return 'pz';
                    }

                    if (self::checkPhraseOccurs($glassesSet['opis'], 'soczewki korekcyjne RB')) {
                        return 'pz';
                    }
                }

                if (! empty($product['_glasses_set'][0]['rozmiar / kolor'])) {
                    if (preg_match('/rbcp|oocp/i', $product['_glasses_set'][0]['rozmiar / kolor'])) {
                        return 'pz';
                    }
                }
            }
        }

        $pzDocumentPhrases = [
            'Z9HI',
            'Z9H4',
            'Z9HZ',
            'Z9JT',
            'ZLGY',
            'ZLGZ',
            '1354926',
            '1212408',
        ];

        if ($this->findInTable('/(' . implode('|', $pzDocumentPhrases) . ')/i')) {
            return 'pz';
        }

        return 'pw';
    }

    private function getWarehouse(): int
    {
        $warehouses = [
            'Sklep 12544' => 1,
            'Sklep 0001130963' => 1,
            'Sklep 0001354926' => 5,
            'Sklep 12545' => 6,
            'Sklep 0001139584' => 6,
            'Sklep 0001331453' => 7,
            'Sklep 0001478626' => 8,
            'Sklep 0001067699' => 10,
            'Sklep 0001212408' => 10,
            'Sklep 12541' => 11,
            'Sklep 0001139614' => 11,
            'Sklep 0001354927' => 12,
            'Sklep 30410' => 12,
        ];

        $warehouseShopName = $this->findInTable('/(sklep [0-9]+)( .*)?/i');
        if (! empty($warehouseShopName)) {
            foreach ($warehouses as $warehouse => $warehouseId) {
                if (strcasecmp($warehouse, $warehouseShopName) === 0) {
                    return $warehouseId;
                }
            }
        }

        throw new OcrParseException(__('Nie odnaleziono magazynu przyjmującego dostawę.'));
    }

    private function getDocumentNote(): string
    {
        $note = [
            $this->getTransportNote(),
            'Faktura nr ' . $this->getInvoiceNo(),
            'Zamówienie nr ' . $this->getOrderNo()
        ];

        return implode(PHP_EOL, $note);
    }

    private function getTransportNote(): string
    {
        $documentNote = $this->findInFormKeys('Delivery ZL01');
        if (! empty($documentNote[0])) {
            return $documentNote[0];
        }

        $documentNote = $this->findInFormKeys('ZL01');
        if (! empty($documentNote[0])) {
            return $documentNote[0];
        }

        $documentNote = $this->findInFormKeys('Delivery');
        if (! empty($documentNote[0])) {
            preg_match('/ZL01 ([0-9]+)/i', $documentNote[0], $matches);
            if (! empty($matches[1]))
                return $matches[1];
        }

        $documentNote = $this->findInTable('/ZL01 ([0-9]+)/i');
        if (! empty($documentNote)) {
            return $documentNote;
        }

        throw new OcrParseException(__('Nie odnaleziono notatki do zamówienia.'));
    }

    private function getInvoiceNo(): string
    {
        $invoiceNo = $this->findInFormKeys('Faktura Nr');

        if (! empty($invoiceNo[0])) {
            return $invoiceNo[0];
        }

        throw new OcrParseException(__('Nie odnaleziono numeru faktury.'));
    }

    private function getOrderNo(): string
    {
        $orderNo = $this->findInFormKeys('Zamówienie:');
        if (! empty($orderNo[0])) {
            return $orderNo[0];
        }

        $orderNo = $this->findInTable('/zamówienie: (.*)/i');
        if (! empty($orderNo[1])) {
            return $orderNo[1];
        }

        throw new OcrParseException(__('Nie odnaleziono numeru zamówienia.'));
    }

    private function getDocumentDate(): string
    {
        $documentDate = $this->findInFormKeys('data wystawienia');
        if (! empty($documentDate[0])) {
            return $documentDate[0];
        }

        throw new OcrParseException(__('Nie odnaleziono daty dokumentu.'));
    }

    private function findInFormKeys(string $key)
    {
        foreach ($this->forms[0]['result'] as $field => $form) {
            if (strcasecmp($field, $key) === 0) {
                return $form;
            }
        }
    }

    private function findInTable(string $reg): string|null
    {
        foreach ($this->tables as $table) {
            foreach ($table['rows'] as $row) {
                foreach ($row as $cell) {
                    if (preg_match($reg, $cell, $matches)) {
                        if (! empty($matches[1])) {
                            return $matches[1];
                        }
                    }
                }
            }
        }

        return null;
    }

    private function getProductsTable(): array
    {
        $productsTable = false;

        foreach ($this->tables as $table) {
            if (count($table['header']) == 11 || count($table['header']) == 12) {
                $productsTable = $table;
            }
        }

        if ($productsTable === false) {
            foreach ($this->tables as $table) {
                if (mb_strtolower(trim($table['header'][array_key_first($table['header'])])) == 'opis') {
                    $productsTable = $table;
                }
            }
        }

        if (! $productsTable) {
            throw new OcrParseException(__('Nie odnaleziono tabeli produktowej.'));
        }

        $header = array_map(fn ($h): string => strtolower($h), $productsTable['header']);

        $products = [];
        $glassesWithLensesSet = false;

        foreach ($productsTable['rows'] as $i => $productRow) {
            $product = array_combine($header, $productRow);

            if (! empty($product['opis'])) {
                if (preg_match('/^transport/i', $product['opis'])) {
                    if ($glassesWithLensesSet !== false) {
                        $glassesWithLensesSet = false;
                    }

                    continue;
                }

                $nextProductRow = isset($productsTable['rows'][$i + 1]) ? array_combine($header, $productsTable['rows'][$i + 1]) : [];
                if ($this->isGlassesWithLensesSet($product, $nextProductRow)) {
                    $products[] = $product;
                    $glassesWithLensesSet = array_key_last($products) ?? 0;

                    continue;
                }

                if ($glassesWithLensesSet === false) {
                    $products[] = $product;
                } else {
                    $glassesWithLensesPhrasesLine2 = $this->getGlassesWithLensesPhrasesLine2();
                    if (!preg_match('/'.implode('|', $glassesWithLensesPhrasesLine2).'/i', Str::ascii($product['opis']))) {
                        $products[$glassesWithLensesSet]['_glasses_set'][] = $product;
                    }
                }
            }
        }

        return $products;
    }

    private function isGlassesWithLensesSet(array $product, array $nextProductRow = []): bool
    {
        $glassesWithLensesSet = false;

        $glassesWithLensesPhrases = $this->getGlassesWithLensesPhrasesLine1();
        if (preg_match('/'.implode('|', $glassesWithLensesPhrases).'/i', Str::ascii($product['opis']), $matches)) {
            if (strcasecmp($matches[0], $glassesWithLensesPhrases[4]) === 0 || strcasecmp($matches[0], $glassesWithLensesPhrases[5]) === 0) {
                $glassesWithLensesPhrasesLine2 = $this->getGlassesWithLensesPhrasesLine2();
                if ($nextProductRow) {
                    if (preg_match('/'.implode('|', $glassesWithLensesPhrasesLine2).'/i', Str::ascii($nextProductRow['opis']))) {
                        $glassesWithLensesSet = true;
                    }
                }
            } else {
                $glassesWithLensesSet = true;
            }
        }

        return $glassesWithLensesSet;
    }

    private function getGlassesWithLensesPhrasesLine1(): array
    {
        $glassesWithLensesPhrases = [
            "Okulary przeciwsłoneczne z soczewkami korekcyjnymi złożone z następujących komponentów",
            "Okulary optyczne z soczewkami korekcyjnymi złożone z następujących komponentów",
            "Okulary przeciwsłoneczne z soczewkami korekcyjnymi złożony z następujących komponentów",
            "Okulary optyczne z soczewkami korekcyjnymi złożony z następujących komponentów",
            "Okulary przeciwsłoneczne z soczewkami korekcyjnymi",
            "Okulary optyczne z soczewkami korekcyjnymi",
        ];
        return array_map(fn ($v): string => Str::ascii($v), $glassesWithLensesPhrases);
    }

    private function getGlassesWithLensesPhrasesLine2(): array
    {
        $glassesWithLensesPhrasesLine2 = [
            "złożone z następujących komponentów",
            "złożony z następujących komponentów",
            "ztożone z następujących komponentów",
            "ztożony z następujących komponentów",
        ];
        return array_map(fn ($v): string => Str::ascii($v), $glassesWithLensesPhrasesLine2);
    }

    private function prepareProductsToIdosell()
    {
        $out = [];

        foreach ($this->products as $product) {
            $productId = null;
            if (! empty($product['_glasses_set'])) {
                foreach ($product['_glasses_set'] as $prod) {
                    if (self::checkPhraseOccurs($prod['opis'], 'soczewki korekcyjne OTD')) {
                        $productId = 5633;
                    } elseif (self::checkPhraseOccurs($prod['opis'], 'soczewki korekcyjne RB')) {
                        $productId = 18330;
                    } elseif (self::checkPhraseOccurs($prod['opis'], 'soczewki korekcyjne OAK')) {
                        $productId = 5633;
                    }
                }
            }

            if ($productId === null && self::checkPhraseOccurs($product['opis'], 'soczewki korekcyjne rb')) {
                $productId = 18330;
            }

            if ($productId === null && self::checkPhraseOccurs($product['opis'], 'soczewki korekcyjne rbcp')) {
                $productId = 73558;
            }

            if ($productId === null && self::checkPhraseOccurs($product['opis'], 'soczewki korekcyjne oocp')) {
                $productId = 73557;
            }

            if ($productId === null) {

            }

            dump($productId);
        }

    }

    private function checkPhraseOccurs(string $text, string $phrase): bool
    {
        $phrase = explode(' ', $phrase);

        $match = true;
        foreach ($phrase as $p) {
            $match &= stripos($text, $p) !== false;
        }

        return $match;
    }
}
