<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tool_pluginvisibility\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;

/**
 * Privacy provider for Plugin Visibility plugin
 *
 * @package     tool_pluginvisibility
 * @copyright   Carlos Arce <carlosarcelopera@catalyst-ca.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Describe stored personal data.
     */
    public static function get_metadata(collection $collection): collection {

        $collection->add_database_table(
            'tool_pluginvisibility_hidden',
            [
                'categoryid' => 'privacy:metadata:tool_pluginvisibility_hidden:categoryid',
                'plugintype' => 'privacy:metadata:tool_pluginvisibility_hidden:plugintype',
                'pluginname' => 'privacy:metadata:tool_pluginvisibility_hidden:pluginname',
                'applytosubcategories' => 'privacy:metadata:tool_pluginvisibility_hidden:applytosubcategories',
                'timecreated' => 'privacy:metadata:tool_pluginvisibility_hidden:timecreated',
                'timemodified' => 'privacy:metadata:tool_pluginvisibility_hidden:timemodified',
                'usermodified' => 'privacy:metadata:tool_pluginvisibility_hidden:usermodified',
            ],
            'privacy:metadata:tool_pluginvisibility_hidden'
        );

        return $collection;
    }

    /**
     * Get contexts containing user data.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {

        $contextlist = new contextlist();

        $sql = "SELECT ctx.id
                  FROM {context} ctx
                 WHERE ctx.contextlevel = :contextlevel
                   AND EXISTS (
                       SELECT 1
                         FROM {tool_pluginvisibility_hidden} h
                        WHERE h.usermodified = :userid
                   )";

        $params = [
            'contextlevel' => CONTEXT_SYSTEM,
            'userid' => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get users who have data in the context.
     */
    public static function get_users_in_context(userlist $userlist) {

        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_SYSTEM) {
            return;
        }

        $sql = "SELECT usermodified
                  FROM {tool_pluginvisibility_hidden}";

        $userlist->add_from_sql('usermodified', $sql, []);
    }

    /**
     * Export user data.
     */
    public static function export_user_data(approved_contextlist $contextlist) {

        global $DB;

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_SYSTEM) {
                continue;
            }

            $records = $DB->get_records('tool_pluginvisibility_hidden', [
                'usermodified' => $userid,
            ]);

            if (!$records) {
                continue;
            }

            writer::with_context($context)->export_data(
                ['pluginvisibility'],
                (object)['records' => array_values($records)]
            );
        }
    }

    /**
     * Delete all user data in the specified context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {

        global $DB;

        if ($context->contextlevel != CONTEXT_SYSTEM) {
            return;
        }

        $DB->delete_records('tool_pluginvisibility_hidden');
    }

    /**
     * Delete data for a specific user.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {

        global $DB;

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_SYSTEM) {
                continue;
            }

            $DB->delete_records('tool_pluginvisibility_hidden', [
                'usermodified' => $userid,
            ]);
        }
    }

    /**
     * Delete data for multiple users.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {

        global $DB;

        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_SYSTEM) {
            return;
        }

        $userids = $userlist->get_userids();

        if (empty($userids)) {
            return;
        }

        [$insql, $params] = $DB->get_in_or_equal($userids);

        $DB->delete_records_select(
            'tool_pluginvisibility_hidden',
            "usermodified $insql",
            $params
        );
    }
}
