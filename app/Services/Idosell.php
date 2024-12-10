<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Idosell
{
    public static function createStockDocument($params)
    {
        $response = Http::withHeaders(self::getHeaders())->
            post(self::getEndpoint('wms/stocksdocuments/documents'), [
                'params' => $params,
            ]);

        self::checkError($response);

        return $response->json();
    }

    public static function addProductsToStockDocument(int $documentId, array $products, string $type)
    {
        $params['type'] = $type;
        $params['id'] = $documentId;

        foreach ($products as $product) {
            $params['products'][]  = [
                'product' => $product['product_id'],
                'size' => 'uniw',
                'quantity' => $product['quantity']
            ];
        }

        $response = Http::withHeaders(self::getHeaders())->
            put(self::getEndpoint('wms/stocksdocuments/products'), [
                'params' => $params,
            ]);
    }

    public static function getProductByEan(string $ean)
    {
        $params = [];

        $response = Http::withHeaders(self::getHeaders())->
            put(self::getEndpoint('products/products/get'), [
                'params' => $params,
            ]);
    }

    private static function getHeaders(): array
    {
        return [
            'X-API-KEY' => config('services.idosell.key'),
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ];
    }

    private static function getEndpoint(string $action)
    {
        return sprintf('https://%s/api/admin/v3/%s', config('services.idosell.domain'), $action);
    }

    private static function checkError(Response $response)
    {
        $msg = match ($response->status()) {
            401 => __('Authorization error'),
            403 => __('Access denied'),
            404 => __('Not found'),
            422 => __('Invalid data format'),
            429 => __('Too many requests'),
            500 => __('Internal server error'),
            default => ''
        };

        if($msg)
            throw new Exception($msg);
    }
}
