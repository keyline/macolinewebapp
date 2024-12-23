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
use App\Mail\PHPMailer\PHPMailerService;

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
        $mailer = new PHPMailerService();
        $current_date       = date('Y-m-d');
        /* cron for admin */
            $notifications      = DB::table('consignments')
                                                    ->join('customers', 'consignments.customer_id', '=', 'customers.id')
                                                    ->join('pols', 'consignments.pol', '=', 'pols.id')
                                                    ->join('pods', 'consignments.pod', '=', 'pods.id')
                                                    ->select('consignments.*', 'customers.name as customer_name', 'pols.name as pol_name', 'pods.name as pod_name')
                                                    ->where('consignments.status', '=', 1)
                                                    ->orderBy('consignments.id', 'DESC')
                                                    ->get();
            // Helper::pr($notifications);
            /* email sent */
                $generalSetting             = GeneralSetting::find('1');
                $mail_header                = 'Process Flow Notification On '.date('M d, Y') . 'For Admin';
                $maildata                   = [
                    'generalSetting'    => $generalSetting,
                    'mail_header'       => $mail_header,
                    'notifications'     => $notifications,
                ];
                $message                     = view('email-templates.notification-template',$maildata);
                // echo $message;die;
                $subject                     = $generalSetting->site_name.' '.$mail_header;
                $mailer->sendMail($generalSetting->system_email, $subject, $message);
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
            // echo "Notification Send !!!";
        /* cron for admin */
        /* cron for import */
            $getImportUsers     = DB::table('admins')
                                                    ->join('roles', 'admins.role_id', '=', 'roles.id')
                                                    ->select('admins.email as admin_email', 'admins.name as admin_name')
                                                    ->where('admins.status', '=', 1)
                                                    ->where('admins.type', '=', 's')
                                                    ->where('admins.is_import_email', '=', 1)
                                                    ->orderBy('admins.id', 'ASC')
                                                    ->get();
            $notifications      = DB::table('consignments')
                                                    ->join('customers', 'consignments.customer_id', '=', 'customers.id')
                                                    ->join('pols', 'consignments.pol', '=', 'pols.id')
                                                    ->join('pods', 'consignments.pod', '=', 'pods.id')
                                                    ->select('consignments.*', 'customers.name as customer_name', 'pols.name as pol_name', 'pods.name as pod_name')
                                                    ->where('consignments.status', '=', 1)
                                                    ->where('consignments.shipment_type', '=', 'Import')
                                                    ->orderBy('consignments.id', 'DESC')
                                                    ->get();
            // Helper::pr($notifications);
            /* email sent */
                $generalSetting             = GeneralSetting::find('1');
                $mail_header                = 'Process Flow Notification On '.date('M d, Y') . 'For Import';
                $maildata                   = [
                    'generalSetting'    => $generalSetting,
                    'mail_header'       => $mail_header,
                    'notifications'     => $notifications,
                ];
                $message                     = view('email-templates.notification-template',$maildata);
                // echo $message;die;
                $subject                     = $generalSetting->site_name.' '.$mail_header;
                if($getImportUsers){
                    foreach($getImportUsers as $getImportUser){
                        $mailer->sendMail($getImportUser->admin_email, $subject, $message);
                        /* email log save */
                            $postData2 = [
                                'name'                  => $getImportUser->admin_name,
                                'email'                 => $getImportUser->admin_email,
                                'subject'               => $subject,
                                'message'               => $message
                            ];
                            EmailLog::insertGetId($postData2);
                        /* email log save */
                    }
                }
                
            /* email sent */
        /* cron for import */
        /* cron for export fcl */
            $getExportUsers     = DB::table('admins')
                                                    ->join('roles', 'admins.role_id', '=', 'roles.id')
                                                    ->select('admins.email as admin_email', 'admins.name as admin_name')
                                                    ->where('admins.status', '=', 1)
                                                    ->where('admins.type', '=', 's')
                                                    ->where('admins.is_fcl_export_email', '=', 1)
                                                    ->orderBy('admins.id', 'ASC')
                                                    ->get();
            $notifications      = DB::table('consignments')
                                                    ->join('customers', 'consignments.customer_id', '=', 'customers.id')
                                                    ->join('pols', 'consignments.pol', '=', 'pols.id')
                                                    ->join('pods', 'consignments.pod', '=', 'pods.id')
                                                    ->select('consignments.*', 'customers.name as customer_name', 'pols.name as pol_name', 'pods.name as pod_name')
                                                    ->where('consignments.status', '=', 1)
                                                    ->where('consignments.shipment_type', '=', 'Export')
                                                    ->where('consignments.type', '=', 'FCL')
                                                    ->orderBy('consignments.id', 'DESC')
                                                    ->get();
            // Helper::pr($notifications);
            /* email sent */
                $generalSetting             = GeneralSetting::find('1');
                $mail_header                = 'Process Flow Notification On '.date('M d, Y') . 'For Export FCL';
                $maildata                   = [
                    'generalSetting'    => $generalSetting,
                    'mail_header'       => $mail_header,
                    'notifications'     => $notifications,
                ];
                $message                     = view('email-templates.notification-template',$maildata);
                // echo $message;die;
                $subject                     = $generalSetting->site_name.' '.$mail_header;
                if($getExportUsers){
                    foreach($getExportUsers as $getExportUser){
                        $mailer->sendMail($getExportUser->admin_email, $subject, $message);
                        /* email log save */
                            $postData2 = [
                                'name'                  => $getExportUser->admin_name,
                                'email'                 => $getExportUser->admin_email,
                                'subject'               => $subject,
                                'message'               => $message
                            ];
                            EmailLog::insertGetId($postData2);
                        /* email log save */
                    }
                }
                
            /* email sent */
            echo "Notification Send !!!";
        /* cron for export fcl */
        /* cron for export lcl */
            $getExportUsers     = DB::table('admins')
                                                    ->join('roles', 'admins.role_id', '=', 'roles.id')
                                                    ->select('admins.email as admin_email', 'admins.name as admin_name')
                                                    ->where('admins.status', '=', 1)
                                                    ->where('admins.type', '=', 's')
                                                    ->where('admins.is_lcl_export_email', '=', 1)
                                                    ->orderBy('admins.id', 'ASC')
                                                    ->get();
            $notifications      = DB::table('consignments')
                                                    ->join('customers', 'consignments.customer_id', '=', 'customers.id')
                                                    ->join('pols', 'consignments.pol', '=', 'pols.id')
                                                    ->join('pods', 'consignments.pod', '=', 'pods.id')
                                                    ->select('consignments.*', 'customers.name as customer_name', 'pols.name as pol_name', 'pods.name as pod_name')
                                                    ->where('consignments.status', '=', 1)
                                                    ->where('consignments.shipment_type', '=', 'Export')
                                                    ->where('consignments.type', '=', 'LCL')
                                                    ->orderBy('consignments.id', 'DESC')
                                                    ->get();
            // Helper::pr($notifications);
            /* email sent */
                $generalSetting             = GeneralSetting::find('1');
                $mail_header                = 'Process Flow Notification On '.date('M d, Y') . 'For Export LCL';
                $maildata                   = [
                    'generalSetting'    => $generalSetting,
                    'mail_header'       => $mail_header,
                    'notifications'     => $notifications,
                ];
                $message                     = view('email-templates.notification-template',$maildata);
                // echo $message;die;
                $subject                     = $generalSetting->site_name.' '.$mail_header;
                if($getExportUsers){
                    foreach($getExportUsers as $getExportUser){
                        $mailer->sendMail($getExportUser->admin_email, $subject, $message);
                        /* email log save */
                            $postData2 = [
                                'name'                  => $getExportUser->admin_name,
                                'email'                 => $getExportUser->admin_email,
                                'subject'               => $subject,
                                'message'               => $message
                            ];
                            EmailLog::insertGetId($postData2);
                        /* email log save */
                    }
                }
                
            /* email sent */
            echo "Notification Send !!!";
        /* cron for export lcl */
        /* cron for export lcl co load */
            $getExportUsers     = DB::table('admins')
                                                    ->join('roles', 'admins.role_id', '=', 'roles.id')
                                                    ->select('admins.email as admin_email', 'admins.name as admin_name')
                                                    ->where('admins.status', '=', 1)
                                                    ->where('admins.type', '=', 's')
                                                    ->where('admins.is_lcl_co_load_export_email', '=', 1)
                                                    ->orderBy('admins.id', 'ASC')
                                                    ->get();
            $notifications      = DB::table('consignments')
                                                    ->join('customers', 'consignments.customer_id', '=', 'customers.id')
                                                    ->join('pols', 'consignments.pol', '=', 'pols.id')
                                                    ->join('pods', 'consignments.pod', '=', 'pods.id')
                                                    ->select('consignments.*', 'customers.name as customer_name', 'pols.name as pol_name', 'pods.name as pod_name')
                                                    ->where('consignments.status', '=', 1)
                                                    ->where('consignments.shipment_type', '=', 'Export')
                                                    ->where('consignments.type', '=', 'LCL CO LOAD')
                                                    ->orderBy('consignments.id', 'DESC')
                                                    ->get();
            // Helper::pr($notifications);
            /* email sent */
                $generalSetting             = GeneralSetting::find('1');
                $mail_header                = 'Process Flow Notification On '.date('M d, Y') . 'For Export LCL CO LOAD';
                $maildata                   = [
                    'generalSetting'    => $generalSetting,
                    'mail_header'       => $mail_header,
                    'notifications'     => $notifications,
                ];
                $message                     = view('email-templates.notification-template',$maildata);
                // echo $message;die;
                $subject                     = $generalSetting->site_name.' '.$mail_header;
                if($getExportUsers){
                    foreach($getExportUsers as $getExportUser){
                        $mailer->sendMail($getExportUser->admin_email, $subject, $message);
                        /* email log save */
                            $postData2 = [
                                'name'                  => $getExportUser->admin_name,
                                'email'                 => $getExportUser->admin_email,
                                'subject'               => $subject,
                                'message'               => $message
                            ];
                            EmailLog::insertGetId($postData2);
                        /* email log save */
                    }
                }
                
            /* email sent */
            echo "Notification Send !!!";
        /* cron for export lcl co load */
    }
}
