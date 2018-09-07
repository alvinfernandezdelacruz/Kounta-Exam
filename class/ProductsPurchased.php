<?php
include_once getcwd() . '/class/Products.php';
include_once getcwd() . '/interface/ProductsPurchasedInterface.php';

class ProductsPurchased implements ProductsPurchasedInterface
{

    // declare initial value per every product
    private $poTotal = array(
        Products::BROWNIE => array(
            'received' => 0,
            'pending' => 0,
        ),
        Products::LAMINGTON => array(
            'received' => 0,
            'pending' => 0,
        ),
        Products::BLUEBERRY_MUFFIN => array(
            'received' => 0,
            'pending' => 0,
        ),
        Products::CROISSANT => array(
            'received' => 0,
            'pending' => 0,
        ),
        Products::CHOCOLATE_CAKE => array(
            'received' => 0,
            'pending' => 0,
        ),
    );

    private $pendingPos = array();

    /*
     * Returns total units purchased and received for the given product
     * @param int $productId
     * @return int
     */
    public function getPurchasedReceivedTotal(int $productId): int
    {
        return $this->poTotal[$productId]['received'];
    }

    /*
     * Returns total units purchased and pending for the given product
     * @param int $productId
     * @return int
     */
    public function getPurchasedPendingTotal(int $productId): int
    {
        return $this->poTotal[$productId]['pending'];
    }

    /*
     * Check if there's a product already have a purchase order for receiving
     * @return array
     */
    public function getPurchaseOrdersForReceiving()
    {
        $poProductIds = array();

        // loop in pendingPOs data and check the corresponding maturity if already 2 days
        foreach ($this->pendingPos as $productId => $maturity) {
            if ($maturity > 1) {
                $poProductIds[] = $productId;
            }
        }

        return $poProductIds;
    }

    /*
     * Process received purchase order and remove to pending purchase order
     * @param int $productId
     */
    public function updatePurchaseOrderAfterReceiving(int $productId)
    {
        // add total received PO from the given product
        $this->poTotal[$productId]['received'] += 20;

        // deduct to total pending PO of product and remove to pending PO data
        if ($this->poTotal[$productId]['pending'] > 0) {
            $this->poTotal[$productId]['pending'] -= 20;
        }
        unset($this->pendingPos[$productId]);
    }

    /*
     * Create pending purchase order for products below stock level limit
     * @param array $productIds
     */
    public function createPurchaseOrders(array $productIds)
    {
        // loop for every product and
        foreach ($productIds as $productId) {
            // create PO as long as the product has no existing pending PO
            if (!isset($this->pendingPos[$productId])) {
                $this->pendingPos[$productId] = 1;

                // add pending total
                $this->poTotal[$productId]['pending'] += 20;
            } else {
                // add maturity if already existing
                $this->pendingPos[$productId] += 1;
            }
        }
    }
}