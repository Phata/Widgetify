<?php

/**
 * Unit test for Phata\Widgetfy\Site\Metacafe
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
 * This file is a unit test for
 * - Phata\Widgetfy\Site\Metacafe
 *
 * @package   Widgetfy
 * @author    Koala Yeung <koalay@gmail.com>
 * @copyright 2014 Koala Yeung
 * @licence   http://www.gnu.org/licenses/lgpl.html
 * @link      http://github.com/Phata/Widgetfy
 */

use Phata\Widgetfy\Site\Metacafe as Metacafe;
use PHPUnit\Framework\TestCase;

class MetacafeTest extends TestCase {

    public function testTranslateVideo() {
        $url = 'http://www.metacafe.com/watch/11395429/arma_iii_altis_life_honest_farmers_lan_party/';
        $url_parsed = parse_url($url);
        $this->assertNotFalse($info = Metacafe::preprocess($url_parsed));

        // test returning embed code
        $options = array('width'=>640);
        $embed = Metacafe::translate($info, $options);

        // Note: Metacafe only support HTTP. Not HTTPS
        $this->assertEquals($embed['html'],
            '<iframe src="http://www.metacafe.com/embed/11395429/" '.
            'width="640" height="361" allowFullScreen frameborder=0></iframe>'
        );
        $this->assertEquals($embed['type'], 'iframe');
        $this->assertEquals($embed['dimension']->factor, 0.5633);
    }

}
