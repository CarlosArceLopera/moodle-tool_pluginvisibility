/**
 * Hide activities in the activity chooser modal based on category plugin visibility settings.
 *
 * @module     tool_pluginvisibility/activity_filter
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialize the activity filter.
 *
 * @param {Array} hiddenModules Array of module names to hide
 */
export const init = (hiddenModules) => {
    // If no hidden modules, nothing to do.
    if (!hiddenModules || hiddenModules.length === 0) {
        return;
    }

    // Debug: Log what we're hiding.
    // eslint-disable-next-line no-console
    console.log('Plugin Visibility: Hiding modules:', hiddenModules);

    // Function to hide activities.
    const hideActivities = () => {
        let hiddenCount = 0;

        hiddenModules.forEach(moduleName => {
            // Find all activity options with this module name.
            const selector = `[data-region="chooser-option-container"][data-internal="${moduleName}"]`;
            const options = document.querySelectorAll(selector);

            // eslint-disable-next-line no-console
            console.log(`Plugin Visibility: Found ${options.length} instances of "${moduleName}"`);

            options.forEach(option => {
                // Hide the option.
                option.style.display = 'none';
                // Also mark it as hidden for accessibility.
                option.setAttribute('aria-hidden', 'true');
                hiddenCount++;
            });
        });

        // eslint-disable-next-line no-console
        console.log(`Plugin Visibility: Hidden ${hiddenCount} activity options`);
    };

    // Function to wait for modal content to be loaded, then hide activities.
    const waitForModalContent = (modalContainer) => {
        // Check if the options are already loaded.
        const checkAndHide = () => {
            const optionsContainer = modalContainer.querySelector('[data-region="chooser-options-container"]');
            if (optionsContainer && optionsContainer.children.length > 0) {
                // eslint-disable-next-line no-console
                console.log('Plugin Visibility: Modal content loaded, hiding activities');
                hideActivities();
                return true;
            }
            return false;
        };

        // Try immediately.
        if (checkAndHide()) {
            return;
        }

        // If not loaded yet, observe for changes.
        const contentObserver = new MutationObserver(() => {
            if (checkAndHide()) {
                contentObserver.disconnect();
            }
        });

        // Observe the modal for content changes.
        contentObserver.observe(modalContainer, {
            childList: true,
            subtree: true,
        });

        // Also try after a short delay as fallback.
        setTimeout(() => {
            checkAndHide();
        }, 100);

        setTimeout(() => {
            checkAndHide();
        }, 500);
    };

    // Wait for the modal to be shown, then hide activities.
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) { // Element node.
                    let modalContainer = null;

                    // Check if this is the modal or contains the modal.
                    if (node.matches && node.matches('.modal[data-region="modal-container"]')) {
                        modalContainer = node;
                    } else if (node.querySelector) {
                        modalContainer = node.querySelector('.modal[data-region="modal-container"]');
                    }

                    if (modalContainer) {
                        // eslint-disable-next-line no-console
                        console.log('Plugin Visibility: Activity chooser modal detected');
                        waitForModalContent(modalContainer);
                    }
                }
            });
        });
    });

    // Observe the body for modal additions.
    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });

    // Also check if modal is already present on page load.
    const existingModal = document.querySelector('.modal[data-region="modal-container"]');
    if (existingModal) {
        // eslint-disable-next-line no-console
        console.log('Plugin Visibility: Existing modal found on page load');
        waitForModalContent(existingModal);
    }
};
