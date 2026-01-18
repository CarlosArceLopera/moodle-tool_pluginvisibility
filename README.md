# moodle-tool_pluginvisibility

Moodle admin tool plugin for managing plugin visibility per category.

## Features

- Adds "Plugin visibility" menu item to category action menus (⋮) in course management
- Uses JavaScript injection - **no core file modifications required**
- Context-aware permissions using Moodle capabilities

## Requirements

- Moodle 5.1 or later
- PHP 8.2 or later

## Installation

1. Copy the plugin directory to `{moodle_root}/admin/tool/pluginvisibility`
2. Visit **Site administration** > **Notifications** to complete the installation
3. Clear all caches: **Site Administration > Development > Purge all caches**

## Usage

1. Navigate to **Site Administration > Courses > Manage courses and categories**
2. Click the **action menu (⋮)** next to any category
3. Select "Plugin visibility"
4. Configure plugin visibility for that category (feature coming soon)

## How It Works

The plugin uses JavaScript (AMD module) to inject the menu item into category action menus. The JavaScript is automatically loaded on the course management page via the `tool_pluginvisibility_before_footer()` callback in lib.php.

## Files Structure

- `version.php` - Plugin version and metadata
- `settings.php` - Plugin settings and admin menu registration
- `index.php` - Main plugin page for managing visibility
- `lib.php` - Plugin library functions with callback
- `lang/en/tool_pluginvisibility.php` - English language strings
- `db/access.php` - Capability definitions
- `amd/src/category_actions.js` - JavaScript to inject menu item
- `amd/build/category_actions.min.js` - Minified JavaScript

## Permissions

The plugin defines the following capability:

- `tool/pluginvisibility:view` - View and manage plugin visibility

By default, this capability is granted to users with the Manager role.

## Development Status

**Current Version:** 1.0.0 (Alpha)

### Implemented
- ✅ Plugin structure and installation
- ✅ Category action menu integration (requires core modification)
- ✅ Proper capability checks
- ✅ Page navigation and context handling

### Planned
- ⏳ Plugin visibility management interface
- ⏳ Per-category plugin visibility settings
- ⏳ Plugin enable/disable functionality
- ⏳ Settings inheritance from parent categories

## Support

For issues, questions, or contributions, please refer to the plugin documentation.

## License

GNU GPL v3 or later

## Credits

Copyright 2026 Patrick Thibaudeau




