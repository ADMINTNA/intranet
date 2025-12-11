<?php
// created: 2020-10-15 00:11:24
$viewdefs['Accounts']['QuickCreate'] = array (
  'templateMeta' => 
  array (
    'form' => 
    array (
      'buttons' => 
      array (
        0 => 'SAVE',
        1 => 'CANCEL',
      ),
    ),
    'maxColumns' => '2',
    'widths' => 
    array (
      0 => 
      array (
        'label' => '10',
        'field' => '30',
      ),
      1 => 
      array (
        'label' => '10',
        'field' => '30',
      ),
    ),
    'includes' => 
    array (
      0 => 
      array (
        'file' => 'modules/Accounts/Account.js',
      ),
    ),
    'useTabs' => false,
    'tabDefs' => 
    array (
      'DEFAULT' => 
      array (
        'newTab' => false,
        'panelDefault' => 'expanded',
      ),
    ),
  ),
  'panels' => 
  array (
    'default' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'name',
          'displayParams' => 
          array (
            'required' => true,
          ),
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'website',
        ),
        1 => 
        array (
          'name' => 'phone_office',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'email1',
        ),
        1 => 
        array (
          'name' => 'rut_c',
          'label' => 'LBL_RUT',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'jjwg_maps_address_c',
          'label' => 'LBL_JJWG_MAPS_ADDRESS',
        ),
        1 => 
        array (
          'name' => 'estatusfinanciero_c',
          'studio' => 'visible',
          'label' => 'LBL_ESTATUSFINANCIERO',
        ),
      ),
      4 => 
      array (
        0 => 
        array (
          'name' => 'assigned_user_name',
        ),
      ),
    ),
  ),
);