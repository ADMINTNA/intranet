<?php
$dashletData['AOS_QuotesDashlet']['searchFields'] = array (
  'date_entered' => 
  array (
    'default' => '',
  ),
  'billing_account' => 
  array (
    'default' => '',
  ),
  'assigned_user_id' => 
  array (
    'default' => '',
  ),
  'stage' => 
  array (
    'default' => '',
  ),
  'invoice_status' => 
  array (
    'default' => '',
  ),
  'ultimo_contacto_c' => 
  array (
    'default' => '',
  ),
  'etapa_cotizacion_c' => 
  array (
    'default' => '',
  ),
);
$dashletData['AOS_QuotesDashlet']['columns'] = array (
  'number' => 
  array (
    'width' => '5%',
    'label' => 'LBL_LIST_NUM',
    'default' => true,
    'name' => 'number',
  ),
  'name' => 
  array (
    'width' => '20%',
    'label' => 'LBL_LIST_NAME',
    'link' => true,
    'default' => true,
    'name' => 'name',
  ),
  'stage' => 
  array (
    'width' => '15%',
    'label' => 'LBL_STAGE',
    'default' => true,
    'name' => 'stage',
  ),
  'total_amount' => 
  array (
    'width' => '15%',
    'label' => 'LBL_GRAND_TOTAL',
    'currency_format' => true,
    'default' => true,
    'name' => 'total_amount',
  ),
  'ultimo_contacto_c' => 
  array (
    'type' => 'date',
    'default' => true,
    'label' => 'LBL_ULTIMO_CONTACTO',
    'width' => '10%',
    'name' => 'ultimo_contacto_c',
  ),
  'invoice_status' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_INVOICE_STATUS',
    'width' => '10%',
    'name' => 'invoice_status',
  ),
  'billing_account' => 
  array (
    'width' => '20%',
    'label' => 'LBL_BILLING_ACCOUNT',
    'name' => 'billing_account',
    'default' => false,
  ),
  'billing_contact' => 
  array (
    'width' => '15%',
    'label' => 'LBL_BILLING_CONTACT',
    'name' => 'billing_contact',
    'default' => false,
  ),
  'opportunity' => 
  array (
    'width' => '25%',
    'label' => 'LBL_OPPORTUNITY',
    'name' => 'opportunity',
    'default' => false,
  ),
  'date_entered' => 
  array (
    'width' => '15%',
    'label' => 'LBL_DATE_ENTERED',
    'name' => 'date_entered',
    'default' => false,
  ),
  'date_modified' => 
  array (
    'width' => '15%',
    'label' => 'LBL_DATE_MODIFIED',
    'name' => 'date_modified',
    'default' => false,
  ),
  'created_by' => 
  array (
    'width' => '8%',
    'label' => 'LBL_CREATED',
    'name' => 'created_by',
    'default' => false,
  ),
  'assigned_user_name' => 
  array (
    'width' => '8%',
    'label' => 'LBL_LIST_ASSIGNED_USER',
    'name' => 'assigned_user_name',
    'default' => false,
  ),
  'expiration' => 
  array (
    'width' => '15%',
    'label' => 'LBL_EXPIRATION',
    'default' => false,
    'name' => 'expiration',
  ),
  'etapa_cotizacion_c' => 
  array (
    'type' => 'enum',
    'default' => false,
    'studio' => 'visible',
    'label' => 'LBL_ETAPA_COTIZACION',
    'width' => '10%',
    'name' => 'etapa_cotizacion_c',
  ),
  'num_dte__compra_c' => 
  array (
    'type' => 'varchar',
    'default' => false,
    'label' => 'LBL_NUM_DTE__COMPRA',
    'width' => '10%',
    'name' => 'num_dte__compra_c',
  ),
);
