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
      $name         = $row->name;
      $module_id    = (($row->module_id != '')?json_decode($row->module_id):[]);
    } else {
      $name         = '';
      $module_id    = [];
    }
    ?>
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body pt-3">
          <form method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="row mb-3">
              <label for="name" class="col-md-2 col-lg-2 col-form-label">Name</label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="name" class="form-control" id="name" value="<?=$name?>" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="shipment_type" class="col-md-2 col-lg-2 col-form-label">Shipment Type</label>
              <div class="col-md-10 col-lg-10">
                  <input type="radio" id="shipment_type1" name="shipment_type" value="Export">
                  <label for="shipment_type1">Export</label>
                  <input type="radio" id="shipment_type2" name="shipment_type" value="Import">
                  <label for="shipment_type2">Import</label>
              </div>
            </div>
            <div class="row mb-3" id="type-row" style="display: none;">
              <label for="type" class="col-md-2 col-lg-2 col-form-label">Type</label>
              <div class="col-md-10 col-lg-10">
                  <input type="checkbox" id="type1" name="type" value="FCL">
                  <label for="type1">FCL</label>
                  <input type="checkbox" id="type2" name="type" value="LCL">
                  <label for="type2">LCL</label>
                  <input type="checkbox" id="type3" name="type" value="LCL CO LOAD">
                  <label for="type3">LCL CO LOAD</label>
              </div>
            </div>
            <div class="row mb-5">
              <label class="col-md-2 col-lg-2 col-form-label">Modules</label>
              <div class="col-md-10 col-lg-10">
                <div class="row">
                  <?php if($modules){ foreach($modules as $module){?>
                    <div class="col-md-4 col-lg-4">
                      <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="module_id[]" value="<?=$module->id?>" id="module<?=$module->id?>" <?=((in_array($module->id, $module_id))?'checked':'')?>>
                        <label class="form-check-label" for="module<?=$module->id?>"><?=$module->name?></label>
                      </div>
                    </div>
                  <?php } }?>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript">
  $(function(){
    const selectedValue = '';
    const selectedValue2 = '';
    if (selectedValue === "Export") {
      $('#type-row').show();
      $('input[name="type"]').attr('required', true);
    } else {
      $('#type-row').hide();
      $('input[name="type"]').removeAttr('required');
    }

    $('input[name="shipment_type"]').on('change', function() {
      const selectedValue = $('input[name="shipment_type"]:checked').val();
      if (selectedValue === "Export") {
        $('#type-row').show();
        $('input[name="type"]').attr('required', true);
      } else {
        $('#type-row').hide();
        $('input[name="type"]').removeAttr('required');
      }
    });
  })
</script>