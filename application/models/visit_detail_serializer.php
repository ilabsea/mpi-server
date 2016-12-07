<?php
class VisitDetailSerializer extends Serializer{
  var $fields = array('visit_id', 'site_code', 'site_name', 'ext_code', 'serv_id', 'info', 'pat_age',
                      'refer_to_vcct', 'refer_to_oiart', 'refer_to_std', 'visit_date', 'date_create');
}
