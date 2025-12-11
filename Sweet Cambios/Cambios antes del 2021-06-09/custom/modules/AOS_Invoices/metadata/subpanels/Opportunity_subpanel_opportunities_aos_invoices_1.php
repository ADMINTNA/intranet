<?php
// created: 2020-10-27 22:15:53
$subpanel_layout['list_fields'] = array (
  'number' => 
  array (
    'type' => 'int',
    'vname' => 'LBL_INVOICE_NUMBER',
    'width' => '10%',
    'default' => true,
  ),
  'name' => 
  array (
    'type' => 'name',
    'link' => true,
    'vname' => 'LBL_NAME',
    'width' => '10%',
    'default' => true,
    'widget_class' => 'SubPanelDetailViewLink',
    'target_module' => NULL,
    'target_record_key' => NULL,
  ),
  'billing_account' => 
  array (
    'type' => 'relate',
    'studio' => 'visible',
    'vname' => 'LBL_BILLING_ACCOUNT',
    'id' => 'BILLING_ACCOUNT_ID',
    'link' => true,
    'width' => '10%',
    'default' => true,
    'widget_class' => 'SubPanelDetailViewLink',
    'target_module' => 'Accounts',
    'target_record_key' => 'billing_account_id',
  ),
  'status' => 
  array (
    'type' => 'enum',
    'studio' => 'visible',
    'vname' => 'LBL_STATUS',
    'width' => '10%',
    'default' => true,
  ),
  'subtotal_amount' => 
  array (
    'type' => 'currency',
    'vname' => 'LBL_SUBTOTAL_AMOUNT',
    'currency_format' => true,
    'width' => '10%',
    'default' => true,
  ),
  'quote_number' => 
  array (
    'type' => 'int',
    'vname' => 'LBL_QUOTE_NUMBER',
    'width' => '10%',
    'default' => true,
  ),
  'num_nota_venta1_c' => 
  array (
    'type' => 'int',
    'default' => true,
    'vname' => 'LBL_NUM_NOTA_VENTA1',
    'width' => '10%',
  ),
  'assigned_user_name' => 
  array (
    'link' => true,
    'type' => 'relate',
    'vname' => 'LBL_ASSIGNED_TO_NAME',
    'id' => 'ASSIGNED_USER_ID',
    'width' => '10%',
    'default' => true,
    'widget_class' => 'SubPanelDetailViewLink',
    'target_module' => 'Users',
    'target_record_key' => 'assigned_user_id',
  ),
  'invoice_date' => 
  array (
    'type' => 'date',
    'vname' => 'LBL_INVOICE_DATE',
    'width' => '10%',
    'default' => true,
  ),
  'edit_button' => 
  array (
    'width' => '4%',
    'default' => true,
    'vname' => 'edit_button',
    'widget_class' => 'SubPanelEditButton',
  ),
  'currency_id' => 
  array (
    'usage' => 'query_only',
  ),
);