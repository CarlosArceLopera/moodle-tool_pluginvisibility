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

global $CFG, $OUTPUT, $PAGE, $DB, $USER;

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

// Instantiate the form.
$formurl = new moodle_url('/admin/tool/pluginvisibility/index.php', ['categoryid' => $categoryid]);
$mform = new \tool_pluginvisibility\form\manage_visibility_form($formurl, ['categoryid' => $categoryid]);

// Load existing data from database.
$existingrecords = $DB->get_records('tool_pluginvisibility_hidden', ['categoryid' => $categoryid]);

$formdata = new stdClass();
$formdata->categoryid = $categoryid;
$formdata->modules = [];
$formdata->blocks = [];
$formdata->applytosubcategories = 0;

if ($existingrecords) {
    foreach ($existingrecords as $record) {
        if ($record->plugintype === 'mod') {
            $formdata->modules[] = $record->pluginname;
        } else if ($record->plugintype === 'block') {
            $formdata->blocks[] = $record->pluginname;
        }
        // Get applytosubcategories value (should be same for all records in this category).
        $formdata->applytosubcategories = $record->applytosubcategories;
    }
}

// Set default data.
$mform->set_data($formdata);

// Handle form submission.
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/management.php', ['categoryid' => $categoryid]));
} else if ($data = $mform->get_data()) {
    global $DB, $USER;

    $timenow = time();

    // Start transaction.
    $transaction = $DB->start_delegated_transaction();

    try {
        // Delete existing records for this category.
        $DB->delete_records('tool_pluginvisibility_hidden', ['categoryid' => $categoryid]);

        // Prepare records to insert.
        $recordstoinsert = [];

        // Process modules (activities and resources).
        if (!empty($data->modules)) {
            foreach ($data->modules as $modulename) {
                $record = new stdClass();
                $record->categoryid = $categoryid;
                $record->plugintype = 'mod';
                $record->pluginname = $modulename;
                $record->applytosubcategories = $data->applytosubcategories;
                $record->timecreated = $timenow;
                $record->timemodified = $timenow;
                $record->usermodified = $USER->id;
                $recordstoinsert[] = $record;
            }
        }

        // Process blocks.
        if (!empty($data->blocks)) {
            foreach ($data->blocks as $blockname) {
                $record = new stdClass();
                $record->categoryid = $categoryid;
                $record->plugintype = 'block';
                $record->pluginname = $blockname;
                $record->applytosubcategories = $data->applytosubcategories;
                $record->timecreated = $timenow;
                $record->timemodified = $timenow;
                $record->usermodified = $USER->id;
                $recordstoinsert[] = $record;
            }
        }

        // Insert all records.
        foreach ($recordstoinsert as $record) {
            $DB->insert_record('tool_pluginvisibility_hidden', $record);
        }

        // Commit transaction.
        $transaction->allow_commit();

        // Show success message and redirect.
        \core\notification::success(get_string('changessaved'));
        redirect(new moodle_url('/admin/tool/pluginvisibility/index.php', ['categoryid' => $categoryid]));

    } catch (Exception $e) {
        $transaction->rollback($e);
        \core\notification::error(get_string('error'));
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managepluginvisibility', 'tool_pluginvisibility'));

// Display instructions using Mustache template.
echo $OUTPUT->render_from_template('tool_pluginvisibility/instructions', [
    'categoryname' => $category->get_formatted_name(),
]);

// Display the form.
$mform->display();

echo $OUTPUT->footer();
