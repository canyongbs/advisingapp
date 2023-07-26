<div>
    <h6 class="mb-6 mt-3 text-sm font-bold uppercase text-blueGray-400">
        {{ __('global.profile_information') }}
    </h6>

    <div class="flex flex-wrap">
        <form
            class="w-full"
            wire:submit="updateProfileInformation"
        >
            <div class="form-group px-4">
                <label
                    class="form-label"
                    for="name"
                >{{ __('global.user_name') }}</label>
                <input
                    class="form-control"
                    id="name"
                    type="text"
                    wire:model="state.name"
                    autocomplete="name"
                >
                @error('state.name')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group px-4">
                <label
                    class="form-label"
                    for="email"
                >{{ __('global.login_email') }}</label>
                <input
                    class="form-control"
                    id="email"
                    type="text"
                    wire:model="state.email"
                    autocomplete="email"
                >
                @error('state.email')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group flex items-center px-4">
                <button class="btn btn-indigo mr-3">
                    {{ __('global.save') }}
                </button>

                <div
                    class="text-sm"
                    style="display: none;"
                    x-data="{ shown: false, timeout: null }"
                    x-init="@this.on('saved', () => {
                        clearTimeout(timeout);
                        shown = true;
                        timeout = setTimeout(() => { shown = false }, 2000);
                    })"
                    x-show.transition.out.opacity.duration.1500ms="shown"
                    x-transition:leave.opacity.duration.1500ms
                >
                    {{ __('global.saved') }}
                </div>

            </div>
        </form>
    </div>
</div>
