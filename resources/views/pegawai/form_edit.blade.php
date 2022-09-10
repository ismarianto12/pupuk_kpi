@extends('layouts.template')
@section('title', 'Master Data Pegawai')
@section('toolbars')
    @include('layouts.toolbars', [
        'url' => Url('pegawai/create'),
        'par' => '<i class="fa fa-copy"></i> All',
        'label' => 'Kembali',
        'ajax_button' => Url('master/pegawai'),
    ])
@endsection
@section('content')


    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa fa-copy"></i>All</div>
        </div>
        <div class="ket"></div>
        <form id="exampleValidation" method="POST" class="simpan">
            <div class="tab-content">
                <div class="card-body">
                    <ul class="nav nav-bold nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-primary flex-grow-1 px-10"
                        role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#biodata"><i class="fa fa-users"></i>Biodata
                                Personal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#pendidikan"><i
                                    class="fa fa-graduation-cap"></i>Pendidikan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#karir"><i class="fa fa-copy"></i>Karir</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#jabatan_departemen"><i
                                    class="fa fa-copy"></i>Jabatan && Departemen</a>
                        </li>

                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show pt-3 pr-5 mr-n5 active" id="biodata" role="tabpanel">
                            <br />
                            <h3><i class="fa fa-user"></i> Biodata </h3>
                            <br />
                            <div class="form-group row">
                                <label for="name" class="col-md-2 text-left">N.I.K <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="nik" name="nik" required>
                                </div>

                                <label for="name" class="col-md-2 text-left">Nama <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                            </div>
                            <div class="form-group row">

                                <label for="name" class="col-md-2 text-left">Alamat Lengkap<span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="alamat_lengkap" name="alamat_lengkap">
                                </div>
                                <label for="name" class="col-md-2 text-left">Tempat Tanggal lahir <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="tempat_tgl_lahir"
                                        name="tempat_tgl_lahir">
                                </div>
                            </div>
                            <div class="form-group row">

                                <label for="name" class="col-md-2 text-left">Jenis Kelamin <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <select class="form-control" name="jk">
                                        @foreach (['1' => 'Laki-laki', 2 => 'Perempuan'] as $jk => $jkval)
                                            <option value="{{ $jk }}">{{ $jkval }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <label for="name" class="col-md-2 text-left">Email <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-md-2 text-left">No. Telephone <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="no_telp" name="no_telp">
                                </div>
                                <label for="name" class="col-md-2 text-left">No. Hanphone <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="no_hp" name="no_hp">
                                </div>
                            </div>


                        </div>
                        <div class="tab-pane fade show pt-3 pr-5 mr-n5" id="pendidikan" role="tabpanel">

                            <br />
                            <h3><i class="fa fa-graduation-cap"></i> Pendidikan </h3>
                            <hr />
                            <div class="form-group row">
                                <label for="name" class="col-md-2 text-left">Perguruan Tinggi/ Institut /
                                    Universitas<span class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="perguruan_tinggi"
                                        name="perguruan_tinggi" required>
                                </div>

                                <label for="name" class="col-md-2 text-left">Tahun Lulus <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="tahun_lulus" name="tahun_lulus"
                                        required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-md-2 text-left">Gelar Akademik <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="gelar_akademik" name="gelar_akademik"
                                        required>
                                </div>


                            </div>

                        </div>
                        <div class="tab-pane fade show pt-3 pr-5 mr-n5" id="karir" role="tabpanel">
                            <br />
                            <h3><i class="fa fa-copy"></i> Karir </h3>
                            <hr />
                            <div class="form-group row">
                                <label for="name" class="col-md-2 text-left">Riwayat Karir <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <textarea class="form-control" name="riwayat_karir">
                                </textarea>
                                </div>

                            </div>
                        </div>
                        <div class="tab-pane fade show pt-3 pr-5 mr-n5" id="jabatan_departemen" role="tabpanel">
                            <br />
                            <h3><i class="fa fa-copy"></i> Jabatan dan departemen</h3>
                            <hr />
                            {{-- starting model in here --}}
                            <div class="form-group row">
                                <label for="name" class="col-md-2 text-left">Bidang <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <select class="form-control" name="tmbidang_id">
                                        @foreach ($bidang as $bidangs)
                                            <option value="{{ $bidangs->id }}">{{ $bidangs->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <label for="name" class="col-md-2 text-left">Jabatan<span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <select class="form-control" name="tmjabatan_id">
                                        @foreach ($jabatan as $jabatans)
                                            <option value="{{ $jabatans->id }}">{{ $jabatans->n_jabatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-md-2 text-left">Tahun Pengukuhan <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="tahun_pengangkatan"
                                        name="tahun_pengangkatan">
                                </div>

                                <label for="name" class="col-md-2 text-left">Status Karyawan <span
                                        class="required-label"></span></label>
                                <div class="col-md-4">
                                    <select class="form-control" name="status_karyawan">
                                        @foreach (Properti_app::Karyawan_status() as $jk => $jkval)
                                            <option value="{{ $jk }}">{{ $jkval }}</option>
                                        @endforeach
                                    </select>


                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-save"></i>Simpan data</button>
                    <button type="reset" class="btn btn-danger">Cancel</button>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript">
        $(function() {
            $('.simpan').on('submit', function(e) {
                e.preventDefault();
                $('.card-body').css({
                    'opacity': '0.2'
                });
                $.ajax({
                    url: "{{ route('master.pegawai.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    chace: false,
                    async: false,
                    success: function(data) {
                        toastr.success('data pegawai berhasil di simpan');
                        window.location.href = "{{ Url('/pegawai') }}";
                    },
                    error: function(data) {
                        var div = $('.container');
                        setInterval(function() {
                            var pos = div.scrollTop();
                            div.scrollTop(pos + 2);
                        }, 10)
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



                    }
                })
                $('.card-body').css({
                    'opacity': '1'
                });
            });
        });
    </script>


@endsection
