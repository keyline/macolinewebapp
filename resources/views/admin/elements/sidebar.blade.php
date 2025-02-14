<?php
use App\Models\Admin;
use App\Models\Role;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Route;
$routeName    = Route::current();
$pageName     = explode("/", $routeName->uri());
$pageSegment  = $pageName[1];
$pageFunction = ((count($pageName)>2)?$pageName[2]:'');
// dd($routeName);
if(!empty($parameters)){
  if (array_key_exists("id1",$parameters)){
    $pId1 = Helper::decoded($parameters['id1']);
  } else {
    $pId1 = Helper::decoded($parameters['id']);
  }
  if(count($parameters) > 1){
    $pId2 = Helper::decoded($parameters['id2']);
  }
}
$user_type                  = session('type');
$user_id                    = session('user_id');
$getAdmin                   = Admin::find($user_id);
$role_id                    = (($getAdmin)?$getAdmin->role_id:0);
$getRole                    = Role::find($role_id);
// Helper::pr($getRole);
?>
<div class="navbar-vertical-container">
  <div class="navbar-vertical-footer-offset">
    <!-- Logo -->
    <a class="navbar-brand" href="<?=url('admin/dashboard')?>" aria-label="Front">
      <img class="navbar-brand-logo" src="<?=env('UPLOADS_URL').$generalSetting->site_logo?>" alt="<?=$generalSetting->site_name?>" data-hs-theme-appearance="default" style="margin: 0 auto;">
      <img class="navbar-brand-logo" src="<?=env('UPLOADS_URL').$generalSetting->site_logo?>" alt="<?=$generalSetting->site_name?>" data-hs-theme-appearance="dark" style="margin: 0 auto;">
      <img class="navbar-brand-logo-mini" src="<?=env('UPLOADS_URL').$generalSetting->site_logo?>" alt="<?=$generalSetting->site_name?>" data-hs-theme-appearance="default" style="margin: 0 auto;">
      <img class="navbar-brand-logo-mini" src="<?=env('UPLOADS_URL').$generalSetting->site_logo?>" alt="<?=$generalSetting->site_name?>" data-hs-theme-appearance="dark" style="margin: 0 auto;">
    </a>
    <!-- End Logo -->
    <!-- Navbar Vertical Toggle -->
    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
      <i class="bi-arrow-bar-left navbar-toggler-short-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Collapse"></i>
      <i class="bi-arrow-bar-right navbar-toggler-full-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Expand"></i>
    </button>
    <!-- End Navbar Vertical Toggle -->
    <!-- Content -->
    <div class="navbar-vertical-content">
      <div id="navbarVerticalMenu" class="nav nav-pills nav-vertical card-navbar-nav">
        <!-- dashboard -->
          <div class="nav-item">
            <a class="nav-link <?=(($pageSegment == 'dashboard')?'active':'')?>" href="<?=url('admin/dashboard')?>" data-placement="left">
              <i class="fa fa-home nav-icon"></i>
              <span class="nav-link-title">Dashboard</span>
            </a>
          </div>
        <!-- End dashboard -->
        <!-- access -->
          <?php if($user_type == 'ma'){?>
            <div class="nav-item">
              <a class="nav-link dropdown-toggle active <?=(($pageSegment == 'module' || $pageSegment == 'sub-user' || $pageSegment == 'access' || $pageSegment == 'role')?'':'collapsed')?>" href="#navbarVerticalMenuAccess" role="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalMenuAccess" aria-expanded="<?=(($pageSegment == 'module' || $pageSegment == 'sub-user' || $pageSegment == 'access' || $pageSegment == 'role')?'true':'false')?>" aria-controls="navbarVerticalMenuAccess">
                <i class="fa fa-lock nav-icon"></i>
                <span class="nav-link-title">Access & Permission</span>
              </a>
              <div id="navbarVerticalMenuAccess" class="nav-collapse collapse <?=(($pageSegment == 'module' || $pageSegment == 'sub-user' || $pageSegment == 'access' || $pageSegment == 'role')?'show':'')?>" data-bs-parent="#navbarVerticalMenu">
                <!-- <a class="nav-link <?=(($pageSegment == 'module')?'active':'')?>" href="<?=url('admin/module/list')?>">Modules</a> -->
                <!-- <a class="nav-link <?=(($pageSegment == 'role')?'active':'')?>" href="<?=url('admin/role/list')?>">Roles</a> -->
                <a class="nav-link <?=(($pageSegment == 'sub-user')?'active':'')?>" href="<?=url('admin/sub-user/list')?>">Sub Users</a>
                <!-- <a class="nav-link <?=(($pageSegment == 'access')?'active':'')?>" href="<?=url('admin/access/list')?>">Give Access</a> -->
              </div>
            </div>
          <?php }?>
        <!-- End access -->
        <!-- masters -->
          <?php if($user_type == 'ma'){?>
            <div class="nav-item">
              <a class="nav-link dropdown-toggle active <?=(($pageSegment == 'pol' || $pageSegment == 'pod' || $pageSegment == 'process-flow')?'':'collapsed')?>" href="#navbarVerticalMenuAccess2" role="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalMenuAccess2" aria-expanded="<?=(($pageSegment == 'pol' || $pageSegment == 'pod' || $pageSegment == 'process-flow')?'true':'false')?>" aria-controls="navbarVerticalMenuAccess2">
                <i class="fa fa-database nav-icon"></i>
                <span class="nav-link-title">Masters</span>
              </a>
              <div id="navbarVerticalMenuAccess2" class="nav-collapse collapse <?=(($pageSegment == 'pol' || $pageSegment == 'pod' || $pageSegment == 'process-flow')?'show':'')?>" data-bs-parent="#navbarVerticalMenu">
                <a class="nav-link <?=(($pageSegment == 'pol')?'active':'')?>" href="<?=url('admin/pol/list')?>">POL</a>
                <a class="nav-link <?=(($pageSegment == 'pod')?'active':'')?>" href="<?=url('admin/pod/list')?>">POD</a>
                <a class="nav-link <?=(($pageSegment == 'process-flow')?'active':'')?>" href="<?=url('admin/process-flow/list')?>">Process Flow</a>
              </div>
            </div>
          <?php }?>
        <!-- End masters -->
        <!-- customer -->
          <?php if($getRole){ if($getRole->add_customer_access == 1){?>
            <div class="nav-item">
              <a class="nav-link <?=(($pageSegment == 'customer')?'active':'')?>" href="<?=url('admin/customer/list')?>" data-placement="left">
                <i class="fa fa-users nav-icon"></i>
                <span class="nav-link-title">Customers</span>
              </a>
            </div>
          <?php } }?>
        <!-- End customer -->
        <!-- Consignments -->
          <div class="nav-item">
            <a class="nav-link <?=(($pageSegment == 'consignment')?'active':'')?>" href="<?=url('admin/consignment/list')?>" data-placement="left">
              <i class="fa fa-list-alt nav-icon"></i>
              <span class="nav-link-title">Consignments</span>
            </a>
          </div>
        <!-- End Consignments -->
        <!-- page -->
          <!-- <div class="nav-item">
            <a class="nav-link <?=(($pageSegment == 'page')?'active':'')?>" href="<?=url('admin/page/list')?>" data-placement="left">
              <i class="fa fa-envelope nav-icon"></i>
              <span class="nav-link-title">Pages</span>
            </a>
          </div> -->
        <!-- End page -->
        <?php if($user_type == 'ma'){?>
          <!-- Settings -->
            <div class="nav-item">
              <a class="nav-link <?=(($pageSegment == 'settings')?'active':'')?>" href="<?=url('admin/settings')?>" data-placement="left">
                <i class="fa fa-cogs nav-icon"></i>
                <span class="nav-link-title">Settings</span>
              </a>
            </div>
          <!-- End Settings -->
          <!-- email logs -->
            <div class="nav-item">
              <a class="nav-link <?=(($pageSegment == 'email-logs')?'active':'')?>" href="<?=url('admin/email-logs')?>" data-placement="left">
                <i class="fa fa-history nav-icon"></i>
                <span class="nav-link-title">Email Logs</span>
              </a>
            </div>
          <!-- End email logs -->
          <!-- login logs -->
            <div class="nav-item">
              <a class="nav-link <?=(($pageSegment == 'login-logs')?'active':'')?>" href="<?=url('admin/login-logs')?>" data-placement="left">
                <i class="fa fa-sign-in nav-icon"></i>
                <span class="nav-link-title">Login Logs</span>
              </a>
            </div>
          <!-- End login logs -->
          <!-- signout -->
            <div class="nav-item">
              <a class="nav-link <?=(($pageSegment == 'logout')?'active':'')?>" href="<?=url('admin/logout')?>" data-placement="left">
                <i class="fa fa-sign-out nav-icon"></i>
                <span class="nav-link-title">Sign Out</span>
              </a>
            </div>
          <!-- End signout -->
        <?php }?>
      </div>
    </div>
    <!-- End Content -->
  </div>
</div>