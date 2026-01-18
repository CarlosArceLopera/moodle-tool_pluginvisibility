/**
 * Hide TinyMCE toolbar buttons based on category plugin visibility settings.
 *
 * @module     tool_pluginvisibility/tinymce_filter
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialize the TinyMCE filter.
 *
 * @param {Array} hiddenPlugins Array of TinyMCE plugin names to hide
 */
export const init = (hiddenPlugins) => {
    if (!hiddenPlugins || hiddenPlugins.length === 0) {
        return;
    }

    /**
     * Hide toolbar buttons for the specified plugins.
     * This function matches buttons where data-mce-name:
     * - Equals tiny_PLUGINNAME (e.g., tiny_h5p)
     * - Starts with tiny_PLUGINNAME_ (e.g., tiny_recordrtc_audio)
     * - Equals PLUGINNAME_button (e.g., menutab_button)
     * - Contains the plugin name in other patterns
     */
    const hideToolbarButtons = () => {
        // Get all toolbar buttons.
        const allButtons = document.querySelectorAll('button[data-mce-name]');

        for (let i = 0; i < allButtons.length; i++) {
            const button = allButtons[i];
            const mceName = button.getAttribute('data-mce-name');

            if (!mceName) {
                continue;
            }

            // Check if this button matches any of the hidden plugins.
            for (let j = 0; j < hiddenPlugins.length; j++) {
                const pluginName = hiddenPlugins[j].toLowerCase();

                // Match patterns:
                // 1. Exact match: tiny_pluginname
                // 2. Prefix match: tiny_pluginname_*
                // 3. Button suffix: pluginname_button
                // 4. Any other pattern containing tiny_pluginname

                const patterns = [
                    `tiny_${pluginName}`,      // Exact: tiny_h5p
                    `tiny_${pluginName}_`,     // Prefix: tiny_recordrtc_audio
                    `${pluginName}_button`,    // Suffix: menutab_button
                    `${pluginName}_`           // Generic prefix: menutab_
                ];

                let shouldHide = false;

                for (let k = 0; k < patterns.length; k++) {
                    const pattern = patterns[k];
                    if (mceName.toLowerCase() === pattern ||
                        mceName.toLowerCase().startsWith(pattern)) {
                        shouldHide = true;
                        break;
                    }
                }

                if (shouldHide && button.style.display !== 'none') {
                    button.style.display = 'none';
                    button.setAttribute('aria-hidden', 'true');
                    break; // No need to check other plugins for this button.
                }
            }
        }
    };

    /**
     * Wait for TinyMCE to be initialized and then hide buttons.
     */
    const waitForTinyMCEAndHide = () => {
        let attempts = 0;
        const maxAttempts = 100;

        const tryHide = () => {
            attempts++;

            const toolbar = document.querySelector('.tox-toolbar__primary, .tox-toolbar');

            if (toolbar) {
                setTimeout(() => {
                    hideToolbarButtons();
                }, 100);
                return;
            }

            if (attempts < maxAttempts) {
                setTimeout(tryHide, 100);
            }
        };

        setTimeout(tryHide, 50);
    };

    // Set up a MutationObserver to detect when TinyMCE editors are added to the page.
    const observer = new MutationObserver(() => {
        const toolbar = document.querySelector('.tox-toolbar__primary, .tox-toolbar');
        if (toolbar) {
            hideToolbarButtons();
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });

    // Try to hide on existing editors.
    waitForTinyMCEAndHide();

    // Listen for focus events that might trigger TinyMCE initialization.
    document.body.addEventListener('focus', () => {
        setTimeout(() => {
            hideToolbarButtons();
        }, 500);
    }, true);

    // Run periodically for the first 10 seconds to catch late-loading editors.
    let periodicAttempts = 0;
    const periodicHide = () => {
        periodicAttempts++;
        hideToolbarButtons();
        if (periodicAttempts < 10) {
            setTimeout(periodicHide, 1000);
        }
    };
    setTimeout(periodicHide, 1000);
};
