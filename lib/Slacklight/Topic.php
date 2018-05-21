<?php
namespace Slacklight;
SessionContext::create();

class ShoppingCart extends BaseObject {

    public static function add(int $bookId) {
        $c = self::getCart();
        $c[$bookId] = $bookId;
        self::storeCart($c);
    }

    public static function remove (int $bookId) {
        $c = self::getCart();
        unset($c[$bookId]);
        self::storeCart($c);
    }

    public static function clear() {
        self::storeCart(array());
    }

    public static function contains (int $bookId) : bool {
        $c = self::getCart();
        return array_key_exists($bookId, $c);
    }

    public static function size() : int {
        return sizeof(self::getCart());
    }

    public static function getAll() : array {
        return self::getCart();
    }


    private static function getCart() : array {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    }

    private static function storeCart(array $cart) {
        $_SESSION['cart'] = $cart;
    } 

}