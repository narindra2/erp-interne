<?php

use App\Models\Menu;
use App\Models\Purchase;

/** Make menu user's localisation in views\layout\demo1\aside\_menu.blade.php */
if (!function_exists('get_menus_list')) {
    function get_menus_list( $auth_user, $key = "")
    {
        /** Verical menu */
        $menu_vertical = $menu_horizontal = [];
        if ($auth_user->isAdmin() || $auth_user->isHR()) {
            $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Utilisateurs</span>',];
            $menu_vertical[] = ["title" => __("lang.collaborator"), 'path'  => 'users',  'icon'  => '<i class="fas fa-users fs-3"></i>'];
            $menu_vertical[] = ["title" => "Projet-membres", 'path'  => '/user/projet-membre',   'icon' => '<i class="fas fa-toolbox"></i>'];
            $menu_vertical[] = ["title" => __("lang.job"), 'path'  => 'jobs',  'icon'  => '<i class="fas fa-solid fa-briefcase fs-3"></i>'];
            $menu_vertical[] = ["title" => __("lang.sanctions"), 'path'  => '/users/sanctions/index',  'icon'  => ' <i class="fas fa-balance-scale fs-3"></i> '];
            /*
                $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Gestion messages</span>',];
                $menu_vertical[] = ["title" => "Messagerie", 'path'  => '/messaging',   'icon' => '<i class="far fa-comment fs-3"></i>'];
                $menu_vertical[] = ["title" => "Gestion canaux", 'path'  => '/messaging',   'icon' => '<i class="fas fa-comment-medical fs-3"></i>'];
                $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Gestion CERFA</span>',];
                $menu_vertical[] = ["title" => "Déclarants", 'path'  => '/cerfa/customer',   'icon' => '<i class="far fa-user fs-3"></i>'];
            **/
            $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Congé et permission</span>'];
            $menu_vertical[] = ["title" => "Ajouter un rapport d'état", 'path'  => '/days-off',  'icon'  => '<i class="far fa-list-alt fs-3"></i>'];
            $menu_vertical[] = ["title" => __("lang.days_of_types"), 'path'  => '/days-off/type',  "icon" => '<i class="fas fa-users-cog fs-3"></i>'];
            $menu_vertical[] = ["title" => __("lang.upgrade_days_off"), 'path'  => '/days-off/upgrade',  "icon" => '<i class="fas fa-chart-line fs-3"></i>'];
            $menu_vertical[] = ["title" => __("lang.public-holiday"), 'path'  => '/public-holidays',  "icon" => '<i class="fas fa-gift fs-3"></i>'];
            $menu_vertical[] = ["title" => "Récupération Heure", 'path'  => '/hour-recoveries',  "icon" => '<i class="fas fa-clock fs-3"></i>'];
            
            $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Pointage</span>'];
            $menu_vertical[] = ["title" => __("lang.web_pointing"), 'path'  => '/users-pointing',  'icon'  => '<i class="fas fa-id-card-alt fs-3"></i>'];
            $menu_vertical[] = ["title" => __("lang.import_excel"), 'path'  => '/users-pointing-excel',  "icon" => '<i class="fas fa-fingerprint fs-3"></i>'];
            $menu_vertical[] = ["title" => "Complément Heure", 'path'  => '/complement-hours',  "icon" => '<i class="fas fa-fingerprint fs-3"></i>'];
            $menu_vertical[] = ["title" => "Pointage temporaire", 'path'  => '/pointing-temp',  "icon" => '<i class="fas fa-fingerprint fs-3"></i>'];

            $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Outils</span>'];
            $menu_vertical[] = ["title" => "Ticketing", 'path'   => 'tickets',  'icon'  => ' <i class="fas fa-clipboard-list fs-3"></i> '];
            $menu_vertical[] = ["title" => "Besoin ticket", 'path'  => '/needToBuy',   'icon' => '<i class="fas fa-clipboard-list fs-3"></i>'];
            $menu_vertical[] = ["title" => "Tâches", 'path'  => '/tâche/list',   'icon' => '<i class="far fa-calendar-check fs-3"></i>'];
            $menu_vertical[] = ["title" => "SDF", 'path'  => '/salle-de-reunion',  'icon'  => '<i class="fas fa-user-clock"></i>'];
            $menu_vertical[] = ["title" => "Suivis & prod", 'path'  => '/suivi/v2/projet',   'icon' => '<i class="fas fa-tasks fs-3"></i>'];
            // $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Suivi</span>'];


            $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Gestion de stock</span>'];
            // $menu_vertical[] = ["title" => "Stock", 'path'  => 'item-movements',  'icon'  => '<i class="fas fa-project-diagram fs-3"></i> '];
            $menu_vertical[] = ["title" => "Stock", 'path'  => 'stock/gerer',  'icon'  => ' <i class="fas fa-project-diagram fs-3"></i> '];
            $menu_vertical[] = ["title" => "Achats", 'path'  => 'purchases',  'icon'  => ' <i class="fas fa-cart-arrow-down fs-3"></i> '];
            $menu_vertical[] = ["title" => "Article", 'path'  => 'items',  'icon'  => ' <i class="fas fa-list-ol  fs-3 "></i> '];
            // $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Debugage</span>'];
            // $menu_vertical[] = ["title" => "Debug/Outils", 'path'  => '/outils-debug',  'icon'  => '<i class="fas fa-wrench"></i>'];
        } elseif (!$auth_user->isAdmin() || !$auth_user->isHR()) {
            /**Profil */
            $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Utilisateurs & Compte</span>',];
            $menu_vertical[] = ["title" => __("lang.my_profile"), 'path'  => '/account/settings',   'icon' => '<i class="fas fa-user fs-3"></i>'];
            /** Congé */
            $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Congé & demande</span>',];
            $menu_vertical[] = ["title" => "Demande des jours de congé", 'path'  => '/my-days-off',   'icon' => '<i class="fas fa-walking fs-3"></i>'];
            $menu_vertical[] = ["title" => "Récupération Heure", 'path'  => '/hour-recoveries',   'icon' => '<i class="fas fa-clock fs-3"></i>'];
            /** Outils erp */
            $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Outils</span>',];
            $menu_vertical[] = ["title" => "Ticketing", 'path'  => '/tickets',   'icon' => '<i class="fas fa-clipboard-list fs-3"></i>'];
            $menu_vertical[] = ["title" => "Tâches", 'path'  => '/tache/list',   'icon' => '<i class="far fa-calendar-check fs-3"></i>'];
            $menu_vertical[] = ["title" => "SDF", 'path'  => '/salle-de-reunion',  'icon'  => '<i class="fas fa-user-clock"></i>'];
            /*** Menu provisiore pour specifique dessi et mdp */
            if (in_array($auth_user->registration_number, Menu::$USER_ALLOWED_PART_ACCESS["suivi_testeur"])) {
                $menu_vertical[] = ["title" => "Suivis", 'path'  => '/suivi/v2/projet',   'icon' => '<i class="fas fa-tasks fs-3"></i>'];
            }
            if ($auth_user->isTech()) {
                $menu_vertical[] = ["title" => "Besoin ticket", 'path'  => '/needToBuy',   'icon' => '<i class="fas fa-clipboard-list fs-3"></i>'];
                $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Gestion de stock</span>'];
                // $menu_vertical[] = ["title" => "Stock", 'path'  => 'item-movements',  'icon'  => ' <i class="fas fa-clipboard-list fs-3"></i> '];
                $menu_vertical[] = ["title" => "Stock", 'path'  => 'stock/gerer',  'icon'  => ' <i class="fas fa-project-diagram fs-3"></i> '];
                $menu_vertical[] = ["title" => "Achats", 'path'  => 'purchases',  'icon'  => ' <i class="fas fa-clipboard-list fs-3"></i> '];
                $menu_vertical[] = ["title" => "Article", 'path'  => 'items',  'icon'  => ' <i class="fas fa-clipboard-list fs-3"></i> '];
            }
            if (in_array($auth_user->registration_number,  Menu::$USER_ALLOWED_PART_ACCESS["debug_tools"])) {
                $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Debugage</span>'];
                $menu_vertical[] = ["title" => "Debug/Outils", 'path'  => '/outils-debug',  'icon'  => '<i class="fas fa-wrench"></i>'];
            }
            if (Purchase::whereRaw('FIND_IN_SET("' . $auth_user->id . '", tagged_users)')->whereDeleted(0)->first()) {
                $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Achat</span>'];
                $menu_vertical[] = ["title" => "Achats", 'path'  => 'purchases',  'icon'  => ' <i class="fas fa-cart-arrow-down fs-3"></i> '];
            }
        }

        // $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Réunion</span>'];

        $menu_vertical[] = ["classes" => ['content' => 'pt-8 pb-2'], 'content' => '<span class="menu-section text-muted text-uppercase fs-8 ls-1">Informations</span>'];
        $menu_vertical[] = ["title" => "Règlement Intérieur", 'path'  => '/informations',  'icon'  => '<i class="fas fa-ruler-vertical fs-3"></i>'];
        $menu_vertical[] = ["title" => "Fonctionnement en Interne", 'path' => '/Guides', 'icon' => '<i class="fas fa-ruler-vertical fs-3"></i>'];

        $menu = ["main" => $menu_vertical, "horizontal" => $menu_horizontal];
        if ($key) {
            return get_array_value($menu, $key);
        }
        return $menu;
    }
}