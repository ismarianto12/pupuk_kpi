<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Menu_app
{
    private static function set_menu($module_name = NULL, $title = NULL, $css_class = NULL, $target = NULL)
    {
        $structure = NULL;
        if ($module_name !== NULL || $module_name !== '')
            if ($css_class === NULL) {
                $structure = "<li class='menu-item'><a href='" . $module_name  . "' " . $target . "  class='menu-link'>" . $title . "</a></li>";
            } else {
                $structure = "<li class='" . $css_class . "'><a href='" . $module_name  . "'><span class='sub-item'></span>" . $title . "</a></li>";
            }

        return $structure;
    }
    private static function menu_single($module_name, $font, $title)
    {

        $structure = '<li class="nav-item">
							<a href="' . $module_name . '">
                                ' . $font . '
                            <p>' . $title . '</p>
								<span class="badge badge-success">1</span>
							</a>
						</li>';
        return $structure;
    }
    private static function parent_dropdown($judul, $icon = NULL)
    {
        $structure = '';
        if ($icon === NULL) {
            $structure .= '<li class="nav-item">
            <a data-toggle="collapse" href="#tables">
                <i class="fas fa-list"></i>
                <p>' . $judul . '</p>
                <span class="caret"></span>
            </a>
            <div class="collapse" id="tables">
                <ul class="nav nav-collapse">';
        } else {
            $structure .= '<li class="nav-item">
            <a data-toggle="collapse" href="#tables">
                <i class="fas fa-' . $icon . '"></i>
                <p>' . $judul . '</p>
                <span class="caret"></span>
            </a>
            <div class="collapse" id="tables">
                <ul class="nav nav-collapse">';
        }
        return $structure;
    }
    public static function tutup_menu()
    {
        $structure = '</ul>
        </div>
    </li>';
        return $structure;
    }

    public static function list_menu()
    {
        $menu = '';
        $user_id = Auth::user()->id;
        $query   = DB::table('users')
            ->select('users.id', 'users.username', 'tmlevel_access.level', 'tmlevel_access.id as level_id')
            ->join('tmlevel_access', 'users.tmlevel_access_id', '=', 'tmlevel_access.id')
            ->where('users.id', $user_id)
            ->get();


        foreach ($query as $ls) {
            switch ($ls->level_id) {
                case 1:
                    require_once 'Menu/admin.php';
                    break;
                case 2:
                    require_once 'Menu/user.php';

                    break;
                default:
                    $menu .= '<li>Null Route Menu</li>';



                    break;
            }
        }
        return $menu;
    }

    // parsing null data menu 
}
