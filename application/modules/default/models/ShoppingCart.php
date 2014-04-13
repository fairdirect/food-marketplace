<?php

class Model_ShoppingCart extends Model_ModelAbstract
{
    public $id;
    public $user_id;
    public $delivery_addr_id;
    public $billing_addr_id;
    public $payment_type;
    public $status = 'running';
    public $created;
    public $ip;

    private $_items = null;

    private $_delivery_address = null;
    private $_billing_address = null;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_ShoppingCart();
    }

    public function getDeliveryAddress(){
        if(is_null($this->_delivery_address) && $this->delivery_addr_id){
            $this->_delivery_address = Model_Address::find($this->delivery_addr_id);
        }
        return $this->_delivery_address;
    }

    public function getBillingAddress(){
        if(is_null($this->_billing_address) && $this->billing_addr_id){
            $this->_billing_address = Model_Address::find($this->billing_addr_id);
        }
        return $this->_billing_address;
    }

    /**
     * structure: 
     *      product_id
     *      shopping_cart_id
     *      product_price_id
     *      quantity
     *      added
     */
    public function getProducts(){
        if($this->id){
            $db = self::getDbTable()->getAdapter();
            $select = $db->select();       

            $select->where('shopping_cart_id = ?', $this->id);
            $select->from('epelia_products_shopping_carts');

            $ret = $db->fetchAll($select);
            return $ret;
        }

        return $this->_items;
    }

    /**
     * structure:
     * array(
     *      $shopId =>
     *          Model_Shop,
     *          'orderedProducts' => array(
     *              product_id
     *              shopping_cart_id
     *              product_price_id
     *              quantity
     *              added
     *           )
     * )
     */          
    public function getShopsWithOrderedProducts(){
        $products = $this->getProducts();
        $shops = array();
        foreach($products as $pr){
            $product = Model_Product::find($pr['product_id']);
            $shop = $product->getShop();
            if(!array_key_exists($shop->id, $shops)){
                $shops[$shop->id] = $shop;
                $shops[$shop->id]->orderedProducts = array();
            }
            $shops[$shop->id]->orderedProducts[] = $pr;
        }
        return $shops;

    }

    public function getShippingCosts(){
        $shops = $this->getShopsWithOrderedProducts();
        $shipping = 0.00;
        if($this->delivery_addr_id){
            $deliveryAddress = Model_Address::find($this->delivery_addr_id);
            foreach($shops as $shop_id => $shop){
                $shippingCosts = $shop->getShippingCosts();
                $val = 0.00;
                foreach($shop->orderedProducts as $pr){
                    $price = Model_ProductPrice::find($pr['product_price_id']);
                    $val += $pr['quantity'] * $price->value;
                }
                foreach($shippingCosts as $shippingCost){
                    if($shippingCost->country_id == $deliveryAddress->country && ($val < $shippingCost->free_from || !$shippingCost->free_from)){
                        $shipping += $shippingCost->value;
                    }
                }
            }
        }
        return $shipping;
    }
            
    public function getShippingCostsForShop($shopID){
        $shops = $this->getShopsWithOrderedProducts();
        $shipping = 0.00;
        if($this->delivery_addr_id){
            $deliveryAddress = Model_Address::find($this->delivery_addr_id);
            foreach($shops as $shop_id => $shop){
                if($shop_id == $shopID){
                    $shippingCosts = $shop->getShippingCosts();
                    $val = 0.00;
                    foreach($shop->orderedProducts as $pr){
                        $price = Model_ProductPrice::find($pr['product_price_id']);
                        $val += $pr['quantity'] * $price->value;
                    }
                    foreach($shippingCosts as $shippingCost){
                        if($shippingCost->country_id == $deliveryAddress->country && ($val < $shippingCost->free_from || !$shippingCost->free_from)){
                            $shipping += $shippingCost->value;
                        }
                    }
                }
            }
        }
        return $shipping;
    }

    public function getPrice(){
        $cartProducts = $this->getProducts();
        $total = 0;
        foreach($cartProducts as $item){
            $product = Model_Product::find($item['product_id']);
            $price = Model_ProductPrice::find($item['product_price_id']);
            $total += $price->value * $item['quantity'];
        }
        return $total;
    }

    public function getPriceTotal(){
        return $this->getPrice() + $this->getShippingCosts();
    }

    public static function findByUser($userID, $status = null){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('user_id = ?', $userID);
        if(!is_null($status)){
            $select->where('status = ?', $status);
        }
        $select->order('id DESC');
        
        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findByStatus($status){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('status = ?', $status);
        $select->order('id ASC');
        
        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }


    public static function getRunningShoppingCart(){
        $session = new Zend_Session_Namespace('Default');
        if(Zend_Auth::getInstance()->hasIdentity() && (Zend_Auth::getInstance()->getIdentity())){
            $shoppingCart = $session->shoppingCart;
            if(!$shoppingCart){
                $shoppingCart = new self(array('user_id' => Zend_Auth::getInstance()->getIdentity()->id));
            }
            $session->shoppingCart = $shoppingCart;
        }
        else{
            if(!$session->shoppingCart){
                $session->shoppingCart = new self(array());
            }
        }
        return $session->shoppingCart;
    }

    public function delete(){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $this->id);
        $table->delete($where); // by ON CASCADE datasets in epelia_products_shopping_carts are removed 
    }

    public function addProduct($productID, $priceID, $quantity){
        $session = new Zend_Session_Namespace('Default');
        $this->_items[] = array(
            'product_id' => $productID,
            'product_price_id' => $priceID,
            'quantity' => $quantity
        );
    }

    public function changeQuantity($productID, $priceID, $delta){
        $session = new Zend_Session_Namespace('Default');
        if(!$this->_items || !is_array($this->_items)){
            return false;
        }
        foreach($this->_items as &$item){
            if($item['product_id'] == $productID && $item['product_price_id'] == $priceID){
                $item['quantity'] += $delta;
                return true;
            }
        }
        return false;
    }

    public function changeQuantityAbsolute($productID, $priceID, $quantity){
        $session = new Zend_Session_Namespace('Default');
        foreach($this->_items as &$item){
            if($item['product_id'] == $productID && $item['product_price_id'] == $priceID){
                $item['quantity'] = $quantity;
                return true;
            }
        }
        return false;
    }

    public function deleteProduct($productID, $priceID){
        $session = new Zend_Session_Namespace('Default');
        foreach($this->_items as $key => $item){
            if($item['product_id'] == $productID && $item['product_price_id'] == $priceID){
                unset($this->_items[$key]);
            }
        }
    }

    /*
     * returns array of failed items if not successfull, empty array if successfull
     */
    public function concludeOrder(){
        
        $this->save();
        $failedItems = array();
       
        self::getDbTable()->getAdapter()->beginTransaction();
        foreach($this->_items as $item){ // cannot use $this->getProducts() here, for this fetched from database
            // using transactions and >= 0 constraint for stock, ensuring stock cant drop below 0
            try{
                $query = self::getDbTable()->getAdapter()->query('UPDATE epelia_products SET stock = (stock - 1) WHERE id = ? AND stock IS NOT NULL', array($item['product_id'])); // do not decrease if stock is null => unlimited stock
                $query = self::getDbTable()->getAdapter()->query('INSERT INTO epelia_products_shopping_carts(product_id, shopping_cart_id, product_price_id, quantity) VALUES(?, ?, ?, ?)', array($item['product_id'], $this->id, $item['product_price_id'], $item['quantity']));
           } catch(Exception $e){
            exit($e->getMessage());
               $failedItems[] = $item;
           }
        }
        if(!empty($failedItems)){
            self::getDbTable()->getAdapter()->rollBack();
            return $failedItems;
        }
        else{
            self::getDbTable()->getAdapter()->commit();
            $this->status = 'ordered';
            $this->save();
            $session = new Zend_Session_Namespace('Default');
            $session->shoppingCart = false;
            return $failedItems;
        }
    }
}

