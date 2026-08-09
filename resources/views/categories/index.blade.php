<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kategori') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-sm text-green-700 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            @foreach (\App\Enums\TransactionType::cases() as $type)
                <section class="mb-10">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $type->label() }}</h3>
                        <a href="{{ route('categories.create', ['type' => $type->value]) }}"
                           class="inline-flex items-center justify-center h-12 px-6 bg-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-sm hover:bg-emerald-500 focus:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            + Tambah {{ $type->label() }}
                        </a>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($categories->where('type', $type->value) as $category)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                                            <td class="px-6 py-4">
                                                @if ($category->is_active)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900">Ubah</a>
                                                @if ($category->is_active)
                                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline ms-3"
                                                          onsubmit="return confirm('Nonaktifkan kategori ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Nonaktifkan</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500">
                                                Belum ada kategori {{ strtolower($type->label()) }}.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-app-layout>