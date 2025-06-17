<?php

namespace App\Helpers;

use CodeIgniter\Database\DatabaseInterface;
use Config\Services;

if (!function_exists('menuGenerate')) {
    function menuGenerate() {
        $session = Services::session();
        $db = \Config\Database::connect();
        
        $user_session_data = $session->get('login_sess_data');

        if (!$user_session_data) {
            return ''; // Handle unauthenticated user case
        }

        $roleid = $user_session_data['role_id'];
        $usergroupid = $user_session_data['group_id'];
        $returnarr = [];
        $menustring = '';

        if ($usergroupid == 1) {
            $get_submodules = $db->query("SELECT modulename, icon, submodule_name, url, hasnochild, modules.priority as moduleorder, submodules.priority as submodorder
                                            FROM submodules
                                            INNER JOIN modules ON modules.id = submodules.moduleid
                                            WHERE isdisplay = 1 AND submodules.active = 1 AND modules.id NOT IN (2,5,8)
                                            ORDER BY modules.priority ASC, submodules.priority ASC")->getResult();
        } else {
            $get_submodules = $db->query("SELECT modulename, icon, submodule_name, url, hasnochild, modules.priority as moduleorder, submodules.priority as submodorder
                                            FROM submodules
                                            INNER JOIN modules ON modules.id = submodules.moduleid
                                            INNER JOIN modsubmodpermission ON modsubmodpermission.submoduleid = submodules.id
                                            WHERE isdisplay = 1 AND modules.active = 1 AND submodules.active = 1
                                            AND modsubmodpermission.groupid = ? AND modsubmodpermission.roleid = ? AND modsubmodpermission.active = 1
                                            AND modules.id NOT IN (5)
                                            ORDER BY modules.priority ASC, submodules.priority ASC", [$usergroupid, $roleid])->getResult();
        }

        if (!empty($get_submodules)) {
            foreach ($get_submodules as $value) {
                $returnarr[$value->modulename][] = $value;
            }

            foreach ($returnarr as $key => $value) {
                $counter = count($value);
                $active_class = "active";

                if ($value[0]->hasnochild == 1) {
                    if ($key == 'Report') {
                        $menustring .= '<li class="nav-item" data-toggle="tooltip" data-placement="right" title="' . $key . '">
                                            <a class="nav-link gb_zb mdBt1" dat_link="' . site_url('/') . $value[0]->url . '" dat_ifrwidth="770" dat_ifrheight="500" title="' . $key . '">
                                                <i class="fa fa-fw ' . $value[0]->icon . '"></i>
                                                <span class="nav-link-text">' . $key . '</span>
                                            </a>';
                    } else {
                        $menustring .= '<li class="nav-item" data-toggle="tooltip" data-placement="right" title="' . $key . '">
                                            <a class="nav-link" href="' . site_url('/') . $value[0]->url . '">
                                                <i class="fa fa-fw ' . $value[0]->icon . '"></i>
                                                <span class="nav-link-text">' . $key . '</span>
                                            </a>';
                    }
                } else {
                    for ($i = 0; $i < $counter; $i++) {
                        if ($i == 0) {
                            $menustring .= '<li class="nav-item" data-toggle="tooltip" data-placement="right" title="' . $key . '">
                                            <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseComponents' . strtolower(str_replace(" ", "", $key)) . '" data-parent="#exampleAccordion">
                                                <i class="fa fa-fw ' . $value[0]->icon . '"></i>
                                                <span class="nav-link-text">' . $key . '</span>
                                            </a>';
                            $menustring .= '<ul class="sidenav-second-level collapse" id="collapseComponents' . strtolower(str_replace(" ", "", $key)) . '">';
                        }

                        $menustring .= '<li>
                                        <a href="' . site_url('/') . $value[$i]->url . '">' . $value[$i]->submodule_name . '</a>
                                    </li>';
                    }

                    $menustring .= '</ul>';
                }
                $menustring .= '</li>';
            }
        }

        echo $menustring;
    }
}
