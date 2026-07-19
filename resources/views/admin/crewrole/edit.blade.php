@extends('layouts.admin')
@section('content')
<div class="containers">
	<div class="container">
		<div class="row">
			<div id="flFormsGrid" class="col-lg-12 layout-spacing">
				<div class="statbox widget box box-shadow">
					<div class="widget-header">
						<div class="row">
							<div class="col-xl-12 col-md-12 col-sm-12 col-12">
								<h4>Edit Crew Role</h4>
							</div>                                                                
						</div>
					</div>
					<div class="widget-content widget-content-area">
						<form action="{{ url('admin/update-crewrole',$role->id)  }}" method="POST" enctype="multipart/form-data">
							@csrf
							<input type="hidden" name="role_id" value="<?php echo  $role->id; ?>">
							<div class="form-group mb-4">
								<label for="role_title">Role Title</label>
								<input type="text" class="form-control" name="role_title" id="role_title" placeholder="Enter Role Title" value="{{ old('role_title', isset($role) ? $role->role_title : '') }}" required>
								@if ($errors->has('role_title'))
									<span class="text-danger">{{ $errors->first('role_title') }}</span>
								@endif
							</div>
						
							 
							<button type="submit" class="btn btn-primary mt-3">Update</button>
						</form>

					</div>
				</div>
			</div>
		</div> 
	</div>
</div>
@endsection