<div>
    <h6 class="mb-6 mt-3 text-sm font-bold uppercase text-blueGray-400">
        {{ __('global.update') }} {{ __('global.login_password') }}
    </h6>

    <div class="flex flex-wrap">
        <form
            class="w-full"
            wire:submit.prevent="updatePassword"
        >
            <div class="form-group px-4">
                <label
                    class="form-label"
                    for="current_password"
                >{{ __('global.current_password') }}</label>
                <input
                    class="form-control"
                    id="current_password"
                    type="password"
                    wire:model.defer="state.current_password"
                    autocomplete="current-password"
                >
                @error('state.current_password')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group px-4">
                <label
                    class="form-label"
                    for="new_password"
                >{{ __('global.new_password') }}</label>
                <input
                    class="form-control"
                    id="new_password"
                    type="password"
                    wire:model.defer="state.password"
                    autocomplete="new-password"
                >
                @error('state.password')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group px-4">
                <label
                    class="form-label"
                    for="password_confirmation"
                >{{ __('global.confirm_password') }}</label>
                <input
                    class="form-control"
                    id="password_confirmation"
                    type="password"
                    wire:model.defer="state.password_confirmation"
                    autocomplete="new-password"
                >
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
