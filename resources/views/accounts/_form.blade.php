<div class="mt-6 space-y-6">
    <div>
        <x-input-label for="name" value="Nama Kas" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                      value="{{ old('name', $account?->name) }}" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="type" value="Jenis" />
        <select id="type" name="type"
                class="mt-1.5 block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2">
            @foreach (\App\Enums\AccountType::cases() as $type)
                <option value="{{ $type->value }}" @selected(old('type', $account?->type) === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="initial_balance" value="Saldo Awal (Rp)" />
        <x-text-input id="initial_balance" name="initial_balance" type="number" step="0.01" min="0"
                      class="mt-1 block w-full" value="{{ old('initial_balance', $account?->initial_balance) }}" />
        <x-input-error :messages="$errors->get('initial_balance')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Keterangan" />
        <textarea id="description" name="description" rows="3"
                  class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2">{{ old('description', $account?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    @if ($account)
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $account->is_active))
                   class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
            <span class="text-sm text-gray-700">Kas aktif</span>
        </label>
    @endif
</div>