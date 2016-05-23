<?php
/*
 * Libravatar plugin for Status.net
 * Copyright (C) 2012 Melissa Draper <melissa@catalyst.net.nz>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package LibravatarPlugin
 * @maintainer Melissa Draper <melissa@catalyst.net.nz>
 */

if (!defined('STATUSNET') && !defined('LACONICA')) {
    // This check helps protect against security problems;
    // your code file can't be executed directly from the web.
    exit(1);
}

class LibravatarPlugin extends Plugin
{
    function onEndProfileGetAvatar(Profile $profile, $size, Avatar &$avatar=null)
    {
        if (empty($avatar)) {
            try {
                $user = $profile->getUser();
                if (!empty($user) && !empty($user->email)) {
                    // Fake one!
                    $avatar = new Avatar();
                    $avatar->width = $avatar->height = $size;
                    $avatar->url = $this->libravatar_url($user->email, $size);
                    return false;
                }
            } catch (Exception $e) {
                common_log(LOG_DEBUG, "Couldn't find User for Profile id: " . $profile->id . " (" . $profile->nickname . ")");
            }
        }

        return true;
    }

    function libravatar_url($email, $size)
    {
        global $config;
        $defaultavatar = Avatar::defaultImage($size);

        if (isset($config['Libravatar']) && isset($config['Libravatar']['nocheck']) && $config['Libravatar']['nocheck'] === true) {
           include_once 'Services/Libravatar.php';
        } else {
            try {
                if (function_exists('stream_resolve_include_path') && stream_resolve_include_path('Services/Libravatar.php')) {
                    include_once 'Services/Libravatar.php';
                }
            } catch (exception $e) {
                return $defaultavatar;
            }
        }

        if (!class_exists('Services_Libravatar')) {
            return $defaultavatar;
        }

        $libravatar = new Services_Libravatar();
        $libravatar->setSize($size)
                   ->setDefault(Avatar::defaultImage($size))
                   ->setHttps(true);
        $url = $libravatar->getUrl($email);

        return $url;

    }

    function onPluginVersion(array &$versions)
    {
        $versions[] = array('name' => 'Libravatar',
                            'version' => GNUSOCIAL_VERSION,
                            'author' => 'Melissa Draper, Eric Helgeson, Evan Prodromou',
                            'homepage' => 'https://github.com/chimo/gs-libravatar',
                            'rawdescription' =>
                            // TRANS: Plugin description.
                            _m('The Libravatar plugin allows users to use their <a href="http://www.libravatar.org/">Libravatar</a> with StatusNet.'));

        return true;
    }
}
