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
                        <h4>Add Merchandise</h4>
                      </div>
                      <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                                        <h4 style="float: right !important;"><a href="<?php echo e(URL('admin/merchandise')); ?>" class="btn btn-primary">Back</a></h4>
                                    </div>
                                </div>
                            </div>
                    <div class="widget-content widget-content-area">
                        <form action="<?php echo e(url('admin/store-merchandise')); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>">
                        <div class="form-row">
                        <div class="form-group col-md-6">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" name="title" placeholder="Title" value="<?php echo e(old('title')); ?>">
                        <?php if($errors->has('title')): ?>
                        <span class="text-danger"><?php echo e($errors->first('title')); ?></span>
                         <?php endif; ?>
                        </div>
                        <div class="form-group col-md-3">
                        <label for="price">Price</label>
                        <input type="text" class="form-control" name="price" placeholder="Price" value="<?php echo e(old('price')); ?>">
                        <?php if($errors->has('price')): ?>
                        <span class="text-danger"><?php echo e($errors->first('price')); ?></span>
                         <?php endif; ?>
                    </div>
                        <div class="form-group col-md-3">
                        <label for="sale_price">Sale Price (optional)</label>
                        <input type="text" class="form-control" name="sale_price" placeholder="Sale Price" value="<?php echo e(old('sale_price')); ?>">
                        <?php if($errors->has('sale_price')): ?>
                        <span class="text-danger"><?php echo e($errors->first('sale_price')); ?></span>
                         <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="size">Available Sizes</label>
                        <input type="text" class="form-control" name="size" placeholder="e.g. S, M, L, XL" value="<?php echo e(old('size')); ?>">
                        <small class="text-muted">Comma-separated list of sizes shown to the customer.</small>
                        <?php if($errors->has('size')): ?>
                        <span class="text-danger"><?php echo e($errors->first('size')); ?></span>
                         <?php endif; ?>
                    </div>
                        <div class="form-group col-md-3">
                        <label for="status">Status</label>
                           <select name="status" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        </div>
                        <div class="form-group col-md-6">
                        <label for="image">Main Image</label>
                        <input type="file" class="form-control" name="image">
                        <?php if($errors->has('image')): ?>
                        <span class="text-danger"><?php echo e($errors->first('image')); ?></span>
                         <?php endif; ?>
                        </div>
                        <div class="form-group col-md-6">
                        <label for="images">Additional Gallery Images</label>
                        <input type="file" class="form-control" name="images[]" multiple>
                        <small class="text-muted">Optional. You can select more than one file.</small>
                        </div>
                        </div>
                        <div class="form-group">
                        <div class="form-group col-md-12">
                        <label for="description">Description</label>
                        <textarea type="text" class="ckeditor" name="description" id="editor1" rows="10" cols="80"><?php echo e(old('description')); ?></textarea>
                        <?php if($errors->has('description')): ?>
                        <span class="text-danger"><?php echo e($errors->first('description')); ?></span>
                         <?php endif; ?>
                    </div>
                        </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    </form>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
<script src="../ckeditor.js"></script>
<script>
                CKEDITOR.config.versionCheck = false;
                CKEDITOR.replace( 'editor1' );
            </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vishnusharma/StagePicker App/stagePicker/resources/views/admin/merchandise/add.blade.php ENDPATH**/ ?>