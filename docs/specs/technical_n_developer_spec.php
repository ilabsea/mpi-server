** 1 framework limitation **
While you are constructing query, you can not instantiate an new active record object. If you insist to do so
you query will behave unexpectedly


  ej:
  <?php
    $patient_active_record = new Patient();
    $patient_active_record->db->select('xx');

    $visit = new Visit();
    $visit->is_field($field_name);

    //cause unexpected result since $visit instance interrupt the $patient_active_record
    //core framework might store the last active record query builder
    $patient_active_record->db->query()


** Fingerprint component client **
how to add a fingerprint component to form http://www.griaulebiometrics.com/en-us/taxonomy/term/72
