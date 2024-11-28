<?php
use Illuminate\Support\Facades\DB;
use App\Models\Consignment;
use App\Models\ConsignmentDetail;
use App\Models\ProcessFlow;
use App\Models\GeneralSetting;
$generalSetting             = GeneralSetting::find('1');
?>
<!doctype html>
<html lang="en">
  <head>
    <title><?=$generalSetting->site_name?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style type="text/css">
      .card{
        max-width: 800px;
        background: #ffffff;
        margin: 0 auto;
        border-radius: 15px;
        padding: 20px 15px;
        box-shadow: 0 0 30px -5px #ccc;
        margin-bottom: 10px;
      }
      .text-danger{
        color: red;
      }
    </style>
  </head>
  <body style="padding: 0; margin: 0; box-sizing: border-box;">
    <section style="padding: 80px 0; height: 80vh; margin: 0 15px;">
        <div style="max-width: 800px; background: #ffffff; margin: 0 auto; border-radius: 15px; padding: 20px 15px; box-shadow: 0 0 30px -5px #ccc;">
          <div style="text-align: center;">
              <img src="<?=env('UPLOADS_URL').$generalSetting->site_logo?>" alt="<?=$generalSetting->site_name?>" style=" width: 100%; max-width: 250px;">
          </div>
          <div>
            <h3 style="text-align: center; font-size: 25px; color: #5c5b5b; font-family: sans-serif;"><?=$mail_header?></h3>
            <?php if($notifications){ $sl=1; foreach($notifications as $notification){?>
              <div class="card">
                <div class="card-header"><h4><?=$notification->consignment_no?></h4></div>
                <div class="card-body">
                  <span style="font-size: 14px;"><b>Customer :</b> <?=$notification->customer_name?></span>&nbsp;|&nbsp;
                  <span style="font-size: 14px;"><b>Job Type :</b> <?=$notification->shipment_type?> <?=(($notification->type != '')?'('.$notification->type.')':'')?></span>&nbsp;|&nbsp;
                  <span style="font-size: 14px;"><b>Port of Loading :</b> <?=$notification->pol_name?></span>&nbsp;|&nbsp;
                  <span style="font-size: 14px;"><b>Port of Discharge :</b> <?=$notification->pod_name?></span>&nbsp;|&nbsp;
                  <?php
                  $filledProcessFlows         = DB::table('consignment_details')
                                                    ->join('process_flows', 'consignment_details.process_flow_id', '=', 'process_flows.id')
                                                    ->select('consignment_details.*', 'process_flows.name as process_flow_name')
                                                    ->where('consignment_details.status', '=', 1)
                                                    ->where('consignment_details.consignment_id', '=', $notification->id)
                                                    ->orderBy('process_flows.id', 'ASC')
                                                    ->get();
                  $notFilledProcessFlows      = DB::table('consignment_details')
                                                    ->join('process_flows', 'consignment_details.process_flow_id', '=', 'process_flows.id')
                                                    ->select('consignment_details.*', 'process_flows.name as process_flow_name')
                                                    ->where('consignment_details.consignment_id', '=', $notification->id)
                                                    ->where('consignment_details.status', '=', 0)
                                                    ->orderBy('process_flows.id', 'ASC')
                                                    ->get();
                  ?>
                  <ul>
                    <?php if($filledProcessFlows){ foreach($filledProcessFlows as $filledProcessFlow){?>
                      <li class="text-success"><?=$filledProcessFlow->process_flow_name?> : <?=$filledProcessFlow->input_value?></li>
                    <?php } }?>
                    <?php if($notFilledProcessFlows){ foreach($notFilledProcessFlows as $notFilledProcessFlow){?>
                      <li class="text-danger"><?=$notFilledProcessFlow->process_flow_name?> : <?=$notFilledProcessFlow->input_value?></li>
                    <?php } }?>
                  </ul>
                </div>
              </div>
            <?php } }?>
          </div>
        </div>
        <div style="border-top: 2px solid #ccc; margin-top: 50px; text-align: center; font-family: sans-serif;">
          <div style="text-align: center; margin: 15px 0 10px;"><?=$generalSetting->site_name?></div>
          <div style="text-align: center; margin: 15px 0 10px;">Phone: <?=$generalSetting->site_phone?></div>
          <div style="text-align: center; margin: 15px 0 10px;">Email: <?=$generalSetting->site_mail?></div>
        </div>
      </div>
    </section>
  </body>
</html>