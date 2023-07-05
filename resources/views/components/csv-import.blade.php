<div x-data="{ open: {{ $errors->has('csv_file') || $errors->has('has_header') ? 'true' : 'false' }} }" class="inline-block">
    <button type="button" class="btn btn-secondary" x-on:click="open = true">
        <i class="fas fa-file-csv fa-fw mr-1"></i>
        csv import
    </button>

    <div x-on:keydown.window.escape="open = false" aria-labelledby="modal-title" aria-modal="true" class="fixed z-10 inset-0 overflow-y-auto" style="display:none;" x-ref="dialog" x-show="open">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-on:click="open = false" aria-hidden="true" class="fixed inset-0 bg-blueGray-500 bg-opacity-75 transition-opacity" x-description="Background overlay, show/hide based on modal state." x-show="open" x-transition:enter-end="opacity-100" x-transition:enter-start="opacity-0" x-transition:enter="ease-out duration-300" x-transition:leave-end="opacity-0" x-transition:leave-start="opacity-100" x-transition:leave="ease-in duration-200"></div>

            <!-- This element is to trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:mt-24 sm:align-top sm:max-w-sm sm:w-full" x-description="Modal panel, show/hide based on modal state." x-show="open" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter="ease-out duration-300" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200">
                <form action="{{ $attributes['route'] }}" method="POST" enctype="multipart/form-data">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blueGray-200 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-file-csv fa-fw text-blueGray-700 fa-lg"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-blueGray-900" id="modal-title">
                                    CSV Import
                                </h3>
                                <div class="mt-3">
                                    <div class="form-group {{ $errors->has('csv_file') ? 'invalid' : '' }}">
                                        <input class="form-control" type="file" name="csv_file">
                                        @error('csv_file')
                                            <div class="validation-message">
                                                {{ $message }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <input type="hidden" name="has_header" value="0">
                                        <input class="form-control" type="checkbox" name="has_header" id="has_header" value="1" checked>
                                        <label class="form-label inline-block ml-1" for="has_header">
                                            File has header row
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blueGray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button class="btn btn-info w-full text-sm" type="submit">
                            upload
                        </button>
                    </div>
                    <input type="hidden" name="route" value="{{ $attributes['route'] }}">
                    @csrf
                </form>
            </div>
        </div>
    </div>


</div>