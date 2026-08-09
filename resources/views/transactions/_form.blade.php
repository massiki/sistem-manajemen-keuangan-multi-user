@php
    $type = old('type', $transaction?->type ?? request('type', 'expense'));
    $today = now()->format('Y-m-d');
@endphp

<div x-data="{ type: '{{ $type }}' }" class="space-y-6">
    <div>
        <x-input-label value="Jenis Transaksi" />
        <div class="mt-1.5 grid grid-cols-2 gap-3">
            <label class="cursor-pointer">
                <input type="radio" name="type" value="income" x-model="type" class="peer sr-only" />
                <span class="flex items-center justify-center h-12 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700 shadow-sm transition peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 focus:ring-2 focus:ring-emerald-500">
                    + Uang Masuk
                </span>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="type" value="expense" x-model="type" class="peer sr-only" />
                <span class="flex items-center justify-center h-12 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-700 shadow-sm transition peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 focus:ring-2 focus:ring-rose-500">
                    - Uang Keluar
                </span>
            </label>
        </div>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="transaction_date" value="Tanggal Transaksi" />
        <x-text-input id="transaction_date" name="transaction_date" type="date"
                      class="mt-1.5 block w-full"
                      value="{{ old('transaction_date', $transaction?->transaction_date?->format('Y-m-d') ?? $today) }}" required />
        <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="account_id" value="Sumber Dana / Kas" />
        <select id="account_id" name="account_id"
                class="mt-1.5 block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2">
            <option value="">-- Pilih kas --</option>
            @foreach ($accounts as $account)
                <option value="{{ $account->id }}" @selected(old('account_id', $transaction?->account_id) == $account->id)>
                    {{ $account->name }}@if (! $account->is_active) (Nonaktif)@endif
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('account_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label value="Kategori" />

        <select name="category_id" x-show="type === 'income'" :disabled="type !== 'income'"
                class="mt-1.5 block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2">
            <option value="">-- Pilih kategori uang masuk --</option>
            @foreach ($categories->where('type', 'income') as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $transaction?->category_id) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="category_id" x-show="type === 'expense'" :disabled="type !== 'expense'" x-cloak
                class="mt-1.5 block w-full h-11 rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2">
            <option value="">-- Pilih kategori uang keluar --</option>
            @foreach ($categories->where('type', 'expense') as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $transaction?->category_id) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="amount" value="Nominal (Rp)" />
        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01"
                      class="mt-1.5 block w-full" placeholder="150000"
                      value="{{ old('amount', $transaction?->amount) }}" required />
        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Keterangan" />
        <x-text-input id="description" name="description" type="text" class="mt-1.5 block w-full"
                      placeholder="Contoh: Membeli kebutuhan dapur"
                      value="{{ old('description', $transaction?->description) }}" />
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="notes" value="Catatan Tambahan" />
        <textarea id="notes" name="notes" rows="3"
                  class="mt-1.5 block w-full rounded-lg border-gray-300 bg-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-2">{{ old('notes', $transaction?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>
</div>