<?php

class Products
{
    public const BROWNIE = 1;
    public const LAMINGTON = 2;
    public const BLUEBERRY_MUFFIN = 3;
    public const CROISSANT = 4;
    public const CHOCOLATE_CAKE = 5;

    public $products = array(
        self::BROWNIE => 'Brownie',
        self::LAMINGTON => 'Lamington',
        self::BLUEBERRY_MUFFIN => 'Blue Berry Muffin',
        self::CROISSANT => 'Croissant',
        self::CHOCOLATE_CAKE => 'Chocolate Cake'
    );

    /*
     * Returns all products and their corresponding display name
     */
    public function getAllProducts()
    {
        return $this->products;
    }
}

