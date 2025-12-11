<?php 
 //WARNING: The contents of this file are auto-generated


 // created: 2016-03-15 16:27:20
$dictionary['AOS_Invoices']['fields']['tipo_factura_c']['labelValue']='Tipo factura';

 

 // created: 2016-04-15 17:30:00
$dictionary['AOS_Invoices']['fields']['billing_account']['required']=true;
$dictionary['AOS_Invoices']['fields']['billing_account']['merge_filter']='disabled';

 

 // created: 2016-05-09 19:33:51
$dictionary['AOS_Invoices']['fields']['num_nota_venta1_c']['inline_edit']='1';
$dictionary['AOS_Invoices']['fields']['num_nota_venta1_c']['options']='numeric_range_search_dom';
$dictionary['AOS_Invoices']['fields']['num_nota_venta1_c']['labelValue']='Nº Nota de venta.';
$dictionary['AOS_Invoices']['fields']['num_nota_venta1_c']['enable_range_search']='1';

 

 // created: 2016-05-18 15:28:40
$dictionary['AOS_Invoices']['fields']['status']['required']=true;
$dictionary['AOS_Invoices']['fields']['status']['inline_edit']=true;
$dictionary['AOS_Invoices']['fields']['status']['merge_filter']='disabled';

 

 // created: 2016-05-05 09:53:45
$dictionary['AOS_Invoices']['fields']['description']['inline_edit']=true;
$dictionary['AOS_Invoices']['fields']['description']['comments']='Full text of the note';
$dictionary['AOS_Invoices']['fields']['description']['merge_filter']='disabled';
$dictionary['AOS_Invoices']['fields']['description']['rows']='2';
$dictionary['AOS_Invoices']['fields']['description']['cols']='80';

 

 // created: 2020-02-12 20:30:37
$dictionary['AOS_Invoices']['fields']['subtotal_amount']['inline_edit']=true;
$dictionary['AOS_Invoices']['fields']['subtotal_amount']['merge_filter']='disabled';
$dictionary['AOS_Invoices']['fields']['subtotal_amount']['enable_range_search']=false;

 

// created: 2020-10-27 20:47:36
$dictionary["AOS_Invoices"]["fields"]["aos_quotes_aos_invoices_1"] = array (
  'name' => 'aos_quotes_aos_invoices_1',
  'type' => 'link',
  'relationship' => 'aos_quotes_aos_invoices_1',
  'source' => 'non-db',
  'module' => 'AOS_Quotes',
  'bean_name' => 'AOS_Quotes',
  'vname' => 'LBL_AOS_QUOTES_AOS_INVOICES_1_FROM_AOS_QUOTES_TITLE',
  'id_name' => 'aos_quotes_aos_invoices_1aos_quotes_ida',
);
$dictionary["AOS_Invoices"]["fields"]["aos_quotes_aos_invoices_1_name"] = array (
  'name' => 'aos_quotes_aos_invoices_1_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_AOS_QUOTES_AOS_INVOICES_1_FROM_AOS_QUOTES_TITLE',
  'save' => true,
  'id_name' => 'aos_quotes_aos_invoices_1aos_quotes_ida',
  'link' => 'aos_quotes_aos_invoices_1',
  'table' => 'aos_quotes',
  'module' => 'AOS_Quotes',
  'rname' => 'name',
);
$dictionary["AOS_Invoices"]["fields"]["aos_quotes_aos_invoices_1aos_quotes_ida"] = array (
  'name' => 'aos_quotes_aos_invoices_1aos_quotes_ida',
  'type' => 'link',
  'relationship' => 'aos_quotes_aos_invoices_1',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'left',
  'vname' => 'LBL_AOS_QUOTES_AOS_INVOICES_1_FROM_AOS_QUOTES_TITLE',
);


// created: 2020-10-27 20:49:18
$dictionary["AOS_Invoices"]["fields"]["opportunities_aos_invoices_1"] = array (
  'name' => 'opportunities_aos_invoices_1',
  'type' => 'link',
  'relationship' => 'opportunities_aos_invoices_1',
  'source' => 'non-db',
  'module' => 'Opportunities',
  'bean_name' => 'Opportunity',
  'vname' => 'LBL_OPPORTUNITIES_AOS_INVOICES_1_FROM_OPPORTUNITIES_TITLE',
  'id_name' => 'opportunities_aos_invoices_1opportunities_ida',
);
$dictionary["AOS_Invoices"]["fields"]["opportunities_aos_invoices_1_name"] = array (
  'name' => 'opportunities_aos_invoices_1_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_OPPORTUNITIES_AOS_INVOICES_1_FROM_OPPORTUNITIES_TITLE',
  'save' => true,
  'id_name' => 'opportunities_aos_invoices_1opportunities_ida',
  'link' => 'opportunities_aos_invoices_1',
  'table' => 'opportunities',
  'module' => 'Opportunities',
  'rname' => 'name',
);
$dictionary["AOS_Invoices"]["fields"]["opportunities_aos_invoices_1opportunities_ida"] = array (
  'name' => 'opportunities_aos_invoices_1opportunities_ida',
  'type' => 'link',
  'relationship' => 'opportunities_aos_invoices_1',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_OPPORTUNITIES_AOS_INVOICES_1_FROM_AOS_INVOICES_TITLE',
);


// created: 2020-10-27 20:58:41
$dictionary["AOS_Invoices"]["fields"]["aos_invoices_aos_quotes_1"] = array (
  'name' => 'aos_invoices_aos_quotes_1',
  'type' => 'link',
  'relationship' => 'aos_invoices_aos_quotes_1',
  'source' => 'non-db',
  'module' => 'AOS_Quotes',
  'bean_name' => 'AOS_Quotes',
  'vname' => 'LBL_AOS_INVOICES_AOS_QUOTES_1_FROM_AOS_QUOTES_TITLE',
  'id_name' => 'aos_invoices_aos_quotes_1aos_quotes_idb',
);
$dictionary["AOS_Invoices"]["fields"]["aos_invoices_aos_quotes_1_name"] = array (
  'name' => 'aos_invoices_aos_quotes_1_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_AOS_INVOICES_AOS_QUOTES_1_FROM_AOS_QUOTES_TITLE',
  'save' => true,
  'id_name' => 'aos_invoices_aos_quotes_1aos_quotes_idb',
  'link' => 'aos_invoices_aos_quotes_1',
  'table' => 'aos_quotes',
  'module' => 'AOS_Quotes',
  'rname' => 'name',
);
$dictionary["AOS_Invoices"]["fields"]["aos_invoices_aos_quotes_1aos_quotes_idb"] = array (
  'name' => 'aos_invoices_aos_quotes_1aos_quotes_idb',
  'type' => 'link',
  'relationship' => 'aos_invoices_aos_quotes_1',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'left',
  'vname' => 'LBL_AOS_INVOICES_AOS_QUOTES_1_FROM_AOS_QUOTES_TITLE',
);


 // created: 2020-10-22 11:35:35
$dictionary['AOS_Invoices']['fields']['estado_contratos_c']['inline_edit']='1';
$dictionary['AOS_Invoices']['fields']['estado_contratos_c']['labelValue']='Estado (Automatico-NO tocar)';

 

 // created: 2020-10-23 18:55:33
$dictionary['AOS_Invoices']['fields']['status']['required']=true;
$dictionary['AOS_Invoices']['fields']['status']['inline_edit']=true;
$dictionary['AOS_Invoices']['fields']['status']['merge_filter']='disabled';
$dictionary['AOS_Invoices']['fields']['status']['audited']=true;
$dictionary['AOS_Invoices']['fields']['status']['massupdate']='1';

 

 // created: 2020-10-23 14:36:32
$dictionary['AOS_Invoices']['fields']['aos_quotes_id_c']['inline_edit']=1;

 

 // created: 2020-10-23 14:27:24
$dictionary['AOS_Invoices']['fields']['estado_cot_c']['inline_edit']='1';
$dictionary['AOS_Invoices']['fields']['estado_cot_c']['labelValue']='Estado(no tocar)';

 

 // created: 2020-10-23 14:30:18
$dictionary['AOS_Invoices']['fields']['estado_cotizacion_c']['inline_edit']='1';
$dictionary['AOS_Invoices']['fields']['estado_cotizacion_c']['labelValue']='Estado (NO tocar)';

 

 // created: 2020-10-27 21:11:24
$dictionary['AOS_Invoices']['fields']['opportunity_id_c']['inline_edit']=1;

 

 // created: 2020-10-23 14:49:05
$dictionary['AOS_Invoices']['fields']['oportunidad_c']['inline_edit']='1';
$dictionary['AOS_Invoices']['fields']['oportunidad_c']['labelValue']='Oportunidad';

 

 // created: 2020-10-23 14:36:32
$dictionary['AOS_Invoices']['fields']['cotizacion_c']['inline_edit']='1';
$dictionary['AOS_Invoices']['fields']['cotizacion_c']['labelValue']='Cotización';

 

 // created: 2020-10-23 15:03:14
$dictionary['AOS_Invoices']['fields']['opportunity_c']['inline_edit']='1';
$dictionary['AOS_Invoices']['fields']['opportunity_c']['labelValue']='Oportunidad';

 

 // created: 2020-10-23 15:23:48
$dictionary['AOS_Invoices']['fields']['estado_factura_c']['inline_edit']='1';
$dictionary['AOS_Invoices']['fields']['estado_factura_c']['labelValue']='Estado factura M';

 

 // created: 2020-10-27 21:15:16
$dictionary['AOS_Invoices']['fields']['oportunidad_m_c']['inline_edit']='1';
$dictionary['AOS_Invoices']['fields']['oportunidad_m_c']['labelValue']='Oportunidad M';

 
?>