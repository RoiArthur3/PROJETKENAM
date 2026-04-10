@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Déclaration CNSS
                    </h4>
                </div>

                <div class="card-body">
                    <form id="cnss-form">
                        @csrf
                        <!-- Période de déclaration -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Période de déclaration
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mois</label>
                                <select name="periode" class="form-select" required>
                                    <option value="">Sélectionner un mois</option>
                                    <option value="janvier-2024">Janvier 2024</option>
                                    <option value="fevrier-2024">Février 2024</option>
                                    <option value="mars-2024">Mars 2024</option>
                                    <option value="avril-2024">Avril 2024</option>
                                    <option value="mai-2024">Mai 2024</option>
                                    <option value="juin-2024">Juin 2024</option>
                                    <option value="juillet-2024">Juillet 2024</option>
                                    <option value="aout-2024">Août 2024</option>
                                    <option value="septembre-2024">Septembre 2024</option>
                                    <option value="octobre-2024">Octobre 2024</option>
                                    <option value="novembre-2024">Novembre 2024</option>
                                    <option value="decembre-2024">Décembre 2024</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Entreprise</label>
                                <input type="text" class="form-control" value="KENAM SERVICES" readonly>
                            </div>
                        </div>

                        <!-- Salariés -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-users me-2"></i>
                                    Salariés
                                </h5>
                            </div>
                            <div class="col-12">
                                <div id="salaries-container">
                                    <!-- Salarié 1 (par défaut) -->
                                    <div class="salarie-item border rounded p-3 mb-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Nom complet</label>
                                                <input type="text" name="salaries[0][nom]" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Matricule</label>
                                                <input type="text" name="salaries[0][matricule]" class="form-control" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Catégorie</label>
                                                <select name="salaries[0][categorie]" class="form-select" required>
                                                    <option value="A">Catégorie A</option>
                                                    <option value="B">Catégorie B</option>
                                                    <option value="C">Catégorie C</option>
                                                    <option value="D">Catégorie D</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Salaire brut (FCFA)</label>
                                                <input type="number" name="salaries[0][salaire_brut]" class="form-control salaire-brut" min="0" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">&nbsp;</label><br>
                                                <button type="button" class="btn btn-danger btn-sm remove-salarie">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="add-salarie" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>
                                    Ajouter un salarié
                                </button>
                            </div>
                        </div>

                        <!-- Taux CNSS -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary">
                                    <i class="fas fa-percentage me-2"></i>
                                    Taux de cotisation CNSS
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Part employeur</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">Prestations familiales:</div>
                                            <div class="col-6 text-end">5.5%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">Accidents de travail:</div>
                                            <div class="col-6 text-end">2.0%</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">Retraite:</div>
                                            <div class="col-6 text-end">7.5%</div>
                                        </div>
                                        <hr>
                                        <div class="row fw-bold">
                                            <div class="col-6">Total employeur:</div>
                                            <div class="col-6 text-end">15.0%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Part employé</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">Retraite:</div>
                                            <div class="col-6 text-end">5.5%</div>
                                        </div>
                                        <hr>
                                        <div class="row fw-bold">
                                            <div class="col-6">Total employé:</div>
                                            <div class="col-6 text-end">5.5%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-calculator me-2"></i>
                                    Calculer les cotisations
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
                                Résultats du calcul - Période: <span id="periode-resultat"></span>
                            </h5>
                            <hr>

                            <!-- Tableau des salariés -->
                            <div class="table-responsive mb-3">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Matricule</th>
                                            <th>Salaire brut</th>
                                            <th>Base calcul</th>
                                            <th>Cotisation employeur</th>
                                            <th>Cotisation employé</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultats-salaries">
                                        <!-- Rempli par JavaScript -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Totaux -->
                            <div class="row">
                                <div class="col-md-3">
                                    <p><strong>Total salaires bruts:</strong></p>
                                    <p class="text-primary">{{ number_format(0, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Total cotisation employeur:</strong></p>
                                    <p class="text-warning">{{ number_format(0, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Total cotisation employé:</strong></p>
                                    <p class="text-info">{{ number_format(0, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>Total à verser:</strong></p>
                                    <p class="text-danger fw-bold">{{ number_format(0, 0, ',', ' ') }} FCFA</p>
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
let salarieIndex = 1;

$(document).ready(function() {
    // Ajouter un salarié
    $('#add-salarie').click(function() {
        const newSalarie = `
            <div class="salarie-item border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Nom complet</label>
                        <input type="text" name="salaries[${salarieIndex}][nom]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Matricule</label>
                        <input type="text" name="salaries[${salarieIndex}][matricule]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Catégorie</label>
                        <select name="salaries[${salarieIndex}][categorie]" class="form-select" required>
                            <option value="A">Catégorie A</option>
                            <option value="B">Catégorie B</option>
                            <option value="C">Catégorie C</option>
                            <option value="D">Catégorie D</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Salaire brut (FCFA)</label>
                        <input type="number" name="salaries[${salarieIndex}][salaire_brut]" class="form-control salaire-brut" min="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label><br>
                        <button type="button" class="btn btn-danger btn-sm remove-salarie">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#salaries-container').append(newSalarie);
        salarieIndex++;
    });

    // Supprimer un salarié
    $(document).on('click', '.remove-salarie', function() {
        if($('.salarie-item').length > 1) {
            $(this).closest('.salarie-item').remove();
        } else {
            alert('Vous devez conserver au moins un salarié');
        }
    });

    // Calculer les cotisations
    $('#cnss-form').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('cnss.calculer') }}",
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Afficher les résultats
                $('#periode-resultat').text(response.periode);

                // Vider et remplir le tableau
                $('#resultats-salaries').empty();
                response.resultats.forEach(function(resultat) {
                    const row = `
                        <tr>
                            <td>${resultat.salarie.nom}</td>
                            <td>${resultat.salarie.matricule}</td>
                            <td>${resultat.salarie.salaire_brut.toLocaleString('fr-FR')} FCFA</td>
                            <td>${resultat.base_calcul.toLocaleString('fr-FR')} FCFA</td>
                            <td>${resultat.cotisations.employeur.total.toLocaleString('fr-FR')} FCFA</td>
                            <td>${resultat.cotisations.employe.total.toLocaleString('fr-FR')} FCFA</td>
                            <td class="fw-bold">${resultat.cotisation_global.toLocaleString('fr-FR')} FCFA</td>
                        </tr>
                    `;
                    $('#resultats-salaries').append(row);
                });

                // Mettre à jour les totaux
                const totauxHtml = `
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>Total salaires bruts:</strong></p>
                            <p class="text-primary">${response.totaux.salaire_brut.toLocaleString('fr-FR')} FCFA</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Total cotisation employeur:</strong></p>
                            <p class="text-warning">${response.totaux.cotisation_employeur.toLocaleString('fr-FR')} FCFA</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Total cotisation employé:</strong></p>
                            <p class="text-info">${response.totaux.cotisation_employe.toLocaleString('fr-FR')} FCFA</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Total à verser:</strong></p>
                            <p class="text-danger fw-bold">${response.totaux.cotisation_global.toLocaleString('fr-FR')} FCFA</p>
                        </div>
                    </div>
                `;

                $('#resultats .row:last').html(totauxHtml);
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

    // Export PDF
    $('#export-pdf').click(function() {
        const url = "{{ route('cnss.export-pdf') }}";
        const form = $('<form>', {
            method: 'POST',
            action: url,
            target: '_blank'
        });

        // Ajouter les données du formulaire
        const formData = $('#cnss-form').serializeArray();
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

.salarie-item {
    background-color: #f8f9fa;
    border-color: #dee2e6 !important;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>
@endsection
