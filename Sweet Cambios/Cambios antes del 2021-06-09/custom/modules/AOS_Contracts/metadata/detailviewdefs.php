<?php
// created: 2020-10-15 00:11:23
$viewdefs['AOS_Contracts']['DetailView'] = array (
  'templateMeta' => 
  array (
    'form' => 
    array (
      'buttons' => 
      array (
        0 => 'EDIT',
        1 => 'DUPLICATE',
        2 => 'DELETE',
        3 => 'FIND_DUPLICATES',
        4 => 
        array (
          'customCode' => '<input type="button" class="button" onClick="showPopup(\'pdf\');" value="{$MOD.LBL_PRINT_AS_PDF}">',
        ),
        5 => 
        array (
          'customCode' => '<input type="button" class="button" onClick="showPopup(\'emailpdf\');" value="{$MOD.LBL_EMAIL_PDF}">',
        ),
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
    'useTabs' => true,
    'syncDetailEditViews' => true,
    'tabDefs' => 
    array (
      'DEFAULT' => 
      array (
        'newTab' => true,
        'panelDefault' => 'expanded',
      ),
      'LBL_LINE_ITEMS' => 
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
          'name' => 'contrato_c',
          'label' => 'LBL_CONTRATO',
        ),
        1 => 
        array (
          'name' => 'anexo_numero_c',
          'label' => 'LBL_ANEXO_NUMERO',
        ),
      ),
      1 => 
      array (
        0 => 'name',
        1 => 
        array (
          'name' => 'assigned_user_name',
          'label' => 'LBL_ASSIGNED_TO_NAME',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'total_contract_value',
          'label' => 'LBL_TOTAL_CONTRACT_VALUE',
        ),
        1 => 
        array (
          'name' => 'contract_account',
          'label' => 'LBL_CONTRACT_ACCOUNT',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'status',
          'studio' => 'visible',
          'label' => 'LBL_STATUS',
        ),
        1 => 
        array (
          'name' => 'contact',
          'studio' => 'visible',
          'label' => 'LBL_CONTACT',
        ),
      ),
      4 => 
      array (
        0 => 
        array (
          'name' => 'start_date',
          'label' => 'LBL_START_DATE',
        ),
        1 => 
        array (
          'name' => 'opportunity',
          'label' => 'LBL_OPPORTUNITY',
        ),
      ),
      5 => 
      array (
        0 => 
        array (
          'name' => 'contract_type',
          'studio' => 'visible',
          'label' => 'LBL_CONTRACT_TYPE',
        ),
      ),
      6 => 
      array (
        0 => 
        array (
          'name' => 'vigencia_c',
          'studio' => 'visible',
          'label' => 'LBL_VIGENCIA',
        ),
      ),
      7 => 
      array (
        0 => 
        array (
          'name' => 'renovacion_c',
          'label' => 'LBL_RENOVACION',
        ),
      ),
      8 => 
      array (
        0 => 'description',
      ),
    ),
    'lbl_line_items' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'currency_id',
          'studio' => 'visible',
          'label' => 'LBL_CURRENCY',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'line_items',
          'label' => 'LBL_LINE_ITEMS',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'comentarioprueba_c',
          'studio' => 'visible',
          'label' => 'LBL_COMENTARIOPRUEBA',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'total_amt',
          'label' => 'LBL_TOTAL_AMT',
        ),
      ),
      4 => 
      array (
        0 => 
        array (
          'name' => 'discount_amount',
          'label' => 'LBL_DISCOUNT_AMOUNT',
        ),
      ),
      5 => 
      array (
        0 => 
        array (
          'name' => 'subtotal_amount',
          'label' => 'LBL_SUBTOTAL_AMOUNT',
        ),
      ),
      6 => 
      array (
        0 => 
        array (
          'name' => 'shipping_amount',
          'label' => 'LBL_SHIPPING_AMOUNT',
        ),
      ),
      7 => 
      array (
        0 => 
        array (
          'name' => 'shipping_tax_amt',
          'label' => 'LBL_SHIPPING_TAX_AMT',
        ),
      ),
      8 => 
      array (
        0 => 
        array (
          'name' => 'tax_amount',
          'label' => 'LBL_TAX_AMOUNT',
        ),
      ),
      9 => 
      array (
        0 => 
        array (
          'name' => 'total_amount',
          'label' => 'LBL_GRAND_TOTAL',
        ),
      ),
    ),
  ),
);