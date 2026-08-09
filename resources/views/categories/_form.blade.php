<div class="mt-6 space-y-6">
    <div>
        <x-input-label for="name" value="Nama Kategori" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                      value="{{ old('name', $category?->name) }}" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="type" value="Jenis" />
        <select id="type" name="type"
                class="mt-1.5 block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2">
            @foreach (\App\Enums\TransactionType::cases() as $type)
                <option value="{{ $type->value }}" @selected(old('type', $category?->type) === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    @if ($category)
        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))
                   class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
            <span class="text-sm text-gray-700">Kategori aktif</span>
        </label>
    @endif
</div>