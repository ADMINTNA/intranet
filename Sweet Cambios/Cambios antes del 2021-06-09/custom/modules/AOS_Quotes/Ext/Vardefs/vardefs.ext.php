<?php 
 //WARNING: The contents of this file are auto-generated


 // created: 2020-03-10 19:13:41
$dictionary['AOS_Quotes']['fields']['opportunity']['required']=false;
$dictionary['AOS_Quotes']['fields']['opportunity']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['opportunity']['inline_edit']=true;

 

 // created: 2019-09-27 15:21:25
$dictionary['AOS_Quotes']['fields']['medio_de_pago_c']['inline_edit']='1';
$dictionary['AOS_Quotes']['fields']['medio_de_pago_c']['labelValue']='Medio de pago';

 

 // created: 2017-05-08 19:09:08
$dictionary['AOS_Quotes']['fields']['ultimo_contacto_c']['inline_edit']='1';
$dictionary['AOS_Quotes']['fields']['ultimo_contacto_c']['labelValue']='ultimo contacto';

 

 // created: 2017-05-06 19:04:10
$dictionary['AOS_Quotes']['fields']['term']['default']='Contado';
$dictionary['AOS_Quotes']['fields']['term']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['term']['merge_filter']='disabled';

 

 // created: 2017-05-09 03:24:57
$dictionary['AOS_Quotes']['fields']['approval_issue']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['approval_issue']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['approval_issue']['rows']='4';
$dictionary['AOS_Quotes']['fields']['approval_issue']['cols']='20';

 

 // created: 2019-09-26 19:51:42
$dictionary['AOS_Quotes']['fields']['fecha_despacho_oc_c']['inline_edit']='1';
$dictionary['AOS_Quotes']['fields']['fecha_despacho_oc_c']['labelValue']='Fecha Despacho ';

 

 // created: 2018-04-26 21:18:25
$dictionary['AOS_Quotes']['fields']['stage']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['stage']['massupdate']='1';
$dictionary['AOS_Quotes']['fields']['stage']['merge_filter']='disabled';

 

 // created: 2019-09-27 20:38:41
$dictionary['AOS_Quotes']['fields']['observaciones_oc_c']['inline_edit']='1';
$dictionary['AOS_Quotes']['fields']['observaciones_oc_c']['labelValue']='observaciones oc';

 

// created: 2020-03-10 18:45:21
$dictionary["AOS_Quotes"]["fields"]["cases_aos_quotes_1"] = array (
  'name' => 'cases_aos_quotes_1',
  'type' => 'link',
  'relationship' => 'cases_aos_quotes_1',
  'source' => 'non-db',
  'module' => 'Cases',
  'bean_name' => 'Case',
  'vname' => 'LBL_CASES_AOS_QUOTES_1_FROM_CASES_TITLE',
);


// created: 2020-10-27 20:47:36
$dictionary["AOS_Quotes"]["fields"]["aos_quotes_aos_invoices_1"] = array (
  'name' => 'aos_quotes_aos_invoices_1',
  'type' => 'link',
  'relationship' => 'aos_quotes_aos_invoices_1',
  'source' => 'non-db',
  'module' => 'AOS_Invoices',
  'bean_name' => 'AOS_Invoices',
  'vname' => 'LBL_AOS_QUOTES_AOS_INVOICES_1_FROM_AOS_INVOICES_TITLE',
  'id_name' => 'aos_quotes_aos_invoices_1aos_invoices_idb',
);
$dictionary["AOS_Quotes"]["fields"]["aos_quotes_aos_invoices_1_name"] = array (
  'name' => 'aos_quotes_aos_invoices_1_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_AOS_QUOTES_AOS_INVOICES_1_FROM_AOS_INVOICES_TITLE',
  'save' => true,
  'id_name' => 'aos_quotes_aos_invoices_1aos_invoices_idb',
  'link' => 'aos_quotes_aos_invoices_1',
  'table' => 'aos_invoices',
  'module' => 'AOS_Invoices',
  'rname' => 'name',
);
$dictionary["AOS_Quotes"]["fields"]["aos_quotes_aos_invoices_1aos_invoices_idb"] = array (
  'name' => 'aos_quotes_aos_invoices_1aos_invoices_idb',
  'type' => 'link',
  'relationship' => 'aos_quotes_aos_invoices_1',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'left',
  'vname' => 'LBL_AOS_QUOTES_AOS_INVOICES_1_FROM_AOS_INVOICES_TITLE',
);


// created: 2020-10-27 20:58:41
$dictionary["AOS_Quotes"]["fields"]["aos_invoices_aos_quotes_1"] = array (
  'name' => 'aos_invoices_aos_quotes_1',
  'type' => 'link',
  'relationship' => 'aos_invoices_aos_quotes_1',
  'source' => 'non-db',
  'module' => 'AOS_Invoices',
  'bean_name' => 'AOS_Invoices',
  'vname' => 'LBL_AOS_INVOICES_AOS_QUOTES_1_FROM_AOS_INVOICES_TITLE',
  'id_name' => 'aos_invoices_aos_quotes_1aos_invoices_ida',
);
$dictionary["AOS_Quotes"]["fields"]["aos_invoices_aos_quotes_1_name"] = array (
  'name' => 'aos_invoices_aos_quotes_1_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_AOS_INVOICES_AOS_QUOTES_1_FROM_AOS_INVOICES_TITLE',
  'save' => true,
  'id_name' => 'aos_invoices_aos_quotes_1aos_invoices_ida',
  'link' => 'aos_invoices_aos_quotes_1',
  'table' => 'aos_invoices',
  'module' => 'AOS_Invoices',
  'rname' => 'name',
);
$dictionary["AOS_Quotes"]["fields"]["aos_invoices_aos_quotes_1aos_invoices_ida"] = array (
  'name' => 'aos_invoices_aos_quotes_1aos_invoices_ida',
  'type' => 'link',
  'relationship' => 'aos_invoices_aos_quotes_1',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'left',
  'vname' => 'LBL_AOS_INVOICES_AOS_QUOTES_1_FROM_AOS_INVOICES_TITLE',
);


 // created: 2020-10-27 11:24:15
$dictionary['AOS_Quotes']['fields']['stage']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['stage']['massupdate']='1';
$dictionary['AOS_Quotes']['fields']['stage']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['stage']['default']='Closed Lost';

 

 // created: 2020-10-27 11:25:16
$dictionary['AOS_Quotes']['fields']['etapa_cotizacion_c']['inline_edit']='1';
$dictionary['AOS_Quotes']['fields']['etapa_cotizacion_c']['labelValue']='Etapa de Cotización';

 

 // created: 2020-10-23 14:49:32
$dictionary['AOS_Quotes']['fields']['opportunity']['required']=false;
$dictionary['AOS_Quotes']['fields']['opportunity']['merge_filter']='disabled';
$dictionary['AOS_Quotes']['fields']['opportunity']['inline_edit']=true;

 

 // created: 2020-10-23 15:10:13
$dictionary['AOS_Quotes']['fields']['aos_quotes_id_c']['inline_edit']=1;

 

 // created: 2020-10-23 15:10:13
$dictionary['AOS_Quotes']['fields']['cotizacion_c']['inline_edit']='1';
$dictionary['AOS_Quotes']['fields']['cotizacion_c']['labelValue']='Cotización';

 

 // created: 2020-10-23 15:20:53
$dictionary['AOS_Quotes']['fields']['etapa_factura_c']['inline_edit']='1';
$dictionary['AOS_Quotes']['fields']['etapa_factura_c']['labelValue']='Estado de factura_M';

 

 // created: 2020-10-27 20:15:18
$dictionary['AOS_Quotes']['fields']['invoice_status']['default']='Not Invoiced';
$dictionary['AOS_Quotes']['fields']['invoice_status']['inline_edit']=true;
$dictionary['AOS_Quotes']['fields']['invoice_status']['merge_filter']='disabled';

 

 // created: 2020-11-23 16:23:56
$dictionary['AOS_Quotes']['fields']['costo_c']['inline_edit']='1';
$dictionary['AOS_Quotes']['fields']['costo_c']['options']='numeric_range_search_dom';
$dictionary['AOS_Quotes']['fields']['costo_c']['labelValue']='costo';
$dictionary['AOS_Quotes']['fields']['costo_c']['enable_range_search']='1';

 
?>