<?php
use Model\Model;

require_once 'Utils/functii.php';

$customer = new Model('customer');
//Insert first record in customer table
$customer->name = 'John Smith';
$customer->city = 'Miami';
$customer->create();
//Insert second record in customer table
$customer->name = 'Fred Falcon';
$customer->city = 'New York';
$customer->create();
//Insert third record in customer table
$customer->name = 'Adam Alan';
$customer->city = 'Miami';
$customer->create();

//Insert first record in product_categories table
$category = new Model('product_categories');
$category->name = 'Laptop';
$category->create();
//Insert second record in product_categories table
$category->name = 'TV';
$category->create();

//Get the laptop category
$laptopCategory = $category->getOneResult('name', 'Laptop');
//Get the id of the laptop category
$productCategory = $laptopCategory->id;

//Insert first record in product table
$product = new Model('product');
$product->category = $productCategory;
$product->name = 'Laptop ASUS A556UQ-DM943D';
$product->price = 702.53;
$laptopAsus = $product->create();

//Get the TV category
$tvCategory = $category->getOneResult('name', 'TV');
//Get the id of the TV category
$productCategory = $tvCategory->id;

//Insert second record in product table
$product = new Model('product');
$product->category = $productCategory;
$product->name = 'TV Sony Bravia 55XE8096';
$product->price = 1002.35;
$product->create();

$productOrder = new Model('product_order');

/*
 * We need to use the clear method because at the last $customer->create() call the object was something like this:
 * $customer->id = 3;
 * $customer->name = 'Adam Alan';
 * $customer->city = 'New York';
 *
 * And we need to clear these properties, to search only by the city
 */
$customer->clear();
$customer->city = 'New York';
$customers = $customer->get();
foreach($customers AS $customer) {
	$productOrder->product_id = $laptopAsus->id;
	$productOrder->customer_id = $customer->id;
}