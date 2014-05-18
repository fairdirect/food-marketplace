<?php

define('ORIG_IMG_PATH', '/var/www/vhosts/epelia.com/httpdocs/apps/epelia/user_files/images/shop_logos/');
define('ORIG_PROD_IMG_PATH', '/var/www/vhosts/epelia.com/httpdocs/apps/epelia/user_files/images/pictures/');

ob_start();
echo "-----------------------------------------------\n";
echo "\033[32m Starte Migrierung...\033[0m\n";
echo "-----------------------------------------------\n";
ob_flush();

/* create postgres connection */
try{
    $postgres = new PDO('pgsql:dbname=epelia;host=localhost', 'epelia', '123456');
    $postgres->exec("set names utf8");
} catch(Exception $e){
    exit($e->getMessage());
}

/* create mysql connection */
$mysql = new mysqli('localhost', 'username', 'password', 'database');
$mysql->set_charset('utf8');

/* empty database */
$postgres->exec('UPDATE epelia_users SET main_delivery_address_id = null, main_billing_address_id = null, last_delivery_address_id = null, last_billing_address_id = null');
$postgres->exec('DELETE FROM epelia_shopping_carts');
$postgres->exec('DELETE FROM epelia_products_orders');
$postgres->exec('DELETE FROM epelia_orders');
$postgres->exec('DELETE FROM epelia_product_prices');
$postgres->exec('DELETE FROM epelia_products_product_attributes');
$postgres->exec('DELETE FROM epelia_products_pictures');
$postgres->exec('DELETE FROM epelia_products');
$postgres->exec('DELETE FROM epelia_shipping_costs');
$postgres->exec('DELETE FROM epelia_shops');
$postgres->exec('DELETE FROM epelia_addresses');
$postgres->exec('DELETE FROM epelia_countries');
$postgres->exec('DELETE FROM epelia_product_categories');
$postgres->exec('DELETE FROM epelia_product_groups');
$postgres->exec('DELETE FROM epelia_bank_accounts');
$postgres->exec('DELETE FROM epelia_products_product_attributes');
$postgres->exec('DELETE FROM epelia_product_attributes');
$postgres->exec('DELETE FROM epelia_users');
$postgres->exec('DELETE FROM epelia_pictures');

/* prepare insert statements */
$country_query = $postgres->prepare(
    "INSERT INTO epelia_countries(
            id, name, phone)
        VALUES(
            ?, ?, ?
        )");
$group_query = $postgres->prepare(
    "INSERT INTO epelia_product_groups(
            name, type, description)
        VALUES(
            ?, ?, ?
        ) RETURNING id");
$category_query = $postgres->prepare(
    "INSERT INTO epelia_product_categories(
            name, product_group_id, description)
        VALUES(
            ?, ?, ?
        ) RETURNING id");
$user_query = $postgres->prepare( 
    "INSERT INTO epelia_users(
            phpbb_id, email, password, salt, birthday, type, is_admin, is_wholesale, status, registered, last_login, deleted, old_password_hash)
        VALUES(
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        ) RETURNING id");
$bank_account_query = $postgres->prepare( 
    "INSERT INTO epelia_bank_accounts(
            user_id, account_nr, bank_id, bank_name, account_holder)
        VALUES(
            ?, ?, ?, ?, ?
        )");
$shop_query = $postgres->prepare(
    "INSERT INTO epelia_shops(
            user_id, name, url, provision, taxnumber, salestax_id, status, company, street, house, zip, city, country, phone, fax, small_business, register_id, register_court, office, representative, eco_control_board, eco_control_id, bank_account_holder, bank_account_number, bank_id, bank_name, bank_swift, bank_iban, description, history, philosophy, procedure, additional, type, created, logo_id)
        VALUES(
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        ) RETURNING id");
$shipping_query = $postgres->prepare( 
    "INSERT INTO epelia_shipping_costs(
            shop_id, country_id, value, free_from)
        VALUES(
            ?, ?, ?, ?
        )");
$product_query = $postgres->prepare(
    "INSERT INTO epelia_products(
            shop_id, name, description, num_views, active, stock, category_id, ingredients, traces, is_bio, is_discount, tags)
        VALUES(
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        ) RETURNING id");

$address_query = $postgres->prepare(
    "INSERT INTO epelia_addresses(
            user_id, gender, company, firstname, name, street, house, zip, city, country)
        VALUES(
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        ) RETURNING id");

$attributes_query = $postgres->prepare(
    "INSERT INTO epelia_product_attributes(
            name, type)
        VALUES(
            ?, ?
        ) RETURNING id");

$attributes_product_query = $postgres->prepare(
    "INSERT INTO epelia_products_product_attributes(
            product_id, product_attribute_id)
        VALUES(
            ?, ?
        )");

$price_query = $postgres->prepare(
    "INSERT INTO epelia_product_prices(
            product_id, value, quantity, is_wholesale, unit_type_id, content_type_id, contents)
        VALUES(
            ?, ?, ?, ?, ?, ?, ?
        )");

$picture_query = $postgres->prepare(
    "INSERT INTO epelia_pictures(
            filename)
        VALUES(
            ?
        ) RETURNING id");

$products_pictures_query = $postgres->prepare(
    "INSERT INTO epelia_products_pictures(
            product_id, picture_id)
        VALUES(
            ?, ?
        )");

$product_ratings_query = $postgres->prepare(
    "INSERT INTO epelia_product_ratings(
            product_id, user_id, comment, rating, status, created)
        VALUES(
            ?, ?, ?, ?, ?, ?
        )");

$main_pic_query = $postgres->prepare("UPDATE epelia_products SET main_picture_id = ? WHERE id = ?");
    

$groupCount = $categoryCount = $attributeCount = $attributesProductCount = $userCount = $shopCount = $productCount = $priceCount = $shippingCount = $bankAccountCount = $addressesCount = $logoCount = $productPictureCount = $productRatingCount = 0; // successcounter
$productWithoutCategoryCount = $shopWithoutAddressCount = 0; // warningcounter
$groupErrorCount = $categoryErrorCount = $attributeErrorCount = $attributesProductErrorCount = $userErrorCount = $shopErrorCount = $productErrorCount = $priceErrorCount = $shippingErrorCount = $bankAccountErrorCount = $addressesErrorCount = 0; // errorCounter

$countries = array(
    array('DE', 'Deutschland', '049'),
    array('AT', 'Österreich', '043'),
    array('CH', 'Schweiz', '041'),
    array('BE', 'Belgien', '032'),
    array('IT', 'Italien', '039'),
    array('DK', 'Dänemark', '045'),
    array('FR', 'Frankreich', '033'),
    array('LT', 'Litauen', '0370'),
    array('LV', 'Lettland', '0371'),
    array('EE', 'Estland', '0372'),
    array('LU', 'Luxemburg', '0352'),
    array('NL', 'Niederlande', '031'),
    array('PL', 'Polen', '048'),
    array('SE', 'Schweden', '046'),
    array('FI', 'Finnland', '0358'),
    array('NO', 'Norwegen', '047'),
    array('RU', 'Russland', '007'),
    array('RO', 'Romänien', '040'),
    array('GR', 'Griechenland', '030'),
    array('GB', 'Vereinigtes Königreich', '044'),
    array('ES', 'Spanien', '034'),
    array('SK', 'Slovakei', '0421'),
    array('LI', 'Liechtenstein', '0423'),
    array('CZ', 'Tschechien', '0420'),
    array('BG', 'Bulgarien', '0359'),
    array('US', 'USA', '001')
);
foreach($countries as $c){
    $country_query->execute($c);
}

/* 
 * $groupMapping has structure:
 * $oldID => $newID
 */
$groupMapping = array();

/* 
 * $catMapping has structure:
 * $oldID => $newID
 */
 $catMapping = array();

/* 
 * $attributeMapping has structure:
 * $oldID => $newID
 */
 $attributeMapping = array();

/* 
 * $userMapping has structure:
 * $oldID => $newID
 */
$userMapping = array();

/* 
 * $productMapping has structure:
 * $oldID => $newID
 */
$productMapping = array();

/*
 * $mainPicMapping has scructure:
 * $oldPicId => $newProductId
 */
$mainPicMapping = array();


/*
 * $unitMapping has structure:
 * $oldID => $newID
 *
 *   1	Paket	Pakete
 *   2	Glas	Gläser
 *   3	Flasche	Flaschen
 *   4	Packung	Packungen
 *   5	Stück	Stücke
 *   6	Laib	Laibe
 *   8	Palette	Paletten
 *   9	Dose	Dosen
 */

$unitMapping = array(
    '1' => '1',
    '2' => '2',
    '3' => '2',
    '4' => '3',
    '5' => '3',
    '6' => '4',
    '7' => '4',
    '8' => '5',
    '9' => '5',
    '10' => '1',
    '11' => '6',
    '12' => '9',
    '13' => '9',
    '14' => '8'
);

/*
 * $contentTypeMapping has structure:
 * $oldID => $newID
 *
 *   1	Liter	f
 *   2	ml	f
 *   3	mg	f
 *   4	g	f
 *   5	kg	f
 *   6	m	f
 *   7	Stück	f
 */

$contentTypeMapping = array(
    '1' => '1',
    '2' => '2',
    '3' => '3',
    '4' => '4',
    '5' => '5',
    '6' => '6',
    '7' => '7'
);


$group_res = $mysql->query("SELECT g.*, n.name, n.description FROM tbl_product_groups g JOIN tbl_product_group_names n ON g.group_id = n.group_id WHERE n.language_code = 'de'");
while($group = $group_res->fetch_object()){
    try{
        switch($group->type){
            case 'lebensmittel':
                $group->type = 'groceries';
                break;
            case 'drogerie':
                $group->type = 'drugstore';
                break;
            default:
                echo "\033[31m Fehler bei Gruppe " . $group->group_id . ": Falscher Typ \"" . $group->type . "\033[0m\n";
                ob_flush();
                continue 2;
        }           
        $ret = $group_query->execute(array(
            $group->name, // name
            $group->type, // type
            $group->description // description
        ));
        if($group_query->errorCode() == '00000'){
            $groupCount++;
            $group_id = $group_query->fetchObject()->id;
            $groupMapping[$group->group_id] = $group_id;
  //          echo 'Neue Gruppe: ' . $group_id . ': ' . $group->name . ' (alte ID: ' . $group->group_id . ")\n";
  //          ob_flush();
            $cat_res = $mysql->query("SELECT c.*, n.name, n.search_description FROM tbl_product_categories c JOIN tbl_product_category_names n ON c.category_id = n.category_id JOIN tbl_product_group_category_attach a ON a.category_id = c.category_id AND a.group_id = '" . $mysql->real_escape_string($group->group_id) . "' WHERE n.language_code = 'de'");
            while($category = $cat_res->fetch_object()){
                try{
                    $ret = $category_query->execute(array(
                        $category->name, // name
                        $group_id, // product_group_id
                        $category->search_description // description
                    ));
                    if($category_query->errorCode() == '00000'){
                        $categoryCount++;
                        $category_id = $category_query->fetchObject()->id;
                        $catMapping[$category->category_id] = $category_id;
    //                    echo 'Neue Kategorie: ' . $category_id . ': ' . $category->name . ' (alte ID: ' . $category->category_id . ")\n";
    //                    ob_flush();
                    }
                    else{
                        $err = $category_query->errorInfo();
                        $categoryErrorCount++;
                        echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
                        ob_flush();
                    }
                } catch(Exception $e){
                    echo $e->getMessage();
                    exit('Fataler Fehler!');
                }
            }
        }
        else{
            $err = $group_query->errorInfo();
            $groupErrorCount++;
            echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
            ob_flush();
        }
    } catch(Exception $e){
        echo $e->getMessage();
        exit('Fataler Fehler!');
    }
}


$attribute_res = $mysql->query("SELECT n.attribute_id, n.name, a.type FROM tbl_product_attribute_names n JOIN tbl_product_attributes a ON n.attribute_id = a.attribute_id WHERE n.language_code = 'de'");
while($attribute = $attribute_res->fetch_object()){
    try{          
        $ret = $attributes_query->execute(array(
            $attribute->name, // name
            $attribute->type // type
        ));
        if($attributes_query->errorCode() == '00000'){
            $attributeCount++;
            $attribute_id = $attributes_query->fetchObject()->id;
            $attributeMapping[$attribute->attribute_id] = $attribute_id;
//            echo 'Neues Attribut: ' . $attribute_id . ': ' . $attribute->name . ' (alte ID: ' . $attribute->attribute_id . ")\n";
//            ob_flush();
        }
        else{
            $err = $attributes_query->errorInfo();
            $attributeErrorCount++;
            echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
            ob_flush();
        }
    } catch(Exception $e){
        echo $e->getMessage();
        exit('Fataler Fehler!');
    }
}



$user_res = $mysql->query("SELECT * FROM tbl_user WHERE deleted <> '1'");
while($user = $user_res->fetch_object()){
    try{
        $ret = $user_query->execute(array(
            0, //phpbb_id
//            'asdf' . $user->user_id,
            $user->email, // email
            '', //password 
            '', //salt
            null, //date('Y-m-d', strtotime($user->birthday)), //birthday
            ($user->type == '1') ? 'customer' : 'shop',  // type 
            ($user->is_admin) ? 't' : 'f',  // is_admin
            $user->is_wholesale ? 't' : 'f', // is_wholesale
            ($user->status == 1) ? 'accepted' : 'new',
            null, //date('Y-m-d', strtotime($user->registered)),  // registered
            null, //date('Y-m-d', strtotime($user->last_online)), // last_login
            'f', // deleted
            $user->password // old_password_hash
        ));
        if($user_query->errorCode() == '00000'){
            $userCount++;
            $user_id = $user_query->fetchObject()->id;
            $userMapping[$user->user_id] = $user_id;
//            echo 'Neuer User: ' . $user_id . ': ' . $user->email . ' (alte ID: ' . $user->user_id . ")\n";
//            ob_flush();

            $bank_account_res = $mysql->query("SELECT * FROM tbl_user_payment WHERE user_id = '" . $mysql->real_escape_string($user->user_id) . "' AND kontonr <> ''");
            while($bank_account = $bank_account_res->fetch_object()){
                try{
                    $ret = $bank_account_query->execute(array(
                        $user_id, // user_id
                        $bank_account->kontonr, // account_nr
                        $bank_account->bankcode, // bank_id
                        $bank_account->bank, // bank_name
                        $bank_account->sender_holder // account_holder
                    ));
                    if($bank_account_query->errorCode() == '00000'){
                        $bankAccountCount++;
//                        echo 'Bankverbindung für User ' . $user_id . " eingetragen\n";
//                        ob_flush();
                    }
                    else{
                        $err = $bank_account_query->errorInfo();
                        $bankAccountErrorCount++;
                        echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
                        ob_flush();
                    }
                } catch(Exception $e){
                    echo $e->getMessage();
                    exit('Fataler Fehler!');
                }
            }

            // addresses
            // GROUP BY needed to eliminate duplicates
            $address_res = $mysql->query("SELECT a.*, cz.city_name AS city FROM tbl_addresses a JOIN tbl_city_zips cz ON a.city_id = cz.location_id WHERE a.type = 'user' AND a.type_id = '" . $mysql->real_escape_string($user->user_id) . "' AND a.street <> '' AND a.street_nr <> '' AND a.zip <> '' AND a.city_id <> 0 GROUP by a.street, a.street_nr, a.zip, a.city_id ORDER BY a.tstamp DESC"); 
            while($address = $address_res->fetch_object()){
                try{
                    $ret = $address_query->execute(array(
                        $user_id, // user_id
                        ($address->gender == 1) ? 'Herr' : 'Frau', // gender
                        $address->firma, // company
                        $address->firstname, // firstname
                        $address->lastname, // name
                        $address->street, // street
                        $address->street_nr, // house
                        $address->zip, // zip
                        $address->city, // city
                        ($address->country_sn) ? $address->country_sn : 'DE' // country
                    ));
                    if($address_query->errorCode() == '00000'){
                        $addressesCount++;
//                        echo 'Adresse für User ' . $user_id . " eingetragen\n";
//                        ob_flush();
                    }
                    else{
                        $err = $address_query->errorInfo();
                        $addressesErrorCount++;
                        echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
                        ob_flush();
                    }
                } catch(Exception $e){
                    echo $e->getMessage();
                    exit('Fataler Fehler!');
                }
            }

            $shop_res = $mysql->query("SELECT s.*, d.description, d.history, d.philosophy, d.workflows FROM tbl_shops s JOIN tbl_shop_user_attach a on a.user_id = '" . $mysql->real_escape_string($user->user_id) . "' JOIN tbl_shop_details d ON s.shop_id = d.shop_id WHERE s.shop_id = a.shop_id AND a.user_id <> 0 AND a.shop_id <> 0 GROUP BY a.user_id");
            $shop = $shop_res->fetch_object();
            $company_res = $mysql->query("SELECT c.*, p.name AS bank_account_holder, p.ktnr, p.blz, p.swift, p.bic FROM tbl_companies c JOIN tbl_company_user_attach a ON c.company_id = a.company_id   LEFT JOIN tbl_company_payments p ON c.company_id = p.company_id WHERE a.user_id = '" . $mysql->real_escape_string($user->user_id) . "' AND a.user_id <> 0 AND a.company_id <> 0 GROUP BY a.user_id");
            $company = $company_res->fetch_object();
            if($company){
                $address_res = $mysql->query("SELECT a.*, cz.city_name AS city FROM tbl_addresses a JOIN tbl_city_zips cz ON a.city_id = cz.location_id WHERE a.type = 'company' AND a.type_id = '" . $mysql->real_escape_string($company->company_id) . "' AND a.street <> '' AND a.street_nr <> '' AND a.zip <> '' AND a.city_id <> 0 GROUP by a.street, a.street_nr, a.zip, a.city_id ORDER BY a.tstamp DESC"); 
                $address = $address_res->fetch_object();
            }
            else{
                $address = false;
            }

            $companyTypes = array(
                '2' => 'AG',
                '10' => 'e.G. (eingetragene Gesellschaft)',
                '11' => 'e.K. (eingetragener Kaufmann)',
                '0' => 'Einzelunternehmen',
                '5' => 'GbR',
                '1' => 'GmbH',
                '6' => 'GmbH & Co. KG',
                '7' => 'KG',
                '4' => 'Ltd.',
                '12' => 'Ltd. und Co. KG.',
                '3' => 'OHG',
                '13' => 'PartG.',
                '8' => 'e.V.',
                '9' => 'andere'
            );

            if($shop && $company){
                $logo_id = null;
                $picture_res = $mysql->query("SELECT * FROM tbl_files WHERE file_id = '" . $mysql->real_escape_string($shop->logopic_id) . "'");
                $logo = $picture_res->fetch_object();
                if($logo){
                    try{
                        $ret = $picture_query->execute(array(
                            $logo->filename
                        ));
                        if($picture_query->errorCode() == '00000'){
                            $logo_id = $picture_query->fetchObject()->id;
                            $logoCount++;
                        }
                    }
                    catch(Exception $e){
                        echo $e->getMessage();
                        exit('Fataler Fehler!');
                    }
                }

                try{
                    $ret = $shop_query->execute(array(
                        $user_id, // user_id
                        $shop->name, // name
                        $shop->url, // url 
                        $shop->provision, // provision
                        $company->taxnumber, // taxnumber
                        ($company->commercialnumber) ? $company->commercialnumber : null, // salestax_id must be unique, setting null if == '' or == 0
                        ($shop->active == '1') ? 'activated' : 'new', // status
                        ($address) ? $address->firma : null, // company
                        ($address) ? $address->street : null, // street
                        ($address) ? $address->street_nr : null , // house
                        ($address) ? $address->zip : null, // zip
                        ($address) ? $address->city : null, // city
                        ($address && $address->country_sn) ? $address->country_sn : 'DE', // country
                        ($address) ? ($address->phone_code . '/' . $address->phone) : null, // phone
                        ($address) ? ($address->fax_code . '/' . $address->fax) : null, // fax
                        ($company->ustg19) ? 't' : 'f', // small_business
                        $company->commercialnumber, // register_id
                        $company->district_court, // register_court
                        $company->placeof_companyhead, // office 
                        ($address) ? ($address->firstname . ' ' . $address->lastname) : null, // representative
                        $company->eco_institute, // eco_control_board
                        $company->eco_controlnr, // eco_control_id
                        $company->bank_account_holder, // bank_account_holder
                        $company->ktnr, // bank_account_number
                        $company->blz, // bank_id
                        null, // bank_name TODO: missing from table
                        $company->swift, // bank_swift
                        $company->bic, // bank_iban TODO: iban is missing in table
                        $shop->description, // description
                        $shop->history, // history
                        $shop->philosophy, // philosophy
                        $shop->workflows, // procedure
                        null, // additional
                        $companyTypes[$company->legalform], // type
                        $shop->registered, // created,
                        (!is_null($logo_id)) ? $logo_id : null // logo_id
                    ));
                    if($shop_query->errorCode() == '00000'){
                        $shopCount++;
                        $shop_id = $shop_query->fetchObject()->id;
//                        echo 'Neuer Shop: ' . $shop_id . ': ' . $shop->name . ' (alte ID: ' . $shop->shop_id . ")\n";
//                        ob_flush();

                        if(!$address){
                            echo "\033[33mKeine Shop-Addresse!\033[0m\n";
                            $shopWithoutAddressCount++;
                        }
                        
                        $shipping_res = $mysql->query("SELECT * FROM tbl_shop_charges WHERE shop_id = '" . $mysql->real_escape_string($shop->shop_id) . "'");
                        while($shipping = $shipping_res->fetch_object()){
                            try{
                                $ret = $shipping_query->execute(array(
                                    $shop_id, // shop_id
                                    strtoupper($shipping->country_sn), // country_id
                                    $shipping->forwarding_charges, // value
                                    ($shipping->forwarding_charges_free > 0) ? $shipping->forwarding_charges_free : null // free_from
                                ));
                                if($shipping_query->errorCode() == '00000'){
                                    $shippingCount++;
//                                    echo 'Versandkosten für Shop ' . $shop_id . ' für Land ' . strtoupper($shipping->country_sn) . " eingetragen\n";
//                                    ob_flush();
                                }
                                else{
                                    $err = $shipping_query->errorInfo();
                                    $shippingErrorCount++;
                                    echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
                                    ob_flush();
                                }
                            } catch(Exception $e){
                                echo $e->getMessage();
                                exit('Fataler Fehler!');
                            }
                        }

                        $product_res = $mysql->query("SELECT p.*, n.name AS name, d.shopproduct_name AS realname, d.main_pic_id AS main_pic_id, pca.category_id AS old_cat_id, d.is_bio, d.is_discount, d.ingredients, d.description, d.active AS active, d.available FROM tbl_products p JOIN tbl_shop_product_attach a on a.shop_id = '" . $mysql->real_escape_string($shop->shop_id) . "' LEFT JOIN tbl_product_names n ON p.product_id = n.product_id AND n.language_code = 'de' LEFT JOIN tbl_product_category_attach pca ON p.product_id = pca.product_id LEFT JOIN tbl_product_details d ON p.product_id = d.product_id  WHERE p.active = '1' AND p.product_id = a.product_id AND a.product_id <> 0 AND a.shop_id <> 0 GROUP BY a.product_id");
                        while($product = $product_res->fetch_object()){
                            try{
                                $ret = $product_query->execute(array(
                                    $shop_id, // shop_id
                                    ($product->realname) ? $product->realname : $product->name, // name
                                    $product->description, // description
                                    $product->num_views, // num_views
                                    ($product->active && $product->main_pic_id) ? 't' : 'f', // active
                                    ($product->available == 1) ? null : 0, //stock
                                    isset($catMapping[$product->old_cat_id]) ? $catMapping[$product->old_cat_id] : null, // category_id
                                    $product->ingredients, // ingredients
                                    null, // traces
                                    $product->is_bio, // is_bio
                                    $product->is_discount, // is_discount
                                    null // tags
                                ));
                                if($product_query->errorCode() == '00000'){
                                    $productCount++;
                                    $product_id = $product_query->fetchObject()->id;
                                    $productMapping[$product->product_id] = $product_id;
                                    $mainPicMapping[$product->main_pic_id] = $product_id;
//                                    echo 'Neues Produkt: ' . $product_id . ': ' . $product->default_name . ' (alte ID: ' . $product->product_id . ")\n";
                                    if(!isset($catMapping[$product->old_cat_id])){
                                        echo "\033[33mKeine Kategorie!\033[0m\n";
                                        $productWithoutCategoryCount++;
                                    }
                                    ob_flush();

                                    $attributes_attach_res = $mysql->query("SELECT * FROM tbl_product_attribute_attach WHERE product_id = '" . $mysql->real_escape_string($product->product_id) . "'");
                                    while($attach = $attributes_attach_res->fetch_object()){
                                        try{
                                            $ret = $attributes_product_query->execute(array(
                                                $product_id, // product_id
                                                $attributeMapping[$attach->attribute_id] // product_attribute_id
                                            ));
                                            if($attributes_product_query->errorCode() == '00000'){
                                                $attributesProductCount++;
//                                                echo 'Attribut für Produkt ' . $product_id . " eingetragen\n";
//                                                ob_flush();
                                            }
                                            else{
                                                $err = $attribues_product_query->errorInfo();
                                                $attributesProductErrorCount++;
                                                echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
                                                ob_flush();
                                            }
                                        } catch(Exception $e){
                                            echo $e->getMessage();
                                            exit('Fataler Fehler!');
                                        }
                                    }

                                    $products_pictures_res = $mysql->query("SELECT p.picture_id AS old_pic_id, f.filename FROM tbl_files f JOIN tbl_pictures p ON f.file_id = p.picture_id WHERE p.kisju_type = 'picture' AND type_id = '" . $mysql->real_escape_string($product->product_id) . "'");
                                    while($picture = $products_pictures_res->fetch_object()){
                                            try{
                                                $pic_ret = $picture_query->execute(array(
                                                    $picture->filename
                                                ));
                                                $pic_id = $picture_query->fetchObject()->id;
                                                if($pic_id){
                                                    $ret = $products_pictures_query->execute(array(
                                                        $product_id, // product_id
                                                        $pic_id
                                                    ));
                                                    
                                                    if($products_pictures_query->errorCode() == '00000'){
                                                        $productPictureCount++;  
                                                        if(isset($mainPicMapping[$picture->old_pic_id])){
                                                            $main_pic_query->execute(array(
                                                                $pic_id,
                                                                $mainPicMapping[$picture->old_pic_id] // product_id
                                                            ));
                                                        }
                                                    }     
                                                    else{
                                                        print_r($products_pictures_query->errorInfo());
                                                        ob_flush();
                                                        exit();
                                                    }
                                                }
                                            } catch(Exception $e){
                                                echo $e->getMessage();
                                                exit('Fataler Fehler!');
                                            }
                                        
                                    }

                                    // NOTICE: field "amount" seems to be not used
                                    $price_res = $mysql->query("SELECT * FROM tbl_product_prices WHERE product_id = '" . $mysql->real_escape_string($product->product_id) . "'");
                                    while($price = $price_res->fetch_object()){
                                        try{
                                            $ret = $price_query->execute(array(
                                                $product_id, // product_id
                                                $price->sales_price, // value
                                                $price->quantity, // quantity
                                                $price->is_wholesale, // is_wholesale
                                                $unitMapping[$price->unit], // unit_type_id
                                                $contentTypeMapping[$price->measure], // content_type_id
                                                (int) $price->contents // contents
                                            ));
                                            if($price_query->errorCode() == '00000'){
                                                $priceCount++;
//                                                echo 'Preis für Produkt ' . $product_id . " eingetragen\n";
//                                                ob_flush();
                                            }
                                            else{
                                                $err = $price_query->errorInfo();
                                                $priceErrorCount++;
                                                echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
                                                ob_flush();
                                            }
                                        } catch(Exception $e){
                                            echo $e->getMessage();
                                            exit('Fataler Fehler!');
                                        }
                                    }

                                }
                                else{
                                    $err = $product_query->errorInfo();
                                    $productErrorCount++;
                                    echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
                                    ob_flush();
                                }
                            } catch(Exception $e){
                                echo $e->getMessage();
                                exit('Fataler Fehler!');
                            }
                        }
                    }
                    else{
                        $err = $shop_query->errorInfo();
                        $shopErrorCount++;
                        echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
                        ob_flush();
                    }
                } catch(Exception $e){
                    echo $e->getMessage();
                    exit('Fataler Fehler!');
                }
            }
        }
        else{
            $err = $user_query->errorInfo();
            $userErrorCount++;
            echo "\033[31m Fehler: " . $err[2] . "\033[0m\n";
            ob_flush();
        }
    } catch(Exception $e){
        echo $e->getMessage();
        exit('Fataler Fehler!');
    }
}

// can do this only when all products and users are migrated
$product_rating_res = $mysql->query("SELECT * FROM tbl_product_ratings");
while($rating = $product_rating_res->fetch_object()){
    if(isset($productMapping[$rating->product_id]) && isset($userMapping[$rating->user_id])){
        try{
            $ret = $product_ratings_query->execute(array(
                $productMapping[$rating->product_id], // product_id
                $userMapping[$rating->user_id], // user_id
                $rating->comment, // comment
                $rating->rating, // rating
                ($rating->status) ? 'accepted' : 'new', // status
                date('Y-m-d', strtotime($rating->timestamp)) // created
            ));
            if($product_ratings_query->errorCode() == '00000'){
                $productRatingCount++;
//                echo 'Rating für Produkt ' . $productMapping[$rating->product_id] . " eingetragen\n";
//                ob_flush();
            }
        } catch(Exception $e){
            echo $e->getMessage();
            exit('Fataler Fehler!');
        }
    }
}


echo "-----------------------------------------------\n";
if($groupCount) echo "\033[32m Migrierte Gruppen: " . $groupCount . "\033[0m\n";
if($groupErrorCount) echo "\033[31m Fehlerhafte Gruppen: " . $groupErrorCount . "\033[0m\n";
if($categoryCount) echo "\033[32m Migrierte Kategorien: " . $categoryCount . "\033[0m\n";
if($categoryErrorCount) echo "\033[31m Fehlerhafte Gruppen: " . $categoryErrorCount . "\033[0m\n";
if($attributeCount) echo "\033[32m Migrierte Attribute: " . $attributeCount . "\033[0m\n";
if($attributeErrorCount) echo "\033[31m Fehlerhafte Attribute: " . $attributeErrorCount . "\033[0m\n";
if($userCount) echo "\033[32m Migrierte User: " . $userCount . "\033[0m\n";
if($userErrorCount) echo "\033[31m Fehlerhafte User: " . $userErrorCount . "\033[0m\n";
if($bankAccountCount) echo "\033[32m Migrierte Bankverbindungen: " . $bankAccountCount . "\033[0m\n";
if($bankAccountErrorCount) echo "\033[31m Fehlerhafte Bankverbindungen: " . $bankAccountErrorCount . "\033[0m\n";
if($addressesCount) echo "\033[32m Migrierte Benutzer-Adressen: " . $addressesCount . "\033[0m\n";
if($addressesErrorCount) echo "\033[31m Fehlerhafte Benutzer-Adressen: " . $addressesErrorCount . "\033[0m\n";
if($shopCount) echo "\033[32m Migrierte Shops: " . $shopCount . "\033[0m\n";
if($shopWithoutAddressCount) echo "\033[33m davon ohne Addresse: " . $shopWithoutAddressCount . "\033[0m\n";
if($shopErrorCount) echo "\033[31m Fehlerhafte Shops: " . $shopErrorCount . "\033[0m\n";
if($logoCount) echo "\033[32m Migrierte Shop-Logos: " . $logoCount . "\033[0m\n";
if($shippingCount) echo "\033[32m Migrierte Versandkosten: " . $shippingCount . "\033[0m\n";
if($shippingErrorCount) echo "\033[31m Fehlerhafte Versandkosten: " . $shippingErrorCount . "\033[0m\n";
if($productCount) echo "\033[32m Migrierte Produkte: " . $productCount . "\033[0m\n";
if($productWithoutCategoryCount) echo "\033[33m davon ohne Kategorie: " . $productWithoutCategoryCount . "\033[0m\n";
if($productErrorCount) echo "\033[31m Fehlerhafte Produkte: " . $productErrorCount . "\033[0m\n";
if($productRatingCount) echo "\033[32m Migrierte Produkt-Bewertungen: " . $productRatingCount . "\033[0m\n";
if($attributesProductCount) echo "\033[32m Zugewiesene Attribute: " . $attributesProductCount . "\033[0m\n";
if($attributesProductErrorCount) echo "\033[31m Fehlerhafte zugewiesene Attribute: " . $attributesProductErrorCount . "\033[0m\n";
if($priceCount) echo "\033[32m Zugewiesene Preise: " . $priceCount . "\033[0m\n";
if($priceErrorCount) echo "\033[31m Fehlerhafte zugewiesene Preise: " . $priceErrorCount . "\033[0m\n";
echo "-----------------------------------------------\n";
