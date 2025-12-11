<?php
// created: 2020-10-26 21:51:16
$subpanel_layout['list_fields'] = array (
  'date_entered' => 
  array (
    'type' => 'datetime',
    'vname' => 'LBL_DATE_ENTERED',
    'width' => '10%',
    'default' => true,
  ),
  'product_qty' => 
  array (
    'vname' => 'LBL_PRODUCT_QTY',
    'width' => '10%',
    'default' => true,
  ),
  'parent_name' => 
  array (
    'vname' => 'LBL_ACCOUNT_PRODUCT_QUOTE_LINK',
    'widget_class' => 'SubPanelDetailViewLink',
    'target_record_key' => 'parent_id',
    'target_module_key' => 'parent_type',
    'width' => '10%',
    'default' => true,
  ),
  'name' => 
  array (
    'vname' => 'LBL_PRODUCTS_SERVICES',
    'widget_class' => 'SubPanelDetailViewLink',
    'target_record_key' => 'product_id',
    'target_module' => 'AOS_Products',
    'width' => '25%',
    'default' => true,
  ),
  'product_total_price' => 
  array (
    'vname' => 'LBL_PRODUCT_TOTAL_PRICE',
    'width' => '10%',
    'default' => true,
  ),
  'description' => 
  array (
    'type' => 'text',
    'vname' => 'LBL_DESCRIPTION',
    'sortable' => false,
    'width' => '10%',
    'default' => true,
  ),
);