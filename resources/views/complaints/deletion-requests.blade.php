<x-app-layout>
    <div class="complaints-page">
        <div class="complaints-heading">
            <div>
                <p class="complaints-eyebrow">Gestão de denúncias</p>
                <h1>Solicitações</h1>
                <p class="complaints-lead">Analise os pedidos enviados pelos cidadãos antes de alterar ou remover uma denúncia.</p>
            </div>
            <a class="complaints-create-link" href="{{ route('complaints.index') }}">Voltar para denúncias</a>
        </div>

        @if (session('success'))
            <div class="complaint-success-message" role="status">{{ session('success') }}</div>
        @endif

        @forelse ($deletionRequests as $deletionRequest)
            <article class="complaint-detail-panel mb-5">
                <div class="complaint-section-heading">
                    <div>
                        <p class="complaint-section-kicker">Solicitação #{{ $deletionRequest->getKey() }}</p>
                        <h2>{{ $deletionRequest->complaint?->title ?? 'Denúncia removida' }}</h2>
                        <p>Solicitada por {{ $deletionRequest->requester?->name ?? 'Usuário' }} em {{ $deletionRequest->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <p><strong>Motivo do cidadão:</strong> {{ $deletionRequest->reason }}</p>
                <form method="POST" action="{{ route('complaints.deletion-requests.review', ['deletionRequest' => $deletionRequest]) }}" class="complaint-request-form mt-5">
                    @csrf
                    @method('PATCH')
                    <div class="complaint-form-row">
                        <div class="complaint-field">
                            <label for="decision-{{ $deletionRequest->getKey() }}">Decisão</label>
                            <select id="decision-{{ $deletionRequest->getKey() }}" name="decision" class="complaint-decision-select" required>
                                <option value="rejected">Recusar exclusão</option>
                                <option value="approved">Aprovar exclusão</option>
                            </select>
                        </div>
                        <div class="complaint-field">
                            <label for="reviewer-reason-{{ $deletionRequest->getKey() }}">Justificativa da análise</label>
                            <textarea id="reviewer-reason-{{ $deletionRequest->getKey() }}" name="reviewer_reason" rows="3" minlength="5" maxlength="1000" required></textarea>
                        </div>
                    </div>
                    <button class="complaint-submit-button" type="submit">Registrar decisão</button>
                </form>
            </article>
        @empty
            <div class="complaints-empty">
                <span class="complaints-empty-icon" aria-hidden="true">✓</span>
                <h2>Nenhuma solicitação de exclusão pendente</h2>
                <p>Todos os pedidos de exclusão foram analisados.</p>
            </div>
        @endforelse

        <h2 class="mt-8 mb-4 text-xl font-semibold">Solicitações de alteração</h2>
        @forelse ($updateRequests as $updateRequest)
            <article class="complaint-detail-panel mb-5">
                <div class="complaint-section-heading">
                    <div>
                        <p class="complaint-section-kicker">Solicitação #{{ $updateRequest->getKey() }}</p>
                        <h2>{{ $updateRequest->complaint?->title ?? 'Denúncia indisponível' }}</h2>
                        <p>Solicitada por {{ $updateRequest->requester?->name ?? 'Usuário' }} em {{ $updateRequest->created_at?->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <p><strong>Motivo do cidadão:</strong> {{ $updateRequest->reason }}</p>
                <div class="complaint-update-proposals mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm">
                    <strong>Alterações propostas</strong>
                    <ul class="mt-2 space-y-1">
                        @foreach ($updateRequest->changes as $field => $value)
                            @if (in_array($field, ['category', 'title', 'address', 'district', 'cep', 'complement'], true))
                                <li><span class="font-medium">{{ ucfirst($field) }}:</span> {{ $value ?: 'Não informado' }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <form method="POST" action="{{ route('complaints.update-requests.review', ['updateRequest' => $updateRequest]) }}" class="complaint-request-form mt-5">
                    @csrf
                    @method('PATCH')
                    <div class="complaint-form-row">
                        <div class="complaint-field">
                            <label for="update-decision-{{ $updateRequest->getKey() }}">Decisão</label>
                            <select id="update-decision-{{ $updateRequest->getKey() }}" name="decision" class="complaint-decision-select" required>
                                <option value="rejected">Recusar alteração</option>
                                <option value="approved">Aprovar alteração</option>
                            </select>
                        </div>
                        <div class="complaint-field">
                            <label for="update-reviewer-reason-{{ $updateRequest->getKey() }}">Justificativa da análise</label>
                            <textarea id="update-reviewer-reason-{{ $updateRequest->getKey() }}" name="reviewer_reason" rows="3" minlength="5" maxlength="1000" required></textarea>
                        </div>
                    </div>
                    <button class="complaint-submit-button" type="submit">Registrar decisão</button>
                </form>
            </article>
        @empty
            <div class="complaints-empty">
                <h2>Nenhuma solicitação de alteração pendente</h2>
            </div>
        @endforelse
    </div>
</x-app-layout>
