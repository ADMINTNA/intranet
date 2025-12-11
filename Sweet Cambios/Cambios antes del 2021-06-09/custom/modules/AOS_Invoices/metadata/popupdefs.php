<?php
$popupMeta = array (
    'moduleMain' => 'AOS_Invoices',
    'varName' => 'AOS_Invoices',
    'orderBy' => 'aos_invoices.name',
    'whereClauses' => array (
  'name' => 'aos_invoices.name',
  'billing_contact' => 'aos_invoices.billing_contact',
  'billing_account' => 'aos_invoices.billing_account',
  'number' => 'aos_invoices.number',
  'total_amount' => 'aos_invoices.total_amount',
  'status' => 'aos_invoices.status',
  'assigned_user_id' => 'aos_invoices.assigned_user_id',
  'invoice_date' => 'aos_invoices.invoice_date',
  'date_entered' => 'aos_invoices.date_entered',
),
    'searchInputs' => array (
  0 => 'name',
  4 => 'billing_contact',
  5 => 'billing_account',
  6 => 'number',
  7 => 'total_amount',
  8 => 'status',
  9 => 'assigned_user_id',
  10 => 'invoice_date',
  11 => 'date_entered',
),
    'searchdefs' => array (
  'name' => 
  array (
    'name' => 'name',
    'width' => '10%',
  ),
  'billing_contact' => 
  array (
    'name' => 'billing_contact',
    'width' => '10%',
  ),
  'billing_account' => 
  array (
    'name' => 'billing_account',
    'width' => '10%',
  ),
  'number' => 
  array (
    'name' => 'number',
    'width' => '10%',
  ),
  'total_amount' => 
  array (
    'name' => 'total_amount',
    'width' => '10%',
  ),
  'status' => 
  array (
    'name' => 'status',
    'width' => '10%',
  ),
  'assigned_user_id' => 
  array (
    'name' => 'assigned_user_id',
    'type' => 'enum',
    'label' => 'LBL_ASSIGNED_TO',
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
  'invoice_date' => 
  array (
    'type' => 'date',
    'label' => 'LBL_INVOICE_DATE',
    'width' => '10%',
    'name' => 'invoice_date',
  ),
  'date_entered' => 
  array (
    'type' => 'datetime',
    'label' => 'LBL_DATE_ENTERED',
    'width' => '10%',
    'name' => 'date_entered',
  ),
),
);
