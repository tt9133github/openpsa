<?php
/**
 * @package org.openpsa.contacts
 * @author The Midgard Project, http://www.midgard-project.org
 * @version $Id: clean.php 23975 2009-11-09 05:44:22Z rambo $
 * @copyright The Midgard Project, http://www.midgard-project.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */

/**
 * Cron handler for clearing tokens from old send receipts
 * @package org.openpsa.contacts
 */
class org_openpsa_contacts_cron_duplicates_clean extends midcom_baseclasses_components_cron_handler
{
    /**
     * Find hanging duplicate marks (that no longer point anywhere) and clear them
     */
    function _on_execute()
    {
        debug_push_class(__CLASS__, __FUNCTION__);
        debug_add('_on_execute called');
        if (!$this->_config->get('enable_duplicate_search'))
        {
            debug_add('Duplicate operations disabled, aborting', MIDCOM_LOG_INFO);
            debug_pop();
            return;
        }

        // Untill the FIXME below is handled we abort
        debug_add('Duplicate cleanup disabled since it needs code cleanup for 1.8 Midgfard, aborting', MIDCOM_LOG_ERROR);
        debug_pop();
        return;

        ignore_user_abort();

        $qb = new midgard_query_builder('midgard_parameter');
        $qb->add_constraint('domain', '=', 'org.openpsa.contacts.duplicates:possible_duplicate');
        $qb->add_order('name', 'ASC');
        $results = @$qb->execute();
        foreach($results as $param)
        {
            $obj = $_MIDCOM->dbfactory->get_object_by_guid($param->name);
            if (   !is_object($obj)
                || empty($obj->guid))
            {
                debug_add("GUID {$param->name} points to nonexistent person, removing possible duplicate mark", MIDCOM_LOG_INFO);
                $stat = $param->delete();
                if (!$stat)
                {
                    debug_add("Failed to delete parameter {$param->guid}, errstr: " . midcom_connection::get_error_string(), MIDCOM_LOG_ERROR);
                }
            }
        }

        debug_add('Done');
        debug_pop();
        return;
    }
}
?>