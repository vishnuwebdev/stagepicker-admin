
<?php $__env->startSection('content'); ?>
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">
                     

                 
                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4>Class View</h4>
                                    </div>
									
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                    <h4 style="float: right !important;"><a href="<?php echo e(URL('admin/classes')); ?>" class="btn btn-primary">Back</a></a></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <div class="table-responsive mb-4">
                                    <table id="style-2" class="table style-2  table-hover">
                                        <thead>
                                            <tr>
                                                <th> Sr. No.</th>
                                                <th>Class Name</th>
                                                <th>Price</th>
                                                <th>Duration</th>
                                                <th>Duration Type</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($category as $key => $categoryval) { ?>
                                            <tr>
                                                <td> 1 </td>
                                                <td><?php echo e($categoryval->title); ?></td>
                                                <td>
												 <?php echo e($categoryval->price); ?>

												</td>
                                                <td>
												 <?php echo e($categoryval->duration); ?>

												</td>
                                                <td>
												 <?php echo e($categoryval->duration_type); ?>

												</td>
                                                <td>
												<?php if($categoryval->status == 1): ?>
                                                <a href="#" class="btn btn-sm btn-primary">Active</a>
                                                    <?php else: ?>
                                                <a href="#" class="btn btn-sm btn-danger">inactive</a>
                                                <?php endif; ?>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/classes/view.blade.php ENDPATH**/ ?>