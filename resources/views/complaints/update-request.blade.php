<x-app-layout>
    @php($location = $complaint['location'])
    <div class="complaints-page complaint-create-page">
        <a class="complaint-back-link" href="{{ route('complaints.mine') }}"><span aria-hidden="true">←</span> Voltar para minhas denúncias</a>

        <div class="complaint-create-heading">
            <p class="complaints-eyebrow">Solicitação de alteração</p>
            <h1>Corrigir dados da denúncia</h1>
            <p class="complaints-lead">Envie os dados corrigidos e explique o motivo. Um agente analisará a solicitação antes de aplicar qualquer mudança.</p>
        </div>

        @if ($errors->any())
            <div class="complaint-form-errors" role="alert">
                <strong>Confira os campos destacados:</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="complaint-create-layout">
            <form class="complaint-form-panel" method="POST" action="{{ route('complaints.update-requests.store', ['complaint' => $complaint['complaintId']]) }}">
                @csrf
                <section class="complaint-form-section">
                    <div class="complaint-form-section-heading"><span>01</span><div><h2>Dados corrigidos</h2><p>Informe como os dados devem ficar.</p></div></div>
                    <div class="complaint-field">
                        <label for="category">Tipo de problema <span aria-hidden="true">*</span></label>
                        <select id="category" name="category" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(old('category', $complaint['category']) === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="complaint-field">
                        <label for="title">Título <span aria-hidden="true">*</span></label>
                        <input id="title" name="title" type="text" value="{{ old('title', $complaint['title']) }}" maxlength="120" required>
                    </div>
                    <div class="complaint-field">
                        <label for="description">Descrição <span aria-hidden="true">*</span></label>
                        <textarea id="description" name="description" rows="5" maxlength="2000" required>{{ old('description', $complaint['description']) }}</textarea>
                    </div>
                </section>

                <section class="complaint-form-section">
                    <div class="complaint-form-section-heading"><span>02</span><div><h2>Localização corrigida</h2><p>Corrija endereço, bairro, CEP ou coordenadas.</p></div></div>
                    <div class="complaint-field">
                        <label for="address">Endereço <span aria-hidden="true">*</span></label>
                        <input id="address" name="address" type="text" value="{{ old('address', $location['address']) }}" maxlength="180" required>
                    </div>
                    <div class="complaint-form-row">
                        <div class="complaint-field"><label for="district">Bairro</label><input id="district" name="district" type="text" value="{{ old('district', $location['district']) }}" maxlength="100"></div>
                        <div class="complaint-field"><label for="cep">CEP</label><input id="cep" name="cep" type="text" value="{{ old('cep', $location['cep']) }}" inputmode="numeric" maxlength="9"></div>
                    </div>
                    <div class="complaint-field"><label for="complement">Ponto de referência</label><input id="complement" name="complement" type="text" value="{{ old('complement', $location['complement']) }}" maxlength="160"></div>
                    <div class="complaint-form-row">
                        <div class="complaint-field"><label for="latitude">Latitude <span aria-hidden="true">*</span></label><input id="latitude" name="latitude" type="number" value="{{ old('latitude', $location['latitude']) }}" min="-90" max="90" step="any" required></div>
                        <div class="complaint-field"><label for="longitude">Longitude <span aria-hidden="true">*</span></label><input id="longitude" name="longitude" type="number" value="{{ old('longitude', $location['longitude']) }}" min="-180" max="180" step="any" required></div>
                    </div>
                </section>

                <section class="complaint-form-section">
                    <div class="complaint-form-section-heading"><span>03</span><div><h2>Justificativa</h2><p>Explique o que precisa ser corrigido.</p></div></div>
                    <div class="complaint-field">
                        <label for="reason">Motivo da solicitação <span aria-hidden="true">*</span></label>
                        <textarea id="reason" name="reason" rows="5" minlength="10" maxlength="1000" required>{{ old('reason') }}</textarea>
                    </div>
                </section>

                <div class="complaint-form-actions">
                    <a class="complaint-cancel-button" href="{{ route('complaints.mine') }}">Cancelar</a>
                    <button class="complaint-submit-button" type="submit">Enviar solicitação</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
