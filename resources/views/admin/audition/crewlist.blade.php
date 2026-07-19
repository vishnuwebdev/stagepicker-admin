@extends('layouts.admin')

@section('content')
<div class="layout-px-spacing">
	<div class="row layout-top-spacing layout-spacing">
		<div class="col-lg-12">
			<div class="statbox widget box box-shadow">
				<div class="widget-header">
					<div class="row">
						<div class="col-xl-6 col-md-6 col-sm-6 col-6">
							<h4>Production Crew</h4>
						</div>
					</div>
				</div>
				<div class="widget-content widget-content-area">
					<div class="table-responsive mb-4">
						<table id="style-2" class="table style-2  table-hover">
							<thead>
								<tr>
									<th class="checkbox-column"> Record Id </th>
									<th>Title</th>
									<th>Category</th>
									<th>Expiry Date</th> 
									<th class="text-center">Status</th>
									<th class="text-center">Pin To Top</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								
								<?php foreach ($postaudition as $key => $postauditionval) { ?>
									<tr>
									<td class="checkbox-column"> 1 </td>
									<td><?php echo $postauditionval->title; ?></td>
									<td><?php if(!empty($postauditionval->category_name)){ echo $postauditionval->category_name; }else{ echo "";} ?></td>
									<td><?php echo $postauditionval->expire_date; ?></td> 
									<td class="text-center">
										<form>
											@csrf 
											<input type="checkbox" name="status" value="1" class="new-control updatecrewstatus" data-id="<?php echo $postauditionval->id;?>" <?php if($postauditionval->status == 1){echo "checked";}?>>
										</form>
									
									
										<?php if($postauditionval->status == 0){ ?> 
										Expired
										<?php }elseif ($postauditionval->status == 1) { ?>
											Active
										<?php } ?>
									</td>
									<td class="text-center">
										<form>
											@csrf
											<input type="checkbox" name="pin_to_top" value="1" class="new-control pintopcrew" data-id="<?php echo $postauditionval->id;?>" <?php if($postauditionval->pin_to_top == 1){echo "checked";}?>>
										</form>
									</td>
									<td class="text-center">
										<ul class="table-controls">
										<!--<li>-->
										<!--    <a onclick="return confirm('Are you sure want to delete this record?')" href="#" data-toggle="tooltip" data-placement="top" title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>-->
										<!--</li>-->
										<li>
											<a href="{{ url('admin/crewdetail') }}/<?php echo base64_encode($postauditionval->id); ?>" data-toggle="tooltip" data-placement="top" title="Edit">
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