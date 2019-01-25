<?php
/**
 * @package openpsa.test
 * @author CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @copyright CONTENT CONTROL http://www.contentcontrol-berlin.de/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

namespace midcom\datamanager\test;

use openpsa_testcase;
use midcom_core_context;
use midcom\routing\parser;

/**
 * OpenPSA testcase
 *
 * @package openpsa.test
 */
class parserTest extends openpsa_testcase
{
    public function test__construct()
    {
        $context = new midcom_core_context();
        $context->set_key(MIDCOM_CONTEXT_URI, '/list/0/');
        $parser = new parser($context);
        $this->assertEquals(['list', '0'], $parser->argv);
    }
}
