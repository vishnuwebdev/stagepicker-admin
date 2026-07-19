<?php $__env->startSection('content'); ?>
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">

                        <div class="statbox widget box box-shadow">
                            <div class="widget-header">
                                <div class="row">
                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4>Merchandise Detail</h4>
                                    </div>

                                    <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                    <h4 style="float: right !important;"><a href="<?php echo e(URL('admin/merchandise')); ?>" class="btn btn-primary">Back</a></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="widget-content widget-content-area">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width:200px;">Title</th>
                                        <td><?php echo e($merchandise->title); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Price</th>
                                        <td><?php echo e($merchandise->price); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Sale Price</th>
                                        <td><?php echo e($merchandise->sale_price); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Available Sizes</th>
                                        <td><?php echo e($merchandise->size); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <?php if($merchandise->status == 1): ?>
                                            <span class="btn btn-sm btn-primary">Active</span>
                                            <?php else: ?>
                                            <span class="btn btn-sm btn-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td><?php echo $merchandise->description; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Main Image</th>
                                        <td>
                                            <?php if($merchandise->image): ?>
                                            <img src="<?php echo e(url('admin/uploads/merchandise/'.$merchandise->image)); ?>" width="100" height="100" style="object-fit:cover;">
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Gallery Images</th>
                                        <td>
                                            <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <img src="<?php echo e(url('admin/uploads/merchandise/'.$img->image)); ?>" width="80" height="80" style="object-fit:cover; margin-right:8px;">
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vishnusharma/StagePicker App/stagePicker/resources/views/admin/merchandise/view.blade.php ENDPATH**/ ?>