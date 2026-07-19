<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<script src="{{asset('assets/js/libs/jquery-3.1.1.min.js')}}"></script>
<script src="{{asset('bootstrap/js/popper.min.js')}}"></script>
<script src="{{asset('bootstrap/js/bootstrap.min.js')}}"></script>

<script src="{{asset('plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('assets/js/app.js')}}"></script>
<script>
    $(document).ready(function() {
        App.init();
    });
</script>
<script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
<script src="{{asset('plugins/highlight/highlight.pack.js')}}"></script>
<script src="{{asset('assets/js/custom.js')}}"></script>

<script src="{{asset('plugins/table/datatable/datatables.js')}}"></script>
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
              "lengthMenu": [5, 10, 20, 50],
              "pageLength": 5 
          });

          multiCheck(c2);

      </script>

<!-- END GLOBAL MANDATORY SCRIPTS -->

<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
<?php $page_name = ''; ?>
@switch($page_name)
    @case('analytics')
      {{-- Dashboard --}}
      <script src="{{asset('plugins/apex/apexcharts.min.js')}}"></script>
      <script src="{{asset('assets/js/dashboard/dash_2.js')}}"></script>
      @break

    @case('sales')
      {{-- Dashboard 2 --}}
      <script src="{{asset('plugins/apex/apexcharts.min.js')}}"></script>
      <script src="{{asset('assets/js/dashboard/dash_1.js')}}"></script>
      @break

    @case('calendar')
      {{-- App Calendar --}}
      <script src="{{asset('plugins/fullcalendar/moment.min.js')}}"></script>
      <script src="{{asset('plugins/flatpickr/flatpickr.js')}}"></script>
      <script src="{{asset('plugins/fullcalendar/fullcalendar.min.js')}}"></script>
      <script src="{{asset('plugins/fullcalendar/custom-fullcalendar.advance.js')}}"></script>
      @break  

    
    @case('mailbox')
      {{-- App Mailbox --}}
      <script src="{{asset('assets/js/ie11fix/fn.fix-padStart.js')}}"></script>
      <script src="{{asset('plugins/editors/quill/quill.js')}}"></script>
      <script src="{{asset('plugins/sweetalerts/sweetalert2.min.js')}}"></script>
      <script src="{{asset('plugins/notification/snackbar/snackbar.min.js')}}"></script>
      <script src="{{asset('assets/js/apps/custom-mailbox.js')}}"></script>
      @break

    @case('notes')
      {{-- App Notes --}}
      <script src="{{asset('assets/js/ie11fix/fn.fix-padStart.js')}}"></script>
      <script src="{{asset('assets/js/apps/notes.js')}}"></script>
      @break
    
    @case('scrumboard')
      {{-- App Scrumboard --}}
      <script src="{{asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
      <script src="{{asset('assets/js/ie11fix/fn.fix-padStart.js')}}"></script>
      <script src="{{asset('assets/js/apps/scrumboard.js')}}"></script>
      @break

    @case('todo-list')
      {{-- App Todo List --}}
      <script src="{{asset('assets/js/ie11fix/fn.fix-padStart.js')}}"></script>
      <script src="{{asset('plugins/editors/quill/quill.js')}}"></script>
      <script src="{{asset('assets/js/apps/todoList.js')}}"></script>  
      @break

    @case('auth_boxed')
      {{-- Auth Lockscreen Boxed --}}
      <script src="{{asset('assets/js/authentication/form-2.js')}}"></script>
      @break
    
    @case('auth_default')
      {{-- Auth Lockscreen --}}
      <script src="{{asset('assets/js/authentication/form-1.js')}}"></script>
      @break

    @case('charts')
      {{-- Apex Chart --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/apex/apexcharts.min.js')}}"></script>
      <script src="{{asset('plugins/apex/custom-apexcharts.js')}}"></script>
      @break

    @case('accordions')
      {{-- Components Accordion --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('assets/js/components/ui-accordions.js')}}"></script>
      @break

    @case('blockui')
      {{-- Components Block UI --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/blockui/jquery.blockUI.min.js')}}"></script>
      <script src="{{asset('plugins/blockui/custom-blockui.js')}}"></script>
      @break

    @case('bootstrap_carousel')
      {{-- Components Bootstrap Carousel --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      @break

    @case('countdown')
      {{-- Components Countdown --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/countdown/jquery.countdown.min.js')}}"></script>
      <script src="{{asset('assets/js/components/custom-countdown.js')}}"></script>
      @break

    @case('counter')  
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/counter/jquery.countTo.js')}}"></script>
      <script src="{{asset('assets/js/components/custom-counter.js')}}"></script>
      @break

    @case('lightbox')
      {{-- Components Lightbox --}}
      <script src="{{asset('plugins/lightbox/photoswipe.min.js')}}"></script>
      <script src="{{asset('plugins/lightbox/photoswipe-ui-default.min.js')}}"></script>
      <script src="{{asset('plugins/lightbox/custom-photswipe.js')}}"></script>
      @break

    @case('modals')
      {{-- Components Modal --}}
      <script>
        $('#yt-video-link').click(function () {
            var src = 'https://www.youtube.com/embed/YE7VzlLtp-4';
            $('#videoMedia1').modal('show');
            $('<iframe>').attr({
                'src': src,
                'width': '560',
                'height': '315',
                'allow': 'encrypted-media'
            }).css('border', '0').appendTo('#videoMedia1 .video-container');
        });
        $('#vimeo-video-link').click(function () {
            var src = 'https://player.vimeo.com/video/1084537';
            $('#videoMedia2').modal('show');
            $('<iframe>').attr({
                'src': src,
                'width': '560',
                'height': '315',
                'allow': 'encrypted-media'
            }).css('border', '0').appendTo('#videoMedia2 .video-container');
        });
        $('#videoMedia1 button, #videoMedia2 button').click(function () {
            $('#videoMedia1 iframe, #videoMedia2 iframe').removeAttr('src');
        });
    </script>
    @break


    @case('pricing_table')
      {{-- Components Pricing Tables --}}
      <script>        
        var getInputStatus = document.getElementById('radio-6');
        var getPricingContainer = document.getElementsByClassName('pricing-plans-container')[0];
        var getYearlySwitch = document.getElementsByClassName('billed-yearly-radio')[0];
        getInputStatus.addEventListener('change', function() {
            var isChecked = getInputStatus.checked;
            if (isChecked) {
                getPricingContainer.classList.add("billed-yearly");
                getYearlySwitch.classList.add("billed-yearly-switch");
            } else {
                getYearlySwitch.classList.remove("billed-yearly-switch");
                getPricingContainer.classList.remove("billed-yearly");
            }
        })
      </script>
      @break

    @case('session_timeout')
      {{-- Compoentnts session timeout --}}
      <script src="{{asset('assets/js/components/session-timeout/bootstrap-session-timeout.js')}}"></script>
      <script src="{{asset('assets/js/components/session-timeout/custom-bootstrap_session_timeout.js')}}"></script>
      @break

    @case('notifications')
      {{-- Compoents Snackbar --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/notification/snackbar/snackbar.min.js')}}"></script>
      <script src="{{asset('assets/js/components/notification/custom-snackbar.js')}}"></script>
      <script>
          // Get the Toast button
          var toastButton = document.getElementById("toast-btn");
          // Get the Toast element
          var toastElement = document.getElementsByClassName("toast")[0];

          toastButton.onclick = function() {
              $('.toast').toast('show');
          }
      </script>
      @break

    @case('sweet_alerts')
      {{-- Components Sweetalerts --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/sweetalerts/sweetalert2.min.js')}}"></script>
      <script src="{{asset('plugins/sweetalerts/custom-sweetalert.js')}}"></script>
      @break

    @case('tabs')
      {{-- Components Tabs --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      @break

    @case('drag_n_drop')
      {{-- Drag and Drop ----> Dragula --}}
      <script src="{{asset('plugins/drag-and-drop/dragula/dragula.min.js')}}"></script>
      <script src="{{asset('plugins/drag-and-drop/dragula/custom-dragula.js')}}"></script>
      @break

    @case('badges')
      {{-- Elements Badges --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/tagInput/tags-input.js')}}"></script>
      <script>
          var instance = new TagsInput({
              selector: 'skill-input'
          });
          instance.addData(['PHP', 'Wordpress', 'Javascript', 'jQuery'])
      </script>      
      @break

    @case('popovers')
      {{-- Element popovers --}}
      <script src="{{asset('assets/js/elements/popovers.js')}}"></script>
      @break
    
    @case('search')
      {{-- Elements Serach --}}
      <script src="{{asset('assets/js/elements/custom-search.js')}}"></script>
      @break

    @case('tooltips')
      {{-- Elemnets Tooltips --}}
      <script src="{{asset('assets/js/elements/tooltip.js')}}"></script>
      @break

    @case('treeview')
      {{-- Elements Treeview --}}
      <script src="{{asset('plugins/treeview/custom-jstree.js')}}"></script>
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      @break

    @case('font_icons')
      <script src="{{asset('plugins/font-icons/feather/feather.min.js')}}"></script>
      <script type="text/javascript">
          feather.replace();
      </script>
      @break

    @case('maps')
      {{-- Maps Jvector --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/jvector/jquery-jvectormap-2.0.3.min.js')}}"></script>
      <script src="{{asset('plugins/jvector/africa/jquery-jvectormap-africa-en.js')}}"></script>
      <script src="{{asset('plugins/jvector/asia/jquery-jvectormap-asia-en.js')}}"></script>
      <script src="{{asset('plugins/jvector/continents/jquery-jvectormap-continents-en.js')}}"></script>
      <script src="{{asset('plugins/jvector/europe/jquery-jvectormap-europe-en.js')}}"></script>
      <script src="{{asset('plugins/jvector/north_america/jquery-jvectormap-north-america-en.js')}}"></script>
      <script src="{{asset('plugins/jvector/oceania/jquery-jvectormap-oceanina-en.js')}}"></script>
      <script src="{{asset('plugins/jvector/south-america/jquery-jvectormap-south-america-en.js')}}"></script>
      <script src="{{asset('plugins/jvector/worldmap_script/jquery-jvectormap-world-mill-en.js')}}"></script>
      <script src="{{asset('plugins/jvector/jvector_script.js')}}"></script>
      @break
    
    @case('coming_soon')
      {{-- Pages Coming Soon --}}
      <script src="{{asset('assets/js/pages/coming-soon/coming-soon.js')}}"></script>
      @break

    @case('faq')
      {{-- Pages FAQ --}}
      <script src="{{asset('assets/js/pages/faq/faq.js')}}"></script>
      @break

    @case('faq2')
      {{-- Pages FAQ2 --}}
      <script src="{{asset('assets/js/pages/faq/faq2.js')}}"></script>
      @break

    @case('helpdesk')
      {{-- Pages Helpdesk --}}
      <script src="{{asset('assets/js/pages/helpdesk.js')}}"></script>
      @break
    
    @case('privacy')
      {{-- Pages Privacy --}}
      <script>
          // Scroll To Top
          $(document).on('click', '.arrow', function(event) {
            event.preventDefault();
            var body = $("html, body");
            body.stop().animate({scrollTop:0}, 500, 'swing');
          });
      </script>
      @break

    @case('bootstrap_basic_table')
      {{-- Table Basic --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script>
          checkall('todoAll', 'todochkbox');
          $('[data-toggle="tooltip"]').tooltip()
      </script>
      @break

    @case('alternative_pagination')
      {{-- Table Datatable Alternative Pagination --}}
      <script src="{{asset('plugins/table/datatable/datatables.js')}}"></script>
      <script>
          $(document).ready(function() {
              $('#alter_pagination').DataTable( {
                  "pagingType": "full_numbers",
                  "oLanguage": {
                      "oPaginate": { 
                          "sFirst": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                          "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                          "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>',
                          "sLast": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>'
                      },
                      "sInfo": "Showing page _PAGE_ of _PAGES_",
                      "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                      "sSearchPlaceholder": "Search...",
                      "sLengthMenu": "Results :  _MENU_",
                  },
                  "stripeClasses": [],
                  "lengthMenu": [7, 10, 20, 50],
                  "pageLength": 7 
              });
          } );
      </script>
      @break

    @case('basic-light')
      {{-- Table Datatable Basic Light --}}
      <script src="{{asset('plugins/table/datatable/datatables.js')}}"></script>
      <script>
          $('#zero-config').DataTable({
              "oLanguage": {
                  "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                  "sInfo": "Showing page _PAGE_ of _PAGES_",
                  "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                  "sSearchPlaceholder": "Search...",
                  "sLengthMenu": "Results :  _MENU_",
              },
              "stripeClasses": [],
              "lengthMenu": [7, 10, 20, 50],
              "pageLength": 7 
          });
      </script>
      @break

    @case('basic')
      {{-- Tables Datatable Basic --}}
      <script src="{{asset('plugins/table/datatable/datatables.js')}}"></script>
      <script>
          $('#zero-config').DataTable({
              "oLanguage": {
                  "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                  "sInfo": "Showing page _PAGE_ of _PAGES_",
                  "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                  "sSearchPlaceholder": "Search...",
                  "sLengthMenu": "Results :  _MENU_",
              },
              "stripeClasses": [],
              "lengthMenu": [7, 10, 20, 50],
              "pageLength": 7 
          });
      </script>
      @break

    @case('custom')
      {{-- Table Datatable Custom --}}
      <script src="{{asset('plugins/table/datatable/datatables.js')}}"></script>
      <script>
          // var e;
          c1 = $('#style-1').DataTable({
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
              "lengthMenu": [5, 10, 20, 50],
              "pageLength": 5
          });

          multiCheck(c1);

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
              "lengthMenu": [5, 10, 20, 50],
              "pageLength": 5 
          });

          multiCheck(c2);

          c3 = $('#style-3').DataTable({
              "oLanguage": {
                  "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                  "sInfo": "Showing page _PAGE_ of _PAGES_",
                  "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                  "sSearchPlaceholder": "Search...",
                  "sLengthMenu": "Results :  _MENU_",
              },
              "stripeClasses": [],
              "lengthMenu": [5, 10, 20, 50],
              "pageLength": 5
          });

          multiCheck(c3);
      </script>
      @break

      @case('users')
      {{-- Table Datatable Custom --}}
      
      @break
      
    @case('html5')
      {{-- Table Datatable HTML5 --}}
      <script src="{{asset('plugins/table/datatable/datatables.js')}}"></script>
      <script src="{{asset('plugins/table/datatable/button-ext/dataTables.buttons.min.js')}}"></script>
      <script src="{{asset('plugins/table/datatable/button-ext/jszip.min.js')}}"></script>    
      <script src="{{asset('plugins/table/datatable/button-ext/buttons.html5.min.js')}}"></script>
      <script src="{{asset('plugins/table/datatable/button-ext/buttons.print.min.js')}}"></script>
      <script>
          $('#html5-extension').DataTable( {
              dom: '<"row"<"col-md-12"<"row"<"col-md-6"B><"col-md-6"f> > ><"col-md-12"rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>> >',
              buttons: {
                  buttons: [
                      { extend: 'copy', className: 'btn' },
                      { extend: 'csv', className: 'btn' },
                      { extend: 'excel', className: 'btn' },
                      { extend: 'print', className: 'btn' }
                  ]
              },
              "oLanguage": {
                  "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                  "sInfo": "Showing page _PAGE_ of _PAGES_",
                  "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                  "sSearchPlaceholder": "Search...",
                  "sLengthMenu": "Results :  _MENU_",
              },
              "stripeClasses": [],
              "lengthMenu": [7, 10, 20, 50],
              "pageLength": 7 
          } );
      </script>
      @break


    @case('account_settings')
      {{-- User Account Setting  --}}
      <script src="{{asset('plugins/dropify/dropify.min.js')}}"></script>
      <script src="{{asset('plugins/blockui/jquery.blockUI.min.js')}}"></script>
      <script src="{{asset('assets/js/users/account-settings.js')}}"></script>
      @break


    @case('widgets')
      {{-- Widgets --}}
      <script src="{{asset('plugins/apex/apexcharts.min.js')}}"></script>
      <script src="{{asset('assets/js/widgets/modules-widgets.js')}}"></script>
      @break

    @case('bootstrap_select')
      {{-- Forms Bootstrap Select --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/bootstrap-select/bootstrap-select.min.js')}}"></script>
      @break

    @case('touchspin')
      {{-- Forms Touchspin --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js')}}"></script>
      <script src="{{asset('plugins/bootstrap-touchspin/custom-bootstrap-touchspin.js')}}"></script>
      @break

    @case('clipboard')
      {{-- Forms clipboard --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('assets/js/clipboard/clipboard.min.js')}}"></script>
      <script src="{{asset('assets/js/forms/custom-clipboard.js')}}"></script>
      @break
      
    @case('date_range_picker')
      {{-- Forms Date Range Picker --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/flatpickr/flatpickr.js')}}"></script>
      <script src="{{asset('plugins/noUiSlider/nouislider.min.js')}}"></script>
      <script src="{{asset('plugins/flatpickr/custom-flatpickr.js')}}"></script>
      <script src="{{asset('plugins/noUiSlider/custom-nouiSlider.js')}}"></script>
      <script src="{{asset('plugins/bootstrap-range-Slider/bootstrap-rangeSlider.js')}}"></script>      
      @break
    
    @case('file_upload')
      {{-- Forms File Upload --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/file-upload/file-upload-with-preview.min.js')}}"></script>
      <script>
          //First upload
          var firstUpload = new FileUploadWithPreview('myFirstImage')
          //Second upload
          var secondUpload = new FileUploadWithPreview('mySecondImage')
      </script>
      @break

    @case('input_mask')
      {{-- Forms Input Mask --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/input-mask/jquery.inputmask.bundle.min.js')}}"></script>
      <script src="{{asset('plugins/input-mask/input-mask.js')}}"></script>
      @break

    @case('maxlength')
      {{-- Forms Maxlength --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
      <script src="{{asset('plugins/bootstrap-maxlength/custom-bs-maxlength.js')}}"></script>
      @break

    @case('markdown')
      {{-- Forms Markdown --}}
      <script src="{{asset('plugins/editors/markdown/simplemde.min.js')}}"></script>
      <script src="{{asset('plugins/editors/markdown/custom-markdown.js')}}"></script>
      @break

    @case('quill')
      {{-- Forms Quill --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/editors/quill/quill.js')}}"></script>
      <script src="{{asset('plugins/editors/quill/custom-quill.js')}}"></script>
      @break

    @case('select2')
      {{-- Forms Select2 --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/select2/select2.min.js')}}"></script>
      <script src="{{asset('plugins/select2/custom-select2.js')}}"></script>
      @break

    @case('typeahead')
      {{-- Forms Typeahead --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/autocomplete/jquery.mockjax.js')}}"></script>
      <script src="{{asset('plugins/autocomplete/jquery.autocomplete.js')}}"></script>
      <script src="{{asset('plugins/autocomplete/countries.js')}}"></script>
      <script src="{{asset('plugins/autocomplete/demo.js')}}"></script>
      @break

    @case('validation')
      {{-- Forms Validation --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('assets/js/forms/bootstrap_validation/bs_validation_script.js')}}"></script>
      @break
    
    @case('wizards')
      {{-- Forms Wizards --}}
      <script src="{{asset('assets/js/scrollspyNav.js')}}"></script>
      <script src="{{asset('plugins/jquery-step/jquery.steps.min.js')}}"></script>
      <script src="{{asset('plugins/jquery-step/custom-jquery.steps.js')}}"></script>
      @break

    @default
    <script>console.log('No custom script available.')</script>
@endswitch
<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->