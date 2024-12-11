<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Idosell
{
    protected static function post(string $uri, array $body = []): object
    {
        $url = 'https://' . config('services.idosell.domain') . '/api/admin/v3/' . $uri;

        $req = Http::withHeaders(self::headers())->post($url, $body);

        $res = json_decode($req->body());

        if (empty($res)) {
            $res = (object) [];
        }

        return $res;
    }

    protected static function put(string $uri, array $body = []): object
    {
        $url = 'https://' . config('services.idosell.domain') . '/api/admin/v3/' . $uri;

        $req = Http::withHeaders(self::headers())->put($url, $body);

        $res = json_decode($req->body());

        if (empty($res)) {
            $res = (object) [];
        }

        return $res;
    }

    public static function createStockDocument($request)
    {
        return self::post('wms/stocksdocuments/documents', ['params' => $request])->id;
    }

    public static function addProductsToStockDocument(int $documentId, array $products, string $type)
    {
        $request['params']['type'] = $type;
        $request['params']['id'] = $documentId;

        foreach ($products as $product) {
            $request['params']['products'][]  = [
                'product' => $product['product_id'],
                'size' => 'uniw',
                'quantity' => $product['quantity'],
                'productPurchasePrice' => $product['price'],
            ];
        }

        self::put('wms/stocksdocuments/products', $request);
    }

    public static function getProductByEan(string $ean)
    {
        $request['params']['returnElements'] = ['sizes'];
        $request['params']['containsCodePart'] = $ean;

        return self::post('products/products/get', $request);
    }

    private static function headers(): array
    {
        return [
            'X-API-KEY' => config('services.idosell.key'),
        ];
    }
}
