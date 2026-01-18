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
 * Form for managing plugin visibility.
 *
 * @package    tool_pluginvisibility
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginvisibility\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Manage plugin visibility form.
 *
 * @package    tool_pluginvisibility
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_visibility_form extends \moodleform {

    /**
     * Define the form.
     */
    protected function definition() {
        $mform = $this->_form;

        // Hidden category ID field.
        $mform->addElement('hidden', 'categoryid');
        $mform->setType('categoryid', PARAM_INT);

        // Apply to all subfolders.
        $mform->addElement(
            'selectyesno',
            'applytosubcategories',
            get_string('applytosubcategories', 'tool_pluginvisibility')
        );
        $mform->setDefault('applytosubcategories', 0);
        $mform->addHelpButton('applytosubcategories', 'applytosubcategories', 'tool_pluginvisibility');

        // Activities and Resources - Autocomplete multi-select.
        $modules = $this->get_enabled_modules();
        $mform->addElement(
            'autocomplete',
            'modules',
            get_string('activitiesandresources', 'tool_pluginvisibility'),
            $modules,
            [
                'multiple' => true,
                'noselectionstring' => get_string('noselection', 'tool_pluginvisibility'),
            ]
        );
        $mform->addHelpButton('modules', 'activitiesandresources', 'tool_pluginvisibility');

        // Blocks - Autocomplete multi-select.
        $blocks = $this->get_enabled_blocks();
        $mform->addElement(
            'autocomplete',
            'blocks',
            get_string('blocks'),
            $blocks,
            [
                'multiple' => true,
                'noselectionstring' => get_string('noselection', 'tool_pluginvisibility'),
            ]
        );
        $mform->addHelpButton('blocks', 'blocks', 'tool_pluginvisibility');

        // Action buttons.
        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * Get list of enabled activity modules.
     *
     * @return array Array of module names keyed by module name
     */
    protected function get_enabled_modules() {
        $modules = [];
        $moduleinfo = \core_plugin_manager::instance()->get_plugins_of_type('mod');

        foreach ($moduleinfo as $modulename => $module) {
            if ($module->is_enabled()) {
                $modules[$modulename] = $module->displayname;
            }
        }

        // Sort alphabetically by display name.
        asort($modules);

        return $modules;
    }

    /**
     * Get list of enabled blocks.
     *
     * @return array Array of block names keyed by block name
     */
    protected function get_enabled_blocks() {
        $blocks = [];
        $blockinfo = \core_plugin_manager::instance()->get_plugins_of_type('block');

        foreach ($blockinfo as $blockname => $block) {
            if ($block->is_enabled()) {
                $blocks[$blockname] = $block->displayname;
            }
        }

        // Sort alphabetically by display name.
        asort($blocks);

        return $blocks;
    }

    /**
     * Form validation.
     *
     * @param array $data Data from the form.
     * @param array $files Files uploaded.
     * @return array Array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Add custom validation if needed.

        return $errors;
    }
}
