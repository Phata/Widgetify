<?php

/**
 * class Phata\Widgetfy\Site\Vimeo
 *
 * Licence:
 *
 * This file is part of Widgetfy.
 *
 * Widgetfy is free software: you can redistribute
 * it and/or modify it under the terms of the GNU
 * Lesser General Public License as published by the
 * Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Widgetfy is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser
 * General Public Licensefor more details.
 *
 * You should have received a copy of the GNU Lesser
 * General Public License along with Widgetfy.  If
 * not, see <http://www.gnu.org/licenses/lgpl.html>.
 *
 * Description:
 *
 * This file defines Phata\Widgetfy\Site\Youtube
 * which is a site definition that implements
 * Phata\Widgetfy\Site\Common
 *
 * @package   Widgetfy
 * @author    Koala Yeung <koalay@gmail.com>
 * @copyright 2014 Koala Yeung
 * @licence   http://www.gnu.org/licenses/lgpl.html
 * @link      http://github.com/Phata/Widgetfy
 */

namespace Phata\Widgetfy\Site;

use Phata\Widgetfy\Utils\Cache as Cache;
use Phata\Widgetfy\Utils\Dimension as Dimension;

class Vimeo implements Common {

    /**
     * Implements Phata\Widgetfy\Site\Common::translate
     *
     * preprocess the URL
     * by this site adapter
     * @param string[] $url_parsed result of parse_url($url)
     * @return mixed array of preprocess result; boolean FALSE if not translatable
     */
    public static function preprocess($url_parsed) {
        if (preg_match('/^\/([\d]+)$/',
            $url_parsed['path'], $matches) == 1) {
            return array(
                'vid' => $matches[1],
            );
        }
        return FALSE;
    }

    /**
     * Implements Phata\Widgetfy\Site\Common::translate
     *
     * translate the provided URL into
     * HTML embed code of it
     * @param mixed[] $info array of preprocessed url information
     * @param mixed[] $options array of options
     * @return mixed[] array of embed information or NULL if not applicable
     */
    public static function translate($info, $options=array()) {
        Cache::init();
        $cache_group = 'vimeo';
        $vid = $info['vid'];

        // try to retrieve api respond with the help of cache
        if (Cache::exists($cache_group, $vid)) {
            $cache = Cache::get($cache_group, $vid);
            $api_respond = $cache['value'];
        } else {
            $api_respond = file_get_contents('https://vimeo.com/api/v2/video/'.$vid.'.php');
            $api_respond = unserialize($api_respond);
            Cache::set($cache_group, $vid, $api_respond);
        }

        if (($api_respond !== FALSE) && !empty($api_respond)) {

            // read width, height from API
            $width  = !empty($api_respond[0]['width']) ? $api_respond[0]['width'] : 640;
            $height = !empty($api_respond[0]['height']) ? $api_respond[0]['height'] : 320;

            // calculate the factor from API results
            $factor = round($height / $width, 4);

            // default dimension is calculated
            $d = Dimension::fromOptions($options, array(
                'factor' => $factor, // calculated
                'default_width'=> 640,
            ));

            return array(
                'type' => 'iframe',
                'html' => '<iframe src="//player.vimeo.com/video/'.$vid.'" '.
                    $d->toAttr().' '.
                    'frameborder="0" webkitallowfullscreen '.
                    'mozallowfullscreen allowfullscreen></iframe>',
                'dimension' => $d,
            );
        }

        return NULL;
	}
}