<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\OpenAiAuth;
use Illuminate\Http\Request;
use PHPExperts\RESTSpeaker\RESTSpeaker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Models\GeneralSetting;
use App\Models\Consignment;
use App\Models\ConsignmentDetail;
use App\Models\Customer;
use App\Models\Pol;
use App\Models\Pod;
use App\Models\ProcessFlow;
use App\Models\EmailLog;

use Auth;
use Session;
use Helper;
use Hash;
use stripe;

class FrontController extends Controller
{
    public function home(){
        return redirect(url('/admin'));
    }
    public function cron_for_notification(){
        $current_date       = date('Y-m-d');
        $notifications      = DB::table('consignment_details')
                                                ->join('consignments', 'consignment_details.consignment_id', '=', 'consignments.id')
                                                ->join('customers', 'consignments.customer_id', '=', 'customers.id')
                                                ->join('pols', 'consignments.pol', '=', 'pols.id')
                                                ->join('pods', 'consignments.pod', '=', 'pods.id')
                                                ->join('process_flows', 'consignment_details.process_flow_id', '=', 'process_flows.id')
                                                ->select('consignments.*', 'customers.name as customer_name', 'pols.name as pol_name', 'pods.name as pod_name', 'process_flows.name as process_flow_name')
                                                ->where('consignment_details.status', '=', 1)
                                                ->where('consignment_details.input_value', '=', '')
                                                ->where('consignment_details.notification_date', '=', $current_date)
                                                ->orderBy('consignments.id', 'DESC')
                                                ->get();
        // Helper::pr($notifications);
        /* email sent */
            $generalSetting             = GeneralSetting::find('1');
            $mail_header                = 'Process Flow Notification On '.date('M d, Y');
            $maildata                   = [
                'generalSetting'    => $generalSetting,
                'mail_header'       => $mail_header,
                'notifications'     => $notifications,
            ];
            $message                     = view('email-templates.notification-template',$maildata);
            // echo $message;die;
            $subject                     = $generalSetting->site_name.' '.$mail_header;
            $this->sendMail($generalSetting->system_email, $subject, $message);
        /* email sent */
        /* email log save */
            $postData2 = [
                'name'                  => $generalSetting->site_name,
                'email'                 => $generalSetting->system_email,
                'subject'               => $subject,
                'message'               => $message
            ];
            EmailLog::insertGetId($postData2);
        /* email log save */
        echo "Notification Send !!!";
    }
}
