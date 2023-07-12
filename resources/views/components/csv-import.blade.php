<div
    class="inline-block"
    x-data="{ open: {{ $errors->has('csv_file') || $errors->has('has_header') ? 'true' : 'false' }} }"
>
    <button
        class="btn btn-secondary"
        type="button"
        x-on:click="open = true"
    >
        <i class="fas fa-file-csv fa-fw mr-1"></i>
        csv import
    </button>

    <div
        class="fixed inset-0 z-10 overflow-y-auto"
        aria-labelledby="modal-title"
        aria-modal="true"
        style="display:none;"
        x-on:keydown.window.escape="open = false"
        x-ref="dialog"
        x-show="open"
    >
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
            <div
                class="fixed inset-0 bg-blueGray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true"
                x-on:click="open = false"
                x-description="Background overlay, show/hide based on modal state."
                x-show="open"
                x-transition:enter-end="opacity-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter="ease-out duration-300"
                x-transition:leave-end="opacity-0"
                x-transition:leave-start="opacity-100"
                x-transition:leave="ease-in duration-200"
            ></div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span
                class="hidden sm:inline-block sm:h-screen sm:align-middle"
                aria-hidden="true"
            >&#8203;</span>

            <div
                class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:mt-24 sm:w-full sm:max-w-sm sm:align-top"
                x-description="Modal panel, show/hide based on modal state."
                x-show="open"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter="ease-out duration-300"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
            >
                <form
                    action="{{ $attributes['route'] }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blueGray-200 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-file-csv fa-fw fa-lg text-blueGray-700"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3
                                    class="text-lg font-medium leading-6 text-blueGray-900"
                                    id="modal-title"
                                >
                                    CSV Import
                                </h3>
                                <div class="mt-3">
                                    <div class="form-group {{ $errors->has('csv_file') ? 'invalid' : '' }}">
                                        <input
                                            class="form-control"
                                            name="csv_file"
                                            type="file"
                                        >
                                        @error('csv_file')
                                            <div class="validation-message">
                                                {{ $message }}
                                            </div>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <input
                                                name="has_header"
                                                type="hidden"
                                                value="0"
                                            >
                                            <input
                                                class="form-control"
                                                id="has_header"
                                                name="has_header"
                                                type="checkbox"
                                                value="1"
                                                checked
                                            >
                                            <label
                                                class="form-label ml-1 inline-block"
                                                for="has_header"
                                            >
                                                File has header row
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-blueGray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button
                                class="btn btn-info w-full text-sm"
                                type="submit"
                            >
                                upload
                            </button>
                        </div>
                        <input
                            name="route"
                            type="hidden"
                            value="{{ $attributes['route'] }}"
                        >
                        @csrf
                    </form>
                </div>
            </div>
        </div>

    </div>
