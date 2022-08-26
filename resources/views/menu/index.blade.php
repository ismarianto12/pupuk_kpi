@extends('layouts.template')
@section('content')
@section('title', 'manajemen modul aplikasi')
@section('toolbars')
    @include('layouts.toolbars', [
        'url' => Url('pegawai/create'),
        'par' => '<i class="fa fa-copy"></i> All Data',
        'label' => '#',
        'ajax_button' => '',
    ])
@endsection

<script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
<link href="{{ asset('assets') }}/css/menu.css" rel="stylesheet">
<script src="{{ asset('assets/js/plugin/jquery.nestable.js') }}"></script>



<div class="row">

    <div class="ket"></div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">

                <div class="ibox">
                    <div class="ibox-content">
                        <table class="table table-striped">
                            <tr>
                                <td><input class='form-control' id="label" type="text" placeholder="Nama Menu"
                                        required><br /><button class="btn btn-success" id="label_cr"><i
                                            class="fa fa-search"></i>Cari Menu.</button></td>
                            </tr>
                            <tr>
                                <td><input class='form-control link' type="text" id="link"
                                        placeholder="example.com" autocomplete='off' required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div id="icon">
                                        <br /> <br />
                                        <a data-toggle="modal" class="btn btn-primary btn-sm" href="#modal-form">Pilih
                                            Icon
                                            Menu</a>
                                    </div>
                                    <br /> <br />

                                    <input class="form-control" name="fa_icon" id="fa_icon" placeholder="...data" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h4>Level Akses</h4>
                                    <br />
                                    @php
                                    $level = [1 => 'admin', 2 => 'user']; @endphp
                                    @foreach ($level as $ll => $s)
                                        <div class="form-group">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <div id="level_akses_otp"></div>

                                                <input id="checkbox{{ $s }}" type="checkbox" name="checkbox"
                                                    value="{{ $ll }}" class="form-check-input widget-9-check">
                                                <label for="checkbox{{ $s }}">{{ ucfirst($s) }}</label>

                                            </div>
                                        </div>
                                    @endforeach

                                </td>
                            </tr>
                            <tr>
                                <td><button class='btn btn-sm btn-success' id="submit">Submit</button> <button
                                        class='btn btn-sm btn-default' id="reset">Reset</button></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <input type="hidden" id="id">
                <div class="ibox-content">

                    <div class="dd" id="nestable">
                        @php
                            $ref = [];
                            $items = [];
                            foreach ($record as $data) {
                                $thisRef = &$ref[$data->id_menu];
                                $thisRef['id_parent'] = $data->id_parent;
                                $thisRef['icon'] = $data->icon;
                                $thisRef['level'] = $data->level;
                                $thisRef['nama_menu'] = $data->nama_menu;
                                $thisRef['link'] = $data->link;
                                $thisRef['id_menu'] = $data->id_menu;
                                $thisRef['position'] = $data->position;
                            
                                if ($data->id_parent == 0) {
                                    $items[$data->id_menu] = &$thisRef;
                                } else {
                                    $ref[$data->id_parent]['child'][$data->id_menu] = &$thisRef;
                                }
                            }
                            echo Properti_app::get_menu($items);
                        @endphp

                    </div>
                </div>
                <input type="hidden" id="nestable-output">

            </div>
        </div>
    </div>
</div>



{{--  --}}


<script>
    jQuery(document).ready(function() {
        $('.tampil').html('<br /><div class="callout callout-danger">Icon Menu</div>');
        $('.icon_r').click(function() {
            var id = $(this).val();
            $('.modal').modal('hide');
            $('.tampil').html(
                '<div class="control-group"><div class="radio"><label><input id="fa_icon" type="radio" value="fa ' +
                id + '" checked><span class="text"><i class="fa ' + id + '"></i>' + id +
                '</span></label></div></div>');

        });
    });

    jQuery(document).ready(function() {
        var updateOutput = function(e) {
            var list = e.length ? e : $(e.target),
                output = list.data('output');
            if (window.JSON) {
                output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
            } else {
                output.val('JSON browser support required for this demo.');
            }
        };

        // activate Nestable for list 1
        $('#nestable').nestable({
                group: 1
            })
            .on('change', updateOutput);

        // output initial serialised data
        updateOutput($('#nestable').data('output', $('#nestable-output')));
        $('#nestable-menu').on('click', function(e) {
            var target = $(e.target),
                action = target.data('action');
            if (action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        });
    });

    function kosong() {
        $("input[name='checkbox']").val('');
        $('.tampil').html('<br /><div class="callout callout-danger">Icon Menu</div>');
    }
</script>

<script>
    jQuery(document).ready(function() {
        $("#load").hide();
        $("#submit").click(function() {
            if ($("#fa_icon").val() == '') {
                alert('Semua form isian wajib di isi');
            } else {

                $("#load").show();
                var levels = [];
                $.each($("input[name='checkbox']:checked"), function() {
                    levels.push($(this).val());
                });
                var selected_values = levels.join(".");
                var dataString = {
                    level: selected_values,
                    icon: $("#fa_icon").val(),
                    label: $("#label").val(),
                    link: $("#link").val(),
                    id: $("#id").val()
                };

                $.ajax({
                    type: "POST",
                    url: "{{ route('setting.menu.store') }}",
                    data: dataString,
                    dataType: "json",
                    cache: false,
                    success: function(data) {
                        if (data.type == 'add') {
                            $("#menu-id").append(data.menu);
                            // window.location.href = '{{ asset('setting/menu?save=sc') }}';
                        } else if (data.type == 'edit') {
                            $('#label_show' + data.id).html(data.label);
                            $('#label_show').removeAttr('id');
                            $('#link_show' + data.id).html(data.link);
                            $('#page_show' + data.id).html(data.page);
                            $('#kategori_show' + data.id).html(data.kategori);
                            // window.location.href = '{{ asset('setting/menu?save=sc') }}';
                        }
                        $('#label').val('');
                        $('#link').val('');
                        $('#page').val('');
                        $('#kategori').val('');
                        $('#id').val('');
                        $("#load").hide();
                        kosong();
                    },
                    error: function(data) {
                        err = '';
                        respon = data.responseJSON;
                        $.each(respon.errors, function(index, value) {
                            err += "<li>" + value + "</li>";
                        });

                        toastr.error(err);
                        $('.ket').html(
                            "<div class='container'><div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong></strong> " +
                            respon.message + "<ol class='pl-3 m-0'>" + err +
                            "</ol></div></div>");
                    },
                });

            }

        });


        $('.dd').on('change', function() {
            $("#load").show();

            var dataString = {
                data: $("#nestable-output").val(),
            };

            $.ajax({
                type: "POST",
                url: "{{ Url('setting/menu_save_position') }}",
                data: dataString,
                cache: false,
                success: function(data) {
                    $("#load").hide();
                },
                error: function(xhr, status, error) {
                    Swal('Tidak Dapat Merespon Data 404');
                },
            });
        });

        $(document).on("click", ".pos-button", function() {
            var id = $(this).attr('id');
            $("#load").show();
            $.ajax({
                type: "POST",
                url: "{{ asset('menu/menu_web/kategori') }}",
                data: {
                    id: id
                },
                cache: false,
                success: function(data) {
                    $("#load").hide();
                },
                error: function(xhr, status, error) {
                    Swal('Tidak Dapat Merespon Data 404');
                },
            });
        });

        jQuery(document).on("click", ".del-button", function() {
            var id = $(this).attr('id');
            Swal.fire({
                title: 'confirm',
                text: 'Anda akan hapus?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    setTimeout(function() {
                        $("#load").show();
                        $.ajax({
                            type: "DELETE",
                            url: '{{ route('setting.menu.destroy', ':id') }}'
                                .replace(
                                    ':id', id),
                            data: {
                                id: id
                            },
                            cache: false,
                            success: function(data) {
                                $("#load").hide();
                                $("li[data-id='" + id + "']").remove();
                            },
                            error: function(xhr, status, error) {
                                alert(data.Message);
                            },
                        });
                    }, 100);
                }
            });

        });


        jQuery(document).on("click", ".edit-button", function() {

            var id = $(this).attr('id');
            var label = $(this).attr('label');
            var link = $(this).attr('link');
            var level = $(this).attr('level');
            var icon = $(this).attr('icon');

            $("#id").val(id);
            $("#label").val(label);
            $("#link").val(link);
            $('#level_akses').html(level);

            var kali = $('#level_akses').html(level);
            $('#level_akses_otp').html(level.toUpperCase() + '<br />');
            var level_aks = $("#level_akses").html(level);
            var lev = level.split(',');
            for (i = 0; i < lev.length; i++) {
                $('#checkbox' + lev[i]).attr('checked', true);
                $('[value=' + lev[i] + ']').attr('checked', true);
            }


            $(".tampil").html(
                '<div class="control-group"><div class="radio"><label><input tabindex="7" class="check" id="fa_icon minimal-radio-1" type="radio" value="' +
                icon + '" checked><span class="text">' + icon + '" ' + icon +
                '</span></label></div></div>');
        });

        $(document).on("click", "#reset", function() {
            $('#label').val('');
            $('#link').val('');
            $('#level_akses').val('');
            $('#id').val('');
        });

    });
</script>

<script type="text/javascript">
    function UbahLevel(nm) {
        ck = "";
        if (nm.checked == true) {
            var nilai = data.LevelID.value;
            if (nilai.match(nm.value + ".") != nm.value + ".") data.LevelID.value += nm.value + ".";
        } else {
            var nilai = data.LevelID.value;
            data.LevelID.value = nilai.replace(nm.value + ".", "");
        }
    }
</script>


<script type="text/javascript">
    $(function() {
        $('#label_cr').click(function() {
            $('#tampil_cmodal').modal('show');

        });
        $('.menu_app').click(function() {
            var id_menu = $(this).val();
            $('#label').val(id_menu);
            $('#link').val(id_menu).attr('readonly', true);
            $('#tampil_cmodal').modal('hide');
            $('.menu_app').attr('checked', false);

        })
    })
</script>


<div id="modal-form" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">,
                <h4>Favicon Menu</h4>
                <br /><br />
                <div class="row">
                    @include('menu.list_menu')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
