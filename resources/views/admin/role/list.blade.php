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
                                        <h4>Role List</h4>
                                    </div>
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;">
                                            <?php  if($addPermission) { ?>
                                              <a href="{{ URL('admin/add-role') }}" class="btn btn-primary">Create Role</a>
                                            <?php } ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <div class="table-responsive mb-4">
                                    <table id="style-2" class="table style-2  table-hover">
                                        <thead>
                                            <tr>
                                                <th> Sr. No.</th>
                                                <th>Role Title</th>
                                                <th>Age</th>
                                                <th>Skills</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($role as $key => $roleval) { ?>
                                            <tr>
                                                <td> 1 </td>
                                                <td>{{ $roleval->role_title }}</td>
                                                <td> 
													@if($roleval->age_min != '') 
														{{ $roleval->age_min }}
													@endif
													
													@if($roleval->age_max != '') 
														-{{ $roleval->age_max }}
													@endif
												</td>
                                                <td>{{ $roleval->skills }}</td>
                                                <td class="text-center">
                                                        <ul class="table-controls">
                                                            
                                                            <li>
                                                                 <?php  if($editPermission) { ?>
                                                                <a href="{{ url('admin/edit-role',$roleval->id)  }}" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                                <?php } ?>
                                                            </li>
                                                            <li>
                                                                 <?php  if($deletePermission) { ?>
                                                                <a onclick="return confirm('Are you sure want to delete this record?')" href="{{ url('admin/role-delete',$roleval->id)  }}" data-toggle="tooltip" data-placement="top" title="Delete"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                                                <?php } ?>
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