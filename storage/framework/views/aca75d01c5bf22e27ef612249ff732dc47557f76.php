
<?php $__env->startSection('content'); ?>
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing layout-spacing">
                    <div class="col-lg-12">
                    <script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
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
                        <h4>Class Update</h4>
                      </div>
                      <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="<?php echo e(URL('admin/classes')); ?>" class="btn btn-primary">Back</a></a></h4>
                                    </div>
                                </div>
                            </div>
                         <div class="widget-content widget-content-area">
                        <form action="<?php echo e(url('admin/update-classes',$category->id)); ?>" method="post" enctype = "multipart/form-data">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>">
                        <div class="form-row">
                        <div class="form-group col-md-6">
                        <label for="inputEmail4">Title</label>
                        <input type="text" class="form-control" name="title"  value="<?php echo e($category->title); ?>" >
                        <?php if($errors->has('title')): ?>
                        <span class="text-danger"><?php echo e($errors->first('title')); ?></span>
                         <?php endif; ?>
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Price</label>
                        <input type="text" class="form-control" name="price"value="<?php echo e($category->price); ?>">
                        <?php if($errors->has('price')): ?>
                        <span class="text-danger"><?php echo e($errors->first('price')); ?></span>
                         <?php endif; ?> 
                    </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Tages</label>
                        <input type="text" class="form-control" name="tags" value="<?php echo e($category->tags); ?>">
                        <?php if($errors->has('tags')): ?>
                        <span class="text-danger"><?php echo e($errors->first('tags')); ?></span>
                         <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="inputPassword4">Duration</label>
                        <input type="text" class="form-control" name="duration"value="<?php echo e($category->duration); ?>">
                        <?php if($errors->has('tags')): ?>
                        <span class="text-danger"><?php echo e($errors->first('duration')); ?></span>
                         <?php endif; ?>
                    </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Duration Type</label>
                           <select name="duration_type" id="cars"   class="form-control" >
                           <option value="">---Select----</option>
                            <option value="month" <?php echo e((isset($category->duration_type)&& $category->duration_type=='month')?'selected':''); ?>>Month</option>
                            <option value="year"  <?php echo e((isset($category->duration_type)&& $category->duration_type=='year')?'selected':''); ?>>Year</option>
                            <option value="day" <?php echo e((isset($category->duration_type)&& $category->duration_type=='day')?'selected':''); ?>>Day</option>
                         
                        </select> 
                        <?php if($errors->has('duration_type')): ?>
                        <span class="text-danger"><?php echo e($errors->first('duration_type')); ?></span>
                         <?php endif; ?>
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputPassword4">Image</label>
                        <input type="file" class="form-control" name="image"  value="<?php echo e($category->image); ?>">
                        <?php echo e($category->image); ?>

                        <?php if($errors->has('image')): ?>
                        <span class="text-danger"><?php echo e($errors->first('image')); ?></span>
                         <?php endif; ?>
                        </div>
                        </div>
                        <div class="form-group">
                        <div class="form-group col-md-12">
                        <label for="inputPassword4">Description</label>
                        <textarea  type="text" class="ckeditor" name="description" id="editor1"   rows="10" cols="80"> <?php echo e($category->description); ?></textarea>
                        <?php if($errors->has('description')): ?>
                        <span class="text-danger"><?php echo e($errors->first('description')); ?></span>
                         <?php endif; ?>
                    </div>
                        </div>
                         <div class="form-group">
                        <div class="form-check">
                    </div>
                    </div>
                    <input type="hidden" placeholder="Enter Password" name="id" id="psw" value = "<?php echo e($category->id); ?>" >
                    <button type="submit" class="btn btn-primary">Submit</button>
                  
                    </div>
                    </form>
                    </div>
                    </div>
                    </div>
                </div>
</div>
            </div>

</div>
<script src="../ckeditor.js"></script>
<script>
                // Replace the <textarea id="editor1"> with a CKEditor 4
                // instance, using default configuration.
                CKEDITOR.config.versionCheck = false;
                CKEDITOR.replace( 'editor1' );
            </script>>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vishnusharma/StagePicker App/stagePicker/resources/views/admin/classes/edit.blade.php ENDPATH**/ ?>