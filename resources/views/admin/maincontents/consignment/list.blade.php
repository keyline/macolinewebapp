<?php
use App\Models\ProcessFlow;
use App\Models\Consignment;
use App\Models\ConsignmentDetail;
use App\Models\Role;
use App\Helpers\Helper;
$user_type = session('type');
$controllerRoute        = $module['controller_route'];
$getRole                = Role::where('id', '=', $admin->role_id)->first();
$import_access          = (($getRole)?$getRole->import_access:0);
$export_access          = (($getRole)?$getRole->export_access:0);
$add_consignment_access = (($getRole)?$getRole->add_consignment_access:0);
?>
<style>
.progress.consignment-list-progress {
    height: 20px;
}
.progress.consignment-list-progress .progress-bar {
    font-size: 12px;
}
</style>
<div class="pagetitle">
  <h1><?=$page_header?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=url('admin/dashboard')?>">Home</a></li>
      <li class="breadcrumb-item active"><?=$page_header?></li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
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
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">
            <?php if($add_consignment_access){?>
              <a href="<?=url('admin/' . $controllerRoute . '/add/')?>" class="btn btn-outline-success btn-sm">Add New <?=$module['title']?></a>
            <?php }?>
          </h5>
          <div class="dt-responsive table-responsive">
            
            <ul class="nav nav-tabs nav-tabs-bordered">
              <li class="nav-item">
                <?php if($import_access){?>
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1">Import [<?=count($rows1)?>]</button>
                <?php }?>
              </li>
              <?php if($export_access){?>
                <?php
                if($export_access == 1 && $import_access == 1){
                  $className = '';
                } elseif($export_access == 1 && $import_access == 0){
                  $className = 'show active';
                }
                ?>
                <li class="nav-item">
                  <button class="nav-link <?=$className?>" data-bs-toggle="tab" data-bs-target="#tab2">Export (FCL) [<?=count($rows2)?>]</button>
                </li>
                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab3">Export (LCL) [<?=count($rows3)?>]</button>
                </li>
                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab4">Export (LCL CO LOAD) [<?=count($rows4)?>]</button>
                </li>
              <?php }?>
            </ul>
            <div class="tab-content pt-2">
              <?php if($import_access){?>
                <div class="tab-pane fade show active profile-overview" id="tab1">
                  <table id="simpletable" class="table table-striped table-bordered nowrap">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Consignment No.</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Date Of Booking</th>
                        <th scope="col">Type</th>
                        <th scope="col">POL<br>POD</th>
                        <th scope="col">MBL Number<br>HBL Number</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(count($rows1)>0){ $sl=1; foreach($rows1 as $row){?>
                        <tr>
                          <th scope="row"><?=$sl++?></th>
                          <td>
                            <?=$row->consignment_no?><br>
                            <?php if($row->consignment_status == 'New'){?>
                              <span class="badge bg-primary"><?=$row->consignment_status?></span>
                            <?php } elseif($row->consignment_status == 'Process'){?>
                              <span class="badge bg-warning"><?=$row->consignment_status?></span>
                            <?php } elseif($row->consignment_status == 'Completed'){?>
                              <span class="badge bg-success"><?=$row->consignment_status?></span>
                            <?php }?><br><br>
                            <?php
                            $total_process_flow_count = ProcessFlow::where('shipment_type', '=', 'Import')->where('status', '=', 1)->count();
                            $per_flow = (100 / ($total_process_flow_count + 1));
                            $total_filled_process_flow_count = ConsignmentDetail::where('consignment_id', '=', $row->id)->where('status', '=', 1)->count();
                            if($row->delivery_status){
                              $progress_bar_percentage = 100;
                            } else {
                              $progress_bar_percentage = ($per_flow * $total_filled_process_flow_count);
                            }
                            ?>
                            <div class="progress consignment-list-progress" style="max-width: 100%;">
                              <?php if($progress_bar_percentage >= 0 && $progress_bar_percentage <= 25){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 26 && $progress_bar_percentage <= 50){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 51 && $progress_bar_percentage <= 75){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 76 && $progress_bar_percentage <= 99){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0d6a0d">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage == 100){?>
                                <!-- <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0d6a0d">
                                  
                                </div> -->
                                <div class="progress-bar progress-bar-stripped" style="width: 100%; background-color: #0b520b">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                            </div>
                          </td>
                          <td><?=$row->customer_name?></td>
                          <td><?=date_format(date_create($row->booking_date), "M d, Y")?></td>
                          <td><?=$row->shipment_type?> <?=(($row->type != '')?'('.$row->type.')':'')?></td>
                          <td><?=$row->pol_name?><br><?=$row->pod_name?></td>
                          <td>
                            <?php
                            $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 1)->first();
                            echo (($getConsignmentDetails)?$getConsignmentDetails->input_value:'');
                            ?><br>
                            <?php
                            $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 2)->first();
                            echo (($getConsignmentDetails)?$getConsignmentDetails->input_value:'');
                            ?>
                          </td>
                          <td>
                            <?php if($user_type == 'ma'){?>
                              <a href="<?=url('admin/' . $controllerRoute . '/edit/'.Helper::encoded($row->id))?>" class="btn btn-outline-primary btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-edit"></i></a>
                              <!-- <a href="<?=url('admin/' . $controllerRoute . '/delete/'.Helper::encoded($row->id))?>" class="btn btn-outline-danger btn-sm" title="Delete <?=$module['title']?>" onclick="return confirm('Do You Want To Delete This <?=$module['title']?>');"><i class="fa fa-trash"></i></a> -->
                              <?php if($row->status){?>
                                <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm" title="Activate <?=$module['title']?>"><i class="fa fa-check"></i></a>
                              <?php } else {?>
                                <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-warning btn-sm" title="Deactivate <?=$module['title']?>"><i class="fa fa-times"></i></a>
                              <?php }?>
                            <?php }?>
                            <br><br>
                            <a href="<?=url('admin/' . $controllerRoute . '/process-flow-details/'.Helper::encoded($row->id))?>" class="btn btn-info btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-info-circle"></i> Update Status</a>
                          </td>
                        </tr>
                      <?php } } else {?>
                        <tr>
                          <td colspan="8" style="text-align: center;color: red;">No Records Found !!!</td>
                        </tr>
                      <?php }?>
                    </tbody>
                  </table>
                </div>
              <?php }?>
              <?php if($export_access){?>
                <?php
                if($export_access == 1 && $import_access == 1){
                  $className = '';
                } elseif($export_access == 1 && $import_access == 0){
                  $className = 'show active';
                }
                ?>
                <div class="tab-pane fade <?=$className?> profile-overview" id="tab2">
                  <table id="simpletable" class="table table-striped table-bordered nowrap">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Consignment No.</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Date Of Booking</th>
                        <th scope="col">Type</th>
                        <th scope="col">POL<br>POD</th>
                        <th scope="col">MBL Number<br>HBL Number</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(count($rows2)>0){ $sl=1; foreach($rows2 as $row){?>
                        <tr>
                          <th scope="row"><?=$sl++?></th>
                          <td>
                            <?=$row->consignment_no?><br>
                            <?php if($row->consignment_status == 'New'){?>
                              <span class="badge bg-primary"><?=$row->consignment_status?></span>
                            <?php } elseif($row->consignment_status == 'Process'){?>
                              <span class="badge bg-warning"><?=$row->consignment_status?></span>
                            <?php } elseif($row->consignment_status == 'Completed'){?>
                              <span class="badge bg-success"><?=$row->consignment_status?></span>
                            <?php }?><br><br>
                            <?php
                            $total_process_flow_count = ProcessFlow::where('shipment_type', '=', 'Export')->where('type', '=', 'FCL')->where('status', '=', 1)->count();
                            $per_flow = (100 / ($total_process_flow_count + 1));
                            $total_filled_process_flow_count = ConsignmentDetail::where('consignment_id', '=', $row->id)->where('status', '=', 1)->count();
                            if($row->delivery_status){
                              $progress_bar_percentage = 100;
                            } else {
                              $progress_bar_percentage = ($per_flow * $total_filled_process_flow_count);
                            }
                            ?>
                            <div class="progress consignment-list-progress" style="max-width: 100%;">
                              <?php if($progress_bar_percentage >= 0 && $progress_bar_percentage <= 25){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 26 && $progress_bar_percentage <= 50){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 51 && $progress_bar_percentage <= 75){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 76 && $progress_bar_percentage <= 98){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0d6a0d">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 99 && $progress_bar_percentage <= 100){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0d6a0d">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0b520b">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                            </div>
                          </td>
                          <td><?=$row->customer_name?></td>
                          <td><?=date_format(date_create($row->booking_date), "M d, Y")?></td>
                          <td><?=$row->shipment_type?> <?=(($row->type != '')?'('.$row->type.')':'')?></td>
                          <td><?=$row->pol_name?><br><?=$row->pod_name?></td>
                          <td>
                            <?php
                            if($row->type == 'FCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 20)->first();
                            } elseif($row->type == 'LCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 27)->first();
                            } elseif($row->type == 'LCL CO LOAD'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 34)->first();
                            }
                            echo (($getConsignmentDetails)?$getConsignmentDetails->input_value:'');
                            ?><br>
                            <?php
                            if($row->type == 'FCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 21)->first();
                            } elseif($row->type == 'LCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 28)->first();
                            } elseif($row->type == 'LCL CO LOAD'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 35)->first();
                            }
                            echo (($getConsignmentDetails)?$getConsignmentDetails->input_value:'');
                            ?>
                          </td>
                          <td>
                            <?php if($user_type == 'ma'){?>
                              <a href="<?=url('admin/' . $controllerRoute . '/edit/'.Helper::encoded($row->id))?>" class="btn btn-outline-primary btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-edit"></i></a>
                              <!-- <a href="<?=url('admin/' . $controllerRoute . '/delete/'.Helper::encoded($row->id))?>" class="btn btn-outline-danger btn-sm" title="Delete <?=$module['title']?>" onclick="return confirm('Do You Want To Delete This <?=$module['title']?>');"><i class="fa fa-trash"></i></a> -->
                              <?php if($row->status){?>
                                <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm" title="Activate <?=$module['title']?>"><i class="fa fa-check"></i></a>
                              <?php } else {?>
                                <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-warning btn-sm" title="Deactivate <?=$module['title']?>"><i class="fa fa-times"></i></a>
                              <?php }?>
                            <?php }?>
                            <br><br>
                            <a href="<?=url('admin/' . $controllerRoute . '/process-flow-details/'.Helper::encoded($row->id))?>" class="btn btn-info btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-info-circle"></i> Update Status</a>
                          </td>
                        </tr>
                      <?php } } else {?>
                        <tr>
                          <td colspan="8" style="text-align: center;color: red;">No Records Found !!!</td>
                        </tr>
                      <?php }?>
                    </tbody>
                  </table>
                </div>
                <div class="tab-pane fade profile-overview" id="tab3">
                  <table id="simpletable" class="table table-striped table-bordered nowrap">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Consignment No.</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Date Of Booking</th>
                        <th scope="col">Type</th>
                        <th scope="col">POL<br>POD</th>
                        <th scope="col">MBL Number<br>HBL Number</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(count($rows3)>0){ $sl=1; foreach($rows3 as $row){?>
                        <tr>
                          <th scope="row"><?=$sl++?></th>
                          <td>
                            <?=$row->consignment_no?><br>
                            <?php if($row->consignment_status == 'New'){?>
                              <span class="badge bg-primary"><?=$row->consignment_status?></span>
                            <?php } elseif($row->consignment_status == 'Process'){?>
                              <span class="badge bg-warning"><?=$row->consignment_status?></span>
                            <?php } elseif($row->consignment_status == 'Completed'){?>
                              <span class="badge bg-success"><?=$row->consignment_status?></span>
                            <?php }?><br><br>
                            <?php
                            $total_process_flow_count = ProcessFlow::where('shipment_type', '=', 'Export')->where('type', '=', 'LCL')->where('status', '=', 1)->count();
                            $per_flow = (100 / ($total_process_flow_count + 1));
                            $total_filled_process_flow_count = ConsignmentDetail::where('consignment_id', '=', $row->id)->where('status', '=', 1)->count();
                            if($row->delivery_status){
                              $progress_bar_percentage = 100;
                            } else {
                              $progress_bar_percentage = ($per_flow * $total_filled_process_flow_count);
                            }
                            ?>
                            <div class="progress consignment-list-progress" style="max-width: 100%;">
                              <?php if($progress_bar_percentage >= 0 && $progress_bar_percentage <= 25){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 26 && $progress_bar_percentage <= 50){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 51 && $progress_bar_percentage <= 75){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 76 && $progress_bar_percentage <= 98){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0d6a0d">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 99 && $progress_bar_percentage <= 100){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0d6a0d">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0b520b">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                            </div>
                          </td>
                          <td><?=$row->customer_name?></td>
                          <td><?=date_format(date_create($row->booking_date), "M d, Y")?></td>
                          <td><?=$row->shipment_type?> <?=(($row->type != '')?'('.$row->type.')':'')?></td>
                          <td><?=$row->pol_name?><br><?=$row->pod_name?></td>
                          <td>
                            <?php
                            if($row->type == 'FCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 20)->first();
                            } elseif($row->type == 'LCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 27)->first();
                            } elseif($row->type == 'LCL CO LOAD'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 34)->first();
                            }
                            echo (($getConsignmentDetails)?$getConsignmentDetails->input_value:'');
                            ?><br>
                            <?php
                            if($row->type == 'FCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 21)->first();
                            } elseif($row->type == 'LCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 28)->first();
                            } elseif($row->type == 'LCL CO LOAD'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 35)->first();
                            }
                            echo (($getConsignmentDetails)?$getConsignmentDetails->input_value:'');
                            ?>
                          </td>
                          <td>
                            <?php if($user_type == 'ma'){?>
                              <a href="<?=url('admin/' . $controllerRoute . '/edit/'.Helper::encoded($row->id))?>" class="btn btn-outline-primary btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-edit"></i></a>
                              <!-- <a href="<?=url('admin/' . $controllerRoute . '/delete/'.Helper::encoded($row->id))?>" class="btn btn-outline-danger btn-sm" title="Delete <?=$module['title']?>" onclick="return confirm('Do You Want To Delete This <?=$module['title']?>');"><i class="fa fa-trash"></i></a> -->
                              <?php if($row->status){?>
                                <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm" title="Activate <?=$module['title']?>"><i class="fa fa-check"></i></a>
                              <?php } else {?>
                                <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-warning btn-sm" title="Deactivate <?=$module['title']?>"><i class="fa fa-times"></i></a>
                              <?php }?>
                            <?php }?>
                            <br><br>
                            <a href="<?=url('admin/' . $controllerRoute . '/process-flow-details/'.Helper::encoded($row->id))?>" class="btn btn-info btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-info-circle"></i> Update Status</a>
                          </td>
                        </tr>
                      <?php } } else {?>
                        <tr>
                          <td colspan="8" style="text-align: center;color: red;">No Records Found !!!</td>
                        </tr>
                      <?php }?>
                    </tbody>
                  </table>
                </div>
                <div class="tab-pane fade profile-overview" id="tab4">
                  <table id="simpletable" class="table table-striped table-bordered nowrap">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Consignment No.</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Date Of Booking</th>
                        <th scope="col">Type</th>
                        <th scope="col">POL<br>POD</th>
                        <th scope="col">MBL Number<br>HBL Number</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if(count($rows4)>0){ $sl=1; foreach($rows4 as $row){?>
                        <tr>
                          <th scope="row"><?=$sl++?></th>
                          <td>
                            <?=$row->consignment_no?><br>
                            <?php if($row->consignment_status == 'New'){?>
                              <span class="badge bg-primary"><?=$row->consignment_status?></span>
                            <?php } elseif($row->consignment_status == 'Process'){?>
                              <span class="badge bg-warning"><?=$row->consignment_status?></span>
                            <?php } elseif($row->consignment_status == 'Completed'){?>
                              <span class="badge bg-success"><?=$row->consignment_status?></span>
                            <?php }?><br><br>
                            <?php
                            $total_process_flow_count = ProcessFlow::where('shipment_type', '=', 'Export')->where('type', '=', 'LCL CO LOAD')->where('status', '=', 1)->count();
                            $per_flow = (100 / ($total_process_flow_count + 1));
                            $total_filled_process_flow_count = ConsignmentDetail::where('consignment_id', '=', $row->id)->where('status', '=', 1)->count();
                            if($row->delivery_status){
                              $progress_bar_percentage = 100;
                            } else {
                              $progress_bar_percentage = ($per_flow * $total_filled_process_flow_count);
                            }
                            ?>
                            <div class="progress consignment-list-progress" style="max-width: 100%;">
                              <?php if($progress_bar_percentage >= 0 && $progress_bar_percentage <= 25){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 26 && $progress_bar_percentage <= 50){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 51 && $progress_bar_percentage <= 75){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 76 && $progress_bar_percentage <= 98){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0d6a0d">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                              <?php if($progress_bar_percentage >= 99 && $progress_bar_percentage <= 100){?>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #a0e5a0">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 80%; background-color: #5ecf5e">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #329d32">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0d6a0d">
                                  
                                </div>
                                <div class="progress-bar progress-bar-stripped" style="width: 70%; background-color: #0b520b">
                                  <?=round($progress_bar_percentage).'%';?>
                                </div>
                              <?php }?>
                            </div>
                          </td>
                          <td><?=$row->customer_name?></td>
                          <td><?=date_format(date_create($row->booking_date), "M d, Y")?></td>
                          <td><?=$row->shipment_type?> <?=(($row->type != '')?'('.$row->type.')':'')?></td>
                          <td><?=$row->pol_name?><br><?=$row->pod_name?></td>
                          <td>
                            <?php
                            if($row->type == 'FCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 20)->first();
                            } elseif($row->type == 'LCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 27)->first();
                            } elseif($row->type == 'LCL CO LOAD'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 34)->first();
                            }
                            echo (($getConsignmentDetails)?$getConsignmentDetails->input_value:'');
                            ?><br>
                            <?php
                            if($row->type == 'FCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 21)->first();
                            } elseif($row->type == 'LCL'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 28)->first();
                            } elseif($row->type == 'LCL CO LOAD'){
                              $getConsignmentDetails = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 35)->first();
                            }
                            echo (($getConsignmentDetails)?$getConsignmentDetails->input_value:'');
                            ?>
                          </td>
                          <td>
                            <?php if($user_type == 'ma'){?>
                              <a href="<?=url('admin/' . $controllerRoute . '/edit/'.Helper::encoded($row->id))?>" class="btn btn-outline-primary btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-edit"></i></a>
                              <!-- <a href="<?=url('admin/' . $controllerRoute . '/delete/'.Helper::encoded($row->id))?>" class="btn btn-outline-danger btn-sm" title="Delete <?=$module['title']?>" onclick="return confirm('Do You Want To Delete This <?=$module['title']?>');"><i class="fa fa-trash"></i></a> -->
                              <?php if($row->status){?>
                                <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm" title="Activate <?=$module['title']?>"><i class="fa fa-check"></i></a>
                              <?php } else {?>
                                <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-warning btn-sm" title="Deactivate <?=$module['title']?>"><i class="fa fa-times"></i></a>
                              <?php }?>
                            <?php }?>
                            <br><br>
                            <a href="<?=url('admin/' . $controllerRoute . '/process-flow-details/'.Helper::encoded($row->id))?>" class="btn btn-info btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-info-circle"></i> Update Status</a>
                          </td>
                        </tr>
                      <?php } } else {?>
                        <tr>
                          <td colspan="8" style="text-align: center;color: red;">No Records Found !!!</td>
                        </tr>
                      <?php }?>
                    </tbody>
                  </table>
                </div>
              <?php }?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>