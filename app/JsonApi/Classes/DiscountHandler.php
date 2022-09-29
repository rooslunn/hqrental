<?php

namespace App\JsonApi\Classes;

use App\JsonApi\Contracts\DiscountHandlerInterface;


final class DiscountHandler implements DiscountHandlerInterface
{
    public const DEFAULT_CURRENCY = 'EUR';

    public function __construct(
        private string $fieldName,
        private string $fieldValue,
        private float $discount
    )
    {}

    private function eligibleForDiscount(array $product): bool
    {
        return
            (array_key_exists($this->fieldName, $product))
            && ((string) $product[$this->fieldName] === $this->fieldValue);
    }

    public function applyTo(array &$product): void
    {
        if ($this->eligibleForDiscount($product)) {
            $this->applyDiscount($product, $this->discount);
        }
    }

    private function applyDiscount(array &$product, float $discount): void
    {
        $price = $product['price']['original'];
        $product['price']['final'] = $this->calculateFinalPrice($price, $discount);
        $product['price']['discount_percentage'] = $this->formatDiscountPercentage($discount);
    }

    private function calculateFinalPrice(int $price, float $discount): int
    {
        return (int) round(($price * (1 - $discount)));
    }

    private function formatDiscountPercentage(float $discount): string
    {
        return ((int) (100 * $discount) > 0) ? sprintf('%d%%', 100 * $discount) : 'null';
    }

}