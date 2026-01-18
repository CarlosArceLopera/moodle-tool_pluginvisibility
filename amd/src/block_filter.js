/**
 * Hide blocks in the add block modal based on category plugin visibility settings.
 *
 * @module     tool_pluginvisibility/block_filter
 * @copyright  2026 Patrick Thibaudeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialize the block filter.
 *
 * @param {Array} hiddenBlocks Array of block names to hide
 */
export const init = (hiddenBlocks) => {
    if (!hiddenBlocks || hiddenBlocks.length === 0) {
        return;
    }

    /**
     * Hide the specified blocks from the modal.
     */
    const hideBlocks = () => {
        let hiddenCount = 0;
        hiddenBlocks.forEach(blockName => {
            const selector = `a.list-group-item[data-blockname="${blockName}"]`;
            const options = document.querySelectorAll(selector);

            options.forEach(option => {
                option.style.display = 'none';
                option.setAttribute('aria-hidden', 'true');
                hiddenCount++;
            });
        });
        return hiddenCount;
    };

    /**
     * Wait for the block list to appear in the modal and then hide blocks.
     */
    const waitForBlockListAndHide = () => {
        let attempts = 0;
        const maxAttempts = 50; // 50 attempts * 100ms = 5 seconds max

        const tryHide = () => {
            attempts++;

            // Look for the block list in any visible modal.
            const blockList = document.querySelector('.modal.show .list-group a[data-blockname]');

            if (blockList) {
                hideBlocks();
                return;
            }

            if (attempts < maxAttempts) {
                setTimeout(tryHide, 100);
            }
        };

        // Start trying after a short delay.
        setTimeout(tryHide, 50);
    };

    /**
     * Set up click listener for the "Add a block" button.
     */
    const setupClickListener = () => {
        // Use event delegation on the document body.
        document.body.addEventListener('click', (event) => {
            // Check if the clicked element or its parent is the "Add a block" button.
            const addBlockButton = event.target.closest('[data-key="addblock"]');

            if (addBlockButton) {
                waitForBlockListAndHide();
            }
        });
    };

    // Initialize the click listener.
    setupClickListener();

    // Also set up a MutationObserver as backup for dynamically created modals.
    const observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (node.nodeType === 1) {
                    // Check if a modal with block list was added.
                    const modal = node.matches?.('.modal[data-region="modal-container"]')
                        ? node
                        : node.querySelector?.('.modal[data-region="modal-container"]');

                    if (modal) {
                        const title = modal.querySelector('[data-region="title"]');
                        if (title && title.textContent.trim().toLowerCase().includes('add a block')) {
                            waitForBlockListAndHide();
                        }
                    }
                }
            }
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
};
