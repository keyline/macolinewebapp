<?php
use App\Models\GeneralSetting;
$generalSetting             = GeneralSetting::find('1');
?>
<!doctype html>
<html lang="en">
  <head>
    <title><?=$generalSetting->site_name?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  </head>
  <body style="padding: 0; margin: 0; box-sizing: border-box;">
    <section style="padding: 80px 0; height: 80vh; margin: 0 15px;">
        <div style="max-width: 800px; background: #ffffff; margin: 0 auto; border-radius: 15px; padding: 20px 15px; box-shadow: 0 0 30px -5px #ccc;">
          <div style="text-align: center;">
              <img src="<?=env('UPLOADS_URL').$generalSetting->site_logo?>" alt="<?=$generalSetting->site_name?>" style=" width: 100%; max-width: 250px;">
          </div>
          <div>
            <h3 style="text-align: center; font-size: 25px; color: #5c5b5b; font-family: sans-serif;"><?=$mail_header?></h3>
            <table style="width: 100%;  border-spacing: 2px;">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Consignment No.</th>
                  <th>Customer</th>
                  <th>Type</th>
                  <th>POL<br>POD</th>
                  <th>Booking Date</th>
                  <th>Process Flow</th>
                </tr>
              </thead>
              <tbody>
                <?php if($notifications){ $sl=1; foreach($notifications as $notification){?>
                  <tr>
                    <td style="padding: 10px; background: #cccccc42; text-align: left; color: #000;font-family: sans-serif;font-size: 15px;"><?=$sl++?></td>
                    <td style="padding: 10px; background: #cccccc42; text-align: left; color: #000;font-family: sans-serif;font-size: 15px;">
                      <?=$notification->consignment_no?>
                    </td>
                    <td style="padding: 10px; background: #cccccc42; text-align: left; color: #000;font-family: sans-serif;font-size: 15px;">
                      <?=$notification->customer_name?>
                    </td>
                    <td style="padding: 10px; background: #cccccc42; text-align: left; color: #000;font-family: sans-serif;font-size: 15px;">
                      <?=$notification->shipment_type?> <?=(($notification->type != '')?'('.$notification->type.')':'')?>
                    </td>
                    <td style="padding: 10px; background: #cccccc42; text-align: left; color: #000;font-family: sans-serif;font-size: 15px;">
                      <?=$notification->pol_name?><br><?=$notification->pod_name?>
                    </td>
                    <td style="padding: 10px; background: #cccccc42; text-align: left; color: #000;font-family: sans-serif;font-size: 15px;">
                      <?=date_format(date_create($notification->booking_date), "d-m-Y")?>
                    </td>
                    <td style="padding: 10px; background: #cccccc42; text-align: left; color: #000;font-family: sans-serif;font-size: 15px;">
                      <?=$notification->process_flow_name?>
                    </td>
                  </tr>
                <?php } }?>
              </tbody>
            </table>
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