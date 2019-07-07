<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Test_model extends CI_Model
{
  function __construct(){
    parent :: __construct();
  }
  
  function prefix_fld()
  {
    die(PREFIX_FOLDER);
  }
  
  function yalingo()
  {
    // Data
    $products = array(
    array('name' => 'Keyboard',    'catId' => 'hw', 'quantity' =>  10, 'id' => 1),
    array('name' => 'Mouse',       'catId' => 'hw', 'quantity' =>  20, 'id' => 2),
    array('name' => 'Monitor',     'catId' => 'hw', 'quantity' =>   0, 'id' => 3),
    array('name' => 'Joystick',    'catId' => 'hw', 'quantity' =>  15, 'id' => 4),
    array('name' => 'CPU',         'catId' => 'hw', 'quantity' =>  15, 'id' => 5),
    array('name' => 'Motherboard', 'catId' => 'hw', 'quantity' =>  11, 'id' => 6),
    array('name' => 'Windows',     'catId' => 'os', 'quantity' => 666, 'id' => 7),
    array('name' => 'Linux',       'catId' => 'os', 'quantity' => 666, 'id' => 8),
    array('name' => 'Mac',         'catId' => 'os', 'quantity' => 666, 'id' => 9),
    );
    $categories = array(
    array('name' => 'Hardware',          'id' => 'hw'),
    array('name' => 'Operating systems', 'id' => 'os'),
    );
     
    // Put products with non-zero quantity into matching categories;
    // sort categories by name;
    // sort products within categories by quantity descending, then by name.
    $result = from($categories)
    ->orderBy('$cat ==> $cat["name"]')
    ->groupJoin(
        from($products)
            ->where('$prod ==> $prod["quantity"] > 0')
            ->orderByDescending('$prod ==> $prod["quantity"]')
            ->thenBy('$prod ==> $prod["name"]'),
        '$cat ==> $cat["id"]', '$prod ==> $prod["catId"]',
        '($cat, $prods) ==> array(
            "name" => $cat["name"],
            "products" => $prods
        )'
    );
    

    // Alternative shorter syntax using default variable names
    $result2 = from($categories)
    ->orderBy('$v["name"]')
    ->groupJoin(
        from($products)
            ->where('$v["quantity"] > 0')
            ->orderByDescending('$v["quantity"]')
            ->thenBy('$v["name"]'),
        '$v["id"]', '$v["catId"]',
        'array(
            "name" => $v["name"],
            "products" => $e
        )'
    );

    // Closure syntax, maximum support in IDEs, but verbose and hard to read
    $result3 = from($categories)
    ->orderBy(function ($cat) { return $cat['name']; })
    ->groupJoin(
        from($products)
            ->where(function ($prod) { return $prod["quantity"] > 0; })
            ->orderByDescending(function ($prod) { return $prod["quantity"]; })
            ->thenBy(function ($prod) { return $prod["name"]; }),
        function ($cat) { return $cat["id"]; },
        function ($prod) { return $prod["catId"]; },
        function ($cat, $prods) {
            return array(
                "name" => $cat["name"],
                "products" => $prods
            );
        }
    );

   // print_r($result->toArrayDeep());

    // return [TRUE, ['result' => $result->toArray()]];
    // return [TRUE, ['result' => $result->toArrayDeep()]];
    return [TRUE, ['result' => $result->toList()]];
    // return [TRUE, ['result' => $result->toListDeep()]];
    // return [TRUE, ['result' => $result->toDictionary()]];
    // return [TRUE, ['result' => $result->toJSON()]];
    // return [TRUE, ['result' => $result->toObject()]];
    // return [TRUE, ['result' => $result->toArrayDeepProc()]];
  }

}
