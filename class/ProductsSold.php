<?php
include_once getcwd() . '/class/Products.php';
include_once getcwd() . '/interface/ProductsSoldInterface.php';

class ProductsSold implements ProductsSoldInterface
{
    // declare initial value per every product
    private $sold = array(
        Products::BROWNIE => 0,
        Products::LAMINGTON => 0,
        Products::BLUEBERRY_MUFFIN => 0,
        Products::CROISSANT => 0,
        Products::CHOCOLATE_CAKE => 0,
    );

    /*
     * Returns total units sold for the given product
     * @param int $productId
     * @return int
     */
    public function getSoldTotal(int $productId): int
    {
        return $this->sold[$productId];
    }

    /*
     * Add ordered qty to total product sold
     * @param int $productId
     * @param int $orderedQty
     */
    public function addProductSold(int $productId, int $orderedQty)
    {
        $this->sold[$productId] += $orderedQty;
    }
}