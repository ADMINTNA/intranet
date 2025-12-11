<?php
$viewdefs ['Cases'] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
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
      'useTabs' => false,
      'tabDefs' => 
      array (
        'LBL_CASE_INFORMATION' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
      ),
      'form' => 
      array (
        'enctype' => 'multipart/form-data',
      ),
      'syncDetailEditViews' => true,
    ),
    'panels' => 
    array (
      'lbl_case_information' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'case_number',
            'type' => 'readonly',
          ),
          1 => 'priority',
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'state',
            'comment' => 'The state of the case (i.e. open/closed)',
            'label' => 'LBL_STATE',
          ),
          1 => 'account_name',
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'fecha_resolucion_estimada_c',
            'label' => 'LBL_FECHA_RESOLUCION_ESTIMADA',
          ),
          1 => 
          array (
            'name' => 'contacto_c',
            'studio' => 'visible',
            'label' => 'LBL_CONTACTO',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'name',
            'displayParams' => 
            array (
            ),
          ),
          1 => 
          array (
            'name' => 'categoria_c',
            'studio' => 'visible',
            'label' => 'LBL_CATEGORIA',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'description',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'avances_1_c',
            'studio' => 'visible',
            'label' => 'LBL_AVANCES_1',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'avances_2_c',
            'studio' => 'visible',
            'label' => 'LBL_AVANCES_2',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'avances_3_c',
            'studio' => 'visible',
            'label' => 'LBL_AVANCES_3',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'avances_4_c',
            'studio' => 'visible',
            'label' => 'LBL_AVANCES_4',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'resolution',
            'nl2br' => true,
          ),
        ),
        10 => 
        array (
          0 => 'assigned_user_name',
          1 => 
          array (
            'name' => 'date_entered',
            'comment' => 'Date record created',
            'label' => 'LBL_DATE_ENTERED',
          ),
        ),
      ),
    ),
  ),
);
?>
