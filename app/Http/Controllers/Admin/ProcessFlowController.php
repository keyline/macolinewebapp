<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\ProcessFlow;

use Auth;
use Session;
use Helper;
use Hash;
class ProcessFlowController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Process Flow',
            'controller'        => 'ProcessFlowController',
            'controller_route'  => 'process-flow',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'process-flow.list';
            $data['rows']                   = ProcessFlow::where('status', '!=', 3)->orderBy('id', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $data['module']           = $this->data;
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'name'                      => 'required',
                    'shipment_type'             => 'required',
                    'form_element_type'         => 'required',
                    'is_notification'           => 'required',
                ];
                if($this->validate($request, $rules)){
                    $fields = [
                        'name'                      => $postData['name'],
                        'shipment_type'             => $postData['shipment_type'],
                        'type'                      => (array_key_exists("type",$postData)?$postData['type']:''),
                        'form_element_type'         => $postData['form_element_type'],
                        'options'                   => $postData['options'],
                        'is_notification'           => $postData['is_notification'],
                    ];
                    ProcessFlow::insert($fields);
                    return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Inserted Successfully !!!');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'process-flow.add-edit';
            $data['row']                    = [];
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'process-flow.add-edit';
            $data['row']                    = ProcessFlow::where($this->data['primary_key'], '=', $id)->first();
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'name'                      => 'required',
                    'shipment_type'             => 'required',
                    'form_element_type'         => 'required',
                    'is_notification'           => 'required',
                ];
                if($this->validate($request, $rules)){
                    $fields = [
                        'name'                      => $postData['name'],
                        'shipment_type'             => $postData['shipment_type'],
                        'type'                      => (array_key_exists("type",$postData)?$postData['type']:''),
                        'form_element_type'         => $postData['form_element_type'],
                        'options'                   => $postData['options'],
                        'is_notification'           => $postData['is_notification'],
                    ];
                    // Helper::pr($fields);
                    ProcessFlow::where($this->data['primary_key'], '=', $id)->update($fields);
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
            ProcessFlow::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = ProcessFlow::find($id);
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
