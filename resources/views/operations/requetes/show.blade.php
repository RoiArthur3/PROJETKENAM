@extends('layouts.app')
@section('content')
<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="fas fa-file-alt me-2"></i>Détail de la requête</h3>
    <div>
      <a href="{{ route('requetes.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Retour à la liste</a>
      @if(($operation->statut_requete ?? null) === 'ENREGISTREE' || ($operation->statut_requete ?? null) === 'EN_ATTENTE_ENVOI')
        <form class="d-inline" method="POST" action="{{ route('requetes.envoyer', $operation) }}">
          @csrf
          <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Envoyer</button>
        </form>
      @endif
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header bg-light"><strong>Informations</strong></div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-sm-4">Référence</dt>
            <dd class="col-sm-8"><code class="bg-light px-2 py-1 rounded">{{ $operation->reference_requete ?? '—' }}</code></dd>

            <dt class="col-sm-4">Objet</dt>
            <dd class="col-sm-8">{{ $operation->nom ?? '[Sans objet]' }}</dd>

            <dt class="col-sm-4">Description</dt>
            <dd class="col-sm-8">{{ $operation->description ?? '—' }}</dd>

            <dt class="col-sm-4">Priorité</dt>
            <dd class="col-sm-8">{{ $operation->priorite ?? '—' }}</dd>

            <dt class="col-sm-4">Statut</dt>
            <dd class="col-sm-8">{{ str_replace('_',' ', $operation->statut_requete ?? '—') }}</dd>

            <dt class="col-sm-4">Service émetteur</dt>
            <dd class="col-sm-8">{{ optional($operation->serviceEmetteur)->nom ?? 'Non défini' }}</dd>

            <dt class="col-sm-4">Service destinataire</dt>
            <dd class="col-sm-8">{{ optional($operation->serviceDestinataire)->nom ?? '—' }}</dd>

            <dt class="col-sm-4">Demandeur</dt>
            <dd class="col-sm-8">{{ optional($operation->user)->name ?? 'Utilisateur inconnu' }} ({{ optional($operation->user)->email ?? '—' }})</dd>

            <dt class="col-sm-4">Créée le</dt>
            <dd class="col-sm-8">{{ optional($operation->created_at)->format('d/m/Y H:i') ?? '—' }}</dd>
          </dl>
        </div>
      </div>

      <div class="card">
        <div class="card-header bg-light"><strong>Historique</strong></div>
        <div class="card-body">
          @if(($operation->historiques ?? collect())->count())
            <ul class="list-group list-group-flush">
              @foreach($operation->historiques as $h)
                <li class="list-group-item">
                  <div class="d-flex justify-content-between">
                    <div>
                      <strong>{{ $h->action }}</strong>
                      @if($h->commentaire)
                        <span class="text-muted">— {{ $h->commentaire }}</span>
                      @endif
                      <div class="small text-muted">
                        par {{ optional($h->user)->name ?? 'Système' }}
                      </div>
                    </div>
                    <div class="text-muted small">{{ optional($h->created_at)->format('d/m/Y H:i') ?? '—' }}</div>
                  </div>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-muted">Aucune entrée d'historique.</div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header bg-light"><strong>Actions</strong></div>
        <div class="card-body d-grid gap-2">
          @if(($operation->statut_requete ?? null) === 'ENREGISTREE')
            <form method="POST" action="{{ route('requetes.envoyer', $operation) }}">@csrf
              <button class="btn btn-primary w-100" type="submit"><i class="fas fa-paper-plane me-1"></i>Envoyer la requête</button>
            </form>
          @endif
          <form method="POST" action="{{ route('requetes.cloturer', $operation) }}">@csrf
            <input type="hidden" name="commentaire" value="Clôture depuis la fiche">
            <button class="btn btn-success w-100" type="submit"><i class="fas fa-check me-1"></i>Clôturer</button>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header bg-light"><strong>Transférer</strong></div>
        <div class="card-body">
          <form method="POST" action="{{ route('requetes.transferer', $operation) }}">
            @csrf
            <div class="mb-2">
              <label class="form-label">Nouveau service</label>
              <select class="form-select" name="service_destinataire_id" required>
                @foreach($services as $s)
                  <option value="{{ $s->id }}">{{ $s->nom }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Commentaire</label>
              <textarea class="form-control" name="commentaire" rows="2" placeholder="Commentaire optionnel..."></textarea>
            </div>
            <button class="btn btn-outline-primary w-100" type="submit"><i class="fas fa-exchange-alt me-1"></i>Transférer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
