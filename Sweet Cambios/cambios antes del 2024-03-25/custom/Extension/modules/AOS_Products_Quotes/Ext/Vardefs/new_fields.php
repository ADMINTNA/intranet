<?php
$dictionary['AOS_Products_Quotes']['fields']['account_name']=array(
    'name' => 'account_name',
    'vname' => 'LBL_ACCOUNT_NAME_P',
    'audited' => 1,
    'type' => 'varchar',
    'len' => '255',
);

$dictionary['AOS_Products_Quotes']['fields']['fecha_contrato']=array(
    'name' => 'fecha_contrato',
    'vname' => 'LBL_FECHA_CONTRATO_P',
    'type' => 'date', 
    'massupdate' => 0,
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => 1,
    'reportable' => true,
    'display_default' => '+ 1 hour',
    'enable_range_search' => true,
    'options' => 'date_range_search_dom',
);

$dictionary['AOS_Products_Quotes']['fields']['duracion_contrato']=array(
    'required' => false,
    'name' => 'duracion_contrato',
    'vname' => 'LBL_DURACION_CONTRATO_P',
    'audited' => 1,
    'type' => 'int',
    'len' => '11',
);