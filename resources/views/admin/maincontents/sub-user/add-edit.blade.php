<?php
use App\Helpers\Helper;
$controllerRoute                = $module['controller_route'];
?>
<div class="pagetitle">
  <h1><?=$page_header?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=url('admin/dashboard')?>">Home</a></li>
      <li class="breadcrumb-item active"><a href="<?=url('admin/' . $controllerRoute . '/list/')?>"><?=$module['title']?> List</a></li>
      <li class="breadcrumb-item active"><?=$page_header?></li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section profile">
  <div class="row">
    <div class="col-xl-12">
      @if(session('success_message'))
        <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show autohide" role="alert">
          {{ session('success_message') }}
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      @if(session('error_message'))
        <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show autohide" role="alert">
          {{ session('error_message') }}
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
    </div>
    <?php
    if($row){
      $name                             = $row->name;
      $email                            = $row->email;
      $mobile                           = $row->mobile;
      $role_id                          = $row->role_id;
      $is_import_email                  = $row->is_import_email;
      $is_fcl_export_email              = $row->is_fcl_export_email;
      $is_lcl_export_email              = $row->is_lcl_export_email;
      $is_lcl_co_load_export_email      = $row->is_lcl_co_load_export_email;
    } else {
      $name                             = '';
      $email                            = '';
      $mobile                           = '';
      $role_id                          = '';
      $is_import_email                  = '';
      $is_fcl_export_email              = '';
      $is_lcl_export_email              = '';
      $is_lcl_co_load_export_email      = '';
    }
    ?>
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body pt-3">
          <form method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="row mb-3">
              <label for="role_id" class="col-md-2 col-lg-2 col-form-label">Role</label>
              <div class="col-md-10 col-lg-10">
                <select name="role_id" class="form-control" id="role_id" required>
                  <option value="" selected>Select Role</option>
                  <?php if($roles){ foreach($roles as $role){?>
                  <option value="<?=$role->id?>" <?=(($role->id == $role_id)?'selected':'')?>><?=$role->name?></option>
                  <?php } }?>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label for="name" class="col-md-2 col-lg-2 col-form-label">Name</label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="name" class="form-control" id="name" value="<?=$name?>" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="email" class="col-md-2 col-lg-2 col-form-label">Email</label>
              <div class="col-md-10 col-lg-10">
                <input type="email" name="email" class="form-control" id="email" value="<?=$email?>" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="mobile" class="col-md-2 col-lg-2 col-form-label">Mobile</label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="mobile" class="form-control" id="mobile" value="<?=$mobile?>" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="password" class="col-md-2 col-lg-2 col-form-label">Password</label>
              <div class="col-md-10 col-lg-10">
                <input type="password" name="password" class="form-control" id="password" value="" <?=((!empty($row))?'':'required')?>>
                <?php if($row){?><small class="text-info">* Leave blank if you don't want to change password</small><br><?php }?>
              </div>
            </div>
            <div class="row mb-3">
              <label for="is_import_email" class="col-md-2 col-lg-2 col-form-label">Import Email Access</label>
              <div class="col-md-10 col-lg-10">
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" name="is_import_email" id="is_import_email" <?=(($is_import_email)?'checked':'')?>>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label for="is_fcl_export_email" class="col-md-2 col-lg-2 col-form-label">Export FCL Email Access</label>
              <div class="col-md-10 col-lg-10">
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" name="is_fcl_export_email" id="is_fcl_export_email" <?=(($is_fcl_export_email)?'checked':'')?>>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label for="is_lcl_export_email" class="col-md-2 col-lg-2 col-form-label">Export LCL Email Access</label>
              <div class="col-md-10 col-lg-10">
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" name="is_lcl_export_email" id="is_lcl_export_email" <?=(($is_lcl_export_email)?'checked':'')?>>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label for="is_lcl_co_load_export_email" class="col-md-2 col-lg-2 col-form-label">Export LCL CO LOAD Email Access</label>
              <div class="col-md-10 col-lg-10">
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" name="is_lcl_co_load_export_email" id="is_lcl_co_load_export_email" <?=(($is_lcl_co_load_export_email)?'checked':'')?>>
                </div>
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary"><?=(($row)?'Save':'Add')?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>