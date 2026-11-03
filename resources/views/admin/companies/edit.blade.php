<x-admin-layout title="Compañías" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Compañías',
        'href' => route('admin.companies.index'),
    ],
    [
        'name' => 'Editar',
        'href' => route('admin.companies.edit', $company),
    ],
]">
    @push('css')
    <link rel="stylesheet" href="{{ url('https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.css') }}" type="text/css" />
    @endpush

    <p class="block text-sm font-medium disabled:opacity-60 text-gray-700 dark:text-gray-400 invalidated:text-negative-600 dark:invalidated:text-negative-700">Logo</p>
    <div class="mb-4">
        <form action="{{ route('admin.companies.logo', $company) }}" class="dropzone" id="my-logo-dropzone" method="POST">
            @csrf
        </form>
    </div>

    <p class="block text-sm font-medium disabled:opacity-60 text-gray-700 dark:text-gray-400 invalidated:text-negative-600 dark:invalidated:text-negative-700">Certificado</p>
    <div class="mb-4">
        <form action="{{ route('admin.companies.cert', $company) }}" class="dropzone" id="my-cert-dropzone" method="POST">
            @csrf
        </form>
    </div>

    <x-wire-card>
        <form action="{{ route('admin.companies.update', $company) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <x-wire-input label="Razón Social" name="razon_social" placeholder="Razón social de la compañía" value="{{ old('razon_social', $company->razon_social) }}" />

            <x-wire-input label="RUC" name="ruc" placeholder="Número de RUC" value="{{ old('ruc', $company->ruc) }}" />

            <x-wire-input label="Dirección" name="direccion" placeholder="Dirección de la compañía" value="{{ old('direccion', $company->direccion) }}" />
            
            <x-wire-input label="Usuario SOL" name="sol_user" placeholder="Usuario SOL" value="{{ old('sol_user', $company->sol_user) }}" />
            
            <x-wire-input type="password" label="Contraseña SOL" name="sol_pass" placeholder="Contraseña SOL" value="{{ old('sol_pass', $company->sol_pass) }}" />
            
            <x-wire-input label="Credencial ID" name="client_id" placeholder="Credencial ID" value="{{ old('client_id', $company->client_id) }}" />
            
            <x-wire-input label="Credencial Secret" name="client_secret" placeholder="Credencial Secret" value="{{ old('client_secret', $company->client_secret) }}" />

            <x-wire-native-select label="Produccion" name="production">
                <option value="0" @selected(old('production', $company->production)==0)>No</option>
                <option value="1" @selected(old('production', $company->production)==1)>Si</option>
            </x-wire-native-select>

            <div class="flex justify-end">
                <x-button>
                    Guardar
                </x-button>
            </div>
        </form>
    </x-wire-card>

    @push('js')
    <script src="{{ url('https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.js') }}"></script>

    <script>
        // Note that the name "myLogoDropzone" is the camelized
        // id of the form.
        Dropzone.options.myLogoDropzone = {
            // Configuration options go here
            addRemoveLinks: true,
            init: function() {
                let myDropzone = this;
                let images = @json($company->images);

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
                    axios.delete(`{{ config('app.url') }}/admin/logos/${file.id}`)
                        .then(response => {
                            console.log(response.data);
                        })
                        .catch(error => {
                            console.error(error);
                        });
                });
            }
        };

        Dropzone.options.myCertDropzone = {
            // Configuration options go here
            addRemoveLinks: true,
            init: function() {
                let myDropzone = this;
                let certs = @json($company->certs);

                certs.forEach(function(cert) {
                    let mockFile = {
                        id: cert.id,
                        name: cert.path.split('/').pop(),
                        size: cert.size
                    };

                    myDropzone.displayExistingFile(mockFile, `{{ Storage::url('${cert.path}') }}`);
                    myDropzone.emit('complete', mockFile);
                    myDropzone.files.push(mockFile);
                });

                this.on('success', function(file, response) {
                    file.id = response.id;
                });

                this.on('removedfile', function(file) {
                    axios.delete(`{{ config('app.url') }}/admin/certs/${file.id}`)
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