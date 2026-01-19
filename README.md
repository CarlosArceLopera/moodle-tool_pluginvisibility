# Plugin Visibility (tool_pluginvisibility)

Moodle admin tool plugin for managing plugin visibility per course category. Allows administrators to hide specific activities, blocks, and TinyMCE editor buttons from users within selected categories.

## Features

- **Activity & Resource Filtering** - Hide selected activities and resources from the "Add an activity or resource" modal
- **Block Filtering** - Hide selected blocks from the "Add a block" modal
- **TinyMCE Toolbar Filtering** - Hide selected TinyMCE editor toolbar buttons
- **Category Hierarchy Support** - Apply visibility settings to all subcategories with a single setting
- **Subcategory Override** - Subcategories can override parent category settings
- **No Core Modifications** - Uses JavaScript injection and Moodle hooks

## Requirements

- Moodle 5.1 or later

## Installation

1. Copy the plugin directory to `{moodle_root}/admin/tool/pluginvisibility`
2. Visit **Site Administration > Notifications** to complete the installation
3. Clear all caches: **Site Administration > Development > Purge all caches**

## Usage

1. Navigate to **Site Administration > Courses > Manage courses and categories**
2. Click the **action menu (⋮)** next to any category
3. Select **"Plugin visibility"**
4. Configure which plugins to hide:
   - Select activities/resources to hide from the activity chooser
   - Select blocks to hide from the block drawer
   - Select TinyMCE plugins to hide from the editor toolbar
5. Optionally enable **"Apply to all subcategories"** to propagate settings to child categories
6. Click **Save changes**

## How It Works

The plugin uses JavaScript AMD modules injected via Moodle hooks to filter plugin visibility in real-time:

- **Activities/Resources**: Monitors the activity chooser modal and hides selected modules
- **Blocks**: Listens for the "Add a block" button click and hides selected blocks from the modal
- **TinyMCE**: Scans toolbar buttons and hides those matching selected plugins using pattern matching

When "Apply to all subcategories" is enabled, the plugin creates explicit database records for each subcategory, allowing individual subcategories to override inherited settings.

## Permissions

The plugin defines the following capability:

| Capability | Description | Default Roles |
|------------|-------------|---------------|
| `tool/pluginvisibility:view` | View and manage plugin visibility | Manager |

## License

GNU GPL v3 or later

## Credits

Copyright © 2026 Patrick Thibaudeau
