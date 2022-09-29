<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\JsonApi\Classes\DiscountHandler;
use App\JsonApi\V1\Products\ProductCollectionQuery;
use Illuminate\Support\Collection;
use LaravelJsonApi\Laravel\Http\Controllers\Actions;

final class ProductController extends Controller
{

    use Actions\FetchMany;
    use Actions\FetchOne;

    private array $discountHandlers;

    public function __construct()
    {
        $this->loadDiscounts();
    }

    private function loadDiscounts(): void
    {
        $discounts = config('discounts');

        foreach ($discounts as $discount) {
            $this->discountHandlers[] = new DiscountHandler(
                $discount['field'], $discount['value'], $discount['discount']
            );
        }
    }

    public function searched(mixed $data, ProductCollectionQuery $query): Collection
    {
        $new_data = [];

        foreach ($data as $product) {
            $newProduct = [
                'sku' => $product->sku,
                'category' => $product->category,
                'name' => $product->name,
                'price' => [
                    'original' => $product->price,
                    'final' => $product->price,
                    'discount_percentage' => null,
                    'currency' => DiscountHandler::DEFAULT_CURRENCY,
                ]
            ];
            foreach ($this->discountHandlers as $discountHandler) {
                $discountHandler->applyTo($newProduct);
            }
            $new_data[] = $newProduct;
        }

        return new Collection($new_data);
    }
}
