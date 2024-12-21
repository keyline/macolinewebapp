<?php
use App\Models\ProcessFlow;
use App\Models\ConsignmentDetail;
use App\Models\Admin;
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
$user_type = session('type');
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
          <div class="row">
            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Consignment No.</h5>
                <span><?=(($row)?$row->consignment_no:'')?></span>
                <?php if($row->consignment_status == 'New'){?>
                  <span class="badge bg-primary"><?=$row->consignment_status?></span>
                <?php } elseif($row->consignment_status == 'Process'){?>
                  <span class="badge bg-warning"><?=$row->consignment_status?></span>
                <?php } elseif($row->consignment_status == 'Completed'){?>
                  <span class="badge bg-success"><?=$row->consignment_status?></span>
                <?php }?>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Customer Name</h5>
                <span><?=(($row)?$row->customer_name:'')?></span>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Date Of Booking</h5>
                <span><?=(($row)?date_format(date_create($row->booking_date), "M d, Y"):'')?></span>
              </div>
            </div>

            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Type</h5>
                <span><?=(($row)?$row->shipment_type . ''. (($row->type != '')?'('.$row->type.')':''):'')?></span>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Port Of Loading</h5>
                <span><?=(($row)?$row->pol_name:'')?></span>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Port Of Dispatch</h5>
                <span><?=(($row)?$row->pod_name:'')?></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 mt-3">
              <form method="POST" action="">
                @csrf
                <input type="hidden" name="consignment_id" value="<?=(($row)?$row->id:'')?>">
                <table class="table table-bordered nowrap">
                  <thead>
                    <tr>
                      <th scope="col" style="font-weight: bold; text-align:center;">Process Flow Name</th>
                      <th scope="col" style="font-weight: bold; text-align:center;">ETA/<br>Updated On</th>
                      <th scope="col" style="font-weight: bold; text-align:center;">Updated By</th>
                      <th scope="col" style="font-weight: bold; text-align:center;">Input Value</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if($consignmentDetails){ foreach($consignmentDetails as $consignmentDetail){?>
                      <?php
                      $getProcessFlow = ProcessFlow::select('id', 'name', 'form_element_type', 'options', 'is_dependent', 'type')->where('id', '=', $consignmentDetail->process_flow_id)->first();
                      ?>
                      <?php if($getProcessFlow->is_dependent == 0){?>
                        <tr <?=(($consignmentDetail->input_value != '')?'style="background-color: #90ee904a;"':'')?>>
                          <td>
                            <b><?=(($getProcessFlow)?$getProcessFlow->name:'')?></b>
                            <input type="hidden" name="process_flow_id[]" value="<?=$consignmentDetail->process_flow_id?>">
                            <input type="hidden" name="updated_on[]" value="<?=$consignmentDetail->updated_on?>">
                          </td>
                          <!-- <td><?=date_format(date_create($consignmentDetail->booking_date), "M d, Y")?></td> -->
                          <td><?=date_format(date_create($consignmentDetail->notification_date), "M d, Y")?><br><?=(($consignmentDetail->updated_on != '')?date_format(date_create($consignmentDetail->updated_on), "M d, Y h:i A"):'')?></td>
                          <td>
                            <?php
                            $getUser = Admin::select('name')->where('id', '=', $consignmentDetail->updated_by)->first();
                            echo (($getUser)?$getUser->name:'');
                            ?>
                          </td>
                          <td>
                            <?php if($getProcessFlow){?>
                              <?php if($getProcessFlow->form_element_type == 'textbox'){?>
                                <input type="text" class="form-control" name="input_value[<?=$consignmentDetail->process_flow_id?>]" value="<?=$consignmentDetail->input_value?>" placeholder="Enter <?=$getProcessFlow->name?>">
                              <?php }?>
                              <?php if($getProcessFlow->form_element_type == 'select'){?>
                                <?php
                                $options = explode(',', $getProcessFlow->options);
                                ?>
                                <select class="form-control" name="input_value[<?=$consignmentDetail->process_flow_id?>]">
                                  <option value="" selected>Select</option>
                                  <?php if(!empty($options)){ for($s=0;$s<count($options);$s++){?>
                                    <option value="<?=$options[$s]?>" <?=(($options[$s] == $consignmentDetail->input_value)?'selected':'')?>><?=$options[$s]?></option>
                                  <?php } }?>
                                </select>
                              <?php }?>
                              <?php if($getProcessFlow->form_element_type == 'checkbox'){?>
                                <input type="checkbox" name="input_value[<?=$consignmentDetail->process_flow_id?>]" value="<?=$getProcessFlow->name?>" <?=(($consignmentDetail->input_value == $getProcessFlow->name)?'checked':'')?>> <?=$getProcessFlow->name?>
                              <?php }?>
                              <?php if($getProcessFlow->form_element_type == 'radio'){?>
                                <?php
                                $options = explode(',', $getProcessFlow->options);
                                ?>
                                <?php if(!empty($options)){ for($m=0;$m<count($options);$m++){?>
                                  <input type="radio" name="input_value[<?=$consignmentDetail->process_flow_id?>]" value="<?=$options[$m]?>" <?=(($consignmentDetail->input_value == $options[$m])?'checked':'')?>> <?=$options[$m]?>
                                <?php } }?>
                                <?php if($getProcessFlow->id == 21 && $consignmentDetail->input_value == 'Applicable'){?>
                                  <input type="text" class="form-control" name="hbl_number" value="<?=$consignmentDetail->hbl_number?>" placeholder="Enter HBL Number">
                                <?php }?>
                                <?php if($getProcessFlow->id == 28 && $consignmentDetail->input_value == 'Applicable'){?>
                                  <input type="text" class="form-control" name="hbl_number" value="<?=$consignmentDetail->hbl_number?>" placeholder="Enter HBL Number">
                                <?php }?>
                                <?php if($getProcessFlow->id == 35 && $consignmentDetail->input_value == 'Applicable'){?>
                                  <input type="text" class="form-control" name="hbl_number" value="<?=$consignmentDetail->hbl_number?>" placeholder="Enter HBL Number">
                                <?php }?>
                              <?php }?>
                              <?php if($getProcessFlow->form_element_type == 'datebox'){?>
                                <input type="date" class="form-control" name="input_value[<?=$consignmentDetail->process_flow_id?>]" value="<?=$consignmentDetail->input_value?>">
                              <?php }?>
                            <?php }?>
                          </td>
                        </tr>
                      <?php } else {?>
                        <?php
                        $pre_alert_field = 0;
                        if($getProcessFlow->type == 'FCL'){?>
                          <?php
                          $consignmentDetails40   = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 40)->first();
                          $consignmentDetails21   = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 21)->first();
                          if(($consignmentDetails40->input_value != '') && ($consignmentDetails21->input_value != '')){
                            $pre_alert_field = 1;
                          }?>
                        <?php }?>
                        <?php if($getProcessFlow->type == 'LCL'){?>
                          <?php
                          $consignmentDetails27   = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 27)->first();
                          $consignmentDetails28   = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 28)->first();
                          if(($consignmentDetails27->input_value != '') && ($consignmentDetails28->input_value != '')){
                            $pre_alert_field = 1;
                          }?>
                        <?php }?>
                        <?php if($getProcessFlow->type == 'LCL CO LOAD'){?>
                          <?php
                          $consignmentDetails34   = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 34)->first();
                          $consignmentDetails35   = ConsignmentDetail::select('input_value')->where('consignment_id', '=', $row->id)->where('process_flow_id', '=', 35)->first();
                          if(($consignmentDetails34->input_value != '') && ($consignmentDetails35->input_value != '')){
                            $pre_alert_field = 1;
                          }?>
                        <?php }?>
                        <?php if($pre_alert_field){?>
                          <tr <?=(($consignmentDetail->input_value != '')?'style="background-color: #90ee904a;"':'')?>>
                            <td>
                              <b><?=(($getProcessFlow)?$getProcessFlow->name:'')?></b>
                              <input type="hidden" name="process_flow_id[]" value="<?=$consignmentDetail->process_flow_id?>">
                            </td>
                            <!-- <td><?=date_format(date_create($consignmentDetail->notification_date), "M d, Y")?></td> -->
                            <td><?=date_format(date_create($consignmentDetail->notification_date), "M d, Y")?><br><?=(($consignmentDetail->updated_on != '')?date_format(date_create($consignmentDetail->updated_on), "M d, Y h:i A"):'')?></td>
                            <td>
                              <?php
                              $getUser = Admin::select('name')->where('id', '=', $consignmentDetail->updated_by)->first();
                              echo (($getUser)?$getUser->name:'');
                              ?>
                            </td>
                            <td>
                              <input type="checkbox" name="input_value[<?=$consignmentDetail->process_flow_id?>]" value="<?=$getProcessFlow->name?>" <?=(($consignmentDetail->input_value == $getProcessFlow->name)?'checked':'')?>> <?=$getProcessFlow->name?>
                            </td>
                          </tr>
                        <?php }?>
                      <?php }?>
                    <?php } }?>
                    <?php if($consignmentNotFilled <= 0){?>
                      <tr>
                        <td colspan="2">Delivery Status</td>
                        <td>
                          <?php if(!$row->delivery_status){?>
                            <input type="checkbox" name="delivery_status" id="delivery_status"> <label for="delivery_status">Delivery Status</label>
                            <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-paper-plane"></i> Update</button>
                          <?php } else {?>
                            <span class="badge bg-success">Consignment Completed</span>
                          <?php }?>
                        </td>
                      </tr>
                    <?php }?>
                  </tbody>
                  <?php if($consignmentNotFilled > 0){?>
                    <tfoot>
                      <tr>
                        <th colspan="4" style="text-align:center;">
                          <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-paper-plane"></i> Submit</button>
                        </th>
                      </tr>
                    </tfoot>
                  <?php }?>
                </table>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>