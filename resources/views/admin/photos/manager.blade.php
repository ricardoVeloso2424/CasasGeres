<section class="mt-6 rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-stone-950">Imagens</h2>
            <p class="mt-1 text-sm text-stone-600">Carregue imagens reais, defina a capa e mantenha o texto alternativo atualizado.</p>
        </div>
        <span class="w-fit rounded-md bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">
            {{ $imageable->photos->count() }} imagem{{ $imageable->photos->count() === 1 ? '' : 's' }}
        </span>
    </div>

    <form method="POST" action="{{ route('admin.photos.store', ['type' => $type, 'id' => $imageable->id]) }}" enctype="multipart/form-data" class="mt-5 rounded-lg border border-stone-200 bg-stone-50 p-4">
        @csrf

        <div class="grid gap-4 lg:grid-cols-[1.2fr_1fr_160px]">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Upload
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="rounded-md border border-stone-300 bg-white px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('images') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                @error('images.*') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Alt text
                <input name="alt" value="{{ old('alt') }}" placeholder="{{ $title }}" class="rounded-md border border-stone-300 bg-white px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('alt') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Ordem inicial
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order') }}" class="rounded-md border border-stone-300 bg-white px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('sort_order') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-4">
            <label class="flex items-center gap-2 text-sm font-medium text-stone-800">
                <input type="checkbox" name="is_cover" value="1" @checked(old('is_cover')) class="rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                Definir a primeira imagem enviada como capa
            </label>
            <button class="rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Carregar imagens</button>
        </div>
    </form>

    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($imageable->photos as $photo)
            <article class="overflow-hidden rounded-lg border border-stone-200 bg-stone-50">
                <div class="relative aspect-[4/3] bg-stone-200">
                    <img src="{{ $photo->url }}" alt="{{ $photo->alt ?: $title }}" loading="lazy" decoding="async" class="h-full w-full object-cover">
                    @if ($photo->is_cover)
                        <span class="absolute left-3 top-3 rounded-md bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-950">Capa atual</span>
                    @endif
                </div>

                <div class="grid gap-3 p-4">
                    <form method="POST" action="{{ route('admin.photos.update', $photo) }}" class="grid gap-3">
                        @csrf
                        @method('PATCH')

                        <label class="grid gap-2 text-sm font-medium text-stone-800">
                            Alt text
                            <input name="alt" value="{{ old("photos.{$photo->id}.alt", $photo->alt) }}" class="rounded-md border border-stone-300 bg-white px-3 py-2 text-sm focus:border-emerald-700 focus:outline-none">
                        </label>

                        <label class="grid gap-2 text-sm font-medium text-stone-800">
                            Ordem
                            <input type="number" name="sort_order" min="0" value="{{ old("photos.{$photo->id}.sort_order", $photo->sort_order) }}" class="rounded-md border border-stone-300 bg-white px-3 py-2 text-sm focus:border-emerald-700 focus:outline-none">
                        </label>

                        <button class="rounded-md border border-stone-300 bg-white px-3 py-2 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Guardar dados</button>
                    </form>

                    <div class="flex flex-wrap gap-2">
                        @unless ($photo->is_cover)
                            <form method="POST" action="{{ route('admin.photos.cover', $photo) }}">
                                @csrf
                                @method('PATCH')
                                <button class="rounded-md bg-emerald-800 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-900">Definir como capa</button>
                            </form>
                        @endunless

                        <form method="POST" action="{{ route('admin.photos.destroy', $photo) }}" onsubmit="return confirm('Tem a certeza que quer apagar esta imagem?');">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-md border border-red-200 bg-white px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Apagar</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-stone-300 bg-stone-50 p-6 text-sm text-stone-600">
                Ainda nao existem imagens associadas.
            </div>
        @endforelse
    </div>
</section>
