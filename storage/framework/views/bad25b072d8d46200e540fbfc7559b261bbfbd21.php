
<!--  BEGIN SIDEBAR  -->
<div class="sidebar-wrapper sidebar-theme">
		
	<nav id="sidebar">
		<div class="shadow-bottom"></div>

		<ul class="list-unstyled menu-categories" id="accordionExample">
				<li class="menu <?php echo e(Request::segment(2) === 'dashboard' ? 'active' : null); ?>">
					<a href="<?php echo e(url('admin/dashboard')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
						<div class="dash-part">
							
							 <span><i class="sicon fa fa-tachometer"></i> Dashboard</span> 
						</div>
					</a>
				</li>
				<?php
                   $userPermission = Auth::User();

				    $permissionMenu = json_decode($userPermission->permissions);


				    foreach($permissionMenu as $key => $per) { 

				    	if($per->moduleId == 'p1' && $per->view) { ?>

				    	<li class="menu <?php echo e(Request::segment(2) === 'users' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/users')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="user-ma">
									
									 <span><i class="sicon fa fa-user"></i> User Management</span>
								</div>
							</a>
						</li>
				   <?php } else if($per->moduleId == 'p2' && $per->view)  { ?>

                       <li class="menu <?php echo e(Request::segment(2) === 'adminuser' ? 'active' : null); ?>">
						<a href="<?php echo e(url('admin/adminuser/list')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
							<div class="user-ma">
								
								 <span><i class="sicon fa fa-user"></i> Admin User Management</span>
							</div>
						</a>
					</li>

				  <?php  } else if($per->moduleId == 'p3' && $per->view)  { ?>

				  					<li><a class='menu audition-drop-part' href='#' title='Menu'><i class="sicon fa fa-th-large"></i> Audition Control <i class="fa fa-angle-down" aria-hidden="true"></i>
</a>
					<ul class='menus audition-show-part'> 
						<li class="menu <?php echo e(Request::segment(2) === 'postaudition' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/postaudition')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
								   
									<span>- Post Audition</span>
								</div>
							</a>
						</li>
						<li class="menu <?php echo e(Request::segment(2) === 'skill' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/skill')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
									
									 <span>- Skills Management</span>
								</div>
							</a>
						</li>
						<li class="menu <?php echo e(Request::segment(2) === 'career' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/career')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
								
									 <span>- Manage Career</span>
								</div>
							</a>
						</li>
						<!--li class="menu <?php echo e(Request::segment(2) === 'role' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/role')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
									
									 <span>- Roles</span>
								</div>
							</a>
						</li-->
						<li class="menu <?php echo e(Request::segment(2) === 'roletype' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/roletype')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
								
									 <span>- Role Type</span>
								</div>
							</a>
						</li>
					</ul>
				</li>

				  <?php  } else if($per->moduleId == 'p4' && $per->view)  { ?>

				  					<li><a class='menu production-drop-part' href='#' title='Menu'><i class="sicon fa fa-list"></i> Production Crew <i class="fa fa-angle-down" aria-hidden="true"></i>
</a>
					<ul class='menus production-show-part'> 
						<li class="menu <?php echo e((Request::segment(2) === 'productioncrew' || Request::segment(2) === 'crewdetail') ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/productioncrew')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
								   
									<span>- Post Audition Crew</span>
								</div>
							</a>
						</li>
						<!--li class="menu <?php echo e(Request::segment(2) === 'crewrole' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/crewrole')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
									
									 <span>- Crew Role Management</span>
								</div>
							</a>
						</li-->
						<li class="menu <?php echo e(Request::segment(2) === 'crewrole' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/crewrole')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
									
									 <span>- Crew Role Type</span>
								</div>
							</a>
						</li>
					</ul>
				</li>

                  <?php } else if($per->moduleId == 'p5' && $per->view)  { ?>


				<li class="menu <?php echo e(Request::segment(2) === 'company-verification' ? 'active' : null); ?>">
					<a href="<?php echo e(url('admin/company-verification')); ?>" data-active="" aria-expanded="" class="dropdown-toggle menu">
						<div class="">
							

							 <span><i class="sicon fa fa-check-circle"></i> Verification</span>
						</div>
					</a>
				</li>

                  <?php } else if($per->moduleId == 'p6' && $per->view)  { ?>

                  					<li><a class='menu store-drop-part' href='#' title='Store'><i class="sicon fa fa-database"></i> Store <i class="fa fa-angle-down" aria-hidden="true"></i>
</a>
				<ul class='menus store-show-part' style="display: none;"> 
				<li class="menu <?php echo e(Request::segment(2) === 'classes' ? 'active' : null); ?>">
					<a href="<?php echo e(url('admin/classes')); ?>"  data-active="" aria-expanded="" class="dropdown-toggle">
						<div class="">
							

							 <span>- Classes</span>
						</div>
					</a>
				</li>
				<li class="menu <?php echo e(Request::segment(2) === 'webinars' ? 'active' : null); ?>">
					<a href="<?php echo e(url('admin/webinars')); ?>"  data-active="" aria-expanded="" class="dropdown-toggle">
						<div class="">
							

							 <span>- Webinars</span>
						</div>
					</a>
				</li>
				<li class="menu <?php echo e(Request::segment(2) === 'merchandise' ? 'active' : null); ?>">
					<a href="<?php echo e(url('admin/merchandise')); ?>"  data-active="" aria-expanded="" class="dropdown-toggle">
						<div class="">
							

							 <span>- Merchandise</span>
						</div>
					</a>
				</li>
			</ul>
			</li>	

                  <?php } else if($per->moduleId == 'p7' && $per->view)  { ?>

                  	<li class="menu <?php echo e(Request::segment(2) === 'category' ? 'active' : null); ?>">
					<a href="<?php echo e(url('admin/category')); ?>" data-active="" aria-expanded="" class="dropdown-toggle menu">
						<div class="">
							
							<span><i class="sicon fa fa-th-list"></i> Category Management</span>
						</div>
					</a>
				</li>

                  <?php } else if($per->moduleId == 'p8' && $per->view)  { ?>


				<li><a class='menu app-drop-part' href='#' title='Menu'><i class="sicon fa fa-cog"></i> App Setting <i class="fa fa-angle-down" aria-hidden="true"></i>
</a>
					<ul class='menus app-show-part'> 
						<li class="menu <?php echo e(Request::segment(2) === 'cms' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/cms')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
									
									 <span>- Pages Edit</span>
								</div>
							</a>
						</li>
						<li class="menu <?php echo e(Request::segment(2) === 'slider' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/slider')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
								   
									<span>- Change Slider</span>
								</div>
							</a>
						</li>
						<li class="menu">
							<a href="<?php echo e(url('admin/subscription')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
									
									<span>- Subscription Plan</span>  
								</div>
							</a>
						</li>
					</ul>
				</li>
				

                  <?php } else if($per->moduleId == 'p9' && $per->view)  { ?>

                  					<li><a class='menu announcemet-drop-part' href='#' title='Menu'> <i class="sicon fa fa-chart-bar"></i> Announcement & Reports <i class="fa fa-angle-down" aria-hidden="true"></i>
</a>
					<ul class='menus announcemet-show-part'> 
						<li class="menu <?php echo e(Request::segment(2) === 'push-notification' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/push-notification')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
								  
									<span>- Send Notifications</span>
								</div>
							</a>
						</li>
						
						<li class="menu <?php echo e(Request::segment(2) === 'report' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/report')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
								   
									<span>- Auditions Reports</span>
								</div>
							</a>
						</li> 
						<li class="menu <?php echo e(Request::segment(2) === 'contactlist' ? 'active' : null); ?>">
							<a href="<?php echo e(url('admin/contactlist')); ?>" data-active="" aria-expanded="" class="dropdown-toggle">
								<div class="">
									
									 <span>- Contact Request</span>
								</div>
							</a>
						</li>
					</ul>
				</li>
                      
                  <?php 

                        } 

                     }   
				?>
				
				
  
		</ul>
		
	</nav>

</div>
<!--  END SIDEBAR  -->
<?php /**PATH /Users/vishnusharma/StagePicker App/stagePicker/resources/views/admin/inc/sidebar.blade.php ENDPATH**/ ?>