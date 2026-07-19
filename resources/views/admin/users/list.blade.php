@extends('layouts.admin')

@section('content')
<div class="layout-px-spacing">
	<div class="row layout-top-spacing layout-spacing">
		<div class="col-lg-12">
			@if ($message = Session::get('success'))
				<div class="alert alert-success mb-4" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
					<strong>{{ $message }}</strong></button>
				</div>
			@endif
			<div class="statbox widget box box-shadow">
				<div class="widget-header">
					<div class="row">
						<div class="col-xl-6 col-md-6 col-sm-6 col-6">
							<h4>User List</h4>
						</div>
						<div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="{{ URL('admin/user/download') }}" class="btn btn-primary">Export All</a></h4>
                         </div>
					</div>
				</div>
				<div class="widget-content widget-content-area">
					<div class="table-responsive mb-4">
						<table id="style-2" class="table style-2  table-hover">
							<thead>
								<tr>
									<th class="checkbox-column"> Record Id </th>
									<th>Name</th>
									<th>Email</th>
									<th>Phone</th>
									<th>User Type</th>
									<th>Paid Status</th>
									<th class="text-center">Status</th> 
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($users as $key => $userval) { ?>
									<tr>
									<td class="checkbox-column"> 1 </td>
									<td><?php echo $userval->name; ?></td>
									<td><?php echo $userval->email; ?></td>
									<td><?php echo $userval->phone; ?></td>
									<td class="text-center">
										<?php if($userval->user_type == 1){ ?> 
										Auditioners
										<?php }elseif ($userval->user_type == 2) { ?>
											Producer
										<?php } ?>
									</td>
									<td class="text-center">
										<?php if($userval->pro_member == 1){ ?> 
										Paid
										<?php }else{ ?>
											Free
										<?php } ?>
									</td>
									<td class="text-center">
										<form>
											@csrf 
											<input type="checkbox" name="status" value="1" class="new-control updatestatus" data-id="<?php echo $userval->id;?>" <?php if($userval->status == 1){echo "checked";}?>>
										</form>
										 
										<?php if($userval->status == 1){ ?> 
										<span class="shadow-none badge badge-primary">Active</span>
										<?php }elseif ($userval->status == 0) { ?>
											<span class="shadow-none badge badge-warning">Inactive</span>
										<?php } ?> 
									</td>
									 
									<td class="text-center">
										<ul class="table-controls">
											<!--li> 
												<a onclick="return confirm('Are you sure want to delete this record?')" href="{{ url('admin/userdelete')  }}/<?php echo base64_encode($userval['id']); ?>" data-toggle="tooltip" data-placement="top" title="Delete">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
												</a>
											</li-->
											
											 
											
											<li>
												<a href="{{ url('admin/useredit',$userval->id)  }}" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>

</a>
											</li>
											  
											 <li> 
												<a href="{{ url('admin/userdetail') }}/<?php echo base64_encode($userval->id); ?>" data-toggle="tooltip" data-placement="top" title="Edit">
												<i class="fa fa-eye"></i> </a>
											</li>
										</ul>
									</td>
								</tr>
								<?php } ?>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
            
@endsection