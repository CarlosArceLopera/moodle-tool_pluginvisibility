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

        // Get the course.
        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

        // Get the category.
        $category = \core_course_category::get($course->category);

        return self::get_hidden_plugins_for_category($category->id);
    }

    /**
     * Get hidden plugins for a specific category, considering parent categories with recursion.
     *
     * @param int $categoryid The category ID
     * @return array Array of hidden plugin names grouped by type ['mod' => [...], 'block' => [...]]
     */
    public static function get_hidden_plugins_for_category($categoryid) {
        global $DB;

        $hiddenmodules = [];
        $hiddenblocks = [];

        // Get the category path (from root to current category).
        $category = \core_course_category::get($categoryid);
        $categorypath = array_reverse($category->get_parents()); // Get parents from root to immediate parent.
        $categorypath[] = $categoryid; // Add current category.

        // Query for hidden plugins in the category path.
        // We need to check each category in the path, considering applytosubcategories flag.
        list($insql, $params) = $DB->get_in_or_equal($categorypath, SQL_PARAMS_NAMED);

        $sql = "SELECT *
                  FROM {tool_pluginvisibility_hidden}
                 WHERE categoryid $insql
              ORDER BY categoryid ASC";

        $records = $DB->get_records_sql($sql, $params);

        // Process records considering the hierarchy.
        $processedcategories = [];

        foreach ($categorypath as $catid) {
            foreach ($records as $record) {
                if ($record->categoryid == $catid) {
                    // Check if this setting should apply.
                    $shouldapply = false;

                    if ($catid == $categoryid) {
                        // This is the current category, always apply.
                        $shouldapply = true;
                    } else if ($record->applytosubcategories == 1) {
                        // This is a parent category with recursion enabled.
                        $shouldapply = true;
                    }

                    if ($shouldapply) {
                        if ($record->plugintype === 'mod') {
                            $hiddenmodules[$record->pluginname] = $record->pluginname;
                        } else if ($record->plugintype === 'block') {
                            $hiddenblocks[$record->pluginname] = $record->pluginname;
                        }
                    }
                }
            }
        }

        return [
            'mod' => array_values($hiddenmodules),
            'block' => array_values($hiddenblocks),
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
