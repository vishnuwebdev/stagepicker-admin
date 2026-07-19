<?php $__env->startSection('content'); ?>
<div class="layout-px-spacing">
	<div class="row layout-top-spacing layout-spacing">
		<div class="col-lg-12">
			<div class="statbox widget box box-shadow">
				<div class="widget-header">
					<div class="row">
						<div class="col-xl-6 col-md-6 col-sm-6 col-6">
							<h4>Report</h4>
						</div>
					</div>
				</div>
				<div class="widget-content widget-content-area">
					<div class="table-responsive mb-4">
						<table id="style-2" class="table style-2  table-hover">
							<thead>
								<tr>
									<th class="checkbox-column"> Record Id </th>
									<th>Audition</th>
									<th>User</th>
									<th>Description</th>  
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								
								<?php foreach ($reports as $key => $report) { ?>
									<tr>
									<td class="checkbox-column"> <?php echo $key+1;?> </td>
									<td><?php echo $report->audition_title; ?></td> 
									<td><?php echo $report->user_name; ?></td>
									<td><div class="comment more"><?php echo $report->description; ?></div></td>
									 
									<td class="text-center">
										<ul class="table-controls"> 
											<li>
												<a onclick="return confirm('Are you sure want to delete this record?')" href="<?php echo e(url('admin/report-delete')); ?>/<?php echo $report->id; ?>" data-toggle="tooltip" data-placement="top" title="Delete">
												<i class="fa fa-trash"></i> </a>
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
<style>
a.morelink {
	text-decoration:none;
	outline: none;
}
.morecontent span {
	display: none;
}
.comment {
	width: 400px; 
	margin: 10px;
}
</style>
 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/audition/reportlist.blade.php ENDPATH**/ ?>