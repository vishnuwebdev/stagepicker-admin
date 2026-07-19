
<?php $__env->startSection('content'); ?>
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">
                    <script src="//cdn.ckeditor.com/4.19.1/standard/ckeditor.js"></script>
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



                        <h4>Webinar Update</h4>
                      </div>
                      <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="<?php echo e(URL('admin/webinars')); ?>" class="btn btn-primary">Back</a></a></h4>
                                    </div>
                                </div>
                            </div>
                         
                        <form action="<?php echo e(url('admin/update-webinar',$weninar->id)); ?>" method="post" enctype = "multipart/form-data">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>">
                        <div class="form-row">
                        <div class="form-group col-md-6">
                        <label for="inputEmail4">Title</label>
                        <input type="text" class="form-control" name="title"  value="<?php echo e($weninar->title); ?>" >
                        <?php if($errors->has('title')): ?>
                        <span class="text-danger"><?php echo e($errors->first('title')); ?></span>
                         <?php endif; ?>
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Price</label>
                        <input type="text" class="form-control" name="price"value="<?php echo e($weninar->price); ?>">
                        <?php if($errors->has('price')): ?>
                        <span class="text-danger"><?php echo e($errors->first('price')); ?></span>
                         <?php endif; ?> 
                    </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Link</label>
                        <input type="text" class="form-control" name="link" value="<?php echo e($weninar->link); ?>">
                        <?php if($errors->has('link')): ?>
                        <span class="text-danger"><?php echo e($errors->first('link')); ?></span>
                         <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputPassword4">Date</label>
                        <input type="date" class="form-control"  name="date"value="<?php echo e($weninar->date); ?>">
                        <?php if($errors->has('date')): ?>
                        <span class="text-danger"><?php echo e($errors->first('date')); ?></span>
                         <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputPassword4">Time</label>
                        <input type="time" class="form-control" name="time"value="<?php echo e($weninar->time); ?>">
                        <?php if($errors->has('time')): ?>
                        <span class="text-danger"><?php echo e($errors->first('time')); ?></span>
                         <?php endif; ?>
                    </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Image</label>
                        <input type="file" class="form-control" name="image"  value="<?php echo e($weninar->image); ?>">
                        <?php echo e($weninar->image); ?>

                        <?php if($errors->has('image')): ?>
                        <span class="text-danger"><?php echo e($errors->first('image')); ?></span>
                         <?php endif; ?>
                        </div>
                        </div>
                 
                         <div class="form-group">
                        <div class="form-check">
                    </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                  
                    </div>
                    </form>
                    </div>
                    </div>
                </div>
</div>
            </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/webinar/edit.blade.php ENDPATH**/ ?>