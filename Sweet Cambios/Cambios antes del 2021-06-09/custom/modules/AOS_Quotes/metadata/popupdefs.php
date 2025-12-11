<?php
$popupMeta = array (
    'moduleMain' => 'AOS_Quotes',
    'varName' => 'AOS_Quotes',
    'orderBy' => 'aos_quotes.name',
    'whereClauses' => array (
  'name' => 'aos_quotes.name',
  'billing_contact' => 'aos_quotes.billing_contact',
  'billing_account' => 'aos_quotes.billing_account',
  'number' => 'aos_quotes.number',
  'total_amount' => 'aos_quotes.total_amount',
  'stage' => 'aos_quotes.stage',
  'ultimo_contacto_c' => 'aos_quotes_cstm.ultimo_contacto_c',
  'term' => 'aos_quotes.term',
  'assigned_user_id' => 'aos_quotes.assigned_user_id',
  'date_modified' => 'aos_quotes.date_modified',
),
    'searchInputs' => array (
  0 => 'name',
  4 => 'billing_contact',
  5 => 'billing_account',
  6 => 'number',
  7 => 'total_amount',
  8 => 'stage',
  9 => 'ultimo_contacto_c',
  10 => 'term',
  11 => 'assigned_user_id',
  12 => 'date_modified',
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
  'date_modified' => 
  array (
    'type' => 'datetime',
    'label' => 'LBL_DATE_MODIFIED',
    'width' => '10%',
    'name' => 'date_modified',
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
  'stage' => 
  array (
    'name' => 'stage',
    'width' => '10%',
  ),
  'ultimo_contacto_c' => 
  array (
    'type' => 'date',
    'label' => 'LBL_ULTIMO_CONTACTO',
    'width' => '10%',
    'name' => 'ultimo_contacto_c',
  ),
  'term' => 
  array (
    'name' => 'term',
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
),
);
