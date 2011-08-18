<?php
/**
 * @package org.openpsa.interviews
 * @author The Midgard Project, http://www.midgard-project.org
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Phone interview handler
 *
 * @package org.openpsa.interviews
 */
class org_openpsa_interviews_handler_interview extends midcom_baseclasses_components_handler
implements midcom_helper_datamanager2_interfaces_edit
{
    /**
     * The member we're interviewing
     *
     * @var org_openpsa_directmarketing_campaign_member
     */
    private $_member = null;

    /**
     * Loads and prepares the schema database.
     *
     * Special treatment is done for the name field, which is set readonly for non-creates
     * if the simple_name_handling config option is set. (using an auto-generated urlname based
     * on the title, if it is missing.)
     *
     * The operations are done on all available schemas within the DB.
     */
    public function load_schemadb()
    {
        return midcom_helper_datamanager2_schema::load_database($this->_config->get('schemadb'));
    }

    /**
     * @param mixed $handler_id The ID of the handler.
     * @param Array $args The argument list.
     * @param Array &$data The local request data.
     */
    public function _handler_interview($handler_id, array $args, array &$data)
    {
        $this->_member = new org_openpsa_directmarketing_campaign_member_dba($args[0]);
        $this->_member->require_do('midgard:update');
        $data['campaign'] = new org_openpsa_directmarketing_campaign_dba($this->_member->campaign);

        $data['controller'] = $this->get_controller('simple', $this->_member);

        switch ($data['controller']->process_form())
        {
            case 'save':
                // Redirect to next interviewee
                $_MIDCOM->relocate($_MIDCOM->get_context_data(MIDCOM_CONTEXT_ANCHORPREFIX) . "next/{$data['campaign']->guid}/");
                // This will exit.

            case 'cancel':
                // Clear lock and return to summary
                $this->_member->orgOpenpsaObtype = org_openpsa_directmarketing_campaign_member_dba::NORMAL;
                $this->_member->update();

                $_MIDCOM->relocate($_MIDCOM->get_context_data(MIDCOM_CONTEXT_ANCHORPREFIX) . "campaign/{$data['campaign']->guid}/");
                // This will exit.
        }
    }

    /**
     *
     * @param mixed $handler_id The ID of the handler.
     * @param array &$data The local request data.
     */
    public function _show_interview($handler_id, array &$data)
    {
        $this->_request_data['person'] = new midcom_db_person($this->_member->person);
        midcom_show_style('show-interview');
    }
}
?>