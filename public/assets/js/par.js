function closetoast() {
    $('#panel_tambah').removeClass('offcanvas-on');
    $('#overlay').removeClass('offcanvas-overlay');
    $('#formmodal').modal('hide');
}


var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-center",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
document.getElementById("node-id").innerHTML = (`<div class="modal fade" id="modal_notifikasi" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:60%">
        <div class="modal-content">
            <div class="modal-header border-0">
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="____data_content">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>`);



function detail_notif(id) {
    $('#modal_notifikasi').modal('show');
    console.log(id);
    $.post(BASE_URL + '/master/getdataildata_modal', {
        id: id
    }, function (data) {
        $('#____data_content').html(data);
    });
    // console.log('testing');
}

jQuery(document).ready(function () {

    $('.popover-markup > .trigger').popover({
        html : true,
        title: function() {
          return $(this).parent().find('.head').html();
        },
        content: function() {
          return $(this).parent().find('.content').html();
        },
        container: 'body',
        placement: 'bottom',
        trigger: 'hover'
    });

    $('#logout').on('click', function () {
        Swal.fire({
            title: 'confirm',
            text: 'Anda akan keluar dari halaman aplikasi?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE_URL + "/logout",
                    method: "post",
                    chace: false,
                    success: function (data) {
                        window.location.href = BASE_URL + '/login';
                    },
                    error: function (data) { }
                });
            }
        })
    });
    // setactive data menu
    var url = window.location;
    // var urlpath = url.split("/");

    // console.log(urlpath);

    $('ul.menu-nav a').filter(function () {
        return this.href != url;
    }).parent().removeClass('menu-item-active menu-item-open menu-subnav li');
    // menu-item menu-item-submenu menu-item-open
    $('ul.menu-nav a').filter(function () {
        return this.href == url;
    }).parent().addClass('menu-item-active menu-item-open');

    // console.log($('li.menu-item a menu-item-submenu .menu-submenu menu-subnav li a').filter(function () {
    //     return this.href == url;
    // }));
    // for treeview

    $('ul.menu-subnav a').filter(function () {
        return this.href == url;
    }).parentsUntil(".menu-nav > .menu-subnav").addClass('menu-item-active menu-item-open');

    $(window).on("load", function () {
        var activated_menu = $('menu-item-active menu-item-open')[0];
        if (activated_menu) {
            activated_menu.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
    function scrollTampil(elem) {
        elem.scrollIntoView({
            behavior: 'smooth'
        });
    }

    $('ul.menu-nav').on('expanded.tree', function (e) {
        e.stopImmediatePropagation();
        setTimeout(scrollTampil($('li.menu-item-active menu-item-open')[0]), 500);
    });

    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 100) {
            $(".scrollToTop").fadeIn();
        } else {
            $(".scrollToTop").fadeOut();
        }
    });

    $(".scrollToTop").on('click', function (e) {
        $("html, body").animate({
            scrollTop: 0
        }, 500);
        return false;
    });
    $('.load_nitification').html('Loading ...');


    // get deatail notification 
    $('.topbar-item').on('click', function () {
        $.post(BASE_URL + '/master/notifikasi_data', {
        }, function (data) {
            $('._____notifikasi').html(data);
        });
        $('.load_nitification').load(BASE_URL + '/master/notif');
    });


});

function preloaderFadeOutInit() {
    $('.preloader').fadeOut('slow');
    // $('body').attr('id', '');
}
// Window load function
jQuery(window).on('load', function () {
    (function ($) {
        preloaderFadeOutInit();
    })(jQuery);
});


// 


jQuery(document).ready(function () {
    $('.name_pengajuan_history').html(` 
    <div class="card card-custom card-stretch gutter-b">
        <!--begin::Header-->
        <div class="card-header align-items-center border-0 mt-4">
            <h3 class="card-title align-items-start flex-column">
                <span class="font-weight-bolder text-dark"> History Pengajuan data kpi </span>
                <span class="text-muted mt-3 font-weight-bold font-size-sm">890,344 Sales</span>
            </h3>
            <div class="card-toolbar">
                <div class="dropdown dropdown-inline">
                    <a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ki ki-bold-more-hor"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                        <!--begin::Navigation-->
                        <ul class="navi navi-hover">
                            <li class="navi-header font-weight-bold py-4">
                                <span class="font-size-lg">Choose Label:</span>
                                <i class="flaticon2-information icon-md text-muted" data-toggle="tooltip"
                                    data-placement="right" title="Click to learn more..."></i>
                            </li>
                            <li class="navi-separator mb-3 opacity-70"></li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span
                                            class="label label-xl label-inline label-light-success">Customer</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span
                                            class="label label-xl label-inline label-light-danger">Partner</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span
                                            class="label label-xl label-inline label-light-warning">Suplier</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span
                                            class="label label-xl label-inline label-light-primary">Member</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span class="label label-xl label-inline label-light-dark">Staff</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-separator mt-3 opacity-70"></li>
                            <li class="navi-footer py-4">
                                <a class="btn btn-clean font-weight-bold btn-sm" href="#">
                                    <i class="ki ki-plus icon-sm"></i>Add new</a>
                            </li>
                        </ul>
                        <!--end::Navigation-->
                    </div>
                </div>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body pt-4">
            <!--begin::Timeline-->
            <div class="timeline timeline-6 mt-3">
                <!--begin::Item-->
                <div class="timeline-item align-items-start">
                    <!--begin::Label-->
                    <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">08:42</div>
                    <!--end::Label-->
                    <!--begin::Badge-->
                    <div class="timeline-badge">
                        <i class="fa fa-genderless text-warning icon-xl"></i>
                    </div>
                    <!--end::Badge-->
                    <!--begin::Text-->
                    <div class="font-weight-mormal font-size-lg timeline-content text-muted pl-3">Outlines keep
                        you
                        honest. And keep structure</div>
                    <!--end::Text-->
                </div>
                <!--end::Item-->
                <!--begin::Item-->
                <div class="timeline-item align-items-start">
                    <!--begin::Label-->
                    <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">10:00</div>
                    <!--end::Label-->
                    <!--begin::Badge-->
                    <div class="timeline-badge">
                        <i class="fa fa-genderless text-success icon-xl"></i>
                    </div>
                    <!--end::Badge-->
                    <!--begin::Content-->
                    <div class="timeline-content d-flex">
                        <span class="font-weight-bolder text-dark-75 pl-3 font-size-lg">AEOL meeting</span>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Item-->
                <!--begin::Item-->
                <div class="timeline-item align-items-start">
                    <!--begin::Label-->
                    <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">14:37</div>
                    <!--end::Label-->
                    <!--begin::Badge-->
                    <div class="timeline-badge">
                        <i class="fa fa-genderless text-danger icon-xl"></i>
                    </div>
                    <!--end::Badge-->
                    <!--begin::Desc-->
                    <div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">Make
                        deposit
                        <a href="#" class="text-primary">USD 700</a>. to ESL
                    </div>
                    <!--end::Desc-->
                </div>
                <!--end::Item-->
                <!--begin::Item-->
                <div class="timeline-item align-items-start">
                    <!--begin::Label-->
                    <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">16:50</div>
                    <!--end::Label-->
                    <!--begin::Badge-->
                    <div class="timeline-badge">
                        <i class="fa fa-genderless text-primary icon-xl"></i>
                    </div>
                    <!--end::Badge-->
                    <!--begin::Text-->
                    <div class="timeline-content font-weight-mormal font-size-lg text-muted pl-3">Indulging in
                        poorly driving and keep structure keep great</div>
                    <!--end::Text-->
                </div>
                <!--end::Item-->
                <!--begin::Item-->
                <div class="timeline-item align-items-start">
                    <!--begin::Label-->
                    <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">21:03</div>
                    <!--end::Label-->
                    <!--begin::Badge-->
                    <div class="timeline-badge">
                        <i class="fa fa-genderless text-danger icon-xl"></i>
                    </div>
                    <!--end::Badge-->
                    <!--begin::Desc-->
                    <div class="timeline-content font-weight-bolder text-dark-75 pl-3 font-size-lg">New order
                        placed
                        <a href="#" class="text-primary">#XF-2356</a>.
                    </div>
                    <!--end::Desc-->
                </div>
                <!--end::Item-->
                <!--begin::Item-->
                <div class="timeline-item align-items-start">
                    <!--begin::Label-->
                    <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">23:07</div>
                    <!--end::Label-->
                    <!--begin::Badge-->
                    <div class="timeline-badge">
                        <i class="fa fa-genderless text-info icon-xl"></i>
                    </div>
                    <!--end::Badge-->
                    <!--begin::Text-->
                    <div class="timeline-content font-weight-mormal font-size-lg text-muted pl-3">Outlines keep
                        and
                        you honest. Indulging in poorly driving</div>
                    <!--end::Text-->
                </div>
                <!--end::Item-->
                <!--begin::Item-->
                <div class="timeline-item align-items-start">
                    <!--begin::Label-->
                    <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">16:50</div>
                    <!--end::Label-->
                    <!--begin::Badge-->
                    <div class="timeline-badge">
                        <i class="fa fa-genderless text-primary icon-xl"></i>
                    </div>
                    <!--end::Badge-->
                    <!--begin::Text-->
                    <div class="timeline-content font-weight-mormal font-size-lg text-muted pl-3">Indulging in
                        poorly driving and keep structure keep great</div>
                    <!--end::Text-->
                </div>
                <!--end::Item-->
                <!--begin::Item-->
                <div class="timeline-item align-items-start">
                    <!--begin::Label-->
                    <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">21:03</div>
                    <!--end::Label-->
                    <!--begin::Badge-->
                    <div class="timeline-badge">
                        <i class="fa fa-genderless text-danger icon-xl"></i>
                    </div>
                    <!--end::Badge-->
                    <!--begin::Desc-->
                    <div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">New order
                        placed
                        <a href="#" class="text-primary">#XF-2356</a>.
                    </div>
                    <!--end::Desc-->
                </div>
                <!--end::Item-->
            </div>
            <!--end::Timeline-->
        </div>
        <!--end: Card Body-->
    </div>
     `);


});


