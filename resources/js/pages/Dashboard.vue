<template>
  <div class="dashboard">
    <div class="dashboard-header">
      <div class="header-content">
        <h1>🏠 Dashboard</h1>
        <div class="user-info">
          <span>Bem-vindo, {{ userName }}!</span>
          <button @click="logout" class="btn btn-secondary">Sair</button>
        </div>
      </div>
    </div>

    <div class="dashboard-content">
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">👥</div>
          <div class="stat-info">
            <h3>{{ totalPeople }}</h3>
            <p>Total de Pessoas</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">👤</div>
          <div class="stat-info">
            <h3>{{ individualCount }}</h3>
            <p>Pessoas Físicas</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">🏢</div>
          <div class="stat-info">
            <h3>{{ legalCount }}</h3>
            <p>Pessoas Jurídicas</p>
          </div>
        </div>
      </div>

      <div class="actions-section">
        <h2>🚀 Ações Rápidas</h2>
        <div class="action-buttons">
          <router-link to="/people" class="action-btn primary">
            <span class="btn-icon">👥</span>
            <div class="btn-text">
              <strong>Gerenciar Pessoas</strong>
              <small>Ver, editar e excluir pessoas</small>
            </div>
          </router-link>

          <router-link to="/people/new" class="action-btn secondary">
            <span class="btn-icon">➕</span>
            <div class="btn-text">
              <strong>Adicionar Pessoa</strong>
              <small>Cadastrar nova pessoa física ou jurídica</small>
            </div>
          </router-link>
        </div>
      </div>

      <div class="recent-section" v-if="recentPeople.length > 0">
        <h2>📋 Pessoas Recentes</h2>
        <div class="recent-list">
          <div
            v-for="person in recentPeople"
            :key="person.id"
            class="recent-item"
            @click="viewPerson(person)"
          >
            <div class="person-avatar">
              {{ person.name.charAt(0).toUpperCase() }}
            </div>
            <div class="person-info">
              <strong>{{ person.name }}</strong>
              <span class="person-type">{{ getTypeLabel(person.type) }}</span>
            </div>
            <div class="person-date">
              {{ formatDate(person.created_at) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      totalPeople: 0,
      individualCount: 0,
      legalCount: 0,
      recentPeople: [],
      userName: 'Usuário',
      loading: false
    };
  },
  methods: {
    async loadDashboardData() {
      this.loading = true;

      try {
        const response = await axios.get('/api/people?order_by=created_at&order_direction=desc');

        if (response.data.status === 'success') {
          const people = response.data.data;

          this.totalPeople = people.length;
          this.individualCount = people.filter(p => p.type === 'individual').length;
          this.legalCount = people.filter(p => p.type === 'legal_entity').length;

          // Pegar as 5 pessoas mais recentes
          this.recentPeople = people.slice(0, 5);
        }
      } catch (error) {
        console.error('Erro ao carregar dados do dashboard:', error);
      } finally {
        this.loading = false;
      }
    },

    logout() {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      this.$router.push('/login');
    },

    viewPerson(person) {
      this.$router.push(`/people/edit/${person.id}`);
    },

    getTypeLabel(type) {
      return type === 'individual' ? 'Pessoa Física' : 'Pessoa Jurídica';
    },

    formatDate(dateString) {
      if (!dateString) return '';
      return new Date(dateString).toLocaleDateString('pt-BR');
    }
  },

  mounted() {
    // Verificar se está autenticado
    const token = localStorage.getItem('auth_token');
    const user = localStorage.getItem('user');

    if (!token) {
      this.$router.push('/login');
      return;
    }

    if (user) {
      try {
        const userData = JSON.parse(user);
        this.userName = userData.name || 'Usuário';
      } catch (error) {
        console.error('Erro ao parsear dados do usuário:', error);
      }
    }

    this.loadDashboardData();
  }
};
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f8f9fa;
}

.dashboard-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-content h1 {
  margin: 0;
  font-size: 28px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.dashboard-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 30px 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  border-radius: 10px;
  padding: 25px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-icon {
  font-size: 48px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-info h3 {
  margin: 0;
  font-size: 32px;
  color: #333;
}

.stat-info p {
  margin: 5px 0 0;
  color: #666;
  font-size: 14px;
}

.actions-section {
  margin-bottom: 40px;
}

.actions-section h2 {
  color: #333;
  margin-bottom: 20px;
  font-size: 24px;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.action-btn {
  background: white;
  border-radius: 10px;
  padding: 25px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  text-decoration: none;
  color: #333;
  display: flex;
  align-items: center;
  gap: 20px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
  text-decoration: none;
  color: #333;
}

.action-btn.primary {
  border-left: 5px solid #667eea;
}

.action-btn.secondary {
  border-left: 5px solid #28a745;
}

.btn-icon {
  font-size: 32px;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: #f8f9fa;
}

.btn-text strong {
  display: block;
  font-size: 18px;
  margin-bottom: 5px;
}

.btn-text small {
  color: #666;
  font-size: 14px;
}

.recent-section h2 {
  color: #333;
  margin-bottom: 20px;
  font-size: 24px;
}

.recent-list {
  background: white;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.recent-item {
  display: flex;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: background-color 0.2s;
}

.recent-item:hover {
  background: #f8f9fa;
}

.recent-item:last-child {
  border-bottom: none;
}

.person-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  margin-right: 15px;
}

.person-info {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.person-info strong {
  color: #333;
  margin-bottom: 2px;
}

.person-type {
  color: #666;
  font-size: 12px;
}

.person-date {
  color: #999;
  font-size: 12px;
}

.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  display: inline-block;
  transition: all 0.2s;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
  transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .action-buttons {
    grid-template-columns: 1fr;
  }

  .recent-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .person-avatar {
    margin-right: 0;
  }
}
</style>
