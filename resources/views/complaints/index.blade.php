<x-app-layout>
    <div class="complaints-page">
        <div class="complaints-heading">
            <div>
                <p class="complaints-eyebrow">Participação cidadã</p>
                <h1>{{ ($myComplaints ?? false) ? 'Minhas denúncias' : 'Denúncias' }}</h1>
                <p class="complaints-lead">{{ ($myComplaints ?? false) ? 'Acompanhe e gerencie os relatos enviados pela sua conta.' : 'Acompanhe os relatos da sua cidade e ajude a melhorar o lugar onde você vive.' }}</p>
            </div>
            @if ($myComplaints ?? false)
                <a class="complaints-create-link" href="{{ route('complaints.index') }}">Ver todas as denúncias</a>
            @else
                <a class="complaints-create-link" href="{{ route('complaints.create') }}">
                <span aria-hidden="true">＋</span> Fazer denúncia
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="complaint-success-message" role="status">{{ session('success') }}</div>
        @endif

        <form class="complaints-search" method="GET" action="{{ route(($myComplaints ?? false) ? 'complaints.mine' : 'complaints.index') }}" role="search">
            <label class="sr-only" for="complaints-search">Buscar denúncias</label>
            <input id="complaints-search" name="busca" type="search" value="{{ $search }}" placeholder="Buscar por título, descrição ou endereço">
            <button type="submit">Buscar</button>
        </form>

        <div class="complaints-list-meta">
            <p><strong>{{ $complaints->count() }}</strong> {{ $complaints->count() === 1 ? 'denúncia encontrada' : 'denúncias encontradas' }}</p>
            <span>Relatos da comunidade</span>
        </div>

        @if ($complaints->isEmpty())
            <div class="complaints-empty">
                <span class="complaints-empty-icon" aria-hidden="true">⌕</span>
                <h2>{{ ($myComplaints ?? false) ? 'Você ainda não enviou denúncias' : 'Nenhuma denúncia encontrada' }}</h2>
                <p>{{ ($myComplaints ?? false) ? 'Quando enviar um relato, ele aparecerá nesta página.' : 'Tente buscar por outro termo.' }}</p>
            </div>
        @else
            <div class="complaints-grid">
                @foreach ($complaints as $complaint)
                    <article class="complaint-card">
                        <a class="complaint-card-image" href="{{ route('complaints.show', ['publicId' => $complaint['publicId']]) }}" aria-label="Ver detalhes: {{ $complaint['title'] }}">
                            <img src="{{ route('complaints.image', ['filename' => basename($complaint['imageUrl'])]) }}" alt="Imagem da denúncia: {{ $complaint['title'] }}" loading="lazy">
                            <span class="complaint-status complaint-status--{{ \Illuminate\Support\Str::slug($complaint['status']) }}">{{ $complaint['status'] }}</span>
                        </a>
                        <div class="complaint-card-content">
                            <div class="complaint-card-category">
                                <span aria-hidden="true">{{ $complaint['highPriority'] ? '!' : '•' }}</span>
                                {{ $complaint['highPriority'] ? 'Prioridade alta' : ($complaint['category'] ?? 'Relato cidadão') }}
                            </div>
                            <h2><a href="{{ route('complaints.show', ['publicId' => $complaint['publicId']]) }}">{{ $complaint['title'] }}</a></h2>
                            <p class="complaint-card-description">{{ $complaint['description'] }}</p>
                            <div class="complaint-card-location">
                                <span aria-hidden="true">⌖</span>
                                {{ $complaint['location']['district'] }}, {{ $complaint['location']['address'] }}
                            </div>
                            <div class="complaint-card-footer">
                                <span>Publicada em {{ \Illuminate\Support\Carbon::parse($complaint['createdAt'])->format('d/m/Y') }}</span>
                                @if (auth()->user()?->hasRole(\App\Enums\UserRole::AGENT) || (($myComplaints ?? false) && $complaint['status'] === 'Pendente'))
                                    <div class="complaint-card-actions">
                                        <a class="complaint-action-link" href="{{ route('complaints.edit', ['complaint' => $complaint['complaintId']]) }}">{{ auth()->user()?->hasRole(\App\Enums\UserRole::AGENT) ? 'Atualizar' : 'Editar' }}</a>
                                        @if (($myComplaints ?? false))
                                        <form method="POST" action="{{ route('complaints.destroy', ['complaint' => $complaint['complaintId']]) }}" onsubmit="return confirm('Excluir este relato? Esta ação não pode ser desfeita.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="complaint-action-link complaint-action-delete" type="submit">Excluir</button>
                                        </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
