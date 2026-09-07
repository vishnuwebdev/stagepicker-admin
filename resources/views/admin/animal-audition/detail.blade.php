@extends('layouts.admin')
@section('content')
<div class="layout-px-spacing">
	<div class="row layout-top-spacing layout-spacing">
		<div class="col-lg-12">
			<div class="statbox widget box box-shadow">
				<div class="widget-header">
					<div class="row">
						<div class="col-xl-12 col-md-12 col-sm-12 col-12">
							<h4>Animal Audition Detail</h4>
						</div>
					</div>
				</div>
				<div class="widget-content widget-content-area">
					<?php if (empty($detail)) { ?>
						<p>Post not found.</p>
					<?php } else { ?>
						<table class="table">
							<tr><th style="width:220px;">Title</th><td>{{ $detail->title }}</td></tr>
							<tr><th>Producer</th><td>{{ $detail->producer_name }} ({{ $detail->producer_email }})</td></tr>
							<tr><th>Description</th><td>{{ $detail->description }}</td></tr>
							<tr><th>Location</th><td>{{ $detail->location }}</td></tr>
							<tr><th>Audition Date / Time</th><td>{{ $detail->audition_date }} {{ $detail->audition_time }}</td></tr>
							<tr><th>Desired Species / Breed</th><td>{{ $detail->species_name ?? 'Any' }} / {{ $detail->breed_name ?? 'Any' }}</td></tr>
							<tr><th>Age Range</th><td>{{ $detail->age_range }}</td></tr>
							<tr><th>Gender</th><td>{{ $detail->gender }}</td></tr>
							<tr><th>Size</th><td>{{ $detail->size }}</td></tr>
							<tr><th>Color / Markings</th><td>{{ $detail->color_markings }}</td></tr>
							<tr><th>Temperament</th><td>{{ $detail->temperament }}</td></tr>
							<tr><th>Compensation</th><td>{{ $detail->compensation == 1 ? 'Yes — ' . $detail->compensation_description : 'No' }}</td></tr>
							<tr><th>Status</th><td>{{ $detail->status == 1 ? 'Active' : 'Inactive' }}</td></tr>
						</table>

						<h5 class="mt-4">Photos</h5>
						<div class="row">
							<?php foreach ($photos as $photo) { ?>
								<div class="col-md-2 mb-3">
									<img src="{{ asset($photo->image) }}" class="img-fluid" style="border-radius:4px;">
								</div>
							<?php } ?>
							<?php if ($photos->isEmpty()) { ?><p class="text-muted">No photos.</p><?php } ?>
						</div>

						<h5 class="mt-4">Applicants ({{ $applicants->count() }})</h5>
						<div class="table-responsive">
							<table class="table style-2 table-hover">
								<thead>
									<tr>
										<th>Applicant</th>
										<th>Email</th>
										<th>Animal Profile(s)</th>
										<th>Shortlisted</th>
										<th>Applied On</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($applicants as $applicant) { ?>
										<tr>
											<td>{{ $applicant->applicant_name }}</td>
											<td>{{ $applicant->applicant_email }}</td>
											<td>
												<?php foreach ($applicant->profiles as $profile) { ?>
													<div>{{ $profile->name }} — {{ $profile->species_name }} / {{ $profile->breed_name }}</div>
												<?php } ?>
											</td>
											<td>{{ $applicant->is_selected == '1' ? 'Yes' : 'No' }}</td>
											<td>{{ $applicant->created_at }}</td>
										</tr>
									<?php } ?>
									<?php if ($applicants->isEmpty()) { ?>
										<tr><td colspan="5" class="text-muted">No applicants yet.</td></tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
