<template>
  <div class="dashboard-page">
    <div class="user-greeting mb-4">
      <h3 class="fw-bold mb-1">Salut, {{ user?.name }} 👋</h3>
      <p class="text-muted small">Voici l'état de vos opérations aujourd'hui.</p>
    </div>

    <!-- Statistiques Rapides -->
    <div class="row g-3 mb-4">
      <div class="col-6">
        <div class="stat-card p-3 rounded-4 bg-white shadow-sm border-start border-4 border-warning">
          <div class="icon-circle bg-warning-light text-warning mb-2">
            <i class="fas fa-clock"></i>
          </div>
          <h4 class="fw-bold m-0">12</h4>
          <p class="text-muted xxs-text m-0">En Validation</p>
        </div>
      </div>
      <div class="col-6">
        <div class="stat-card p-3 rounded-4 bg-white shadow-sm border-start border-4 border-success">
          <div class="icon-circle bg-success-light text-success mb-2">
            <i class="fas fa-check-circle"></i>
          </div>
          <h4 class="fw-bold m-0">45</h4>
          <p class="text-muted xxs-text m-0">Approuvées</p>
        </div>
      </div>
    </div>

    <!-- Actions Rapides -->
    <h6 class="fw-bold mb-3">Actions Rapides</h6>
    <div class="action-grid d-flex gap-3 mb-4 overflow-auto pb-2">
      <router-link to="/operations/create" class="action-item p-3 rounded-4 bg-primary-gradient text-white text-center flex-shrink-0 shadow-sm">
        <i class="fas fa-plus-circle mb-2 d-block fa-lg"></i>
        <span class="small fw-bold">Nouvelle Op</span>
      </router-link>
      <div class="action-item p-3 rounded-4 bg-white text-dark text-center flex-shrink-0 shadow-sm border">
        <i class="fas fa-camera mb-2 d-block fa-lg text-primary"></i>
        <span class="small fw-bold">Scan Doc</span>
      </div>
      <div class="action-item p-3 rounded-4 bg-white text-dark text-center flex-shrink-0 shadow-sm border">
        <i class="fas fa-search mb-2 d-block fa-lg text-primary"></i>
        <span class="small fw-bold">Recherche</span>
      </div>
    </div>

    <!-- Dernières Opérations -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="fw-bold m-0">Opérations Récentes</h6>
      <router-link to="/operations" class="small text-primary text-decoration-none">Voir tout</router-link>
    </div>

    <div class="op-list">
      <div v-for="i in 3" :key="i" class="op-card p-3 bg-white rounded-4 shadow-sm mb-3 border">
        <div class="d-flex justify-content-between mb-2">
          <span class="badge bg-light text-dark border small">#OP-2024-00{{ i }}</span>
          <span class="badge bg-warning-light text-warning">En cours</span>
        </div>
        <h6 class="fw-bold mb-1">Livraison Matériel Kenam</h6>
        <p class="text-muted xxs-text mb-2"><i class="fas fa-map-marker-alt me-1"></i> Abidjan -> San Pedro</p>
        <div class="d-flex justify-content-between align-items-center">
          <div class="avatar-group d-flex">
            <div class="avatar-sm rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center border">JD</div>
            <div class="avatar-sm rounded-circle bg-info text-white d-flex align-items-center justify-content-center border ms-n2">+2</div>
          </div>
          <span class="fw-bold text-primary">250 000 FCFA</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const user = ref(JSON.parse(localStorage.getItem('user')))

onMounted(() => {
  // Charger les stats réelles ici
})
</script>

<style scoped>
.bg-primary-gradient {
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c00 100%);
}

.bg-warning-light { background-color: #fff8eb; }
.bg-success-light { background-color: #e6ffed; }

.stat-card h4 { font-size: 1.5rem; }
.xxs-text { font-size: 0.65rem; }

.icon-circle {
  width: 35px;
  height: 35px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}

.action-item {
  width: 100px;
  min-width: 100px;
}

.action-grid::-webkit-scrollbar {
  display: none;
}

.avatar-sm {
  width: 28px;
  height: 28px;
  font-size: 0.6rem;
  font-weight: bold;
}

.ms-n2 { margin-left: -10px !important; }

.op-card {
  transition: transform 0.2s;
}

.op-card:active {
  transform: scale(0.98);
}
</style>
