<?php
$dashletData['AOS_ProductsDashlet']['searchFields'] = array (
  'name' => 
  array (
    'default' => '',
  ),
  'part_number' => 
  array (
    'default' => '',
  ),
  'aos_product_category_name' => 
  array (
    'default' => '',
  ),
  'estado_c' => 
  array (
    'default' => '',
  ),
);
$dashletData['AOS_ProductsDashlet']['columns'] = array (
  'name' => 
  array (
    'width' => '40%',
    'label' => 'LBL_LIST_NAME',
    'link' => true,
    'default' => true,
    'name' => 'name',
  ),
  'aos_product_category_name' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'label' => 'LBL_AOS_PRODUCT_CATEGORYS_NAME',
    'id' => 'AOS_PRODUCT_CATEGORY_ID',
    'link' => true,
    'width' => '10%',
    'default' => true,
    'name' => 'aos_product_category_name',
  ),
  'estado_c' => 
  array (
    'type' => 'dynamicenum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_ESTADO',
    'width' => '10%',
    'name' => 'estado_c',
  ),
  'created_by' => 
  array (
    'width' => '8%',
    'label' => 'LBL_CREATED',
    'name' => 'created_by',
    'default' => false,
  ),
  'currency_id' => 
  array (
    'type' => 'id',
    'studio' => 'visible',
    'label' => 'LBL_CURRENCY',
    'width' => '10%',
    'default' => false,
    'name' => 'currency_id',
  ),
  'cost' => 
  array (
    'type' => 'currency',
    'label' => 'LBL_COST',
    'currency_format' => true,
    'width' => '10%',
    'default' => false,
    'name' => 'cost',
  ),
  'price' => 
  array (
    'type' => 'currency',
    'label' => 'LBL_PRICE',
    'currency_format' => true,
    'width' => '10%',
    'default' => false,
    'name' => 'price',
  ),
);
