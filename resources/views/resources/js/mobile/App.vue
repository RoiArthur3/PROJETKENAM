<template>
  <div class="mobile-wrapper">
    <!-- Barre de navigation (ne s'affiche pas sur Login) -->
    <nav v-if="!$route.meta.hideNav" class="mobile-nav shadow-sm">
      <div class="nav-content d-flex justify-content-between align-items-center px-3">
        <h5 class="m-0 text-white fw-bold">KENAM OPS</h5>
        <div class="nav-icons d-flex gap-3">
          <i class="fas fa-bell text-white"></i>
          <i @click="logout" class="fas fa-sign-out-alt text-white cursor-pointer"></i>
        </div>
      </div>
    </nav>

    <!-- Contenu Principal -->
    <main class="mobile-main">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Menu du bas (Tab Bar) -->
    <footer v-if="!$route.meta.hideNav" class="mobile-footer shadow-lg">
      <div class="tab-bar d-flex justify-content-around align-items-center">
        <router-link to="/" class="tab-item" active-class="active">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </router-link>
        <router-link to="/operations" class="tab-item" active-class="active">
          <i class="fas fa-tasks"></i>
          <span>Ops</span>
        </router-link>
        <router-link to="/operations/create" class="tab-item plus-item">
          <div class="plus-circle shadow">
            <i class="fas fa-plus"></i>
          </div>
        </router-link>
        <router-link to="/tracking" class="tab-item" active-class="active">
          <i class="fas fa-map-marker-alt"></i>
          <span>Suivi</span>
        </router-link>
        <router-link to="/profile" class="tab-item" active-class="active">
          <i class="fas fa-user"></i>
          <span>Profil</span>
        </router-link>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const logout = async () => {
  if (confirm('Voulez-vous vous déconnecter ?')) {
    try {
      await axios.post('/mobile/logout')
    } catch (e) {
      console.error(e)
    } finally {
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
      router.push('/login')
    }
  }
}
</script>

<style>
:root {
  --primary-orange: #ff6b35;
  --secondary-orange: #f7c59f;
  --dark-bg: #1a1a1a;
}

body {
  background-color: #f8f9fa;
  font-family: 'Inter', sans-serif;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

.mobile-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.mobile-nav {
  height: 60px;
  background: linear-gradient(135deg, #ff6b35 0%, #ff8c00 100%);
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
  display: flex;
  align-items: center;
}

.mobile-main {
  flex: 1;
  padding: 80px 15px 100px 15px; /* Compense nav et footer */
}

.mobile-footer {
  height: 70px;
  background: white;
  position: fixed;
  bottom: 0;
  width: 100%;
  z-index: 1000;
  border-top-left-radius: 20px;
  border-top-right-radius: 20px;
}

.tab-bar {
  height: 100%;
  padding: 0 10px;
}

.tab-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-decoration: none;
  color: #6c757d;
  font-size: 0.7rem;
  transition: all 0.3s ease;
}

.tab-item i {
  font-size: 1.3rem;
  margin-bottom: 4px;
}

.tab-item.active {
  color: var(--primary-orange);
}

.plus-item {
  position: relative;
  top: -25px;
}

.plus-circle {
  width: 55px;
  height: 55px;
  background: var(--primary-orange);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
  border: 5px solid white;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
