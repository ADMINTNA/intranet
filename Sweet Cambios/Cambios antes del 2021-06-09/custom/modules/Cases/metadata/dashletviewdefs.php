<?php
$dashletData['CasesDashlet']['searchFields'] = array (
  'case_number' => 
  array (
    'default' => '',
  ),
  'date_entered' => 
  array (
    'default' => '',
  ),
  'fecha_resolucion_estimada_c' => 
  array (
    'default' => '',
  ),
  'priority' => 
  array (
    'default' => '',
  ),
  'tipo_caso_c' => 
  array (
    'default' => '',
  ),
  'name' => 
  array (
    'default' => '',
  ),
  'state' => 
  array (
    'default' => '',
  ),
  'categoria_c' => 
  array (
    'default' => '',
  ),
  'assigned_user_id' => 
  array (
    'default' => '',
  ),
);
$dashletData['CasesDashlet']['columns'] = array (
  'case_number' => 
  array (
    'width' => '6%',
    'label' => 'LBL_NUMBER',
    'default' => true,
    'name' => 'case_number',
  ),
  'name' => 
  array (
    'width' => '40%',
    'label' => 'LBL_LIST_SUBJECT',
    'link' => true,
    'default' => true,
    'name' => 'name',
  ),
  'priority' => 
  array (
    'width' => '15%',
    'label' => 'LBL_PRIORITY',
    'default' => true,
    'name' => 'priority',
  ),
  'state' => 
  array (
    'type' => 'enum',
    'default' => true,
    'label' => 'LBL_STATE',
    'width' => '10%',
    'name' => 'state',
  ),
  'status' => 
  array (
    'width' => '8%',
    'label' => 'LBL_STATUS',
    'default' => true,
    'name' => 'status',
  ),
  'tipo_caso_c' => 
  array (
    'type' => 'enum',
    'default' => true,
    'studio' => 'visible',
    'label' => 'LBL_TIPO_CASO',
    'width' => '10%',
  ),
  'account_name' => 
  array (
    'width' => '29%',
    'link' => true,
    'module' => 'Accounts',
    'id' => 'ACCOUNT_ID',
    'ACLTag' => 'ACCOUNT',
    'label' => 'LBL_ACCOUNT_NAME',
    'related_fields' => 
    array (
      0 => 'account_id',
    ),
    'name' => 'account_name',
    'default' => false,
  ),
  'resolution' => 
  array (
    'width' => '8%',
    'label' => 'LBL_RESOLUTION',
    'name' => 'resolution',
    'default' => false,
  ),
  'adjunto_caso_c' => 
  array (
    'type' => 'image',
    'default' => false,
    'studio' => 'visible',
    'width' => '10%',
    'label' => 'LBL_ADJUNTO_CASO',
    'name' => 'adjunto_caso_c',
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
  'fecha_resolucion_estimada_c' => 
  array (
    'type' => 'date',
    'default' => false,
    'label' => 'LBL_FECHA_RESOLUCION_ESTIMADA',
    'width' => '10%',
    'name' => 'fecha_resolucion_estimada_c',
  ),
  'type' => 
  array (
    'type' => 'enum',
    'label' => 'LBL_TYPE',
    'width' => '10%',
    'default' => false,
    'name' => 'type',
  ),
);
