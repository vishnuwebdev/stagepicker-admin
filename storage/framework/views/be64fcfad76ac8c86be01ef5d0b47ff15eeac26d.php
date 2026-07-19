
<?php $__env->startSection('content'); ?>
<style type="text/css">
	.select2-container .select2-selection--multiple{
		    min-height: 43px !important;
	}
	.select2-container .select2-search--inline .select2-search__field{
		margin-top: 9px !important;
		margin-left: 20px !important;
	}
</style>
<link rel="stylesheet" href="https://cdn.ckbox.io/ckbox/2.5.1/styles/themes/lark.css">
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.css" />
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5-premium-features/42.0.0/ckeditor5-premium-features.css">
<div class="layout-px-spacing">
	<div class="row layout-top-spacing layout-spacing">
			<?php if(count($errors) > 0): ?>
		   
		        <div class="col-md-12">
		            <div class="alert alert-danger">
		                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		                    <div><?php echo e($error); ?></div>
		                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		            </div>
		        </div>
		   
		<?php endif; ?>
			<div id="flFormsGrid" class="col-lg-12 layout-spacing">
				<div class="statbox widget box box-shadow">
					<div class="widget-header">
						<div class="row">
							<div class="col-xl-12 col-md-12 col-sm-12 col-12">
								<h4>Edit Audition</h4>
							</div>                                                                
						</div>
					</div>
					<div class="widget-content widget-content-area">
						<form action="<?php echo e(url('admin/editAudition')); ?>" method="POST" enctype="multipart/form-data">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="p_id" value="<?php echo e($pid); ?>">
							<div class="row">
							    <div class="col-md-6">
							       <div class="form-group mb-4">
										<label for="title">Title</label>
										<input type="text" class="form-control" name="audition_title" id="audition_title" placeholder="Enter Title" value="<?php echo e(old('audition_title', isset($postaudition) ? $postaudition->audition_title : '')); ?>">
										<?php if($errors->has('audition_title')): ?>
											<span class="text-danger"><?php echo e($errors->first('audition_title')); ?></span>
										<?php endif; ?>
									</div> 
							   </div>
							   <div class="col-md-6">
							       <div class="form-group mb-4">
										<label for="title">Studio Name</label>
										<input type="text" class="form-control" name="studio_name" id="studio_name" placeholder="Enter Studio Name" value="<?php echo e(old('studio_name', isset($postaudition) ? $postaudition->studio_name : '')); ?>">
										<?php if($errors->has('studio_name')): ?>
											<span class="text-danger"><?php echo e($errors->first('studio_name')); ?></span>
										<?php endif; ?>
									</div> 
							   </div>
                           
							    <div class="col-md-6">
							       <div class="form-group mb-4">
										<label for="title">Union Type</label>
									
											<select class="form-control" name="union_type">
												<option <?php echo e(($postaudition->union_type == 1) ? 'selected' :' '); ?> value="1">Union</option>
												<option <?php echo e(($postaudition->union_type == 0) ? 'selected' :' '); ?> value="0">Non-Union</option>
											</select>
											<?php if($errors->has('union_type')): ?>
												<span class="text-danger"><?php echo e($errors->first('union_type')); ?></span>
											<?php endif; ?>
										
										
									</div> 
							   </div>
						<div class="col-md-6">
							<div class="form-group mb-4">
								<label for="career">Select Category</label>
								 
								<select class="form-control" id="category" name="category">
									
									<?php foreach($category as $crr){ ?>
										<?php
										  
                                          if($crr->id == $postaudition->category) {
                                          	echo '<option selected value="'.$crr->id.'">'.$crr->category_name.'</option>';
                                          } else {
                                          	echo '<option value="'.$crr->id.'">'.$crr->category_name.'</option>';
                                          }
										?>
									<?php } ?> 
								</select>
								
								<?php if($errors->has('category')): ?>
									<span class="text-danger"><?php echo e($errors->first('category')); ?></span>
								<?php endif; ?>
							</div>
						</div>

						<?php if(!empty($skills)){
                         
                         ?>
						<div class="col-md-6">
						   <div class="form-group mb-4">
								<label for="ethnicities">Ethnicites</label>
								<select class="form-control" id="ethnicities" name="ethnicities">
									
									<?php foreach($ethnicities as $ethnicitie){ ?>
										<?php
										  
                                          if($ethnicitie->name == $postaudition->ethnicities) {
                                          	echo '<option selected value="'.$ethnicitie->name.'">'.$ethnicitie->name.'</option>';
                                          } else {
                                          	echo '<option value="'.$ethnicitie->name.'">'.$ethnicitie->name.'</option>';
                                          }
										?>
									<?php } ?> 
								</select>
								
								<?php if($errors->has('ethnicities')): ?>
									<span class="text-danger"><?php echo e($errors->first('ethnicities')); ?></span>
								<?php endif; ?>
							</div>  
						</div>

					   <?php } ?>
					    <div class="col-md-6">
							       <div class="form-group mb-4">
										<label for="title">Compensation</label>
										<div>
											<input type="radio" <?php echo e(($postaudition->compensation == 1) ? 'checked' :' '); ?>  name="compensation" for="yes" value="1"> Yes
											<input type="radio" <?php echo e(($postaudition->compensation == 0) ? 'checked' :' '); ?> name="compensation" for="no" value="0"> No
											<?php if($errors->has('compensation')): ?>
												<span class="text-danger"><?php echo e($errors->first('compensation')); ?></span>
											<?php endif; ?>
										</div>
										
									</div> 
							   </div>
							    <div class="col-md-12">
							       <div class="form-group mb-4">
										<label for="title">Compensation Description</label>
										
										    <textarea  type="text" class="form-control" name="compensation_description" id="editor1"   rows="10" cols="80"> <?php echo e($postaudition->compensation_description); ?></textarea>
											
											<?php if($errors->has('compensation_description')): ?>
												<span class="text-danger"><?php echo e($errors->first('compensation_description')); ?></span>
											<?php endif; ?>
										
										
									</div> 
							   </div>

							   <div class="col-md-6">
							       <div class="form-group mb-4">
										<label for="date">Audition Date</label>
										<input type="date"  class="form-control" name="audition_date" id="audition_date" placeholder="Audition Date" value="<?php echo e(date('Y-m-d',strtotime($postaudition->expire_date))); ?>">
										<?php if($errors->has('audition_date')): ?>
											<span class="text-danger"><?php echo e($errors->first('audition_date')); ?></span>
										<?php endif; ?>
									</div> 
							   </div>
							   <div class="col-md-6">
							       <div class="form-group mb-4">
										<label for="title">Audition Time</label>
										<input type="time" class="form-control" name="audition_time" id="audition_time" placeholder="Audition Time" value="<?php echo e(date('H:i',strtotime($postaudition->expire_date))); ?>">
										<?php if($errors->has('audition_time')): ?>
											<span class="text-danger"><?php echo e($errors->first('audition_time')); ?></span>
										<?php endif; ?>
									</div> 
							   </div>
							   <div class="col-md-6">
							       <div class="form-group mb-4">
										<label for="location">Location</label>
										<input type="text" class="form-control" name="location" id="location" placeholder="Location"value="<?php echo e(old('location', isset($postaudition) ? $postaudition->location : '')); ?>">
										<?php if($errors->has('location')): ?>
											<span class="text-danger"><?php echo e($errors->first('location')); ?></span>
										<?php endif; ?>
									</div> 
							   </div>

					   <div class="col-md-12">
						   <div class="form-group mb-4">
								<label for="editor">Description</label>
								   <textarea  type="text" class="form-control" name="production_description" id="editor"   rows="10" cols="80"> <?php echo e($postaudition->production_description); ?></textarea>
				                     <?php if($errors->has('production_description')): ?>
				                     <span class="text-danger"><?php echo e($errors->first('production_description')); ?></span>
				                     <?php endif; ?>
							</div>
					   </div>	
					   <hr/>	
                       <div class="col-md-12">
					       <h5>Add Role</h5>	
					   </div>

						
						<div class="col-md-12">
							<button type="submit" class="btn btn-primary mt-3">Update</button>
						</div>
					</div>
					
				</form> 
			</div>
		</div>
	</div>

		
</div>
</div>

<script src="https://cdn.ckbox.io/ckbox/2.5.1/ckbox.js"></script>
<script type="importmap">
   {
       "imports": {
           "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.js",
           "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.0/",
           "ckeditor5-premium-features": "https://cdn.ckeditor.com/ckeditor5-premium-features/42.0.0/ckeditor5-premium-features.js",
           "ckeditor5-premium-features/": "https://cdn.ckeditor.com/ckeditor5-premium-features/42.0.0/"
       }
   }
</script>
<script type="module">
   // This sample still does not showcase all CKEditor 5 features (!)
   // Visit https://ckeditor.com/docs/ckeditor5/latest/features/index.html to browse all the features.
   import {
       ClassicEditor,
       Autoformat,
       Bold,
       Italic,
       Underline,
       BlockQuote,
       Base64UploadAdapter,
       CloudServices,
       CKBox,
       CKBoxImageEdit,
       Essentials,
       FindAndReplace,
       Font,
       Heading,
       Image,
       ImageCaption,
       ImageResize,
       ImageStyle,
       ImageToolbar,
       ImageUpload,
       PictureEditing,
       Indent,
       IndentBlock,
       Link,
       List,
       MediaEmbed,
       Mention,
       Paragraph,
       PasteFromOffice,
       SourceEditing,
       Table,
       TableColumnResize,
       TableToolbar,
       TextTransformation,
       HtmlEmbed,
       CodeBlock,
       RemoveFormat,
       Code,
       SpecialCharacters,
       HorizontalLine,
       PageBreak,
       TodoList,
       Strikethrough,
       Subscript,
       Superscript,
       Highlight,
       Alignment
   } from 'ckeditor5';
   
   import {
       ExportPdf,
       ExportWord
   } from 'ckeditor5-premium-features';
   
   ClassicEditor.create( document.querySelector( '#editor' ), {
       plugins: [
           Autoformat,
           BlockQuote,
           Bold,
           CloudServices,
           CKBox,
           Essentials,
           FindAndReplace,
           Font,
           Heading,
           Image,
           ImageCaption,
           ImageResize,
           ImageStyle,
           ImageToolbar,
           ImageUpload,
           Base64UploadAdapter,
           Indent,
           IndentBlock,
           Italic,
           Link,
           List,
           MediaEmbed,
           Mention,
           Paragraph,
           PasteFromOffice,
           PictureEditing,
           SourceEditing,
           Table,
           TableColumnResize,
           TableToolbar,
           TextTransformation,
           Underline,
           HtmlEmbed,
           CodeBlock,
           RemoveFormat,
           Code,
           SpecialCharacters,
           HorizontalLine,
           PageBreak,
           TodoList,
           Strikethrough,
           Subscript,
           Superscript,
           Highlight,
           Alignment,
           CKBoxImageEdit,
           ExportPdf,
           ExportWord
       ],
       toolbar: {
           items: [
               'undo', 'redo',
               '|',
               'sourceEditing',
               '|',
               'exportPDF','exportWord',
               '|',
               'findAndReplace', 'selectAll',
               '|',
               'heading',
               '|',
               'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor',
               '-',
               'bold', 'italic', 'underline',
               {
                   label: 'Formatting',
                   icon: 'text',
                   items: [ 'strikethrough', 'subscript', 'superscript', 'code', '|', 'removeFormat' ]
               },
               '|',
               'specialCharacters', 'horizontalLine', 'pageBreak',
               '|',
               'link', 'insertImage', 'ckbox', 'ckboxImageEdit', 'insertTable',
               {
                   label: 'Insert',
                   icon: 'plus',
                   items: [ 'highlight', 'blockQuote', 'mediaEmbed', 'codeBlock', 'htmlEmbed' ]
               },
               'alignment',
               '|',
               'bulletedList', 'numberedList', 'todoList',
               {
                   label: 'Indents',
                   icon: 'plus',
                   items: [ 'outdent', 'indent' ]
               }
           ],
           shouldNotGroupWhenFull: true
       },
       list: {
           properties: {
               styles: true,
               startIndex: true,
               reversed: true
           }
       },
       // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
       heading: {
           options: [
               { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
               { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
               { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
               { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
               { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
               { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
               { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
           ]
       },
       placeholder: 'Welcome to CKEditor 5 + CKBox!',
       image: {
           resizeOptions: [
               {
                   name: 'resizeImage:original',
                   label: 'Default image width',
                   value: null
               },
               {
                   name: 'resizeImage:50',
                   label: '50% page width',
                   value: '50'
               },
               {
                   name: 'resizeImage:75',
                   label: '75% page width',
                   value: '75'
               }
           ],
           toolbar: [
               'imageTextAlternative',
               'toggleImageCaption',
               '|',
               'imageStyle:inline',
               'imageStyle:wrapText',
               'imageStyle:breakText',
               '|',
               'resizeImage'
           ],
       },
       link: {
           addTargetToExternalLinks: true,
           defaultProtocol: 'https://'
       },
       table: {
           contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ],
       },
       ckbox: {
           // You need to provide your own token endpoint here
           // Sign up to CKBox to get one: https://ckeditor.com/ckbox/
           tokenUrl: 'https://api.ckbox.io/token/demo',
           theme: 'lark'
       }
   } )
   .then( ( editor ) => {
       window.editor = editor;
   } )
   .catch( ( error ) => {
       console.error( error.stack );
   } );
   
</script>



<?php $__env->stopSection(); ?>



<?php $__env->startSection('script'); ?>
  <script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places" type="text/javascript"></script>

	<script type="text/javascript">
        

function initialize() {
                       var input = document.getElementById('searchTextField');
                       var autocomplete = new google.maps.places.Autocomplete(input);
               }
               google.maps.event.addDomListener(window, 'load', initialize);
		$('#category').select2({
			placeholder: "Select Category",
		});

		$('#ethnicities').select2({
			placeholder: "Select Ethnicity",
		});
		

	</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/audition/edit.blade.php ENDPATH**/ ?>