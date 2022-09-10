<?php

// by ismarianto
namespace App\Helpers;

use App\Models\Jenis_surat;
use App\Models\menu;
use App\Models\menu_group;
use App\Models\Tmbangunan;
use App\Models\Tmlevel;
use App\Models\tmprospektif;
use App\Models\Tmproyek;
use App\Models\Tmsurat_master;
use App\Models\tmtable_assigment;
use App\Models\tmtahun;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Session;

class Properti_app
{

    public static function status_perpanjang()
    {
        return [
            'Closed BAK' => 'Closed BAK',
            'Closed PKS' => 'Closed PKS',
            'Closed PAID' => 'Closed PAID',
            'Closed (Catatan)' => 'Closed (Catatan)',
        ];
    }
    public function jenisSurat()
    {
    }
    public static function hari($hari)
    {
        switch ($hari) {
            case 'Sun':
                $hari_ini = "Minggu";
                break;

            case 'Mon':
                $hari_ini = "Senin";
                break;

            case 'Tue':
                $hari_ini = "Selasa";
                break;

            case 'Wed':
                $hari_ini = "Rabu";
                break;

            case 'Thu':
                $hari_ini = "Kamis";
                break;

            case 'Fri':
                $hari_ini = "Jumat";
                break;

            case 'Sat':
                $hari_ini = "Sabtu";
                break;

            default:
                $hari_ini = "Tidak di ketahui";
                break;
        }
        return $hari_ini;
    }
    public function number_format()
    {
    }

    public static function user_satker()
    {
        $user_id = Auth::user()->id;
        $query = DB::table('user')
            ->select('user.id', 'user.username', 'tmuser_level.description', 'tmuser_level.mapping_sie', 'tmuser_level.id as level_id')
            ->join('tmuser_level', 'user.tmuser_level_id', '=', 'tmuser_level.id')
            ->where('user.id', $user_id);

        $levelid = $query->first()->level_id;
        if ($levelid == 3) {
            return Auth::user()->sikd_satker_id;
        } else {
            return 0;
        }
    }

    public static function load_js(array $url)
    {
        $data = [];
        foreach ($url as $ls) {
            $js[] = '<script type="text/javascript" src="' . $ls . '"></script>';
            $data = $js;
        }
        return $data;
    }

    public static function getlevel()
    {
        $ff = Auth::user();
        // dd($user_id);
        if ($ff != null) {
            $user_id = $ff->id;
            $query = DB::table('users')
                ->select('users.id', 'users.username', 'tmuser_level.description', 'tmuser_level.mapping_sie', 'tmuser_level.id as level_id')
                ->join('tmuser_level', 'users.tmuser_level_id', '=', 'tmuser_level.id')
                ->where('users.id', $user_id)
                ->first();
            return $query->level_id;
        } else {
            return null;
        }
    }

    public static function tgl_indo($tgl)
    {
        $bulan = array(
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        );
        $split = explode('-', $tgl);
        return $split[2] . ' ' . $bulan[(int) $split[1]] . ' ' . $split[0];
    }

    public static function servername()
    {
        return $_SERVER['HTTP_HOST'];
    }
    public static function UserSess()
    {
        $ff = Auth::user();
        if ($ff != null) {
            return $ff->username;
        } else {
            return null;
        }
    }

    public static function propuser($params)
    {
        $ff = Auth::user();
        if ($ff != null) {
            $data = User::find($ff->id);
            // dd($data);
            if ($data != '') {
                return $data[$params];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function comboproyek($id = '')
    {
        $level_id = Auth::user()->level_id;
        $id_user = Auth::user()->id;
        $datas = User::select(
            'users.id as ltjk',
            'users.name',
            'users.username',
            'users.tmlevel_id',
            'users.tmproyek_id',
            'tmproyek.nama_proyek',
            'tmproyek.kode',
            'tmproyek.id'
        )->join('tmproyek', 'tmproyek.id', '=', 'users.tmproyek_id')
            ->where('users.id', $id_user)
            ->get();

        // dd($level_id);
        if (self::propuser('tmlevel_id') != 1) {
            // dd('sss');
            $html = '<select id="tmproyek_id" name="tmproyek_id" class="js-example-basic-single form-control">';
            foreach ($datas as $data) {
                $selected = ($data->id == $id) ? 'selected' : '';
                $html .= '<option value="' . $data['id'] . '" ' . $selected . '>' . $data['kode'] . '-' . $data['nama_proyek'] . '</option>';
            }
            $html .= '</select>';
        } else {
            $html = '<select id="tmproyek_id" name="tmproyek_id" class="js-example-basic-single form-control">';
            $dds = Tmproyek::get();
            // dd($dds);
            $html .= '<option value="">--Semua Proyek-- </option>';
            foreach ($dds as $dd) {
                $selected = ($dd->id == $id) ? 'selected' : '';

                $html .= '<option value="' . $dd['id'] . '" ' . $selected . '>' . $dd['kode'] . '-' . $dd['nama_proyek'] . '</option>';
            }
            $html .= '</select>';
        }
        return $html;
    }

    public static function combobangunan($id = '')
    {
        $level_id = Auth::user()->level_id;
        $datas = Tmbangunan::select('kode', 'nama_bangunan', 'id');

        if ($level_id != 1) {
            $html = '<select name="tmbangunan_id" class="js-example-basic-single form-control">';
            $pars = $datas->where('tmlevel_id', $level_id);
            foreach ($pars->get() as $data) {
                $selected = ($data->id == $id) ? 'selected' : '';
                $html .= '<option value="' . $data['id'] . '" ' . $selected . '>' . $data['kode'] . '-' . $data['nama_proyek'] . '</option>';
            }
            $html .= '</select>';
        } else {
            $html = '<select name="tmbangunan_id" class="js-example-basic-single form-control">';
            $pars = $datas->get();
            foreach ($pars as $data) {
                $selected = ($data->id == $id) ? 'selected' : '';
                $html .= '<option value="' . $data['id'] . '" ' . $selected . '>' . $data['kode'] . '-' . $data['nama_proyek'] . '</option>';
            }
            $html .= '</select>';
        }
        return $html;
    }

    public static function jenis_surat()
    {

        $a = [
            'SIP' => 'SURAT INFORMASI PERPANJANGAN',
            'SIT' => 'SURAT INFORMASI TIDAK PERPANJANG',
            'SPH' => 'SURAT PENAWARAN HARGA',
            'SMR' => 'SUMMARY',
            'BAN' => 'BERITA ACARA NEGOSIASI',
            'BAK' => 'BERITA ACARA KESEPAKATAN',
        ];
        return $a;
    }

    // set change environment dinamically
    public static function parsing($url)
    {
        $val = "?" . base64_decode($url);
        $query_str = parse_url($val, PHP_URL_QUERY);
        parse_str($query_str, $query_params);
        return $query_params;
    }
    public static function no_surat()
    {
        $s = Tmsurat_master::select(\DB::raw('max(download) as idnya'))->first();
        $rdata = isset($s->idnya) ? (int) $s->idnya : 1 + 1;
        return $rdata;
    }

    // self return function

    public static function penyebut($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = self::penyebut($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = self::penyebut($nilai / 10) . " puluh" . self::penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . self::penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = self::penyebut($nilai / 100) . " ratus" . self::penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . self::penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = self::penyebut($nilai / 1000) . " ribu" . self::penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = self::penyebut($nilai / 1000000) . " juta" . self::penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = self::penyebut($nilai / 1000000000) . " milyar" . self::penyebut(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = self::penyebut($nilai / 1000000000000) . " trilyun" . self::penyebut(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    public static function terbilang($nilai)
    {
        if ($nilai < 0) {
            $hasil = "minus " . trim(self::penyebut($nilai));
        } else {
            $hasil = trim(self::penyebut($nilai));
        }
        return $hasil;
    }

    public static function bulan()
    {

        return array(
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        );
    }

    public static function parameterColumn()
    {
        return [
            'SIP' => [
                "nomor_surat" => "Nomor Surat",
                "tanggal_surat" => "Tanggal Surat",
            ], // Jenis surat SIP
            'SIT' => [
                "nomor_surat" => "Nomor Surat",
                "tanggal_surat" => "Tanggal Surat",
            ], // Jenis surat SIT
            'SPH' => [
                "nomor_surat" => "Nomor Surat",
                "tanggal_surat" => "Tanggal Surat",
                "nomor_surat_landlord" => "Nomor Surat Landlord",
                "perihal_surat_landlord" => "Perihal Surat Landlord",
                'periode_sewa_penawaran_awal' => "Periode Sewa Penawaran Awal",
                'periode_sewa_penawaran_akhir' => "Periode Sewa Penawaran Akhir",
                "pic_landlord" => "Pic Landlord",
                "jabatan_landlord" => "Jabatan Landlord",
                "penawaran_harga_sewa" => "Penawaran Harga Sewa",
            ], //dari jenis SPH
            'SMR' => [
                "harga_sewa_baru" => 'Harga Sewa Baru',
                "periode_awal" => 'Periode Awal',
                "periode_akhir" => 'Periode Akhir',
                // "penawaran_pemilik" => 'Penawaran pemilik',
                "penawaran_th_1" => 'Penawaran Telkomsel 1',
                "penawaran_th_2" => 'Penawaran Telkomsel 2',
                "penawaran_th_3" => 'Penawaran Telkomsel 3',
                "penawaran_th_4" => 'Penawaran Telkomsel 4',
                "pemilik_1" => "Penawaran Pemilik  1",
                "pemilik_2" => "Penawaran Pemilik 2",
                "pemilik_3" => "Penawaran Pemilik 3",
                "pemilik_4" => "Penawaran Pemilik 4",
                "total_harga_sewa_baru" => "Total Harga Sewa baru",
                "keterangan_harga_patokan" => "Keterangan Harga Patokan",
            ], //group by SMR
            'BAN' => [
                "tanggal_ban" => "Tanggal",
                "pengelola" => "Pengelola",
                "nama_pic" => "Nama PIC",
                "alamat_pic" => "Alamat PIC",
                "jabatan_pic" => "Jabatan PIC",
                "nomor_telp_pic" => "Nomor Telephone PIC",
            ], //BAN
            'BAK' => [
                "nomor_bak" => "Nomor Surat BAK",
                "lokasi_tempat_sewa" => "Lokasi Tempate Sewa",
                "luas_tempat_sewa" => "Luas Tempat Sewa",
                "nomor_rekening" => "Nomor Rekening",
                "pemilik_rekening_bank" => "Pemilik Rekening Bank",
                "cabang" => "Cabang",
                "nomor_npwp" => "Nomor NPWP",
                "pemilik_npwp" => "Pemilik NPWP",
                "nomor_shm_ajb_hgb" => "Nomor SHM / AJB HGB",
                "nomor_imb" => "Nomor IMB",
                "nomor_sppt_pbb" => 'Nomor SPT PBB',
                "total_harga_net" => "Total Harga NET",
            ],
        ];
    }

    public static function format_percentage($percentage, $precision = 2)
    {
        return round($percentage, $precision) . '%';
    }

    public static function calculate_percentage($number, $total)
    {

        // Can't divide by zero so let's catch that early.
        if ($total == 0) {
            return 0;
        }

        return ($number / $total) * 100;
    }

    public static function calculate_percentage_for_display($number, $total)
    {
        return self::format_percentage(self::calculate_percentage($number, $total));
    }

    public function deskripsi()
    {
        return 'aplikasi key performance individual';
    }

    public function Karyawan_status()
    {
        return [
            1 => 'Tetap',
            2 => 'Kontrak',
            3 => 'Magang',
        ];
    }

    public function active()
    {
        return [
            1 => 'Active',
            2 => 'Non Active',
        ];
    }

    // aldmakdmaskld
    public static function get_menu($items, $class = 'dd-list')
    {
        $html = "<ol class=\"" . $class . "\" id=\"menu-id\">";
        foreach ($items as $key => $value) {
            $akses_level = str_replace('.', ',', $value['level']);
            if ($value['position'] == 'Top') {
                $icon = 'down text-danger';
                $ket = 'Pindah ke Bottom Menu';
            } else {
                $icon = 'up text-success';
                $ket = 'Pindah ke Top Menu';
            }
            $html .= '<li class="dd-item dd3-item" data-id="' . $value['id_menu'] . '" >
  <div style="cursor:move" class="dd-handle dd3-handle ' . $value['position'] . '"></div>
  <div class="dd3-content"><span id="label_show' . $value['id_menu'] . '">' . $value['nama_menu'] . '</span>
  <span class="span-right">/<span id="link_show' . $value['id_menu'] . '">' . $value['link'] . '</span> &nbsp;&nbsp;
  &nbsp;
  <a href="javascript:void(0)" class="edit-button" id="' . $value['id_menu'] . '" label="' . $value['nama_menu'] . '" link="' . $value['link'] . '" level="' . $akses_level . '" icon="' . htmlspecialchars($value['icon']) . '" ><i class="fa fa-edit"></i></a>  &nbsp;
  ' . $value['level'] . '
  <i class="fa fa-user"></i>  &nbsp;
  <a class="del-button" id="' . $value['id_menu'] . '"><i class="fa fa-trash"></i></a></span>
  </div>';
            if (array_key_exists('child', $value)) {
                $html .= self::get_menu($value['child'], 'child');
            }
            $html .= "</li>";
        }
        $html .= "</ol>";
        return $html;
    }

    // icon menu added this line

    public static function Icon()
    {
        return array(
            'fa-glass' => 'f000',
            'fa-music' => 'f001',
            'fa-search' => 'f002',
            'fa-envelope-o' => 'f003',
            'fa-heart' => 'f004',
            'fa-star' => 'f005',
            'fa-star-o' => 'f006',
            'fa-user' => 'f007',
            'fa-film' => 'f008',
            'fa-th-large' => 'f009',
            'fa-th' => 'f00a',
            'fa-th-list' => 'f00b',
            'fa-check' => 'f00c',
            'fa-times' => 'f00d',
            'fa-search-plus' => 'f00e',
            'fa-search-minus' => 'f010',
            'fa-power-off' => 'f011',
            'fa-signal' => 'f012',
            'fa-cog' => 'f013',
            'fa-trash-o' => 'f014',
            'fa-home' => 'f015',
            'fa-file-o' => 'f016',
            'fa-clock-o' => 'f017',
            'fa-road' => 'f018',
            'fa-download' => 'f019',
            'fa-arrow-circle-o-down' => 'f01a',
            'fa-arrow-circle-o-up' => 'f01b',
            'fa-inbox' => 'f01c',
            'fa-play-circle-o' => 'f01d',
            'fa-repeat' => 'f01e',
            'fa-refresh' => 'f021',
            'fa-list-alt' => 'f022',
            'fa-lock' => 'f023',
            'fa-flag' => 'f024',
            'fa-headphones' => 'f025',
            'fa-volume-off' => 'f026',
            'fa-volume-down' => 'f027',
            'fa-volume-up' => 'f028',
            'fa-qrcode' => 'f029',
            'fa-barcode' => 'f02a',
            'fa-tag' => 'f02b',
            'fa-tags' => 'f02c',
            'fa-book' => 'f02d',
            'fa-bookmark' => 'f02e',
            'fa-print' => 'f02f',
            'fa-camera' => 'f030',
            'fa-font' => 'f031',
            'fa-bold' => 'f032',
            'fa-italic' => 'f033',
            'fa-text-height' => 'f034',
            'fa-text-width' => 'f035',
            'fa-align-left' => 'f036',
            'fa-align-center' => 'f037',
            'fa-align-right' => 'f038',
            'fa-align-justify' => 'f039',
            'fa-list' => 'f03a',
            'fa-outdent' => 'f03b',
            'fa-indent' => 'f03c',
            'fa-video-camera' => 'f03d',
            'fa-picture-o' => 'f03e',
            'fa-pencil' => 'f040',
            'fa-map-marker' => 'f041',
            'fa-adjust' => 'f042',
            'fa-tint' => 'f043',
            'fa-pencil-square-o' => 'f044',
            'fa-share-square-o' => 'f045',
            'fa-check-square-o' => 'f046',
            'fa-arrows' => 'f047',
            'fa-step-backward' => 'f048',
            'fa-fast-backward' => 'f049',
            'fa-backward' => 'f04a',
            'fa-play' => 'f04b',
            'fa-pause' => 'f04c',
            'fa-stop' => 'f04d',
            'fa-forward' => 'f04e',
            'fa-fast-forward' => 'f050',
            'fa-step-forward' => 'f051',
            'fa-eject' => 'f052',
            'fa-chevron-left' => 'f053',
            'fa-chevron-right' => 'f054',
            'fa-plus-circle' => 'f055',
            'fa-minus-circle' => 'f056',
            'fa-times-circle' => 'f057',
            'fa-check-circle' => 'f058',
            'fa-question-circle' => 'f059',
            'fa-info-circle' => 'f05a',
            'fa-crosshairs' => 'f05b',
            'fa-times-circle-o' => 'f05c',
            'fa-check-circle-o' => 'f05d',
            'fa-ban' => 'f05e',
            'fa-arrow-left' => 'f060',
            'fa-arrow-right' => 'f061',
            'fa-arrow-up' => 'f062',
            'fa-arrow-down' => 'f063',
            'fa-share' => 'f064',
            'fa-expand' => 'f065',
            'fa-compress' => 'f066',
            'fa-plus' => 'f067',
            'fa-minus' => 'f068',
            'fa-asterisk' => 'f069',
            'fa-exclamation-circle' => 'f06a',
            'fa-gift' => 'f06b',
            'fa-leaf' => 'f06c',
            'fa-fire' => 'f06d',
            'fa-eye' => 'f06e',
            'fa-eye-slash' => 'f070',
            'fa-exclamation-triangle' => 'f071',
            'fa-plane' => 'f072',
            'fa-calendar' => 'f073',
            'fa-random' => 'f074',
            'fa-comment' => 'f075',
            'fa-magnet' => 'f076',
            'fa-chevron-up' => 'f077',
            'fa-chevron-down' => 'f078',
            'fa-retweet' => 'f079',
            'fa-shopping-cart' => 'f07a',
            'fa-folder' => 'f07b',
            'fa-folder-open' => 'f07c',
            'fa-arrows-v' => 'f07d',
            'fa-arrows-h' => 'f07e',
            'fa-bar-chart' => 'f080',
            'fa-twitter-square' => 'f081',
            'fa-facebook-square' => 'f082',
            'fa-camera-retro' => 'f083',
            'fa-key' => 'f084',
            'fa-cogs' => 'f085',
            'fa-comments' => 'f086',
            'fa-thumbs-o-up' => 'f087',
            'fa-thumbs-o-down' => 'f088',
            'fa-star-half' => 'f089',
            'fa-heart-o' => 'f08a',
            'fa-sign-out' => 'f08b',
            'fa-linkedin-square' => 'f08c',
            'fa-thumb-tack' => 'f08d',
            'fa-external-link' => 'f08e',
            'fa-sign-in' => 'f090',
            'fa-trophy' => 'f091',
            'fa-github-square' => 'f092',
            'fa-upload' => 'f093',
            'fa-lemon-o' => 'f094',
            'fa-phone' => 'f095',
            'fa-square-o' => 'f096',
            'fa-bookmark-o' => 'f097',
            'fa-phone-square' => 'f098',
            'fa-twitter' => 'f099',
            'fa-facebook' => 'f09a',
            'fa-github' => 'f09b',
            'fa-unlock' => 'f09c',
            'fa-credit-card' => 'f09d',
            'fa-rss' => 'f09e',
            'fa-hdd-o' => 'f0a0',
            'fa-bullhorn' => 'f0a1',
            'fa-bell' => 'f0f3',
            'fa-certificate' => 'f0a3',
            'fa-hand-o-right' => 'f0a4',
            'fa-hand-o-left' => 'f0a5',
            'fa-hand-o-up' => 'f0a6',
            'fa-hand-o-down' => 'f0a7',
            'fa-arrow-circle-left' => 'f0a8',
            'fa-arrow-circle-right' => 'f0a9',
            'fa-arrow-circle-up' => 'f0aa',
            'fa-arrow-circle-down' => 'f0ab',
            'fa-globe' => 'f0ac',
            'fa-wrench' => 'f0ad',
            'fa-tasks' => 'f0ae',
            'fa-filter' => 'f0b0',
            'fa-briefcase' => 'f0b1',
            'fa-arrows-alt' => 'f0b2',
            'fa-users' => 'f0c0',
            'fa-link' => 'f0c1',
            'fa-cloud' => 'f0c2',
            'fa-flask' => 'f0c3',
            'fa-scissors' => 'f0c4',
            'fa-files-o' => 'f0c5',
            'fa-paperclip' => 'f0c6',
            'fa-floppy-o' => 'f0c7',
            'fa-square' => 'f0c8',
            'fa-bars' => 'f0c9',
            'fa-list-ul' => 'f0ca',
            'fa-list-ol' => 'f0cb',
            'fa-strikethrough' => 'f0cc',
            'fa-underline' => 'f0cd',
            'fa-table' => 'f0ce',
            'fa-magic' => 'f0d0',
            'fa-truck' => 'f0d1',
            'fa-pinterest' => 'f0d2',
            'fa-pinterest-square' => 'f0d3',
            'fa-google-plus-square' => 'f0d4',
            'fa-google-plus' => 'f0d5',
            'fa-money' => 'f0d6',
            'fa-caret-down' => 'f0d7',
            'fa-caret-up' => 'f0d8',
            'fa-caret-left' => 'f0d9',
            'fa-caret-right' => 'f0da',
            'fa-columns' => 'f0db',
            'fa-sort' => 'f0dc',
            'fa-sort-desc' => 'f0dd',
            'fa-sort-asc' => 'f0de',
            'fa-envelope' => 'f0e0',
            'fa-linkedin' => 'f0e1',
            'fa-undo' => 'f0e2',
            'fa-gavel' => 'f0e3',
            'fa-tachometer' => 'f0e4',
            'fa-comment-o' => 'f0e5',
            'fa-comments-o' => 'f0e6',
            'fa-bolt' => 'f0e7',
            'fa-sitemap' => 'f0e8',
            'fa-umbrella' => 'f0e9',
            'fa-clipboard' => 'f0ea',
            'fa-lightbulb-o' => 'f0eb',
            'fa-exchange' => 'f0ec',
            'fa-cloud-download' => 'f0ed',
            'fa-cloud-upload' => 'f0ee',
            'fa-user-md' => 'f0f0',
            'fa-stethoscope' => 'f0f1',
            'fa-suitcase' => 'f0f2',
            'fa-bell-o' => 'f0a2',
            'fa-coffee' => 'f0f4',
            'fa-cutlery' => 'f0f5',
            'fa-file-text-o' => 'f0f6',
            'fa-building-o' => 'f0f7',
            'fa-hospital-o' => 'f0f8',
            'fa-ambulance' => 'f0f9',
            'fa-medkit' => 'f0fa',
            'fa-fighter-jet' => 'f0fb',
            'fa-beer' => 'f0fc',
            'fa-h-square' => 'f0fd',
            'fa-plus-square' => 'f0fe',
            'fa-angle-double-left' => 'f100',
            'fa-angle-double-right' => 'f101',
            'fa-angle-double-up' => 'f102',
            'fa-angle-double-down' => 'f103',
            'fa-angle-left' => 'f104',
            'fa-angle-right' => 'f105',
            'fa-angle-up' => 'f106',
            'fa-angle-down' => 'f107',
            'fa-desktop' => 'f108',
            'fa-laptop' => 'f109',
            'fa-tablet' => 'f10a',
            'fa-mobile' => 'f10b',
            'fa-circle-o' => 'f10c',
            'fa-quote-left' => 'f10d',
            'fa-quote-right' => 'f10e',
            'fa-spinner' => 'f110',
            'fa-circle' => 'f111',
            'fa-reply' => 'f112',
            'fa-github-alt' => 'f113',
            'fa-folder-o' => 'f114',
            'fa-folder-open-o' => 'f115',
            'fa-smile-o' => 'f118',
            'fa-frown-o' => 'f119',
            'fa-meh-o' => 'f11a',
            'fa-gamepad' => 'f11b',
            'fa-keyboard-o' => 'f11c',
            'fa-flag-o' => 'f11d',
            'fa-flag-checkered' => 'f11e',
            'fa-terminal' => 'f120',
            'fa-code' => 'f121',
            'fa-reply-all' => 'f122',
            'fa-star-half-o' => 'f123',
            'fa-location-arrow' => 'f124',
            'fa-crop' => 'f125',
            'fa-code-fork' => 'f126',
            'fa-chain-broken' => 'f127',
            'fa-question' => 'f128',
            'fa-info' => 'f129',
            'fa-exclamation' => 'f12a',
            'fa-superscript' => 'f12b',
            'fa-subscript' => 'f12c',
            'fa-eraser' => 'f12d',
            'fa-puzzle-piece' => 'f12e',
            'fa-microphone' => 'f130',
            'fa-microphone-slash' => 'f131',
            'fa-shield' => 'f132',
            'fa-calendar-o' => 'f133',
            'fa-fire-extinguisher' => 'f134',
            'fa-rocket' => 'f135',
            'fa-maxcdn' => 'f136',
            'fa-chevron-circle-left' => 'f137',
            'fa-chevron-circle-right' => 'f138',
            'fa-chevron-circle-up' => 'f139',
            'fa-chevron-circle-down' => 'f13a',
            'fa-html5' => 'f13b',
            'fa-css3' => 'f13c',
            'fa-anchor' => 'f13d',
            'fa-unlock-alt' => 'f13e',
            'fa-bullseye' => 'f140',
            'fa-ellipsis-h' => 'f141',
            'fa-ellipsis-v' => 'f142',
            'fa-rss-square' => 'f143',
            'fa-play-circle' => 'f144',
            'fa-ticket' => 'f145',
            'fa-minus-square' => 'f146',
            'fa-minus-square-o' => 'f147',
            'fa-level-up' => 'f148',
            'fa-level-down' => 'f149',
            'fa-check-square' => 'f14a',
            'fa-pencil-square' => 'f14b',
            'fa-external-link-square' => 'f14c',
            'fa-share-square' => 'f14d',
            'fa-compass' => 'f14e',
            'fa-caret-square-o-down' => 'f150',
            'fa-caret-square-o-up' => 'f151',
            'fa-caret-square-o-right' => 'f152',
            'fa-eur' => 'f153',
            'fa-gbp' => 'f154',
            'fa-usd' => 'f155',
            'fa-inr' => 'f156',
            'fa-jpy' => 'f157',
            'fa-rub' => 'f158',
            'fa-krw' => 'f159',
            'fa-btc' => 'f15a',
            'fa-file' => 'f15b',
            'fa-file-text' => 'f15c',
            'fa-sort-alpha-asc' => 'f15d',
            'fa-sort-alpha-desc' => 'f15e',
            'fa-sort-amount-asc' => 'f160',
            'fa-sort-amount-desc' => 'f161',
            'fa-sort-numeric-asc' => 'f162',
            'fa-sort-numeric-desc' => 'f163',
            'fa-thumbs-up' => 'f164',
            'fa-thumbs-down' => 'f165',
            'fa-youtube-square' => 'f166',
            'fa-youtube' => 'f167',
            'fa-xing' => 'f168',
            'fa-xing-square' => 'f169',
            'fa-youtube-play' => 'f16a',
            'fa-dropbox' => 'f16b',
            'fa-stack-overflow' => 'f16c',
            'fa-instagram' => 'f16d',
            'fa-flickr' => 'f16e',
            'fa-adn' => 'f170',
            'fa-bitbucket' => 'f171',
            'fa-bitbucket-square' => 'f172',
            'fa-tumblr' => 'f173',
            'fa-tumblr-square' => 'f174',
            'fa-long-arrow-down' => 'f175',
            'fa-long-arrow-up' => 'f176',
            'fa-long-arrow-left' => 'f177',
            'fa-long-arrow-right' => 'f178',
            'fa-apple' => 'f179',
            'fa-windows' => 'f17a',
            'fa-android' => 'f17b',
            'fa-linux' => 'f17c',
            'fa-dribbble' => 'f17d',
            'fa-skype' => 'f17e',
            'fa-foursquare' => 'f180',
            'fa-trello' => 'f181',
            'fa-female' => 'f182',
            'fa-male' => 'f183',
            'fa-gratipay' => 'f184',
            'fa-sun-o' => 'f185',
            'fa-moon-o' => 'f186',
            'fa-archive' => 'f187',
            'fa-bug' => 'f188',
            'fa-vk' => 'f189',
            'fa-weibo' => 'f18a',
            'fa-renren' => 'f18b',
            'fa-pagelines' => 'f18c',
            'fa-stack-exchange' => 'f18d',
            'fa-arrow-circle-o-right' => 'f18e',
            'fa-arrow-circle-o-left' => 'f190',
            'fa-caret-square-o-left' => 'f191',
            'fa-dot-circle-o' => 'f192',
            'fa-wheelchair' => 'f193',
            'fa-vimeo-square' => 'f194',
            'fa-try' => 'f195',
            'fa-plus-square-o' => 'f196',
            'fa-space-shuttle' => 'f197',
            'fa-slack' => 'f198',
            'fa-envelope-square' => 'f199',
            'fa-wordpress' => 'f19a',
            'fa-openid' => 'f19b',
            'fa-university' => 'f19c',
            'fa-graduation-cap' => 'f19d',
            'fa-yahoo' => 'f19e',
            'fa-google' => 'f1a0',
            'fa-reddit' => 'f1a1',
            'fa-reddit-square' => 'f1a2',
            'fa-stumbleupon-circle' => 'f1a3',
            'fa-stumbleupon' => 'f1a4',
            'fa-delicious' => 'f1a5',
            'fa-digg' => 'f1a6',
            'fa-pied-piper-pp' => 'f1a7',
            'fa-pied-piper-alt' => 'f1a8',
            'fa-drupal' => 'f1a9',
            'fa-joomla' => 'f1aa',
            'fa-language' => 'f1ab',
            'fa-fax' => 'f1ac',
            'fa-building' => 'f1ad',
            'fa-child' => 'f1ae',
            'fa-paw' => 'f1b0',
            'fa-spoon' => 'f1b1',
            'fa-cube' => 'f1b2',
            'fa-cubes' => 'f1b3',
            'fa-behance' => 'f1b4',
            'fa-behance-square' => 'f1b5',
            'fa-steam' => 'f1b6',
            'fa-steam-square' => 'f1b7',
            'fa-recycle' => 'f1b8',
            'fa-car' => 'f1b9',
            'fa-taxi' => 'f1ba',
            'fa-tree' => 'f1bb',
            'fa-spotify' => 'f1bc',
            'fa-deviantart' => 'f1bd',
            'fa-soundcloud' => 'f1be',
            'fa-database' => 'f1c0',
            'fa-file-pdf-o' => 'f1c1',
            'fa-file-word-o' => 'f1c2',
            'fa-file-excel-o' => 'f1c3',
            'fa-file-powerpoint-o' => 'f1c4',
            'fa-file-image-o' => 'f1c5',
            'fa-file-archive-o' => 'f1c6',
            'fa-file-audio-o' => 'f1c7',
            'fa-file-video-o' => 'f1c8',
            'fa-file-code-o' => 'f1c9',
            'fa-vine' => 'f1ca',
            'fa-codepen' => 'f1cb',
            'fa-jsfiddle' => 'f1cc',
            'fa-life-ring' => 'f1cd',
            'fa-circle-o-notch' => 'f1ce',
            'fa-rebel' => 'f1d0',
            'fa-empire' => 'f1d1',
            'fa-git-square' => 'f1d2',
            'fa-git' => 'f1d3',
            'fa-hacker-news' => 'f1d4',
            'fa-tencent-weibo' => 'f1d5',
            'fa-qq' => 'f1d6',
            'fa-weixin' => 'f1d7',
            'fa-paper-plane' => 'f1d8',
            'fa-paper-plane-o' => 'f1d9',
            'fa-history' => 'f1da',
            'fa-circle-thin' => 'f1db',
            'fa-header' => 'f1dc',
            'fa-paragraph' => 'f1dd',
            'fa-sliders' => 'f1de',
            'fa-share-alt' => 'f1e0',
            'fa-share-alt-square' => 'f1e1',
            'fa-bomb' => 'f1e2',
            'fa-futbol-o' => 'f1e3',
            'fa-tty' => 'f1e4',
            'fa-binoculars' => 'f1e5',
            'fa-plug' => 'f1e6',
            'fa-slideshare' => 'f1e7',
            'fa-twitch' => 'f1e8',
            'fa-yelp' => 'f1e9',
            'fa-newspaper-o' => 'f1ea',
            'fa-wifi' => 'f1eb',
            'fa-calculator' => 'f1ec',
            'fa-paypal' => 'f1ed',
            'fa-google-wallet' => 'f1ee',
            'fa-cc-visa' => 'f1f0',
            'fa-cc-mastercard' => 'f1f1',
            'fa-cc-discover' => 'f1f2',
            'fa-cc-amex' => 'f1f3',
            'fa-cc-paypal' => 'f1f4',
            'fa-cc-stripe' => 'f1f5',
            'fa-bell-slash' => 'f1f6',
            'fa-bell-slash-o' => 'f1f7',
            'fa-trash' => 'f1f8',
            'fa-copyright' => 'f1f9',
            'fa-at' => 'f1fa',
            'fa-eyedropper' => 'f1fb',
            'fa-paint-brush' => 'f1fc',
            'fa-birthday-cake' => 'f1fd',
            'fa-area-chart' => 'f1fe',
            'fa-pie-chart' => 'f200',
            'fa-line-chart' => 'f201',
            'fa-lastfm' => 'f202',
            'fa-lastfm-square' => 'f203',
            'fa-toggle-off' => 'f204',
            'fa-toggle-on' => 'f205',
            'fa-bicycle' => 'f206',
            'fa-bus' => 'f207',
            'fa-ioxhost' => 'f208',
            'fa-angellist' => 'f209',
            'fa-cc' => 'f20a',
            'fa-ils' => 'f20b',
            'fa-meanpath' => 'f20c',
            'fa-buysellads' => 'f20d',
            'fa-connectdevelop' => 'f20e',
            'fa-dashcube' => 'f210',
            'fa-forumbee' => 'f211',
            'fa-leanpub' => 'f212',
            'fa-sellsy' => 'f213',
            'fa-shirtsinbulk' => 'f214',
            'fa-simplybuilt' => 'f215',
            'fa-skyatlas' => 'f216',
            'fa-cart-plus' => 'f217',
            'fa-cart-arrow-down' => 'f218',
            'fa-diamond' => 'f219',
            'fa-ship' => 'f21a',
            'fa-user-secret' => 'f21b',
            'fa-motorcycle' => 'f21c',
            'fa-street-view' => 'f21d',
            'fa-heartbeat' => 'f21e',
            'fa-venus' => 'f221',
            'fa-mars' => 'f222',
            'fa-mercury' => 'f223',
            'fa-transgender' => 'f224',
            'fa-transgender-alt' => 'f225',
            'fa-venus-double' => 'f226',
            'fa-mars-double' => 'f227',
            'fa-venus-mars' => 'f228',
            'fa-mars-stroke' => 'f229',
            'fa-mars-stroke-v' => 'f22a',
            'fa-mars-stroke-h' => 'f22b',
            'fa-neuter' => 'f22c',
            'fa-genderless' => 'f22d',
            'fa-facebook-official' => 'f230',
            'fa-pinterest-p' => 'f231',
            'fa-whatsapp' => 'f232',
            'fa-server' => 'f233',
            'fa-user-plus' => 'f234',
            'fa-user-times' => 'f235',
            'fa-bed' => 'f236',
            'fa-viacoin' => 'f237',
            'fa-train' => 'f238',
            'fa-subway' => 'f239',
            'fa-medium' => 'f23a',
            'fa-y-combinator' => 'f23b',
            'fa-optin-monster' => 'f23c',
            'fa-opencart' => 'f23d',
            'fa-expeditedssl' => 'f23e',
            'fa-battery-full' => 'f240',
            'fa-battery-three-quarters' => 'f241',
            'fa-battery-half' => 'f242',
            'fa-battery-quarter' => 'f243',
            'fa-battery-empty' => 'f244',
            'fa-mouse-pointer' => 'f245',
            'fa-i-cursor' => 'f246',
            'fa-object-group' => 'f247',
            'fa-object-ungroup' => 'f248',
            'fa-sticky-note' => 'f249',
            'fa-sticky-note-o' => 'f24a',
            'fa-cc-jcb' => 'f24b',
            'fa-cc-diners-club' => 'f24c',
            'fa-clone' => 'f24d',
            'fa-balance-scale' => 'f24e',
            'fa-hourglass-o' => 'f250',
            'fa-hourglass-start' => 'f251',
            'fa-hourglass-half' => 'f252',
            'fa-hourglass-end' => 'f253',
            'fa-hourglass' => 'f254',
            'fa-hand-rock-o' => 'f255',
            'fa-hand-paper-o' => 'f256',
            'fa-hand-scissors-o' => 'f257',
            'fa-hand-lizard-o' => 'f258',
            'fa-hand-spock-o' => 'f259',
            'fa-hand-pointer-o' => 'f25a',
            'fa-hand-peace-o' => 'f25b',
            'fa-trademark' => 'f25c',
            'fa-registered' => 'f25d',
            'fa-creative-commons' => 'f25e',
            'fa-gg' => 'f260',
            'fa-gg-circle' => 'f261',
            'fa-tripadvisor' => 'f262',
            'fa-odnoklassniki' => 'f263',
            'fa-odnoklassniki-square' => 'f264',
            'fa-get-pocket' => 'f265',
            'fa-wikipedia-w' => 'f266',
            'fa-safari' => 'f267',
            'fa-chrome' => 'f268',
            'fa-firefox' => 'f269',
            'fa-opera' => 'f26a',
            'fa-internet-explorer' => 'f26b',
            'fa-television' => 'f26c',
            'fa-contao' => 'f26d',
            'fa-500px' => 'f26e',
            'fa-amazon' => 'f270',
            'fa-calendar-plus-o' => 'f271',
            'fa-calendar-minus-o' => 'f272',
            'fa-calendar-times-o' => 'f273',
            'fa-calendar-check-o' => 'f274',
            'fa-industry' => 'f275',
            'fa-map-pin' => 'f276',
            'fa-map-signs' => 'f277',
            'fa-map-o' => 'f278',
            'fa-map' => 'f279',
            'fa-commenting' => 'f27a',
            'fa-commenting-o' => 'f27b',
            'fa-houzz' => 'f27c',
            'fa-vimeo' => 'f27d',
            'fa-black-tie' => 'f27e',
            'fa-fonticons' => 'f280',
            'fa-reddit-alien' => 'f281',
            'fa-edge' => 'f282',
            'fa-credit-card-alt' => 'f283',
            'fa-codiepie' => 'f284',
            'fa-modx' => 'f285',
            'fa-fort-awesome' => 'f286',
            'fa-usb' => 'f287',
            'fa-product-hunt' => 'f288',
            'fa-mixcloud' => 'f289',
            'fa-scribd' => 'f28a',
            'fa-pause-circle' => 'f28b',
            'fa-pause-circle-o' => 'f28c',
            'fa-stop-circle' => 'f28d',
            'fa-stop-circle-o' => 'f28e',
            'fa-shopping-bag' => 'f290',
            'fa-shopping-basket' => 'f291',
            'fa-hashtag' => 'f292',
            'fa-bluetooth' => 'f293',
            'fa-bluetooth-b' => 'f294',
            'fa-percent' => 'f295',
            'fa-gitlab' => 'f296',
            'fa-wpbeginner' => 'f297',
            'fa-wpforms' => 'f298',
            'fa-envira' => 'f299',
            'fa-universal-access' => 'f29a',
            'fa-wheelchair-alt' => 'f29b',
            'fa-question-circle-o' => 'f29c',
            'fa-blind' => 'f29d',
            'fa-audio-description' => 'f29e',
            'fa-volume-control-phone' => 'f2a0',
            'fa-braille' => 'f2a1',
            'fa-assistive-listening-systems' => 'f2a2',
            'fa-american-sign-language-interpreting' => 'f2a3',
            'fa-deaf' => 'f2a4',
            'fa-glide' => 'f2a5',
            'fa-glide-g' => 'f2a6',
            'fa-sign-language' => 'f2a7',
            'fa-low-vision' => 'f2a8',
            'fa-viadeo' => 'f2a9',
            'fa-viadeo-square' => 'f2aa',
            'fa-snapchat' => 'f2ab',
            'fa-snapchat-ghost' => 'f2ac',
            'fa-snapchat-square' => 'f2ad',
            'fa-pied-piper' => 'f2ae',
            'fa-first-order' => 'f2b0',
            'fa-yoast' => 'f2b1',
            'fa-themeisle' => 'f2b2',
            'fa-google-plus-official' => 'f2b3',
            'fa-font-awesome' => 'f2b4',
        );
    }

    // update data route aplikasi
    public static function buitlmenu($parent, $menu)
    {

        $html = "";
        if (isset($menu['parents'][$parent])) {
            if ($parent == '0') {
                if (isset($menu['position']['Bottom']) == "Bottom") {
                    $html .= ' <li class="menu-item menu-item-active" aria-haspopup="true">
                    <a href="' . Url('home') . '" class="menu-link">
                        <span class="svg-icon menu-icon">
                        <span class="svg-icon menu-icon">
                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24" />
                                <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero" />
                                <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3" />
                            </g>
                        </svg>
                        </span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>';
                } else {
                    null;
                }
            } else {
                $html .= '<div class="menu-submenu">
                <i class="menu-arrow"></i><ul class="menu-subnav">';
            }
            foreach ($menu['parents'][$parent] as $itemId) {
                $icon = ($menu['items'][$itemId]->icon) ? $menu['items'][$itemId]->icon : '<i class="fa fa-bars"></i>';

                $header_menu = menu_group::whereId($menu['items'][$itemId]->menu_group_id)->get();

                if ($header_menu->count() > 0) {
                    $html .= '<li class="menu-section">
                    <h4 class="menu-text" style="color: #fff">' . $header_menu->first()->nama_group . '</h4>
                    <i class="menu-icon ki ki-bold-more-hor icon-md"></i>

                </li>';
                }

                if (!isset($menu['parents'][$itemId])) {
                    if (preg_match("/^http/", strtolower($menu['items'][$itemId]->link))) {
                        $html .= "<li class='menu-item' class='menu-link'><a href='" . strtolower($menu['items'][$itemId]->link) . "' class='menu-link'><i class='menu-bullet menu-bullet-line'><span></i></i>" . $menu['items'][$itemId]->nama_menu . "</a></span><span class='menu-label'>
                        <span class='label label-danger label-inline'>new</span><i class='menu-arrow'></i></li>";
                    } else {
                        if ($menu['items'][$itemId]->id_parent == 0):
                            $html .= "<li class='menu-item' class='menu-link'><a href='" . Url('/') . '' . strtolower($menu['items'][$itemId]->link) . "' class='menu-link'>" . $icon . "&nbsp;&nbsp;<span class='menu-text'>" . $menu['items'][$itemId]->nama_menu . "</span> </a></li>";
                        else:
                            $html .= "<li class='menu-item' class='menu-link'><a href='" . Url('/') . '' . strtolower($menu['items'][$itemId]->link) . "' class='menu-link'><i class='menu-bullet menu-bullet-line'><span></span></i></i><span class='menu-text'>" . $menu['items'][$itemId]->nama_menu . "</span></a></li>";
                        endif;
                    }
                }

                if (isset($menu['parents'][$itemId])) {

                    $get_count = menu::where('id_parent', $menu['items'][$itemId]->id_menu)->get()->count();
                    $fget_count = isset($get_count) ? $get_count : 0;
                    if ($fget_count % 2) {
                        $warna = 'success';
                    } else {
                        $warna = 'warning'; 
                    } 
                    if (preg_match("/^http/", strtolower($menu['items'][$itemId]->link))) {
                        $html .= "<li class='menu-item menu-item-submenu' aria-haspopup='true' data-menu-toggle='hover'><a href='" . strtolower($menu['items'][$itemId]->link) . "' class='menu-link menu-toggle'>" . $icon . "&nbsp;&nbsp;<span class='menu-text'>" . $menu['items'][$itemId]->nama_menu . "</span> <span class='menu-label'>
                        <span class='label label-" . $warna . " label-inline'>" . $fget_count . "</span>
                    </span><i class='menu-arrow'></i></a>";
                    } else {

                        $html .= "<li class='menu-item menu-item-submenu' aria-haspopup='true' data-menu-toggle='hover'><a href='" . strtolower($menu['items'][$itemId]->link) . "' class='menu-link menu-toggle'>" . $icon . "&nbsp;&nbsp;<span class='menu-text'>" . $menu['items'][$itemId]->nama_menu . "</span><span class='menu-label'>
                        <span class='label label-" . $warna . " label-inline'>" . $fget_count . "</span>
                    </span> <i class='menu-arrow'></i></a>";
                    }
                    $html .= self::buitlmenu($itemId, $menu);
                    $html .= "</li>";
                }
            }
            $html .= "</ul></div>";
        }
        return $html;
    }

    public function menu_app()
    {
        $level = Auth::user()->tmlevel_id;
        $query = menu::select(
            'id_menu',
            'nama_menu',
            'link',
            'id_parent',
            'position',
            'icon',
            'menu_group_id'
        )->where([
            "aktif" => "Ya",
        ])
            ->whereRaw("locate('$level',level) > 0 order by urutan")
            ->get();

        $menu = array('items' => array(), 'parents' => array());
        foreach ($query as $menus) {
            $menu['items'][$menus->id_menu] = $menus;
            $menu['position'][$menus->position] = $menus->position;
            $menu['parents'][$menus->id_parent][] = $menus->id_menu;
        }
        $result = self::buitlmenu(0, $menu);
        return $result;

    }

    public function AppName()
    {
        return 'Key Performance Indicator';
    }
    public function corporate()
    {
        return 'PT PUPUK INDONESIA PERSERO';
    }

    public static function status_app()
    {
        return [
            1 => 'Baru',
            2 => 'Pending',
            3 => 'Reject',
            4 => 'Approve',
        ];
    }

    public static function history_pengajua()
    {

    }

    public static function getTahunActive()
    {
        return tmtahun::select(
            'id',
            'tahun',
            'kode',
            'active',
            'created_at',
            'updated_at',
            'user_id',
        )->where('active', 1)->get();
    }

    public static function assignment_status($id)
    {
        $data = tmtable_assigment::where('id', $id)->get()->count();

        return $data > 0 ? '<span class="label label-lg label-light-success label-inline">Assingned</span>' : '<span class="label label-lg label-light-warning label-inline">Unassingned</span>';

    }

    public static function Child_prosepective($id)
    {
        $data = tmprospektif::where('parent_id', $id)->get();
        // $child = isset($data['nama_prospektif']) ? $data['nama_prospektif'] : '';
        // return $child;
        $n = [];
        foreach ($data as $datas) {
            $n = $datas['nama_prospektif'];
        }
        return $n;
    }

    public static function Parent_prosepective($child_id)
    {
        $data = tmprospektif::first();
        $paretn = $data['nama_prospektif'];
        return $paretn;
    }
    public function jenis_kamus()
    {
        return [0 => 'Parent', 1 => 'child'];
    }

    // icon action

    public function delete_icon()
    {
        return '<span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\legacy\metronic\theme\html\demo1\dist/../src/media/svg/icons\General\Trash.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"/>
            <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero"/>
            <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
        </g>
    </svg><!--end::Svg Icon--></span>';
    }
    public function edit_icon()
    {
        return '<span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\legacy\metronic\theme\html\demo1\dist/../src/media/svg/icons\Design\Edit.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"/>
            <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
            <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
        </g>
    </svg><!--end::Svg Icon--></span>';
    }

    public function view_icon()
    {
        return '<span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:C:\wamp64\www\keenthemes\legacy\metronic\theme\html\demo1\dist/../src/media/svg/icons\Text\Article.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <rect x="0" y="0" width="24" height="24"/>
            <rect fill="#000000" x="4" y="5" width="16" height="3" rx="1.5"/>
            <path d="M5.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 Z M5.5,10 L12.5,10 C13.3284271,10 14,10.6715729 14,11.5 C14,12.3284271 13.3284271,13 12.5,13 L5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 Z" fill="#000000" opacity="0.3"/>
        </g>
    </svg><!--end::Svg Icon--></span>';
    }

    public static function getActiveYear()
    {
        $data = tmtahun::select(
            'id',
            'tahun',
            'kode',
            'active',
            'created_at',
            'updated_at',
            'user_id'

        )->where('active', 1)->get();
        return $data;
    }

    public function getUnitkerja()
    {
        $data = Tmlevel::get();
        return $data;
    }
    public function AssignStatus()
    {
        return [
            1 => "Sesuai Ketentuan",
            2 => "Tidak Sesuai Ketentuan",
        ];
    }
    public static function Batch()
    {
        $data = tmtable_assigment::distinct()->select(\DB::raw('max(batch) as id_match'));
        return isset($data->first()->id_match) ? $data->first()->id_match : 1;
    }

    public function getBatch()
    {
        return tmtable_assigment::distinct()->select('batch')->get();
    }

}
