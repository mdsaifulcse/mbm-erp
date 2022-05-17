<script src="{{ asset('js/app.js') }}"></script>
<script src="{{asset('assets/js/all.js')}}"></script>
<!-- jQuery Confirm alert -->
<script src="{{asset('assets/js/jquery-confirm/jquery-confirm.min.js')}}"></script>
<!-- toastr alert -->
<script src="{{asset('notification_assets/js/toastr.min.js')}}"></script>
<!-- sweet alert -->
<script src="{{asset('notification_assets/js/sweetalert.min.js')}}"></script>


<!-- BOOTSTRAP select -->
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script>
    var count = 0;
    var refreshIntervalId =setInterval(function(){
        count++;
        jQuery(document).ready(function() {
            clearInterval(refreshIntervalId);
            jQuery("#load").fadeOut();
            jQuery("#loading").fadeOut("");

        });
        if( count == 5){
            clearInterval(refreshIntervalId);
            jQuery("#load").fadeOut();
            jQuery("#loading").fadeOut("");
        }
    }, 300);
</script>
<script>
    var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
    let afterLoader = '<div class="loading-select left"><img src="{{ asset('images/loader.gif')}}" /></div>';
</script>
<!-- Custom JavaScript -->
@stack('js')
@toastr_render
<script>
    //

    $( document ).ajaxComplete(function() {
        // Required for Bootstrap tooltips in DataTables
        $('[data-toggle="tooltip"]').tooltip({
            "html": true,
            //"delay": {"show": 1000, "hide": 0},
        });
        $('[data-toggle="popover"]').popover({
            html: true,
        });
    });
    $('[data-toggle="popover"]').click(function () {
        $(this).popover('show');
    });


    $(document).ajaxError(function(event, jqxhr, settings, exception) {
        if (exception == 'Unauthorized') {
            $.notify("Your session has expired!", 'error');
            setTimeout(function(){
                window.location = '{{ url()->full() }}';
            }, 1000)
        }
    });
    let panelOptions = [];
    let Scrollbar = window.Scrollbar;
    if (jQuery('.col-panel-scroll').length) {
        Scrollbar.init(document.querySelector('.col-panel-scroll'), panelOptions);
    }
    let Scrollbar1 = window.Scrollbar;
    if (jQuery('.col-panel-scroll1').length) {
        Scrollbar1.init(document.querySelector('.col-panel-scroll1'), panelOptions);
    }
    let Scrollbar2 = window.Scrollbar;
    if (jQuery('.col-panel-scroll2').length) {
        Scrollbar2.init(document.querySelector('.col-panel-scroll2'), panelOptions);
    }
    // on first focus (bubbles up to document), open the menu
    $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
        $(this).closest(".select2-container").siblings('select:enabled').select2('open');
    });
    // steal focus during close - only capture once and stop propogation
    $('select.select2').on('select2:closing', function (e) {
        $(e.target).data("select2").$selection.one('focus focusin', function (e) {
            e.stopPropagation();
        });
    });

    //Notify using swal
    function notify(message,type) {
        swal({
            icon: type,
            text: message,
            button: false
        });
        setTimeout(()=>{
            swal.close();
    }, 1500);
    }
    //select 2
    $(document).ready(function() {
        $('.select2').select2();

        $(".select2-tags").select2({
          tags: true
        });
    });
</script>


<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>

<script>
    (function ($) {
        "use script";
        let ulIqMenu = document.querySelector('.iq-menu');
        let subIqMenu = ulIqMenu.querySelectorAll('ul');
        $.map(subIqMenu, function (item, key) {
            let subMenu = item.querySelectorAll('li');
            // console.log(subMenu)
            $.map(subMenu, function (element, index) {
                let browseUrl = window.location.href;
                if(browseUrl === element.childNodes[0].getAttribute('href')){
                    element.parentElement.classList.add('show')
                    element.parentElement.parentElement.classList.add('active')
                }
            })
        });
    })(jQuery);
</script>

<!-- Datetime picker -->
<script src="{{asset('plugins/air-datepicker/js/datepicker.min.js')}}"></script>

<script type="text/javascript">
    $('.air-datepicker').datepicker({
        language: 'en',
        dateFormat: 'dd-mm-yyyy',
        minDate:new Date(),
        autoClose: true,
        timepicker: true,
    });

     $('.search-datepicker').datepicker({
        language: 'en',
        dateFormat: 'dd-mm-yyyy',
        autoClose: true,
        timepicker: false,
    });



    const showPreloader = (value) => {
            if (value==='none') {
                document.getElementById("loading").setAttribute("style", "display:none");
            }else{
                document.getElementById("loading").setAttribute("style", "display:block");
            }
        };


    function markAsRead(id) {
      $.ajax({
            url: "{{ url('pms/requisition/mark-as-read') }}",
            type: 'POST',
            dataType: 'json',
            data: {_token: "{{ csrf_token() }}", id:id},
        })
        .done(function(response) {
            if(response.result=='success'){
                $('#read'+id).hide();
                notify(response.message,response.result);
            }
        })
        .fail(function(response){
            notify('Something went wrong!','error');
        });
        return false;
    }
</script>