<template>
  <div class="operations-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold m-0">Opérations</h3>
      <div class="filter-badge bg-white shadow-sm px-3 py-2 rounded-pill border">
        <i class="fas fa-filter text-primary small me-2"></i>
        <span class="small fw-bold">Filtres</span>
      </div>
    </div>

    <!-- Search Bar -->
    <div class="search-container mb-4">
      <div class="input-group bg-white rounded-pill shadow-sm px-3">
        <span class="input-group-text border-0 bg-transparent text-muted"><i class="fas fa-search"></i></span>
        <input v-model="search" type="text" class="form-control border-0 bg-transparent py-2" placeholder="Rechercher une opération...">
      </div>
    </div>

    <!-- List -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <div v-else class="op-list">
      <div v-for="op in filteredOperations" :key="op.id" class="op-card p-3 bg-white rounded-4 shadow-sm mb-3 border">
        <div class="d-flex justify-content-between mb-2">
          <span class="badge bg-light text-dark border xxs-text">#{{ op.numero_ordre || 'OP-'+op.id }}</span>
          <span :class="getStatusBadgeClass(op.statut_courant)">{{ formatStatus(op.statut_courant) }}</span>
        </div>
        <h6 class="fw-bold mb-1">{{ op.objet }}</h6>
        <div class="d-flex align-items-center text-muted xxs-text mb-3">
          <i class="fas fa-calendar-alt me-1"></i> {{ formatDate(op.date_operation) }}
          <span class="mx-2">•</span>
          <i class="fas fa-map-marker-alt me-1"></i> {{ op.lieu_depart || 'N/A' }}
        </div>
        
        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
          <div class="small fw-bold text-primary">{{ formatCurrency(op.montant) }}</div>
          <button @click="viewDetails(op.id)" class="btn btn-primary-light btn-sm rounded-pill px-3 fw-bold">Détails</button>
        </div>
      </div>

      <div v-if="filteredOperations.length === 0" class="text-center py-5">
        <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
        <p class="text-muted">Aucune opération trouvée.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const operations = ref([])
const loading = ref(true)
const search = ref('')

const fetchOperations = async () => {
  try {
    const response = await axios.get('/operations')
    operations.value = response.data.data // Laravel Paginate
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const filteredOperations = computed(() => {
  if (!search.value) return operations.value
  return operations.value.filter(op => 
    op.objet.toLowerCase().includes(search.value.toLowerCase()) ||
    op.id.toString().includes(search.value)
  )
})

const getStatusBadgeClass = (status) => {
  const classes = {
    'en_cours': 'badge bg-info-light text-info',
    'pending_validation': 'badge bg-warning-light text-warning',
    'en_validation': 'badge bg-warning-light text-warning',
    'approuvee': 'badge bg-success-light text-success',
    'rejetee': 'badge bg-danger-light text-danger',
  }
  return classes[status] || 'badge bg-secondary-light text-secondary'
}

const formatStatus = (status) => {
  const labels = {
    'en_cours': 'En cours',
    'pending_validation': 'En Validation',
    'en_validation': 'En Validation',
    'approuvee': 'Approuvée',
    'rejetee': 'Rejetée',
  }
  return labels[status] || status
}

const formatDate = (date) => new Date(date).toLocaleDateString('fr-FR')
const formatCurrency = (amount) => new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA'

onMounted(fetchOperations)
</script>

<style scoped>
.xxs-text { font-size: 0.65rem; }

.bg-info-light { background-color: #e3f2fd; }
.bg-warning-light { background-color: #fff8eb; }
.bg-success-light { background-color: #e6ffed; }
.bg-danger-light { background-color: #ffebee; }
.bg-secondary-light { background-color: #f5f5f5; }

.btn-primary-light {
  background-color: #fff0eb;
  color: #ff6b35;
  border: none;
}

.op-card {
  transition: all 0.2s;
}

.op-card:active {
  transform: scale(0.98);
}
</style>
