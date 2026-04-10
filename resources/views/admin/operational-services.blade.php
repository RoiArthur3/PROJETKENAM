@extends('layouts.app')

@section('title', 'Paramétrage - Services opérationnels')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-concierge-bell me-2 text-primary"></i>Services opérationnels
      </h1>
      <p class="text-muted mb-0">Gérez les services et leurs modules d'accès</p>
    </div>
    <a href="{{ route('admin.systeme.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Paramètres système</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <!-- Statistiques des services -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card bg-primary text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-0">{{ ($services ?? collect())->count() }}</h4>
              <small>Total services</small>
            </div>
            <i class="fas fa-building fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-0">{{ ($services ?? collect())->where('actif', true)->count() }}</h4>
              <small>Services actifs</small>
            </div>
            <i class="fas fa-check-circle fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-0">{{ ($services ?? collect())->filter(fn($s) => $s->email)->count() }}</h4>
              <small>Avec email</small>
            </div>
            <i class="fas fa-envelope fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h4 class="mb-0">{{ ($services ?? collect())->filter(fn($s) => $s->responsable)->count() }}</h4>
              <small>Avec responsable</small>
            </div>
            <i class="fas fa-user-check fa-2x opacity-75"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-5">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white">
          <h6 class="mb-0"><i class="fas fa-plus me-2"></i>Nouveau service</h6>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.services.store') }}" class="row g-3">
            @csrf
            <div class="col-md-5">
              <label class="form-label">Code</label>
              <input type="text" name="code" class="form-control" placeholder="Ex: TRANSPORT" required>
            </div>
            <div class="col-md-7">
              <label class="form-label">Nom</label>
              <input type="text" name="nom" class="form-control" placeholder="Libellé du service" required>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <input type="text" name="description" class="form-control" placeholder="Optionnel">
            </div>
            <div class="col-12">
              <label class="form-label">Email du service</label>
              <input type="email" name="email" class="form-control" placeholder="service@kenam.ci">
            </div>
            <div class="col-md-5">
              <label class="form-label">Ordre</label>
              <input type="number" name="ordre" class="form-control" min="1" value="1">
            </div>
            <div class="col-md-7 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" name="actif" id="svcActif" checked>
                <label class="form-check-label" for="svcActif">Actif</label>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Modules d'accès</label>
              <div class="row g-2">
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="modules[]" value="dashboard" id="mod_dashboard" checked>
                    <label class="form-check-label" for="mod_dashboard">Dashboard</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="modules[]" value="operations" id="mod_operations" checked>
                    <label class="form-check-label" for="mod_operations">Opérations</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="modules[]" value="fleet" id="mod_fleet">
                    <label class="form-check-label" for="mod_fleet">Parc auto</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="modules[]" value="warehouse" id="mod_warehouse">
                    <label class="form-check-label" for="mod_warehouse">Entrepôt</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="modules[]" value="hr" id="mod_hr">
                    <label class="form-check-label" for="mod_hr">RH</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="modules[]" value="accounting" id="mod_accounting">
                    <label class="form-check-label" for="mod_accounting">Comptabilité</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="modules[]" value="reporting" id="mod_reporting">
                    <label class="form-check-label" for="mod_reporting">Rapports</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="modules[]" value="treasury" id="mod_treasury">
                    <label class="form-check-label" for="mod_treasury">Trésorerie</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
              <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Enregistrer</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white">
          <h6 class="mb-0"><i class="fas fa-list me-2"></i>Liste des services</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">@@
              <thead class="table-light">
                <tr>
                  <th>Ordre</th>
                  <th>Code</th>
                  <th>Nom</th>
                  <th>Email service</th>
                  <th>Compte</th>
                  <th>Responsable</th>
                  <th>Modules</th>
                  <th>Statut</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse(($services ?? collect()) as $s)
                <tr>
                  <td>{{ $s->ordre }}</td>
                  <td><code>{{ $s->code }}</code></td>
                  <td class="fw-semibold">{{ $s->nom }}</td>
                  <td>
                    @if($s->email)
                      <a href="mailto:{{ $s->email }}" class="text-decoration-none">
                        <i class="fas fa-envelope me-1"></i>{{ $s->email }}
                      </a>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    @if($s->email && $s->password)
                      <span class="badge bg-success">Configuré</span>
                    @else
                      <span class="badge bg-warning">Incomplet</span>
                    @endif
                  </td>
                  <td>
                    @if($s->responsable)
                      <span class="badge bg-info">{{ $s->responsable->name }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex flex-wrap gap-1">
                      @if($s->modules)
                        @foreach(json_decode($s->modules ?? '[]') as $module)
                          <span class="badge bg-secondary" style="font-size: 0.7rem;">{{ $module }}</span>
                        @endforeach
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </div>
                  </td>
                  <td>
                    @if($s->actif)
                      <span class="badge bg-success">Actif</span>
                    @else
                      <span class="badge bg-danger">Inactif</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <div class="btn-group btn-group-sm">
                      <button class="btn btn-outline-primary" onclick="editService({{ $s->id }})" title="Modifier">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button class="btn btn-outline-info" onclick="manageModules({{ $s->id }})" title="Gérer les modules">
                        <i class="fas fa-cogs"></i>
                      </button>
                      <button class="btn btn-outline-warning" onclick="manageAccount({{ $s->id }})" title="Gérer le compte">
                        <i class="fas fa-user-cog"></i>
                      </button>
                      <button class="btn btn-outline-danger" onclick="deleteService({{ $s->id }})" title="Supprimer">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="8" class="text-center text-muted">Aucun service défini</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modulesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modules d'accès</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="modulesForm">
          <input type="hidden" id="modules_service_id" />
          <div class="row g-2">
            <div class="col-6">
              <div class="form-check"><input class="form-check-input" type="checkbox" value="dashboard" id="modal_dashboard"><label class="form-check-label" for="modal_dashboard">Dashboard</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" value="operations" id="modal_operations"><label class="form-check-label" for="modal_operations">Opérations</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" value="fleet" id="modal_fleet"><label class="form-check-label" for="modal_fleet">Parc auto</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" value="warehouse" id="modal_warehouse"><label class="form-check-label" for="modal_warehouse">Entrepôt</label></div>
            </div>
            <div class="col-6">
              <div class="form-check"><input class="form-check-input" type="checkbox" value="hr" id="modal_hr"><label class="form-check-label" for="modal_hr">RH</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" value="accounting" id="modal_accounting"><label class="form-check-label" for="modal_accounting">Comptabilité</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" value="reporting" id="modal_reporting"><label class="form-check-label" for="modal_reporting">Rapports</label></div>
              <div class="form-check"><input class="form-check-input" type="checkbox" value="treasury" id="modal_treasury"><label class="form-check-label" for="modal_treasury">Trésorerie</label></div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" onclick="saveModules()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="accountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Compte du service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="account_service_id" />
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" id="account_email" placeholder="service@kenam.ci">
        </div>
        <div class="mb-3">
          <label class="form-label">Mot de passe</label>
          <input type="text" class="form-control" id="account_password" placeholder="Mot de passe">
        </div>
        <div class="mb-3">
          <label class="form-label">Téléphone</label>
          <input type="text" class="form-control" id="account_phone" placeholder="+225 ...">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" onclick="saveAccount()">Enregistrer</button>
      </div>
    </div>
  </div>
</div>

<script>
  function editService(serviceId) {
    window.location.href = `/admin/services/${serviceId}/edit`;
  }

  function deleteService(serviceId) {
    if (!confirm('Supprimer ce service ?')) return;
    fetch(`/admin/services/${serviceId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
      .then(r => r.json())
      .then(() => location.reload());
  }

  function manageModules(serviceId) {
    fetch(`/admin/services/${serviceId}/modules`)
      .then(r => r.json())
      .then(data => {
        document.getElementById('modules_service_id').value = serviceId;
        document.querySelectorAll('#modulesForm input[type="checkbox"]').forEach(cb => cb.checked = false);
        (data.modules || []).forEach(m => {
          const cb = document.getElementById('modal_' + m);
          if (cb) cb.checked = true;
        });
        new bootstrap.Modal(document.getElementById('modulesModal')).show();
      });
  }

  function saveModules() {
    const serviceId = document.getElementById('modules_service_id').value;
    const modules = [];
    document.querySelectorAll('#modulesForm input[type="checkbox"]:checked').forEach(cb => modules.push(cb.value));

    fetch(`/admin/services/${serviceId}/modules`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ modules })
    })
      .then(r => r.json())
      .then(data => {
        if (data && data.success) {
          bootstrap.Modal.getInstance(document.getElementById('modulesModal')).hide();
          location.reload();
        }
      });
  }

  function manageAccount(serviceId) {
    fetch(`/admin/services/${serviceId}/account`)
      .then(r => r.json())
      .then(data => {
        document.getElementById('account_service_id').value = serviceId;
        document.getElementById('account_email').value = (data && data.email) ? data.email : '';
        document.getElementById('account_password').value = (data && data.password) ? data.password : '';
        document.getElementById('account_phone').value = (data && data.phone) ? data.phone : '';
        new bootstrap.Modal(document.getElementById('accountModal')).show();
      });
  }

  function saveAccount() {
    const serviceId = document.getElementById('account_service_id').value;
    const payload = {
      email: document.getElementById('account_email').value,
      password: document.getElementById('account_password').value,
      phone: document.getElementById('account_phone').value,
    };

    fetch(`/admin/services/${serviceId}/account`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(payload)
    })
      .then(r => r.json())
      .then(data => {
        if (data && data.success) {
          bootstrap.Modal.getInstance(document.getElementById('accountModal')).hide();
          location.reload();
        }
      });
  }
</script>
@endsection
