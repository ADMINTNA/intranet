<?php
$viewdefs ['Cases'] = 
array (
  'DetailView' => 
  array (
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
      'useTabs' => false,
      'tabDefs' => 
      array (
        'LBL_CASE_INFORMATION' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
        'LBL_EDITVIEW_PANEL1' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
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
            'label' => 'LBL_CASE_NUMBER',
          ),
          1 => 
          array (
            'name' => 'state',
            'comment' => 'The state of the case (i.e. open/closed)',
            'label' => 'LBL_STATE',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO',
          ),
          1 => 
          array (
            'name' => 'date_entered',
            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'name',
            'label' => 'LBL_SUBJECT',
          ),
          1 => 
          array (
            'name' => 'tipo_caso_c',
            'studio' => 'visible',
            'label' => 'LBL_TIPO_CASO',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'contacto_c',
            'studio' => 'visible',
            'label' => 'LBL_CONTACTO',
          ),
          1 => 'account_name',
        ),
        4 => 
        array (
          0 => 'priority',
          1 => 
          array (
            'name' => 'horario_c',
            'studio' => 'visible',
            'label' => 'LBL_HORARIO',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'categoria_c',
            'studio' => 'visible',
            'label' => 'LBL_CATEGORIA',
          ),
          1 => 
          array (
            'name' => 'responsable_c',
            'studio' => 'visible',
            'label' => 'LBL_RESPONSABLE',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'fecha_resolucion_estimada_c',
            'label' => 'LBL_FECHA_RESOLUCION_ESTIMADA',
          ),
        ),
      ),
      'lbl_editview_panel1' => 
      array (
        0 => 
        array (
          0 => 'description',
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'avances_1_c',
            'studio' => 'visible',
            'label' => 'LBL_AVANCES_1',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'avances_2_c',
            'studio' => 'visible',
            'label' => 'LBL_AVANCES_2',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'avances_3_c',
            'studio' => 'visible',
            'label' => 'LBL_AVANCES_3',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'avances_4_c',
            'studio' => 'visible',
            'label' => 'LBL_AVANCES_4',
          ),
        ),
        5 => 
        array (
          0 => 'resolution',
        ),
      ),
    ),
  ),
);
;
?>
