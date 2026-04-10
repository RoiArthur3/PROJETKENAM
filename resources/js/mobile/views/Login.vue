<template>
  <div class="login-container d-flex flex-column justify-content-center align-items-center p-4">
    <div class="brand-section text-center mb-5">
      <div class="logo-circle mb-3 mx-auto shadow">
        <i class="fas fa-truck-loading"></i>
      </div>
      <h2 class="fw-bold text-dark">KENAM OPS</h2>
      <p class="text-muted">Logistique & Opérations Terrain</p>
    </div>

    <div class="login-card p-4 shadow-sm w-100 bg-white rounded-4">
      <h4 class="mb-4 fw-bold">Connexion</h4>
      
      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label class="form-label small fw-bold">N° Téléphone</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-0"><i class="fas fa-phone"></i></span>
            <input 
              v-model="form.telephone" 
              type="text" 
              class="form-control bg-light border-0 px-3" 
              placeholder="Ex: 0707..."
              required
            >
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label small fw-bold">Mot de passe</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-0"><i class="fas fa-lock"></i></span>
            <input 
              v-model="form.password" 
              type="password" 
              class="form-control bg-light border-0 px-3" 
              placeholder="••••••••"
              required
            >
          </div>
        </div>

        <div v-if="error" class="alert alert-danger py-2 small border-0 mb-4">
          <i class="fas fa-exclamation-circle me-2"></i> {{ error }}
        </div>

        <button 
          type="submit" 
          class="btn btn-primary w-100 py-3 rounded-3 fw-bold border-0 shadow-sm"
          :disabled="loading"
        >
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          Se Connecter
        </button>
      </form>
    </div>

    <p class="mt-5 text-muted small">© 2026 KENAM SERVICES - Version 1.0.0</p>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const loading = ref(false)
const error = ref('')
const form = reactive({
  telephone: '',
  password: '',
  device_name: 'mobile_pwa'
})

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await axios.post('/mobile/login', form)
    const { token, user } = response.data
    
    localStorage.setItem('auth_token', token)
    localStorage.setItem('user', JSON.stringify(user))
    
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    
    router.push('/')
  } catch (e) {
    error.value = e.response?.data?.message || 'Erreur de connexion'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  background-color: #f8f9fa;
}

.logo-circle {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c00 100%);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2.5rem;
}

.login-card {
  max-width: 400px;
}

.btn-primary {
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c00 100%);
}

.input-group-text {
  color: #ff6b35;
}

.form-control:focus {
  box-shadow: none;
  background-color: #fff !important;
  border: 1px solid #ff6b35 !important;
}
</style>
