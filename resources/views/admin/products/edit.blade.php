<x-admin-layout title="Productos" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Productos',
        'href' => route('admin.products.index'),
    ],
    [
        'name' => 'Editar',
    ],
]">

    @push('css')
        <link rel="stylesheet" href="{{ asset('build/assets/css/dropzone.min.css') }}" type="text/css" />
    @endpush

    <div class="mb-4">
        <form action="{{ route('admin.products.dropzone', $product) }}" class="dropzone" id="my-dropzone" method="POST">
            @csrf
        </form>
    </div>

    <x-wire-card>
        <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <x-wire-input label="Nombre" name="name" placeholder="Nombre del producto"
                value="{{ old('name', $product->name) }}" />

            <x-wire-textarea label="Descripción" name="description"
                placeholder="Descripción del producto">{{ old('description', $product->description) }}</x-wire-textarea>

            <x-wire-input type="number" label="Precio" name="price" placeholder="Precio del producto"
                value="{{ old('price', $product->price) }}" />

            <x-wire-native-select label="Categoría" name="category_id">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </x-wire-native-select>

            <x-wire-input label="Stock Mínimo" type="number" name="min_stock" placeholder="Cantidad mínima de stock"
                value="{{ old('min_stock', $product->min_stock) }}" />

            <div class="flex justify-end">
                <x-button>
                    Actualizar
                </x-button>
            </div>
        </form>
    </x-wire-card>

    @push('js')
        <script src="{{ asset('build/assets/js/dropzone.min.js') }}"></script>

        <script>
            // Note that the name "myDropzone" is the camelized
            // id of the form.
            Dropzone.options.myDropzone = {
                // Configuration options go here
                addRemoveLinks: true,
                init: function() {
                    let myDropzone = this;
                    let images = @json($product->images);

                    images.forEach(function(image) {
                        let mockFile = {
                            id: image.id,
                            name: image.path.split('/').pop(),
                            size: image.size
                        };

                        myDropzone.displayExistingFile(mockFile, `{{ Storage::url('${image.path}') }}`);
                        myDropzone.emit('complete', mockFile);
                        myDropzone.files.push(mockFile);
                    });

                    this.on('success', function(file, response) {
                        file.id = response.id;
                    });

                    this.on('removedfile', function(file) {
                        axios.delete(`{{ config('app.url') }}/admin/images/${file.id}`)
                            .then(response => {
                                console.log(response.data);
                            })
                            .catch(error => {
                                console.error(error);
                            });
                    });
                }
            };
        </script>
    @endpush
</x-admin-layout>
