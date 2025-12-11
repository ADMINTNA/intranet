<?php
$popupMeta = array (
    'moduleMain' => 'Account',
    'varName' => 'ACCOUNT',
    'orderBy' => 'name',
    'whereClauses' => array (
  'name' => 'accounts.name',
  'billing_address_city' => 'accounts.billing_address_city',
  'billing_address_state' => 'accounts.billing_address_state',
  'email' => 'accounts.email',
  'assigned_user_id' => 'accounts.assigned_user_id',
  'empresarelacionada_c' => 'accounts_cstm.empresarelacionada_c',
),
    'searchInputs' => array (
  0 => 'name',
  1 => 'billing_address_city',
  5 => 'billing_address_state',
  7 => 'email',
  8 => 'assigned_user_id',
  9 => 'empresarelacionada_c',
),
    'create' => array (
  'formBase' => 'AccountFormBase.php',
  'formBaseClass' => 'AccountFormBase',
  'getFormBodyParams' => 
  array (
    0 => '',
    1 => '',
    2 => 'AccountSave',
  ),
  'createButton' => 'LNK_NEW_ACCOUNT',
),
    'searchdefs' => array (
  'name' => 
  array (
    'name' => 'name',
    'width' => '10%',
  ),
  'billing_address_city' => 
  array (
    'name' => 'billing_address_city',
    'width' => '10%',
  ),
  'billing_address_state' => 
  array (
    'name' => 'billing_address_state',
    'width' => '10%',
  ),
  'email' => 
  array (
    'name' => 'email',
    'width' => '10%',
  ),
  'empresarelacionada_c' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_EMPRESARELACIONADA',
    'width' => '10%',
    'name' => 'empresarelacionada_c',
  ),
  'assigned_user_id' => 
  array (
    'name' => 'assigned_user_id',
    'label' => 'LBL_ASSIGNED_TO',
    'type' => 'enum',
    'function' => 
    array (
      'name' => 'get_user_array',
      'params' => 
      array (
        0 => false,
      ),
    ),
    'width' => '10%',
  ),
),
    'listviewdefs' => array (
  'NAME' => 
  array (
    'width' => '40%',
    'label' => 'LBL_LIST_ACCOUNT_NAME',
    'link' => true,
    'default' => true,
    'name' => 'name',
  ),
  'ESTATUSFINANCIERO_C' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_ESTATUSFINANCIERO',
    'width' => '10%',
  ),
  'ACCOUNT_TYPE' => 
  array (
    'type' => 'enum',
    'label' => 'LBL_TYPE',
    'width' => '10%',
    'default' => true,
    'name' => 'account_type',
  ),
  'RUT_C' => 
  array (
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_RUT',
    'width' => '10%',
    'name' => 'rut_c',
  ),
  'PHONE_OFFICE' => 
  array (
    'type' => 'phone',
    'label' => 'LBL_PHONE_OFFICE',
    'width' => '10%',
    'default' => true,
    'name' => 'phone_office',
  ),
  'BILLING_ADDRESS_STREET' => 
  array (
    'type' => 'varchar',
    'label' => 'LBL_BILLING_ADDRESS_STREET',
    'width' => '10%',
    'default' => true,
    'name' => 'billing_address_street',
  ),
  'EMAIL1' => 
  array (
    'type' => 'varchar',
    'studio' => 
    array (
      'editField' => true,
      'searchview' => false,
    ),
    'label' => 'LBL_EMAIL',
    'width' => '10%',
    'default' => true,
    'name' => 'email1',
  ),
  'BILLING_ADDRESS_CITY' => 
  array (
    'width' => '10%',
    'label' => 'LBL_LIST_CITY',
    'default' => true,
    'name' => 'billing_address_city',
  ),
  'ASSIGNED_USER_NAME' => 
  array (
    'width' => '2%',
    'label' => 'LBL_LIST_ASSIGNED_USER',
    'default' => true,
    'name' => 'assigned_user_name',
  ),
  'EMPRESARELACIONADA_C' => 
  array (
    'type' => 'varchar',
    'default' => true,
    'label' => 'LBL_EMPRESARELACIONADA',
    'width' => '10%',
    'name' => 'empresarelacionada_c',
  ),
),
);
