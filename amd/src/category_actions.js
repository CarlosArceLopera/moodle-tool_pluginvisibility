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
 * Add plugin visibility to category action menus.
 *
 * @module     tool_pluginvisibility/category_actions
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialize the category actions menu items.
 */
export const init = () => {
    // Find all category action menus
    const actionMenus = document.querySelectorAll('.category-item-actions');

    actionMenus.forEach(actionMenu => {
        // Find the parent category list item
        const categoryItem = actionMenu.closest('.listitem-category');

        if (!categoryItem) {
            return;
        }

        // Get category ID from data attribute
        const categoryId = categoryItem.getAttribute('data-id');

        if (!categoryId) {
            return;
        }

        // Find the dropdown menu
        const dropdownMenu = actionMenu.querySelector('.dropdown-menu');

        if (!dropdownMenu) {
            return;
        }

        // Check if already added
        if (dropdownMenu.querySelector('.action-pluginvisibility')) {
            return;
        }

        // Create the URL
        const url = M.cfg.wwwroot + '/admin/tool/pluginvisibility/index.php?categoryid=' + categoryId;

        // Create menu item
        const menuItem = document.createElement('a');
        menuItem.href = url;
        menuItem.className = 'dropdown-item action-pluginvisibility menu-action';
        menuItem.setAttribute('data-action', 'pluginvisibility');
        menuItem.setAttribute('role', 'menuitem');
        menuItem.setAttribute('tabindex', '-1');

        // Create icon
        const icon = document.createElement('i');
        icon.className = 'icon fa fa-cog fa-fw';
        icon.setAttribute('title', 'Plugin visibility');
        icon.setAttribute('role', 'img');
        icon.setAttribute('aria-label', 'Plugin visibility');

        // Create text span
        const textSpan = document.createElement('span');
        textSpan.className = 'menu-action-text';
        textSpan.textContent = 'Plugin visibility';

        // Assemble menu item
        menuItem.appendChild(icon);
        menuItem.appendChild(textSpan);

        // Add to menu
        dropdownMenu.appendChild(menuItem);
    });
};
