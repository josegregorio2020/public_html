document.addEventListener('DOMContentLoaded', function () {
    const observer = new MutationObserver(() => {
        const dialogs = document.querySelectorAll('.moodle-dialogue-base');

        dialogs.forEach(dialog => {
            const title = dialog.querySelector('.moodle-dialogue-hd');
            if (title && title.textContent.trim() === 'undefined') {
                dialog.style.display = 'none';
            }
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
