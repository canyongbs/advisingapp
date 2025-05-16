/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/
document.addEventListener('alpine:init', () => {
    Alpine.data('requests', ($wire) => ({
        loading: {
            type: null,
            identifier: null,
        },
        requestId: null,
        startFolder: null,
        dragging: false,
        expandedFolder: null,
        async drop(folderId) {
            try {
                if (this.startFolder === folderId) {
                    return;
                }

                const result = await this.$wire.movedRequest(this.requestId, folderId);

                if (result.original.success) {
                    if (folderId) {
                        this.expandedFolder = folderId;
                    }

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
            } catch (exception) {
                new FilamentNotification()
                    .icon('heroicon-o-x-circle')
                    .title('Something went wrong, please try again later.')
                    .iconColor('danger')
                    .send();
            } finally {
                this.requestId = null;
                this.startFolder = null;
            }
        },
        start(requestId, folderId) {
            this.dragging = true;
            this.requestId = requestId;
            this.startFolder = folderId;
        },
        end() {
            this.dragging = false;
        },
        expand(folderId) {
            if (this.expandedFolder === folderId) {
                this.expandedFolder = null;
            } else {
                this.expandedFolder = folderId;
            }
        },
        expanded(folderId) {
            return this.expandedFolder === folderId;
        },
        async selectRequest(request) {
            this.loading.type = 'request';
            this.loading.identifier = request.id;

            await $wire.selectRequest(request);

            this.loading.type = null;
            this.loading.identifier = null;

            let url = new URL(window.location);
            history.pushState({}, '', url.pathname);
        },
        async moveRequest(requestId) {
            this.loading.type = 'moveRequestAction';
            this.loading.identifier = requestId;

            await $wire.mountAction('moveRequest', { request: requestId });

            this.loading.type = null;
            this.loading.identifier = null;
        },
        async editRequest(requestId) {
            this.loading.type = 'editRequestAction';
            this.loading.identifier = requestId;

            await $wire.mountAction('editRequest', { request: requestId });

            this.loading.type = null;
            this.loading.identifier = null;
        },
        async deleteRequest(requestId) {
            this.loading.type = 'deleteRequestAction';
            this.loading.identifier = requestId;

            await $wire.mountAction('deleteRequest', { request: requestId });

            this.loading.type = null;
            this.loading.identifier = null;
        },
        async renameFolder(folderId) {
            this.loading.type = 'renameFolderAction';
            this.loading.identifier = folderId;

            await $wire.mountAction('renameFolder', { folder: folderId });

            this.loading.type = null;
            this.loading.identifier = null;
        },
        async deleteFolder(folderId) {
            this.loading.type = 'deleteFolderAction';
            this.loading.identifier = folderId;

            await $wire.mountAction('deleteFolder', { folder: folderId });

            this.loading.type = null;
            this.loading.identifier = null;
        },
    }));
});

document.addEventListener('livewire:init', () => {
    Livewire.on('remove-request-param', (event) => {
        let url = new URL(window.location);
        history.pushState({}, '', url.pathname);
    });
});
