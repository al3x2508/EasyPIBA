# EasyPIBA - A lightweight PHP framework

In order to install EasyPIBA you need to follow these instructions:

Download from github using git clone https://github.com/al3x2508/EasyPIBA.git or download a zip file from here
Go to your url where you have the project, for example http://www.myurl.com/
Go through setup
Delete the setup/ folder from the application folder
Customize your template by editing the templates/template.html and css/main.css
Customize your app
Your urls are loaded from the pages table from your database or from pages/ folder in the application root directory;
If you want to create a static html page you would need to create it from the administration section.
If you want to create a dynamic content page (eg: my-orders page) you would need to create a php file in your pages/ folder (eg: pages/my-orders.php); You need to set the following variables: $page_title, $description and $content;
The url for accessing your page would be http://yourAppUrl/my-orders.html (the app is striping pages/ from url and replaces php with html for extension)
 

Let's say you want to build a simple e-commerce application. Your shop should have the following structure:

Users that are looking for Products inside your shop that are organized into Categories. After they found the specified products they add them into carts and finally are placing Orders.

Download the demo sql file for this structure from here.
Let's generate our informations and populate our tables by creating a php file containing the code bellow:
```php
<?php
use \Model\Model;
//We need Bcrypt class for encrypting passwords
$bcrypt          = new Utils\Bcrypt(10);
$usersArray      = array(
    array(
        'firstname' => 'Bob',
        'lastname' => 'Fleming',
        'email' => 'bob.fleming@example.com',
        'phone' => '5524833823',
        'address' => 'Sample address bob',
        'city' => 'Chicago',
        'state' => 'Illinois',
        'country' => 'United States',
        'password' => 'bobfpassword',
        'status' => 1
    ),
    array(
        'firstname' => 'Rowley',
        'lastname' => 'Birkin',
        'email' => 'rowley.birkin@example.com',
        'phone' => '552396314',
        'address' => 'Sample address rowley',
        'city' => 'Los Angeles',
        'state' => 'California',
        'country' => 'United States',
        'password' => 'rowleybpassword',
        'status' => 1
    ),
    array(
        'firstname' => 'Louis',
        'lastname' => 'Balfour',
        'email' => 'louis.balfour@example.com',
        'phone' => '722963266',
        'address' => 'Sample address louis',
        'city' => 'Seattle',
        'state' => 'Washington',
        'country' => 'United States',
        'password' => 'louisbpassword',
        'status' => 1
    ),
    array(
        'firstname' => 'Alex',
        'lastname' => 'Glavan',
        'email' => 'alex.glavan@example.com',
        'phone' => '0721234560',
        'address' => 'Sample address alex',
        'city' => 'Bucharest',
        'state' => 'Bucharest',
        'country' => 'Romania',
        'password' => 'alexgpassword',
        'status' => 1
    ),
    array(
        'firstname' => 'John',
        'lastname' => 'Smith',
        'email' => 'john.smith@example.com',
        'phone' => '57452235',
        'address' => 'Sample address john smith',
        'city' => 'Houston',
        'state' => 'Texas',
        'country' => 'United States',
        'password' => 'johnspassword',
        'status' => 1
    ),
    array(
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '34748568',
        'address' => 'Sample address john doe',
        'city' => 'Boston',
        'state' => 'Massachusetts',
        'country' => 'United States',
        'password' => 'johndpassword',
        'status' => 1
    )
);
$usersIds        = array();
$countries       = array();
$countriesEntity = new Model('countries');
$countriesEntity = $countriesEntity->get();
foreach ($countriesEntity AS $country)
    $countries[$country->name] = $country->id;

//Create users from the usersArray
$users = new Model('users');
foreach ($usersArray AS $userArray) {
    $users->firstname = $userArray['firstname'];
    $users->lastname  = $userArray['lastname'];
    $users->email     = $userArray['email'];
    $users->phone     = $userArray['phone'];
    $users->address   = $userArray['address'];
    $users->city      = $userArray['city'];
    $users->state     = $userArray['state'];
    $users->country   = $countries[$userArray['country']];
    $users->password  = $bcrypt->hash($userArray['password']);
    $users->status    = $userArray['status'];
    $user             = $users->create();
    $usersIds[]       = $user->id;
    $users->clear();
}

//Create categories
$categories                  = new Model('categories');
//Create Laptops category
$categories->parent_category = '0xNULL';
$categories->name            = 'Laptops';
$categories->create();
$laptopsCategory = $categories->id;
$categories->clear();
//Create TV category
$categories->parent_category = '0xNULL';
$categories->name            = 'TV';
$categories->create();
$tvCategory = $categories->id;
$categories->clear();
//Create Gaming laptops category
$categories->parent_category = $laptopsCategory;
$categories->name            = 'Gaming laptops';
$categories->create();
$gamingLaptopsCategory = $categories->id;
$categories->clear();
//Create Business laptops category
$categories->parent_category = $laptopsCategory;
$categories->name            = 'Business laptops';
$categories->create();
$businessLaptopsCategory = $categories->id;
$categories->clear();

//Create products
//Create first Asus gaming laptop
$products              = new Model('products');
$products->name        = 'Laptop ASUS Gaming 17.3" ROG GL752VW';
$products->sku         = 'GL752VW';
$products->description = 'Laptop ASUS Gaming 17.3" ROG GL752VW, FHD, Intel® Core™ i7-6700HQ (6M Cache, up to 3.50 GHz), 8GB DDR4, 1TB 7200 RPM, GeForce GTX 960M 4GB, Black';
$products->price       = 1099.99;
$products->stock       = 4;
$products->sort_order  = 1;
$products->create();
$asus1 = $products->id;
$products->clear();

//Create second Asus gaming laptop
$products->name        = 'Laptop ASUS Gaming 17.3" ROG STRIX GL702VM-DB71';
$products->sku         = 'GL702VM-DB71';
$products->description = 'Asus ROG Strix GL702VM-DB71 17.3-Inch. G-SYNC VR Ready Thin and Light Gaming Laptop (NVIDIA GTX 1060 6GB Intel Core i7-6700HQ 16GB DDR4 1TB 7200RPM HDD)';
$products->price       = 1799.99;
$products->stock       = 3;
$products->sort_order  = 2;
$products->create();
$asus2 = $products->id;
$products->clear();

//Create Lenovo laptop; will be added in Gaming laptops category and Business laptops category
$products->name        = 'Laptop Lenovo Thinkpad T470';
$products->sku         = 'T470';
$products->description = 'Laptop Lenovo ThinkPad T470';
$products->price       = 919.99;
$products->stock       = 5;
$products->sort_order  = 1;
$products->create();
$lenovo = $products->id;
$products->clear();

//Create Acer business laptop
$products->name        = 'Laptop Acer TravelMate P278';
$products->sku         = 'TMP278-M-52UJ-US';
$products->description = 'Lenovo Acer TravelMate P278';
$products->price       = 856.99;
$products->stock       = 8;
$products->sort_order  = 2;
$products->create();
$acer = $products->id;
$products->clear();

//Create Sony TV
$products->name        = 'TV Sony Bravia 55" 2017';
$products->sku         = 'XBR55X900E';
$products->description = 'Sony XBR55X900E 55-Inch 4K Ultra HD Smart LED TV (2017 Model)';
$products->price       = 1198.99;
$products->stock       = 4;
$products->sort_order  = 1;
$products->create();
$sony = $products->id;
$products->clear();

//Add products into categories
$category_products = new Model('category_products');

//Add first Asus gaming laptop into Gaming laptops category
$category_products->category = $gamingLaptopsCategory;
$category_products->product  = $asus1;
$category_products->create();
$category_products->clear();

//Add second Asus gaming laptop into Gaming laptops category
$category_products->category = $gamingLaptopsCategory;
$category_products->product  = $asus2;
$category_products->create();
$category_products->clear();

//Add Lenovo laptop into Gaming laptops category and into Business laptops category
$category_products->category = $gamingLaptopsCategory;
$category_products->product  = $lenovo;
$category_products->create();
$category_products->clear();
$category_products->category = $businessLaptopsCategory;
$category_products->product  = $lenovo;
$category_products->create();
$category_products->clear();

//Add Acer business laptop into Business laptops category
$category_products->category = $businessLaptopsCategory;
$category_products->product  = $acer;
$category_products->create();
$category_products->clear();

//Add Sony TV into TV category
$category_products->category = $tvCategory;
$category_products->product  = $sony;
$category_products->create();
$category_products->clear();

//Create orders
$orders         = new Model('orders');
$order_products = new Model('order_products');
//For the first user we will create an order with the following products: Acer business laptop (qty: 2); Sony TV (qty: 1)
$orders->status = 0 /* Status = Order created by user */ ;
$orders->user   = $usersIds[0] /* First created user id*/ ;
$orders->create();
$order = $orders->id;
$orders->clear();
//Now we will add the products into the order
$order_products->orderId  = $order;
$order_products->product  = $acer;
$order_products->quantity = 2;
$order_products->price    = 809.99 /* He got a better price for the product at the moment of order */ ;
$order_products->create();
$order_products->clear();

$order_products->orderId  = $order;
$order_products->product  = $sony;
$order_products->quantity = 1;
$order_products->price    = 1198.99;
$order_products->create();
$order_products->clear();

//For the second user we will create an order with the following products: first Asus gaming laptop (qty: 1)
$orders->status = 1 /* Status = Processing by the store */ ;
$orders->user   = $usersIds[1] /* Second created user id*/ ;
$orders->create();
$order = $orders->id;
$orders->clear();
//Now we will add the products into the order
$order_products->orderId  = $order;
$order_products->product  = $asus1;
$order_products->quantity = 1;
$order_products->price    = 1099.99;
$order_products->create();
$order_products->clear();

//For the third user we will create an order with the following products: first Asus gaming laptop (qty: 1); second Asus gaming laptop (qty: 2)
$orders->status = 2 /* Status = In delivery */ ;
$orders->user   = $usersIds[2] /* Third created user id*/ ;
$orders->create();
$order = $orders->id;
$orders->clear();
//Now we will add the products into the order
$order_products->orderId  = $order;
$order_products->product  = $asus1;
$order_products->quantity = 1;
$order_products->price    = 1099.99;
$order_products->create();
$order_products->clear();

$order_products->orderId  = $order;
$order_products->product  = $asus2;
$order_products->quantity = 2;
$order_products->price    = 1799.99;
$order_products->create();
$order_products->clear();

//Create special permission for shop; you could create different levels, eg: Refund orders, Edit products, Edit orders, Deliver orders, Check order payment
$permission       = new Model('permissions');
$permission->name = 'Manage shop';
$permission->create();

//Add permission to administrator
$administrator         = new Model('admins');
$administrator         = $administrator->getOneResult('username', 'admin');
$adminPermissions      = json_decode($administrator->access, true);
$adminPermissions[]    = $permission->id;
$administrator->access = json_encode($adminPermissions);
$administrator->update();

$content     = 'Data generated!';
$page_title  = __('Generate data');
$description = __('Generate data');
$h1          = '';
$js          = array();
$css         = array();
```
Save this code into your pages folder inside your application directory (eg: pages/generate_data.php); Go in your browser to the address http://yourAppUrl/generate_data.html

Download from here the administration files used for this demo.
Take a look at the files.
