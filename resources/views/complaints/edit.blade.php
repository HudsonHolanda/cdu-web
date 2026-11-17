<x-app-layout>
    @php
        $location = $complaint['location'];
        $remainingPhotos = max(0, 5 - count($complaint['attachments']));
    @endphp

    <div class="complaints-page complaint-create-page">
        <a class="complaint-back-link" href="{{ route('complaints.mine') }}"><span aria-hidden="true">←</span> Voltar para minhas denúncias</a>

        <div class="complaint-create-heading">
            <p class="complaints-eyebrow">Relato pendente</p>
            <h1>Editar denúncia</h1>
            <p class="complaints-lead">Atualize as informações enquanto o relato ainda está pendente.</p>
        </div>

        @if ($errors->any())
            <div class="complaint-form-errors" role="alert">
                <strong>Confira os campos destacados:</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="complaint-create-layout complaint-edit-layout">
            <form class="complaint-form-panel" method="POST" action="{{ route('complaints.update', ['complaint' => $complaint['complaintId']]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <section class="complaint-form-section">
                    <div class="complaint-form-section-heading"><span>01</span><div><h2>Sobre o problema</h2><p>Revise a categoria e a descrição.</p></div></div>
                    <div class="complaint-field">
                        <label for="category">Tipo de problema <span aria-hidden="true">*</span></label>
                        <select id="category" name="category" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('category', $complaint['category']) === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                        @error('category')<span class="complaint-field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="complaint-field">
                        <label for="title">Título <span aria-hidden="true">*</span></label>
                        <input id="title" name="title" type="text" value="{{ old('title', $complaint['title']) }}" maxlength="120" required>
                        @error('title')<span class="complaint-field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="complaint-field">
                        <label for="description">Descrição <span aria-hidden="true">*</span></label>
                        <textarea id="description" name="description" rows="5" maxlength="2000" required>{{ old('description', $complaint['description']) }}</textarea>
                        @error('description')<span class="complaint-field-error">{{ $message }}</span>@enderror
                    </div>
                </section>

                <section class="complaint-form-section">
                    <div class="complaint-form-section-heading"><span>02</span><div><h2>Onde acontece</h2><p>Revise o endereço e as coordenadas.</p></div></div>
                    <div class="complaint-field">
                        <label for="address">Endereço <span aria-hidden="true">*</span></label>
                        <input id="address" name="address" type="text" value="{{ old('address', $location['address']) }}" maxlength="180" required>
                        @error('address')<span class="complaint-field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="complaint-form-row">
                        <div class="complaint-field">
                            <label for="district">Bairro</label>
                            <input id="district" name="district" type="text" value="{{ old('district', $location['district']) }}" maxlength="100">
                            @error('district')<span class="complaint-field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="complaint-field">
                            <label for="cep">CEP</label>
                            <input id="cep" name="cep" type="text" value="{{ old('cep', $location['cep']) }}" inputmode="numeric" maxlength="9" placeholder="00000-000">
                            @error('cep')<span class="complaint-field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="complaint-field">
                        <label for="complement">Ponto de referência <span class="complaint-optional">Opcional</span></label>
                        <input id="complement" name="complement" type="text" value="{{ old('complement', $location['complement']) }}" maxlength="160">
                    </div>
                    <div class="complaint-form-row">
                        <div class="complaint-field">
                            <label for="latitude">Latitude <span aria-hidden="true">*</span></label>
                            <input id="latitude" name="latitude" type="number" value="{{ old('latitude', $location['latitude']) }}" min="-90" max="90" step="any" required>
                            @error('latitude')<span class="complaint-field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="complaint-field">
                            <label for="longitude">Longitude <span aria-hidden="true">*</span></label>
                            <input id="longitude" name="longitude" type="number" value="{{ old('longitude', $location['longitude']) }}" min="-180" max="180" step="any" required>
                            @error('longitude')<span class="complaint-field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </section>

                <section class="complaint-form-section">
                    <div class="complaint-form-section-heading"><span>03</span><div><h2>Fotos do local</h2><p>As fotos atuais são mantidas. Você pode adicionar mais {{ $remainingPhotos }}.</p></div></div>
                    @if (count($complaint['attachments']))
                        <div class="complaint-existing-photos">
                            @foreach ($complaint['attachments'] as $filename)
                                <img src="{{ route('complaints.image', ['filename' => basename($filename)]) }}" alt="Foto atual do relato">
                            @endforeach
                        </div>
                    @endif
                    @if ($remainingPhotos > 0)
                        <label class="complaint-upload-box {{ $errors->has('photos') || $errors->has('photos.*') ? 'is-invalid' : '' }}" for="photos">
                            <span class="complaint-upload-icon" aria-hidden="true">＋</span>
                            <strong>Adicionar fotos</strong>
                            <span>JPG, PNG ou WebP · até {{ $remainingPhotos }} fotos · 5 MB cada</span>
                            <input id="photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
                        </label>
                        @error('photos')<span class="complaint-field-error">{{ $message }}</span>@enderror
                        @error('photos.*')<span class="complaint-field-error">{{ $message }}</span>@enderror
                    @else
                        <p class="complaint-field-hint">Este relato já tem o limite de cinco fotos.</p>
                    @endif
                </section>

                <div class="complaint-form-actions">
                    <a href="{{ route('complaints.mine') }}" class="complaint-cancel-button">Cancelar</a>
                    <button type="submit" class="complaint-submit-button">Salvar alterações <span aria-hidden="true">→</span></button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
