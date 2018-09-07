<?php
include_once getcwd() . '/class/Products.php';
include_once getcwd() . '/interface/InventoryInterface.php';

class Inventory implements InventoryInterface
{
    // declare initial value per every product
    private $stock = array(
        Products::BROWNIE => 20,
        Products::LAMINGTON => 20,
        Products::BLUEBERRY_MUFFIN => 20,
        Products::CROISSANT => 20,
        Products::CHOCOLATE_CAKE => 20,
    );

    /*
     * Returns current stock level of the given product
     * @param int $productId
     * @return int
     */
    public function getStockLevel(int $productId): int
    {
        return $this->stock[$productId];
    }

    /*
     * Deduct ordered quantity in the current stock level
     * @param int $productId
     * @param int $orderQty
     */
    public function deductStock(int $productId, int $orderedQty)
    {
        $this->stock[$productId] -= $orderedQty;
    }

    /*
     * Add the stock from received purchase order in the current stock level
     * @param int $productId
     * @param int $additionalStock
     */
    public function addStock(int $productId, int $additionalStock)
    {
        $this->stock[$productId] += $additionalStock;
    }


    /*
     * Returns products below stock level limit (for creation of Purchase Order)
     * @return array
     */
    public function getProductsForPurchaseOrder()
    {
        $poProductIds = array();

        // loop for every products in inventory data
        foreach ($this->stock as $productId => $stockLevel) {
            // check if current stock level of product is already below stock limit
            if ($stockLevel < 10) {
                $poProductIds[] = $productId;
            }
        }

        return $poProductIds;
    }
}