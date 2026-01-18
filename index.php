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
 * Plugin visibility management.
 *
 * @package    tool_pluginvisibility
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Get category ID parameter.
$categoryid = required_param('categoryid', PARAM_INT);

// Get the category object.
$category = core_course_category::get($categoryid, MUST_EXIST, true);

// Set up the page.
$context = context_coursecat::instance($category->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/pluginvisibility/index.php', ['categoryid' => $categoryid]);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'tool_pluginvisibility'));
$PAGE->set_heading($category->get_formatted_name());

// Check capabilities.
require_login();
require_capability('tool/pluginvisibility:view', $context);

// Set up breadcrumb navigation.
$PAGE->navbar->add(get_string('categories'), new moodle_url('/course/index.php'));
$PAGE->navbar->add($category->get_formatted_name(), new moodle_url('/course/management.php', ['categoryid' => $categoryid]));
$PAGE->navbar->add(get_string('pluginname', 'tool_pluginvisibility'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managepluginvisibility', 'tool_pluginvisibility'));

// Display category information.
echo html_writer::tag('p', get_string('categoryinfo', 'tool_pluginvisibility', $category->get_formatted_name()));

// TODO: Add plugin visibility management functionality here.
echo html_writer::tag('div', get_string('comingsoon', 'tool_pluginvisibility'), ['class' => 'alert alert-info']);

echo $OUTPUT->footer();
