<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<script src="{{asset('public/assets/admin/js/libs/jquery-3.1.1.min.js')}}"></script>
<script src="{{asset('public/bootstrap/js/popper.min.js')}}"></script>
<script src="{{asset('public/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{asset('public/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('public/assets/admin/js/app.js')}}"></script>
<script>
    $(document).ready(function() {
        App.init();
    });
</script>
<script src="{{asset('public/assets/admin/js/scrollspyNav.js')}}"></script>
<script src="{{asset('public/plugins/highlight/highlight.pack.js')}}"></script>
<script src="{{asset('public/assets/admin/js/custom.js')}}"></script>

<script src="{{asset('public/plugins/table/datatable/datatables.js')}}"></script>
      <script>
          c2 = $('#style-2').DataTable({
              headerCallback:function(e, a, t, n, s) {
                  e.getElementsByTagName("th")[0].innerHTML='<label class="new-control new-checkbox checkbox-outline-info m-auto">\n<input type="checkbox" class="new-control-input chk-parent select-customers-info" id="customer-all-info">\n<span class="new-control-indicator"></span><span style="visibility:hidden">c</span>\n</label>'
              },
              columnDefs:[ {
                  targets:0, width:"30px", className:"", orderable:!1, render:function(e, a, t, n) {
                      return'<label class="new-control new-checkbox checkbox-outline-info  m-auto">\n<input type="checkbox" class="new-control-input child-chk select-customers-info" id="customer-all-info">\n<span class="new-control-indicator"></span><span style="visibility:hidden">c</span>\n</label>'
                  }
              }],
              "oLanguage": {
                  "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                  "sInfo": "Showing page _PAGE_ of _PAGES_",
                  "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                  "sSearchPlaceholder": "Search...",
                  "sLengthMenu": "Results :  _MENU_",
              },
              "lengthMenu": [10, 20, 50],
              "pageLength": 10 
          });

          multiCheck(c2);
		  
		  
		  $(document).on("click",".pintop", function() {
			if($(this).is(':checked')){
				var pin_to_top = 1;
			}else{
				var pin_to_top = 0;
			}
			
			var postAudId = $(this).attr('data-id'); 
			var urldata = '{{url("admin/audition-post-audition")}}';
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'POST',
				dataType: "json",
				url: urldata, 
				data: {'pin_to_top': pin_to_top,'post_aud_id': postAudId},
				success: function(result){ 
					  location.reload(1);
				}
			});  
		});
		
		
		$(document).on("click",".pintopcrew", function() {
			if($(this).is(':checked')){
				var pin_to_top = 1;
			}else{
				var pin_to_top = 0;
			}
			
			var postAudId = $(this).attr('data-id'); 
			var urldata = '{{url("admin/production-post-crew")}}';
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'POST',
				dataType: "json",
				url: urldata, 
				data: {'pin_to_top': pin_to_top,'post_aud_id': postAudId},
				success: function(result){ 
					  location.reload(1);
				}
			});  
		});
		
		
		//update user status
		$(document).on("click",".updatestatus", function() {
			if($(this).is(':checked')){
				var status = 1;
			}else{
				var status = 0;
			}
			
			var uId = $(this).attr('data-id'); 
			var urldata = '{{url("admin/update-user-status")}}';
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'POST',
				dataType: "json",
				url: urldata, 
				data: {'status': status,'uId': uId},
				success: function(result){ 
					  location.reload(1);
				}
			});   
		});
		
		//update audition status
		$(document).on("click",".updateauditionstatus", function() {
			if($(this).is(':checked')){
				var status = 1;
			}else{
				var status = 0;
			}
			
			var Id = $(this).attr('data-id'); 
			var urldata = '{{url("admin/update-audition-status")}}';
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'POST',
				dataType: "json",
				url: urldata, 
				data: {'status': status,'Id': Id},
				success: function(result){ 
					  location.reload(1);
				}
			});  
		});

		//update animal audition status
		$(document).on("click",".updateanimalauditionstatus", function() {
			if($(this).is(':checked')){
				var status = 1;
			}else{
				var status = 0;
			}

			var Id = $(this).attr('data-id');
			var urldata = '{{url("admin/update-animal-audition-status")}}';
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'POST',
				dataType: "json",
				url: urldata,
				data: {'status': status,'Id': Id},
				success: function(result){
					  location.reload(1);
				}
			});
		});
		
		//update audition crew status
		$(document).on("click",".updatecrewstatus", function() {
			if($(this).is(':checked')){
				var status = 1;
			}else{
				var status = 0;
			}
			
			var Id = $(this).attr('data-id'); 
			var urldata = '{{url("admin/update-crew-status")}}';
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: 'POST',
				dataType: "json",
				url: urldata, 
				data: {'status': status,'Id': Id},
				success: function(result){ 
					  location.reload(1);
				}
			});  
		});
		
		
		$(document).on('click', '.suspendBtn', function () {
            var id = $(this).attr('data-id'); 
            $('#uidval').val(id);
            $('#succsmsg').text('');
            $('#modalLoginForm').modal('show');
        });
        
        $(document).on('click', '.suspendSubmit', function () {
            $(this).text('Submitting...');
	        $(this).prop('disabled', true);
	
            var urldata = '{{url("admin/usersuspend")}}';
			$.ajax({ 
				type: 'POST',
				dataType: "json",
				url: urldata, 
				data:$('#suspendForm').serialize(),
				success: function(result){ 
					  if(result.status == '200'){ 
					      $('.suspendSubmit').text('Submit');
			              $('.suspendSubmit').prop('disabled', false);
					      $('#modalLoginForm').modal('hide');
					      location.reload(1);
					  }else{
					      $('.suspendSubmit').text('Submit');
			              $('.suspendSubmit').prop('disabled', false);
			              $('#succsmsg').text();
					  }
				}
			}); 
        });
		
		
		 
		$(document).ready(function() {
			var showChar = 100;
			var ellipsestext = "...";
			var moretext = "more";
			var lesstext = "less";
			$('.more').each(function() {
				var content = $(this).html();

				if(content.length > showChar) {

					var c = content.substr(0, showChar);
					var h = content.substr(showChar-1, content.length - showChar);

					var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

					$(this).html(html);
				}

			});

			$(".morelink").click(function(){
				if($(this).hasClass("less")) {
					$(this).removeClass("less");
					$(this).html(moretext);
				} else {
					$(this).addClass("less");
					$(this).html(lesstext);
				}  
				$(this).parent().prev().toggle();
				$(this).prev().toggle();
				return false;
			});
		});
        jQuery(".audition-drop-part").click(function(){
  jQuery(".audition-show-part").toggle("slow");
});
      jQuery(".production-drop-part").click(function(){
  jQuery(".production-show-part").toggle("slow");
});
jQuery(".app-drop-part").click(function(){
  jQuery(".app-show-part").toggle("slow");
});
jQuery(".store-drop-part").click(function(){
  jQuery(".store-show-part").toggle("slow");
});
jQuery(".announcemet-drop-part").click(function(){
  jQuery(".announcemet-show-part").toggle("slow");
});

// Gallery image hover
$(".img-wrapper").hover(
  function () {
    $(this).find(".img-overlay").animate({ opacity: 1 }, 600);
  },
  function () {
    $(this).find(".img-overlay").animate({ opacity: 0 }, 600);
  }
);

// Lightbox
var $overlay = $('<div id="overlay"></div>');
var $image = $("<img>");
var $prevButton = $(
  '<div id="prevButton"><i class="fa fa-chevron-left"></i></div>'
);
var $nextButton = $(
  '<div id="nextButton"><i class="fa fa-chevron-right"></i></div>'
);
var $exitButton = $('<div id="exitButton"><i class="fa fa-times"></i></div>');

// Add overlay
$overlay
  .append($image)
  .prepend($prevButton)
  .append($nextButton)
  .append($exitButton);
$("#gallery").append($overlay);

// Hide overlay on default
$overlay.hide();

// When an image is clicked
$(".img-overlay").click(function (event) {
  // Prevents default behavior
  event.preventDefault();
  // Adds href attribute to variable
  var imageLocation = $(this).prev().attr("href");
  // Add the image src to $image
  $image.attr("src", imageLocation);
  // Fade in the overlay
  $overlay.fadeIn("slow");
});

// When the overlay is clicked
$overlay.click(function () {
  // Fade out the overlay
  $(this).fadeOut("slow");
});

// When next button is clicked
$nextButton.click(function (event) {
  // Hide the current image
  $("#overlay img").hide();
  // Overlay image location
  var $currentImgSrc = $("#overlay img").attr("src");
  // Image with matching location of the overlay image
  var $currentImg = $('#image-gallery img[src="' + $currentImgSrc + '"]');
  // Finds the next image
  var $nextImg = $($currentImg.closest(".image").next().find("img"));
  // All of the images in the gallery
  var $images = $("#image-gallery img");
  // If there is a next image
  if ($nextImg.length > 0) {
    // Fade in the next image
    $("#overlay img").attr("src", $nextImg.attr("src")).fadeIn(800);
  } else {
    // Otherwise fade in the first image
    $("#overlay img").attr("src", $($images[0]).attr("src")).fadeIn(800);
  }
  // Prevents overlay from being hidden
  event.stopPropagation();
});

// When previous button is clicked
$prevButton.click(function (event) {
  // Hide the current image
  $("#overlay img").hide();
  // Overlay image location
  var $currentImgSrc = $("#overlay img").attr("src");
  // Image with matching location of the overlay image
  var $currentImg = $('#image-gallery img[src="' + $currentImgSrc + '"]');
  // Finds the next image
  var $nextImg = $($currentImg.closest(".image").prev().find("img"));
  // Fade in the next image
  $("#overlay img").attr("src", $nextImg.attr("src")).fadeIn(800);
  // Prevents overlay from being hidden
  event.stopPropagation();
});

// When the exit button is clicked
$exitButton.click(function () {
  // Fade out the overlay
  $("#overlay").fadeOut("slow");
});

      </script>


<!-- END GLOBAL MANDATORY SCRIPTS -->
