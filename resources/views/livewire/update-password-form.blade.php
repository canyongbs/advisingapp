<div>
    <h6 class="text-blueGray-400 text-sm mt-3 mb-6 font-bold uppercase">
        {{ __('global.update') }} {{ __('global.login_password') }}
    </h6>

    <div class="flex flex-wrap">
        <form wire:submit.prevent="updatePassword" class="w-full">
            <div class="form-group px-4">
                <label class="form-label" for="current_password">{{ __('global.current_password') }}</label>
                <input class="form-control" id="current_password" type="password" wire:model.defer="state.current_password" autocomplete="current-password">
                @error('state.current_password')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group px-4">
                <label class="form-label" for="new_password">{{ __('global.new_password') }}</label>
                <input class="form-control" id="new_password" type="password" wire:model.defer="state.password" autocomplete="new-password">
                @error('state.password')
                    <span class="text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group px-4">
                <label class="form-label" for="password_confirmation">{{ __('global.confirm_password') }}</label>
                <input class="form-control" id="password_confirmation" type="password" wire:model.defer="state.password_confirmation" autocomplete="new-password">
            </div>

            <div class="form-group px-4 flex items-center">
                <button class="btn btn-indigo mr-3">
                    {{ __('global.save') }}
                </button>

                <div x-data="{ shown: false, timeout: null }" x-init="@this.on('saved', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000);  })" x-show.transition.out.opacity.duration.1500ms="shown" x-transition:leave.opacity.duration.1500ms class="text-sm" style="display: none;">
                    {{ __('global.saved') }}
                </div>

            </div>
        </form>
    </div>
</div>