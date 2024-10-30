<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Consignment;
use App\Models\Customer;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
class ConsignmentController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Consignment',
            'controller'        => 'ConsignmentController',
            'controller_route'  => 'consignment',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'consignment.list';
            $data['rows']                   = DB::table('consignments')
                                                ->join('customers', 'consignments.customer_id', '=', 'customers.id')
                                                ->select('consignments.*', 'customers.name as customer_name')
                                                ->where('consignments.status', '!=', 3)
                                                ->orderBy('consignments.id', 'DESC')
                                                ->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $data['module']           = $this->data;
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'shipment_type'             => 'required',
                    'customer_id'               => 'required',
                    'pol'                       => 'required',
                    'pod'                       => 'required',
                    'booking_date'              => 'required',
                ];
                if($this->validate($request, $rules)){
                    /* generate consignment no */
                        if($postData['shipment_type'] == 'Import'){
                            $shipmentTypeShort = 'IMP';
                        } else {
                            $shipmentTypeShort = 'EXP';
                        }
                        $getLastEnquiry = Consignment::orderBy('id', 'DESC')->first();
                        if($getLastEnquiry){
                            $sl_no              = $getLastEnquiry->sl_no;
                            $next_sl_no         = $sl_no + 1;
                            $next_sl_no_string  = str_pad($next_sl_no, 7, 0, STR_PAD_LEFT);
                            $consignment_no     = 'MACOLINE-'.$shipmentTypeShort.'-'.$next_sl_no_string;
                        } else {
                            $next_sl_no         = 1;
                            $next_sl_no_string  = str_pad($next_sl_no, 7, 0, STR_PAD_LEFT);
                            $consignment_no     = 'MACOLINE-'.$shipmentTypeShort.'-'.$next_sl_no_string;
                        }
                    /* generate consignment no */
                    $fields = [
                        'sl_no'                             => $next_sl_no,
                        'consignment_no'                    => $consignment_no,
                        'shipment_type'                     => $postData['shipment_type'],
                        'type'                              => ((array_key_exists("type",$postData))?$postData['type']:''),
                        'customer_id'                       => $postData['customer_id'],
                        'pol'                               => $postData['pol'],
                        'pod'                               => $postData['pod'],
                        'booking_date'                      => date_format(date_create($postData['booking_date']), "Y-m-d"),
                    ];
                    // Helper::pr($fields);
                    Consignment::insert($fields);
                    return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Inserted Successfully !!!');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'consignment.add-edit';
            $data['row']                    = [];
            $data['customers']              = Customer::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'consignment.add-edit';
            $data['row']                    = Consignment::where($this->data['primary_key'], '=', $id)->first();
            $data['customers']              = Customer::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'shipment_type'             => 'required',
                    'customer_id'               => 'required',
                    'pol'                       => 'required',
                    'pod'                       => 'required',
                    'booking_date'              => 'required',
                ];
                if($this->validate($request, $rules)){
                    $fields = [
                        'shipment_type'                     => $postData['shipment_type'],
                        'type'                              => ((array_key_exists("type",$postData))?$postData['type']:''),
                        'customer_id'                       => $postData['customer_id'],
                        'pol'                               => $postData['pol'],
                        'pod'                               => $postData['pod'],
                        'booking_date'                      => date_format(date_create($postData['booking_date']), "Y-m-d"),
                    ];
                    // Helper::pr($fields);
                    Consignment::where($this->data['primary_key'], '=', $id)->update($fields);
                    return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Updated Successfully !!!');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* edit */
    /* delete */
        public function delete(Request $request, $id){
            $id                             = Helper::decoded($id);
            $fields = [
                'status'             => 3
            ];
            Consignment::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = Consignment::find($id);
            if ($model->status == 1)
            {
                $model->status  = 0;
                $msg            = 'Deactivated';
            } else {
                $model->status  = 1;
                $msg            = 'Activated';
            }            
            $model->save();
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' '.$msg.' Successfully !!!');
        }
    /* change status */
}
