<?php
/**
 * @package org.openpsa.calendar
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Calendar filters handler
 *
 * @package org.openpsa.calendar
 */
class org_openpsa_calendar_handler_filters extends midcom_baseclasses_components_handler
implements midcom_helper_datamanager2_interfaces_edit
{
    /**
     * Handle the AJAX request
     *
     * @todo This function is unused
     */
    public function _handle_ajax()
    {
        $update_succeeded = false;
        $errstr = null;
        $user = new midcom_db_person($this->_request_data['user']->id);
        if (array_key_exists('org_openpsa_calendar_filters_add', $_POST))
        {
            try
            {
                $target = new midcom_db_person($_POST['org_openpsa_calendar_filters_add']);
                $update_succeeded = $user->parameter('org_openpsa_calendar_show', $_POST['org_openpsa_calendar_filters_add'], 1);
            }
            catch (midcom_error $e)
            {
                $e->log();
            }
        }
        else if (array_key_exists('org_openpsa_calendar_filters_remove', $_POST))
        {
            try
            {
                $target = new midcom_db_person($_POST['org_openpsa_calendar_filters_remove']);
                $update_succeeded = $user->parameter('org_openpsa_calendar_show', $_POST['org_openpsa_calendar_filters_remove'], '');
            }
            catch (midcom_error $e)
            {
                $e->log();
            }
        }
        $errstr = midcom_connection::get_error_string();

        $response = new midcom_response_xml;
        $response->result = $update_succeeded;
        $response->status = $errstr;
        $response->send();
        //This will exit.
    }

    public function load_schemadb()
    {
        return midcom_helper_datamanager2_schema::load_database($this->_config->get('schemadb_filters'));
    }

    /**
     * Handle the request for editing contact list
     *
     * @param String $handler_id    Name of the request handler
     * @param array $args           Variable arguments
     * @param array &$data          Public request data, passed by reference
     */
    public function _handler_edit($handler_id, array $args, array &$data)
    {
        midcom::get('auth')->require_valid_user();

        // Get the current user
        $this->_person = new midcom_db_person(midcom_connection::get_user());
        $this->_person->require_do('midgard:update');

        // Load the controller
        $data['controller'] = $this->get_controller('simple', $this->_person);

        // Process the form
        switch ($data['controller']->process_form())
        {
            case 'save':
            case 'cancel':
                if (isset($_GET['org_openpsa_calendar_returnurl']))
                {
                    $url = $_GET['org_openpsa_calendar_returnurl'];
                }
                else
                {
                    $url = '';
                }
                midcom::get()->relocate($url);
                // This will exit
        }

        // Add the breadcrumb pieces
        if (isset($_GET['org_openpsa_calendar_returnurl']))
        {
            $this->add_breadcrumb($_GET['org_openpsa_calendar_returnurl'], $this->_l10n->get('calendar'));
        }

        $this->add_breadcrumb('filters/', $this->_l10n->get('choose calendars'));
    }

    /**
     * Show the contact editing interface
     *
     * @param String $handler_id    Name of the request handler
     * @param array &$data          Public request data, passed by reference
     */
    public function _show_edit($handler_id, array &$data)
    {
        $data['person'] =& $this->_person;

        midcom_show_style('calendar-filter-chooser');
    }
}
?>