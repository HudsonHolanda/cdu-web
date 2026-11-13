<x-app-layout>
    <div class="complaints-page complaint-create-page">
        <a class="complaint-back-link" href="{{ route('complaints.index') }}"><span aria-hidden="true">←</span> Voltar para denúncias</a>

        <div class="complaint-create-heading">
            <p class="complaints-eyebrow">Sua cidade, sua participação</p>
            <h1>Fazer denúncia</h1>
            <p class="complaints-lead">Conte para a gente o que está acontecendo. As informações ajudam a equipe responsável a localizar e entender o problema.</p>
        </div>

        @if ($errors->any())
            <div class="complaint-form-errors" role="alert">
                <strong>Confira os campos destacados:</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="complaint-create-layout">
            <form class="complaint-form-panel" method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data">
                @csrf

                <section class="complaint-form-section">
                    <div class="complaint-form-section-heading"><span>01</span><div><h2>Sobre o problema</h2><p>Descreva o que você encontrou.</p></div></div>

                    <div class="complaint-field">
                        <label for="category">Tipo de problema <span aria-hidden="true">*</span></label>
                        <select id="category" name="category" required class="{{ $errors->has('category') ? 'is-invalid' : '' }}">
                            <option value="">Selecione uma categoria</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                        @error('category')<span class="complaint-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="complaint-field">
                        <label for="title">Título <span aria-hidden="true">*</span></label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}" maxlength="120" required placeholder="Ex.: Buraco grande na Rua das Flores" class="{{ $errors->has('title') ? 'is-invalid' : '' }}">
                        @error('title')<span class="complaint-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="complaint-field">
                        <label for="description">Descrição <span aria-hidden="true">*</span></label>
                        <textarea id="description" name="description" rows="5" maxlength="2000" required placeholder="Explique o que aconteceu, há quanto tempo e como isso afeta a região." class="{{ $errors->has('description') ? 'is-invalid' : '' }}">{{ old('description') }}</textarea>
                        <span class="complaint-field-hint">Inclua detalhes que ajudem a equipe a avaliar o caso.</span>
                        @error('description')<span class="complaint-field-error">{{ $message }}</span>@enderror
                    </div>
                </section>

                <section class="complaint-form-section">
                    <div class="complaint-form-section-heading"><span>02</span><div><h2>Onde acontece</h2><p>Informe o endereço e a posição aproximada.</p></div></div>

                    <div class="complaint-field">
                        <label for="address">Endereço <span aria-hidden="true">*</span></label>
                        <input id="address" name="address" type="text" value="{{ old('address') }}" maxlength="180" required placeholder="Rua, avenida e número" class="{{ $errors->has('address') ? 'is-invalid' : '' }}">
                        @error('address')<span class="complaint-field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="complaint-form-row">
                        <div class="complaint-field">
                            <label for="district">Bairro <span aria-hidden="true">*</span></label>
                            <input id="district" name="district" type="text" value="{{ old('district') }}" maxlength="100" required placeholder="Seu bairro" class="{{ $errors->has('district') ? 'is-invalid' : '' }}">
                            @error('district')<span class="complaint-field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="complaint-field">
                            <label for="cep">CEP <span aria-hidden="true">*</span></label>
                            <input id="cep" name="cep" type="text" value="{{ old('cep') }}" inputmode="numeric" maxlength="9" required placeholder="00000-000" class="{{ $errors->has('cep') ? 'is-invalid' : '' }}">
                            @error('cep')<span class="complaint-field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="complaint-field">
                        <label for="complement">Ponto de referência <span class="complaint-optional">Opcional</span></label>
                        <input id="complement" name="complement" type="text" value="{{ old('complement') }}" maxlength="160" placeholder="Ex.: em frente à escola">
                    </div>

                    <div class="complaint-form-row">
                        <div class="complaint-field">
                            <label for="latitude">Latitude <span aria-hidden="true">*</span></label>
                            <input id="latitude" name="latitude" type="number" value="{{ old('latitude') }}" min="-90" max="90" step="any" required placeholder="Ex.: -23.5505" class="{{ $errors->has('latitude') ? 'is-invalid' : '' }}">
                            @error('latitude')<span class="complaint-field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="complaint-field">
                            <label for="longitude">Longitude <span aria-hidden="true">*</span></label>
                            <input id="longitude" name="longitude" type="number" value="{{ old('longitude') }}" min="-180" max="180" step="any" required placeholder="Ex.: -46.6333" class="{{ $errors->has('longitude') ? 'is-invalid' : '' }}">
                            @error('longitude')<span class="complaint-field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <p class="complaint-field-hint">Use a latitude e longitude do local. Você pode encontrá-las tocando no ponto desejado em um aplicativo de mapas.</p>
                    <button id="locate-me" class="complaint-location-button" type="button">Usar minha localização atual</button>
                    <span id="location-feedback" class="complaint-field-hint" role="status"></span>
                </section>

                <section class="complaint-form-section">
                    <div class="complaint-form-section-heading"><span>03</span><div><h2>Fotos do local</h2><p>Uma imagem ajuda a equipe a entender melhor o problema.</p></div></div>

                    <label class="complaint-upload-box {{ $errors->has('photos') || $errors->has('photos.*') ? 'is-invalid' : '' }}" for="photos">
                        <span class="complaint-upload-icon" aria-hidden="true">＋</span>
                        <strong>Escolher fotos</strong>
                        <span>JPG, PNG ou WebP · até 5 fotos · 5 MB cada</span>
                        <input id="photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple required>
                    </label>
                    <div id="photo-selection" class="complaint-photo-selection" aria-live="polite">Nenhuma foto selecionada.</div>
                    @error('photos')<span class="complaint-field-error">{{ $message }}</span>@enderror
                    @error('photos.*')<span class="complaint-field-error">{{ $message }}</span>@enderror
                </section>

                <div class="complaint-form-actions">
                    <a href="{{ route('complaints.index') }}" class="complaint-cancel-button">Cancelar</a>
                    <button type="submit" class="complaint-submit-button">Enviar denúncia <span aria-hidden="true">→</span></button>
                </div>
            </form>

            <aside class="complaint-create-aside">
                <section class="complaint-tips-card">
                    <span class="complaint-tips-icon" aria-hidden="true">✦</span>
                    <h2>Um bom relato ajuda</h2>
                    <ul>
                        <li>Seja claro e objetivo na descrição.</li>
                        <li>Informe o endereço e um ponto de referência.</li>
                        <li>Inclua fotos atuais do local, sem dados pessoais de terceiros.</li>
                        <li>Envie apenas relatos relacionados à sua cidade.</li>
                    </ul>
                </section>
                <section class="complaint-privacy-card">
                    <h2>Seus dados</h2>
                    <p>Seu relato fica associado à sua conta para que você possa acompanhá-lo. Não inclua informações pessoais na descrição ou nas fotos.</p>
                </section>
            </aside>
        </div>
    </div>
    <script>
        document.getElementById('locate-me')?.addEventListener('click', () => {
            const feedback = document.getElementById('location-feedback');
            if (!navigator.geolocation) {
                feedback.textContent = 'Este navegador não disponibiliza localização.';
                return;
            }
            feedback.textContent = 'Obtendo localização…';
            navigator.geolocation.getCurrentPosition(
                ({ coords }) => {
                    document.getElementById('latitude').value = coords.latitude.toFixed(6);
                    document.getElementById('longitude').value = coords.longitude.toFixed(6);
                    feedback.textContent = 'Coordenadas preenchidas. Confirme se correspondem ao local da ocorrência.';
                },
                () => { feedback.textContent = 'Não foi possível obter a localização. Você pode preencher as coordenadas manualmente.'; },
                { enableHighAccuracy: true, timeout: 10000 },
            );
        });

        document.getElementById('photos')?.addEventListener('change', (event) => {
            const files = Array.from(event.target.files || []);
            document.getElementById('photo-selection').textContent = files.length
                ? `${files.length} ${files.length === 1 ? 'foto selecionada' : 'fotos selecionadas'}: ${files.map((file) => file.name).join(', ')}`
                : 'Nenhuma foto selecionada.';
        });
    </script>
</x-app-layout>
