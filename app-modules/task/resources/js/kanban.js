document.addEventListener('alpine:init', () => {
    Alpine.data('kanban', ($wire) => ({
        init() {
            const kanbanLists = document.querySelectorAll('[id^="kanban-list-"]');

            kanbanLists.forEach((kanbanList) => {
                window.Sortable.create(kanbanList, {
                    group: 'kanban',
                    animation: 100,
                    forceFallback: true,
                    dragClass: 'drag-card',
                    ghostClass: 'ghost-card',
                    easing: 'cubic-bezier(0, 0.55, 0.45, 1)',
                    onAdd: async function (evt) {
                        try {
                            const result = await $wire.movedTask(
                                evt.item.dataset.task,
                                evt.from.dataset.status,
                                evt.to.dataset.status,
                            );

                            if (result.original.success) {
                                new FilamentNotification()
                                    .icon('heroicon-o-check-circle')
                                    .title(result.original.message)
                                    .iconColor('success')
                                    .send();
                            } else {
                                new FilamentNotification()
                                    .icon('heroicon-o-x-circle')
                                    .title(result.original.message)
                                    .iconColor('danger')
                                    .send();
                            }
                        } catch (e) {
                            new FilamentNotification()
                                .icon('heroicon-o-x-circle')
                                .title('Something went wrong, please try again later')
                                .iconColor('danger')
                                .send();
                        }
                    },
                });
            });
        },
    }));
});
