<?php
// created: 2020-10-15 00:11:23
$searchdefs['AOS_Contracts'] = array (
  'templateMeta' => 
  array (
    'maxColumns' => '3',
    'maxColumnsBasic' => '4',
    'widths' => 
    array (
      'label' => '10',
      'field' => '30',
    ),
  ),
  'layout' => 
  array (
    'basic_search' => 
    array (
      0 => 
      array (
        'type' => 'int',
        'default' => true,
        'label' => 'LBL_CONTRATO',
        'width' => '10%',
        'name' => 'contrato_c',
      ),
      1 => 
      array (
        'name' => 'name',
        'default' => true,
        'width' => '10%',
      ),
      2 => 
      array (
        'name' => 'current_user_only',
        'label' => 'LBL_CURRENT_USER_FILTER',
        'type' => 'bool',
        'default' => true,
        'width' => '10%',
      ),
      3 => 
      array (
        'name' => 'favorites_only',
        'label' => 'LBL_FAVORITES_FILTER',
        'type' => 'bool',
        'default' => true,
        'width' => '10%',
      ),
    ),
    'advanced_search' => 
    array (
      0 => 
      array (
        'type' => 'int',
        'default' => true,
        'label' => 'LBL_CONTRATO',
        'width' => '10%',
        'name' => 'contrato_c',
      ),
      1 => 
      array (
        'name' => 'name',
        'default' => true,
        'width' => '10%',
      ),
      2 => 
      array (
        'name' => 'contract_account',
        'default' => true,
        'width' => '10%',
      ),
      3 => 
      array (
        'name' => 'opportunity',
        'default' => true,
        'width' => '10%',
      ),
      4 => 
      array (
        'name' => 'start_date',
        'default' => true,
        'width' => '10%',
      ),
      5 => 
      array (
        'name' => 'end_date',
        'default' => true,
        'width' => '10%',
      ),
      6 => 
      array (
        'name' => 'total_contract_value',
        'default' => true,
        'width' => '10%',
      ),
      7 => 
      array (
        'name' => 'status',
        'default' => true,
        'width' => '10%',
      ),
      8 => 
      array (
        'name' => 'contract_type',
        'default' => true,
        'width' => '10%',
      ),
      9 => 
      array (
        'name' => 'assigned_user_id',
        'label' => 'LBL_ASSIGNED_TO_NAME',
        'type' => 'enum',
        'function' => 
        array (
          'name' => 'get_user_array',
          'params' => 
          array (
            0 => false,
          ),
        ),
        'default' => true,
        'width' => '10%',
      ),
    ),
  ),
);