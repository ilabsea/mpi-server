<br/><br/>
<div class="row-fluid">
   <div class="span2 offset1" align="center">
       <a href="<?=site_url("patients/patientlist")?>"><img style="width: 128px" src="<?=base_url("img/patients.jpg")?>" alt="Patients"/></a>
       <h4><a href="<?=site_url("patients/patientlist")?>">Patients</a></h4>
   </div>

   <div class="span2 offset2" align="center">
       <a href="<?=site_url("sites/sitelist")?>"><img style="width: 128px" src="<?=base_url("img/hospital_icon2.png")?>" alt="Sites"/></a>
       <h4><a href="<?=site_url("sites/sitelist")?>">Sites</a></h4>
   </div>

   <div class="span2 offset2" align="center">
       <a href="<?=site_url("reports/reportmenu")?>"><img style="width: 128px" src="<?=base_url("img/reports_icon.png")?>" alt="Reports"/></a>
       <h4><a href="<?=site_url("reports/reportmenu")?>">Reports</a></h4>
   </div>

</div>
<br/>
<div class="row-fluid">
   <?php if ($mpi_user["grp_id"] == Iconstant::USER_ADMIN) : ?>
   <div class="span2 offset1" align="center">
       <a href="<?=site_url("users/userlist")?>"><img style="width: 128px" src="<?=base_url("img/users.png")?>" alt="Users"/></a>
       <h4><a href="<?=site_url("users/userlist")?>">Users</a></h4>
   </div>
   <?php endif;?>

   <?php if ($mpi_user["grp_id"] == Iconstant::USER_ADMIN) : ?>
   <div class="span2 offset2" align="center">
       <a href="<?=site_url("members/memberlist")?>"><img style="width: 128px" src="<?=base_url("img/doctor.png")?>" alt="Members"/></a>
       <h4><a href="<?=site_url("members/memberlist")?>">Members</a></h4>
   </div>
   <?php endif;?>

   <?php if ($mpi_user["grp_id"] == Iconstant::USER_ADMIN) : ?>
   <div class="span2 offset2" align="center">
       <a href="<?=site_url("datas/csvexport")?>"><img style="width: 128px" src="<?=base_url("img/csv.png")?>" alt="CSV Export"/></a>
       <h4><a href="<?=site_url("datas/csvexport")?>">CSV Export</a></h4>
   </div>
   <?php endif;?>

   <div class="row-fluid">
     <?php if ($mpi_user["grp_id"] == Iconstant::USER_ADMIN) : ?>
       <div class="span2 offset1" align="center">
           <a href="<?=site_url("fields/index")?>">
             <img style="width: 128px" src="<?=base_url("img/api-dynamic-field.png")?>" alt="Dynamic field"/></a>
             <h4><a href="<?=site_url("fields/index")?>">Dynamic field</a></h4>
       </div>
     <?php endif;?>

     <?php if ($mpi_user["grp_id"] == Iconstant::USER_ADMIN) : ?>
       <div class="span2 offset2" align="center">
           <a href="<?=site_url("scopes/index")?>">
             <img style="width: 128px" src="<?=base_url("img/api-scope.png")?>" alt="API Scope"/></a>
             <h4><a href="<?=site_url("scopes/index")?>">API Scope</a></h4>
       </div>
     <?php endif;?>

     <?php if ($mpi_user["grp_id"] == Iconstant::USER_ADMIN) : ?>
       <div class="span2 offset2" align="center">
           <a href="<?=site_url("applications/index")?>">
             <img style="width: 128px" src="<?=base_url("img/api-application.png")?>" alt="API application"/></a>
             <h4><a href="<?=site_url("applications/index")?>">Application</a></h4>
       </div>
     <?php endif;?>
  </div>
</div>
