const addTaskButton = document.querySelector('[data-add-task]');

if (addTaskButton) {
    addTaskButton.addEventListener('click', () => {
        const list = document.querySelector('[data-task-list]');

        if (! list) {
            return;
        }

        const row = document.createElement('div');
        row.className = 'flex gap-2';
        row.innerHTML = `
            <input type="text" name="titles[]" required maxlength="255"
                class="w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm text-stone-800 outline-none focus:border-stone-500"
                placeholder="عنوان وظیفه">
            <button type="button" data-remove-task
                class="rounded-md border border-stone-300 px-3 text-sm text-stone-600 hover:bg-stone-100">حذف</button>
        `;
        list.appendChild(row);
    });
}

document.addEventListener('click', (event) => {
    const target = event.target;

    if (target instanceof HTMLElement && target.matches('[data-remove-task]')) {
        target.closest('div')?.remove();
    }
});
