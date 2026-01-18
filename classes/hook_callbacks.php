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
 * Hook callbacks for tool_pluginvisibility.
 *
 * @package    tool_pluginvisibility
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginvisibility;

/**
 * Hook callbacks.
 *
 * @package    tool_pluginvisibility
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {

    /**
     * Inject JavaScript to add menu item to category action menus.
     *
     * @param \core\hook\output\before_standard_top_of_body_html_generation $hook
     */
    public static function before_standard_top_of_body_html(
        \core\hook\output\before_standard_top_of_body_html_generation $hook
    ): void {
        global $PAGE, $COURSE;

        // Only load on course management page.
        if (strpos($PAGE->url->get_path(), '/course/management.php') !== false) {
            $PAGE->requires->js_call_amd('tool_pluginvisibility/category_actions', 'init');
        }

        // Load filters on course pages.
        if ($PAGE->context && $PAGE->context->contextlevel == CONTEXT_COURSE && $COURSE->id > 1) {
            $hiddenplugins = \tool_pluginvisibility\helper::get_hidden_plugins_for_course($COURSE->id);

            if (!empty($hiddenplugins['mod'])) {
                $PAGE->requires->js_call_amd('tool_pluginvisibility/activity_filter', 'init', [$hiddenplugins['mod']]);
            }

            if (!empty($hiddenplugins['block'])) {
                $PAGE->requires->js_call_amd('tool_pluginvisibility/block_filter', 'init', [$hiddenplugins['block']]);
            }
        }

        // Load TinyMCE filter on any page within a course context (including module pages).
        // This covers course view, activity edit pages, etc.
        if ($COURSE && $COURSE->id > 1) {
            $hiddenplugins = \tool_pluginvisibility\helper::get_hidden_plugins_for_course($COURSE->id);

            if (!empty($hiddenplugins['tiny'])) {
                $PAGE->requires->js_call_amd('tool_pluginvisibility/tinymce_filter', 'init', [$hiddenplugins['tiny']]);
            }
        }
    }
}
