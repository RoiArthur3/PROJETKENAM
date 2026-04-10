<template>
  <div class="create-op-page">
    <div class="d-flex align-items-center mb-4">
      <router-link to="/" class="btn btn-light rounded-circle me-3 shadow-sm">
        <i class="fas fa-arrow-left"></i>
      </router-link>
      <h3 class="fw-bold m-0">Nouvelle Opération</h3>
    </div>

    <form @submit.prevent="handleSubmit" class="pb-5">
      <!-- Section Informations de base -->
      <div class="card p-3 rounded-4 shadow-sm border-0 mb-4">
        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i>Informations Générales</h6>
        
        <div class="mb-3">
          <label class="form-label small fw-bold">Objet de l'opération</label>
          <input v-model="form.objet" type="text" class="form-control bg-light border-0 py-2" placeholder="Ex: Livraison de carburant..." required>
        </div>

        <div class="mb-3">
          <label class="form-label small fw-bold">Type d'opération</label>
          <select v-model="form.type_operation_id" class="form-select bg-light border-0 py-2" required>
            <option value="">Sélectionner un type</option>
            <option v-for="type in operationTypes" :key="type.id" :value="type.id">{{ type.nom }}</option>
          </select>
        </div>

        <div class="mb-0">
          <label class="form-label small fw-bold">Montant estimé (FCFA)</label>
          <input v-model="form.montant" type="number" class="form-control bg-light border-0 py-2" placeholder="0" required>
        </div>
      </div>

      <!-- Logistique -->
      <div class="card p-3 rounded-4 shadow-sm border-0 mb-4">
        <h6 class="fw-bold mb-3"><i class="fas fa-truck text-primary me-2"></i>Logistique & Trajet</h6>
        
        <div class="mb-3">
          <label class="form-label small fw-bold">Départ</label>
          <input v-model="form.lieu_depart" type="text" class="form-control bg-light border-0 py-2" placeholder="Ville ou site de départ">
        </div>

        <div class="mb-0">
          <label class="form-label small fw-bold">Destination</label>
          <input v-model="form.lieu_destination" type="text" class="form-control bg-light border-0 py-2" placeholder="Ville ou site d'arrivée">
        </div>
      </div>

      <!-- Validateurs -->
      <div class="card p-3 rounded-4 shadow-sm border-0 mb-4">
        <h6 class="fw-bold mb-3"><i class="fas fa-user-check text-primary me-2"></i>Circuit de Validation</h6>
        
        <div class="mb-3">
          <label class="form-label small fw-bold">Validateur Principal (Service)</label>
          <select v-model="form.destinataire_principal_id" class="form-select bg-light border-0 py-2" required>
            <option value="">Choisir un service</option>
            <option v-for="service in services" :key="service.id" :value="service.id">{{ service.nom }}</option>
          </select>
        </div>
      </div>

      <!-- Bouton Fixe en bas (si possible) ou en fin de page -->
      <div class="mt-4">
        <button type="submit" class="btn btn-primary-gradient w-100 py-3 rounded-4 fw-bold shadow border-0" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          Soumettre l'Opération
        </button>
      </div>
    </form>

    <!-- Overlay de succès -->
    <div v-if="success" class="success-overlay d-flex flex-column align-items-center justify-content-center px-4">
      <div class="success-icon mb-4">
        <i class="fas fa-check"></i>
      </div>
      <h3 class="fw-bold text-white mb-2">Succès !</h3>
      <p class="text-white opacity-75 text-center mb-5">L'opération a été créée et soumise au circuit de validation.</p>
      <button @click="$router.push('/operations')" class="btn btn-white w-100 py-3 rounded-4 fw-bold">Continuer</button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)
const success = ref(false)
const services = ref([])
const operationTypes = ref([])

const form = reactive({
  objet: '',
  type_operation_id: '',
  montant: '',
  lieu_depart: '',
  lieu_destination: '',
  destinataire_principal_id: '',
  date_operation: new Date().toISOString().split('T')[0]
})

const fetchData = async () => {
    try {
        const [servicesRes, typesRes] = await Promise.all([
            axios.get('/services'), // Ajuster selon vos routes API
            axios.get('/settings/types-operations')
        ])
        services.value = servicesRes.data
        operationTypes.value = typesRes.data
    } catch (e) {
        console.error("Erreur chargement données", e)
    }
}

const handleSubmit = async () => {
  loading.value = true
  try {
    await axios.post('/operations', form)
    success.value = true
  } catch (e) {
    alert("Erreur lors de la création : " + (e.response?.data?.message || e.message))
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)
</script>

<style scoped>
.btn-primary-gradient {
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c00 100%);
  color: white;
}

.success-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c00 100%);
  z-index: 2000;
}

.success-icon {
  width: 100px;
  height: 100px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ff6b35;
  font-size: 3rem;
  animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.btn-white {
  background: white;
  color: #ff6b35;
}

@keyframes bounceIn {
  from { opacity: 0; transform: scale(0.3); }
  to { opacity: 1; transform: scale(1); }
}
</style>
