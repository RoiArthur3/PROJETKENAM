<!-- Section Diagramme de Gantt (Mermaid) -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="fas fa-project-diagram me-2"></i>Missions Logistique
        </h6>
    </div>
    <div class="card-body">
        <div class="mermaid">
            gantt
                title Gestion des Missions Logistique
                dateFormat  YYYY-MM-DD
                section Missions
                Mission 1 :a1, 2026-03-01, 5d
                Mission 2 :a2, 2026-03-06, 3d
                Mission 3 :a3, 2026-03-10, 7d
                section Pointages Cost Control
                Pointage Mission 1 :a1, 2026-03-01, 5d
                Pointage Mission 2 :a2, 2026-03-06, 3d
                Pointage Mission 3 :a3, 2026-03-10, 7d
                section Livraison
                Livraison Mission 1 :a1, 2026-03-05, 1d
                Livraison Mission 2 :a2, 2026-03-09, 1d
                Livraison Mission 3 :a3, 2026-03-17, 1d
        </div>
        <small class="text-muted">Ce diagramme est un exemple. Pour le brancher à la base de données, générez dynamiquement les missions, pointages et livraisons.</small>
    </div>
</div>
<!-- Inclure Mermaid.js -->
<script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
<script>mermaid.initialize({ startOnLoad:true });</script>
