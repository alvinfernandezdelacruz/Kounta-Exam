<?php
include_once getcwd() . '/interface/OrderProcessorInterface.php';

class OrderProcessor implements OrderProcessorInterface
{
    protected $Inventory;
    protected $ProductsSold;
    protected $ProductsPurchased;

    public function __construct(
        Inventory $Inventory,
        ProductsSold $ProductsSold,
        ProductsPurchased $ProductsPurchased,
        Products $Products
    ) {
        $this->Inventory         = $Inventory;
        $this->ProductsSold      = $ProductsSold;
        $this->ProductsPurchased = $ProductsPurchased;
        $this->Products          = $Products;
    }

    /*
     * Process inputted file path and output the total results of orders for the whole week
     * @param string $filePath
     */
    public function processFromJson(string $filePath): void
    {
        // check if $filePath has a value
        if (empty($filePath)) {
            echo "Please indicate JSON file path";
            exit;
        }

        // check if file path exists
        if (!file_exists($filePath)) {
            echo "Inputted JSON file path does not exists";
            exit;
        }
        // parse JSON file
        $jsonContent = file_get_contents($filePath);
        $weeklyOrders = json_decode($jsonContent, true);

        // check if JSON file has been decoded properly
        if (empty($weeklyOrders)) {
            echo "Invalid JSON file. Please check content format.";
            exit;
        }

        // loop for every day in a week
        foreach ($weeklyOrders as $dailyOrders) {

            // at the start of the day, check if there's a purchase order for receiving
            $poReceivedProductIds = $this->ProductsPurchased->getPurchaseOrdersForReceiving();
            if (!empty($poReceivedProductIds)) {
                // process purchase order for every products fetched
                $this->processPurchaseOrder($poReceivedProductIds);
            }

            // loop for every orders in a day
            foreach ($dailyOrders as $order) {
                // check if order is not empty and all ordered products have enough stock. Otherwise, reject the order
                if (!$this->validateOrder($order)) {
                    continue;
                }

                // proceed to processing the order
                $this->processOrder($order);
            }

            // at the end of the day, check if there's a product already below stock limit for PO
            $productsIdsForPo = $this->Inventory->getProductsForPurchaseOrder();

            // create a purchase order for every products fetched
            if (!empty($productsIdsForPo)) {
                $this->ProductsPurchased->createPurchaseOrders($productsIdsForPo);
            }
        }

        $this->outputResults();
    }

    /*
     * Checks if all the products ordered have enough stock
     * @param array $order
     * @return bool
     */
    public function validateOrder($order)
    {
        if (empty($order)) {
            return false;
        }

        // check if supplies are enough for every product in the order
        foreach ($order as $productId => $orderedQty) {
            $productStockLevel = $this->Inventory->getStockLevel($productId);
            if ($orderedQty > $productStockLevel) {
                return false;
            }
        }
        return true;
    }

    /*
     * Deduct the products sold in current stock level
     * @param array $order
     */
    public function processOrder($order)
    {
        // loop for every ordered product
        foreach ($order as $productId => $orderedQty) {
            // update inventory data
            $this->Inventory->deductStock($productId, $orderedQty);

            // update product sold data
            $this->ProductsSold->addProductSold($productId, $orderedQty);
        }
    }

    /*
     * Process purchase order already for receiving
     * @param array $productIds
     */
    public function processPurchaseOrder($productIds)
    {
        // process every products for PO creation
        foreach ($productIds as $productId) {
            // get the additional stock received from PO
            $this->Inventory->addStock($productId, 20);

            // update PO data after receiving
            $this->ProductsPurchased->updatePurchaseOrderAfterReceiving($productId);
        }
    }

    /*
     * Output the result of the orders for the whole week in a nice text format
     */
    public function outputResults()
    {
        // loop all products and display results per product
        foreach ($this->Products->getAllProducts() as $productId => $productName) {
            echo "Product ID: " . $productId . "\n";
            echo "Product Name: " . $productName . "\n";
            echo "Total Units Sold: " . $this->ProductsSold->getSoldTotal($productId) . "\n";
            echo "Total Units Purchased and Pending: " . $this->ProductsPurchased->getPurchasedPendingTotal($productId) . "\n";
            echo "Total Units Purchased and Received: " . $this->ProductsPurchased->getPurchasedReceivedTotal($productId) . "\n";
            echo "Current Stock Level: " . $this->Inventory->getStockLevel($productId) . "\n\n";
        }
    }
}