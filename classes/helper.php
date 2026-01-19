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

/**
 * Plugin visibility helper class.
 *
 * @package    tool_pluginvisibility
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginvisibility;

/**
 * Helper class for plugin visibility functionality.
 *
 * @package    tool_pluginvisibility
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Get hidden plugins for a specific course, considering category hierarchy and recursion.
     *
     * @param int $courseid The course ID
     * @return array Array of hidden plugin names grouped by type ['mod' => [...], 'block' => [...]]
     */
    public static function get_hidden_plugins_for_course($courseid) {
        global $DB;

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        $category = \core_course_category::get($course->category);

        return self::get_hidden_plugins_for_category($category->id);
    }

    /**
     * Get hidden plugins for a specific category.
     *
     * @param int $categoryid The category ID
     * @return array Array of hidden plugin names grouped by type ['mod' => [...], 'block' => [...], 'tiny' => [...]]
     */
    public static function get_hidden_plugins_for_category($categoryid) {
        global $DB;

        $hiddenmodules = [];
        $hiddenblocks = [];
        $hiddentinymce = [];

        // Get all records for this specific category.
        $records = $DB->get_records('tool_pluginvisibility_hidden', ['categoryid' => $categoryid]);

        foreach ($records as $record) {
            if ($record->plugintype === 'mod') {
                $hiddenmodules[$record->pluginname] = $record->pluginname;
            } else if ($record->plugintype === 'block') {
                $hiddenblocks[$record->pluginname] = $record->pluginname;
            } else if ($record->plugintype === 'tiny') {
                $hiddentinymce[$record->pluginname] = $record->pluginname;
            }
        }

        return [
            'mod' => array_values($hiddenmodules),
            'block' => array_values($hiddenblocks),
            'tiny' => array_values($hiddentinymce),
        ];
    }

    /**
     * Get hidden modules (activities/resources) for a course as a simple array.
     *
     * @param int $courseid The course ID
     * @return array Array of hidden module names
     */
    public static function get_hidden_modules_for_course($courseid) {
        $hidden = self::get_hidden_plugins_for_course($courseid);
        return $hidden['mod'];
    }

    /**
     * Get hidden blocks for a course as a simple array.
     *
     * @param int $courseid The course ID
     * @return array Array of hidden block names
     */
    public static function get_hidden_blocks_for_course($courseid) {
        $hidden = self::get_hidden_plugins_for_course($courseid);
        return $hidden['block'];
    }
}
