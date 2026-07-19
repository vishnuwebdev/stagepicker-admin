<?php $__env->startSection('content'); ?>
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">
                        <?php if($message = Session::get('success')): ?>
                            <div class="alert alert-success mb-4" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                <strong><?php echo e($message); ?></strong></button>
                            </div>
                        <?php endif; ?>
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4>Verification List</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <div class="table-responsive mb-4">
                                    <table id="style-2" class="table style-2  table-hover">
                                        <thead>
                                            <tr>
                                                <th class="checkbox-column"> Record Id </th>
                                                <th>Producer Name</th>
                                                <th>Email / Phone</th>
                                                <th>Website First</th>
                                                <th>Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($verifications as $key => $verificationsval) { ?>
                                                <tr>
                                                <td class="checkbox-column"> 1 </td>
                                                <td><?php echo $verificationsval->name; ?></td>
                                                <td><?php echo $verificationsval->email; ?></td>
                                                <td><?php echo $verificationsval->company_website; ?></td>
                                                <td></td>
                                                <td class="text-center">
                                                    <ul class="table-controls">
                                                    <?php if($editPermission) { ?>
                                                        <li>
                                                            <a href="<?php echo e(url('admin/edit-verification',$verificationsval->user_id)); ?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                        </li>
                                                        <li>
                                                            
                                                            <a href="<?php echo e(url('admin/view-verification',$verificationsval->user_id)); ?>" data-toggle="tooltip" data-placement="top" title="view"><i class="fa fa-eye"></i>
                                                            </a>
                                                            
                                                        </li>
                                                        <?php } ?>
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
            
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/users/verificationlist.blade.php ENDPATH**/ ?>