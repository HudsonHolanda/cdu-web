<x-app-layout>
    <div class="complaints-page complaint-create-page">
        <a class="complaint-back-link" href="{{ route('complaints.mine') }}"><span aria-hidden="true">←</span> Voltar para minhas denúncias</a>

        <div class="complaint-create-heading">
            <p class="complaints-eyebrow">Solicitação de exclusão</p>
            <h1>Solicitar exclusão da denúncia</h1>
            <p class="complaints-lead">A denúncia não será removida imediatamente. Um agente analisará o pedido antes de tomar uma decisão.</p>
        </div>

        @if ($errors->any())
            <div class="complaint-form-errors" role="alert">
                <strong>Confira o motivo informado:</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="complaint-create-layout">
            <section class="complaint-form-panel">
                <div class="complaint-form-section">
                    <div class="complaint-form-section-heading"><span>01</span><div><h2>{{ $complaint['title'] }}</h2><p>{{ $complaint['category'] }} · {{ $complaint['location']['district'] }}</p></div></div>
                    <p>{{ $complaint['description'] }}</p>
                </div>

                <form method="POST" action="{{ route('complaints.deletion-requests.store', ['complaint' => $complaint['complaintId']]) }}">
                    @csrf
                    <div class="complaint-form-section">
                        <div class="complaint-form-section-heading"><span>02</span><div><h2>Justificativa</h2><p>Explique por que esta denúncia deve ser excluída.</p></div></div>
                        <div class="complaint-field">
                            <label for="reason">Motivo da solicitação <span aria-hidden="true">*</span></label>
                            <textarea id="reason" name="reason" rows="6" minlength="10" maxlength="1000" required>{{ old('reason') }}</textarea>
                            <span class="complaint-field-hint">Exemplo: denúncia registrada por engano ou problema já solucionado.</span>
                        </div>
                    </div>
                    <div class="complaint-form-actions">
                        <a class="complaint-cancel-button" href="{{ route('complaints.mine') }}">Cancelar</a>
                        <button class="complaint-submit-button" type="submit">Enviar solicitação</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
