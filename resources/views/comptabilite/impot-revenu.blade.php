@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Déclaration d'Impôt sur le Revenu
                    </h4>
                </div>

                <div class="card-body">
                    <form id="impot-form">
                        @csrf
                        <!-- Informations personnelles -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-user me-2"></i>
                                    Informations personnelles
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nom complet</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre de parts fiscales</label>
                                <select name="nombre_parts" class="form-select" required>
                                    <option value="1">1 part (célibataire)</option>
                                    <option value="1.5">1.5 parts (marié/pacsé sans enfant)</option>
                                    <option value="2">2 parts (marié/pacsé 1 enfant)</option>
                                    <option value="2.5">2.5 parts (marié/pacsé 2 enfants)</option>
                                    <option value="3">3 parts (marié/pacsé 3 enfants)</option>
                                    <option value="3.5">3.5 parts (marié/pacsé 4 enfants)</option>
                                    <option value="4">4 parts (marié/pacsé 5 enfants ou plus)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Revenus -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-money-bill-wave me-2"></i>
                                    Revenus déclarés
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Salaires et traitements (FCFA)</label>
                                <input type="number" name="revenus_salaire" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Autres revenus (pensions, loyers, etc.)</label>
                                <input type="number" name="revenus_autres" class="form-control" min="0">
                            </div>
                        </div>

                        <!-- Charges -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-receipt me-2"></i>
                                    Charges déductibles
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Charges familiales (FCFA)</label>
                                <input type="number" name="charges_familiales" class="form-control" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Charges professionnelles (FCFA)</label>
                                <input type="number" name="charges_professionnelles" class="form-control" min="0">
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-calculator me-2"></i>
                                    Calculer l'impôt
                                </button>
                                <button type="button" id="export-pdf" class="btn btn-danger btn-lg ms-2 d-none">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    Exporter en PDF
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Résultats du calcul -->
                    <div id="resultats" class="d-none">
                        <div class="alert alert-success">
                            <h5 class="alert-heading">
                                <i class="fas fa-chart-pie me-2"></i>
                                Résultats du calcul
                            </h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Revenu total :</strong> <span id="revenu-total"></span> FCFA</p>
                                    <p><strong>Total charges :</strong> <span id="total-charges"></span> FCFA</p>
                                    <p><strong>Revenu imposable :</strong> <span id="revenu-imposable"></span> FCFA</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Impôt brut :</strong> <span id="impot-brut"></span> FCFA</p>
                                    <p><strong>Nombre de parts :</strong> <span id="nombre-parts"></span></p>
                                    <p><strong>Impôt par part :</strong> <span id="impot-net" class="text-danger fw-bold"></span> FCFA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#impot-form').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('impot-revenu.calculer') }}",
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#revenu-total').text(response.revenu_total.toLocaleString('fr-FR'));
                $('#total-charges').text(response.total_charges.toLocaleString('fr-FR'));
                $('#revenu-imposable').text(response.revenu_imposable.toLocaleString('fr-FR'));
                $('#impot-brut').text(response.impot_brut.toLocaleString('fr-FR'));
                $('#nombre-parts').text(response.nombre_parts);
                $('#impot-net').text(response.impot_net.toLocaleString('fr-FR'));

                $('#resultats').removeClass('d-none');
                $('#export-pdf').removeClass('d-none');

                // Scroll vers les résultats
                $('#resultats')[0].scrollIntoView({ behavior: 'smooth' });
            },
            error: function(xhr) {
                alert('Erreur lors du calcul : ' + (xhr.responseJSON?.message || 'Veuillez vérifier vos données'));
            }
        });
    });

    $('#export-pdf').click(function() {
        const data = $('#impot-form').serialize();

        // Ouvrir dans une nouvelle fenêtre pour l'impression
        const url = "{{ route('impot-revenu.export-pdf') }}";
        const form = $('<form>', {
            method: 'POST',
            action: url,
            target: '_blank'
        });

        // Ajouter les données du formulaire
        const formData = $('#impot-form').serializeArray();
        formData.forEach(function(field) {
            form.append($('<input>', {
                type: 'hidden',
                name: field.name,
                value: field.value
            }));
        });

        // Ajouter le token CSRF
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: $('meta[name="csrf-token"]').attr('content')
        }));

        $('body').append(form);
        form.submit();
        form.remove();
    });
});
</script>

<style>
.form-label {
    font-weight: 600;
    color: #495057;
}

.card-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.btn-primary {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1e7e34 0%, #28a745 100%);
}

.alert-success {
    border-left: 4px solid #28a745;
}

.text-primary {
    color: #28a745 !important;
}
</style>
@endsection
