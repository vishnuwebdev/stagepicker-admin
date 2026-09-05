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
						<div class="col-xl-12 col-md-12 col-sm-12 col-12">
							<h4>Animal Audition Posts</h4>
						</div>
					</div>
				</div>
				<div class="widget-content widget-content-area">
					<div class="table-responsive mb-4">
						<table id="style-2" class="table style-2  table-hover">
							<thead>
								<tr>
									<th> Sr. No.</th>
									<th>Title</th>
									<th>Producer</th>
									<th>Species</th>
									<th>Audition Date</th>
									<th class="text-center">Status</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>

								<?php $i=0;foreach ($animalAudition as $key => $auditionval) { $i++;?>
									<tr>
										<td> <?= $i;?> </td>
										<td>{{ $auditionval->title }}</td>
										<td>{{ $auditionval->producer_name }}</td>
										<td>{{ $auditionval->species_name ?? 'Any' }}</td>
										<td>{{ $auditionval->audition_date }}</td>
										<td class="text-center">
											<?php if($editPermission) { ?>
											<form>
												@csrf
												<input type="checkbox" name="status" value="1" class="new-control updateanimalauditionstatus" data-id="<?php echo $auditionval->id;?>" <?php if($auditionval->status == 1){echo "checked";}?>>
											</form>
											<?php } else { ?>
												{{ $auditionval->status == 1 ? 'Active' : 'Inactive' }}
											<?php } ?>
										</td>
										<td class="text-center">
											<a href="{{ url('admin/animal-audition-detail') }}/<?php echo base64_encode($auditionval->id); ?>" data-toggle="tooltip" data-placement="top" title="View"><i class="fa fa-eye"></i></a>
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
