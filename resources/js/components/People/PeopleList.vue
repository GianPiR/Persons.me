<template>
  <div class="people-list-container">
    <div class="header">
      <div class="header-left">
        <button @click="goToDashboard" class="btn btn-secondary btn-back">
          ← Dashboard
        </button>
        <h1>👥 Gerenciamento de Pessoas</h1>
      </div>
      <button @click="addPerson" class="btn btn-primary">
        <span class="btn-icon">➕</span>
        Adicionar Pessoa
      </button>
    </div>

    <div v-if="loading" class="loading">
      <p>🔄 Carregando pessoas...</p>
    </div>

    <div v-else-if="error" class="error-message">
      ❌ Error: {{ error }}
      <button @click="fetchPeople" class="btn btn-secondary">🔄 Tentar Novamente</button>
    </div>

    <div v-else-if="people.length === 0" class="empty-state">
      <h3>📭 Nenhuma pessoa cadastrada</h3>
      <p>Clique em "Adicionar Pessoa" para começar</p>
      <button @click="addPerson" class="btn btn-primary">
        <span class="btn-icon">➕</span>
        Adicionar Primeira Pessoa
      </button>
    </div>

    <div v-else class="table-container">
      <div class="table-header">
        <h3>📋 Lista de Pessoas ({{ people.length }})</h3>
        <div class="search-box">
          <input
            v-model="searchQuery"
            placeholder="🔍 Buscar por nome, CPF ou email..."
            class="search-input"
          >
        </div>
      </div>

      <table class="people-table">
        <thead>
          <tr>
            <th>🆔 ID</th>
            <th>👤 Nome</th>
            <th>⚙️ Ações</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="person in filteredPeople" :key="person.id">
            <!-- Linha principal da pessoa -->
            <tr class="person-row" :class="{ 'expanded': expandedPersonId === person.id }">
              <td>{{ person.id }}</td>
              <td class="name-cell">
                <strong>{{ person.name }}</strong>
              </td>
              <td class="actions-cell">
                <button @click="toggleViewPerson(person)" class="btn btn-info btn-sm" :title="expandedPersonId === person.id ? 'Fechar' : 'Visualizar'">
                  {{ expandedPersonId === person.id ? '⬆️' : '👁️' }}
                </button>
                <button @click="editPerson(person)" class="btn btn-warning btn-sm" title="Editar">
                  ✏️
                </button>
                <button @click="confirmDelete(person)" class="btn btn-danger btn-sm" title="Excluir">
                  🗑️
                </button>
              </td>
            </tr>

            <!-- Linha expandida com detalhes -->
            <tr v-if="expandedPersonId === person.id" class="person-details-row">
              <td colspan="3" class="person-details-cell">
                <div class="person-details-container">
                  <div class="details-header">
                    <h4>📋 Detalhes de {{ person.name }}</h4>
                    <button @click="closeDetails" class="btn-close">❌</button>
                  </div>
                  <div class="details-grid">
                    <div class="detail-card">
                      <div class="detail-label">🆔 ID</div>
                      <div class="detail-value">{{ person.id }}</div>
                    </div>
                    <div class="detail-card">
                      <div class="detail-label">👤 Nome Completo</div>
                      <div class="detail-value">{{ person.name }}</div>
                    </div>
                    <div class="detail-card">
                      <div class="detail-label">📄 {{ person.type === 'individual' ? 'CPF' : 'CNPJ' }}</div>
                      <div class="detail-value">{{ person.cpf }}</div>
                    </div>
                    <div class="detail-card">
                      <div class="detail-label">🏷️ Tipo</div>
                      <div class="detail-value">
                        <span :class="getTypeClass(person.type)">
                          {{ getTypeLabel(person.type) }}
                        </span>
                      </div>
                    </div>
                    <div class="detail-card">
                      <div class="detail-label">📞 Telefone</div>
                      <div class="detail-value">{{ person.phone || 'Não informado' }}</div>
                    </div>
                    <div class="detail-card">
                      <div class="detail-label">📧 Email</div>
                      <div class="detail-value">{{ person.email || 'Não informado' }}</div>
                    </div>
                    <div class="detail-card" v-if="person.created_at">
                      <div class="detail-label">📅 Cadastrado em</div>
                      <div class="detail-value">{{ formatDate(person.created_at) }}</div>
                    </div>
                    <div class="detail-card" v-if="person.updated_at">
                      <div class="detail-label">🔄 Última atualização</div>
                      <div class="detail-value">{{ formatDate(person.updated_at) }}</div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Modal de Confirmação de Exclusão SIMPLIFICADO -->
    <div v-if="showDeleteModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; display: flex; align-items: center; justify-content: center;">
      <div style="background: white; padding: 30px; border-radius: 10px; min-width: 300px; text-align: center;">
        <h3 style="margin-bottom: 20px; color: #333;">⚠️ Confirmar Exclusão</h3>
        <p style="margin-bottom: 10px;">Tem certeza que deseja excluir <strong>{{ personToDelete && personToDelete.name }}</strong>?</p>
        <p style="color: #dc3545; margin-bottom: 30px; font-size: 14px;">❗ Esta ação não pode ser desfeita.</p>
        <div style="display: flex; gap: 15px; justify-content: center;">
          <button @click="cancelDelete" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
            ❌ Cancelar
          </button>
          <button @click="deletePerson" :disabled="deleting" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
            {{ deleting ? '🔄 Excluindo...' : '🗑️ Excluir' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'PeopleList',
  setup() {
    const router = useRouter();

    // Reactive data
    const people = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const searchQuery = ref('');
    const showDeleteModal = ref(false);
    const personToDelete = ref(null);
    const deleting = ref(false);
    const expandedPersonId = ref(null);

    // Computed properties
    const filteredPeople = computed(() => {
      if (!searchQuery.value) {
        return people.value;
      }

      const query = searchQuery.value.toLowerCase();
      return people.value.filter(person =>
        person.name.toLowerCase().includes(query) ||
        person.cpf.toLowerCase().includes(query) ||
        (person.email && person.email.toLowerCase().includes(query))
      );
    });
    // Methods
    const fetchPeople = async () => {
      loading.value = true;
      error.value = null;

      try {
        const response = await axios.get('/api/people');

        if (response.data.status === 'success') {
          people.value = response.data.data || [];
        } else {
          error.value = response.data.message || 'Erro ao carregar pessoas';
        }
      } catch (err) {
        console.error('Erro ao buscar pessoas:', err);
        error.value = 'Erro de conexão. Verifique sua internet.';
      } finally {
        loading.value = false;
      }
    };

    const addPerson = () => {
      router.push('/people/new');
    };

    const editPerson = (person) => {
      router.push(`/people/edit/${person.id}`);
    };

    const deletePerson = async () => {
      if (!personToDelete.value) return;

      deleting.value = true;
      try {
        const response = await axios.delete(`/api/people/${personToDelete.value.id}`);

        if (response.data.status === 'success') {
          people.value = people.value.filter(p => p.id !== personToDelete.value.id);
          showDeleteModal.value = false;
          personToDelete.value = null;
        } else {
          error.value = response.data.message || 'Erro ao excluir pessoa';
        }
      } catch (err) {
        console.error('Erro ao excluir pessoa:', err);
        error.value = 'Erro ao excluir pessoa. Tente novamente.';
      } finally {
        deleting.value = false;
      }
    };

    const confirmDelete = (person) => {
      personToDelete.value = person;
      showDeleteModal.value = true;
    };

    const cancelDelete = () => {
      showDeleteModal.value = false;
      personToDelete.value = null;
    };

    const toggleViewPerson = (person) => {
      if (expandedPersonId.value === person.id) {
        expandedPersonId.value = null;
      } else {
        expandedPersonId.value = person.id;
      }
    };

    const closeDetails = () => {
      expandedPersonId.value = null;
    };

    const getTypeLabel = (type) => {
      return type === 'individual' ? '👤 Pessoa Física' : '🏢 Pessoa Jurídica';
    };

    const getTypeClass = (type) => {
      return type === 'individual' ? 'badge badge-primary' : 'badge badge-secondary';
    };

    const goToDashboard = () => {
      router.push('/dashboard');
    };

    const formatDate = (dateString) => {
      if (!dateString) return '-';
      try {
        return new Date(dateString).toLocaleDateString('pt-BR');
      } catch (error) {
        return dateString;
      }
    };

    // Lifecycle
    onMounted(() => {
      fetchPeople();
    });

    return {
      // Reactive data
      people,
      loading,
      error,
      searchQuery,
      showDeleteModal,
      personToDelete,
      deleting,
      expandedPersonId,
      // Computed
      filteredPeople,
      // Methods
      fetchPeople,
      addPerson,
      editPerson,
      deletePerson,
      confirmDelete,
      cancelDelete,
      toggleViewPerson,
      closeDetails,
      getTypeLabel,
      getTypeClass,
      goToDashboard,
      formatDate
    };
  }
};
</script>

<style scoped>
.people-list-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 2px solid #f8f9fa;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.btn-back {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
}

h1 {
  margin: 0;
  color: #333;
  font-size: 24px;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}

.error-message {
  background: #f8d7da;
  color: #721c24;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: #f8f9fa;
  border-radius: 10px;
  color: #666;
}

.table-container {
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  overflow: hidden;
}

.table-header {
  padding: 20px;
  background: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.search-box {
  flex: 1;
  max-width: 300px;
  margin-left: 20px;
}

.search-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 14px;
}

.people-table {
  width: 100%;
  border-collapse: collapse;
}

.people-table th,
.people-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.people-table th {
  background: #f8f9fa;
  font-weight: 600;
  color: #555;
}

.people-table th:last-child {
  text-align: center;
}

.person-row:hover {
  background: #f8f9fa;
}

.person-row.expanded {
  background: #e3f2fd;
}

.name-cell strong {
  color: #333;
}

.actions-cell {
  white-space: nowrap;
  text-align: center;
  width: 150px;
}

.btn {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  margin-right: 5px;
  text-decoration: none;
  display: inline-block;
}

.btn-sm {
  padding: 4px 8px;
  font-size: 11px;
  margin: 0 2px;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-info {
  background: #17a2b8;
  color: white;
}

.btn-warning {
  background: #ffc107;
  color: #212529;
}

.btn-danger {
  background: #dc3545;
  color: white;
}

.btn:hover {
  opacity: 0.8;
}

.btn-icon {
  margin-right: 8px;
  font-size: 14px;
}

.person-details-row {
  background: #f8f9ff;
}

.person-details-cell {
  padding: 0;
}

.person-details-container {
  padding: 20px;
  border-left: 4px solid #007bff;
}

.details-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.details-header h4 {
  margin: 0;
  color: #333;
}

.btn-close {
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 50%;
  padding: 6px;
  cursor: pointer;
  font-size: 12px;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 15px;
}

.detail-card {
  background: white;
  padding: 15px;
  border-radius: 8px;
  border: 1px solid #e3e6f0;
}

.detail-label {
  font-size: 12px;
  color: #666;
  margin-bottom: 5px;
  font-weight: 600;
  text-transform: uppercase;
}

.detail-value {
  font-size: 14px;
  color: #333;
  font-weight: 500;
}

.badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
}

.badge-primary {
  background: #007bff;
  color: white;
}

.badge-secondary {
  background: #6c757d;
  color: white;
}

@media (max-width: 768px) {
  .header {
    flex-direction: column;
    gap: 15px;
    align-items: stretch;
  }

  .table-header {
    flex-direction: column;
    gap: 15px;
    align-items: stretch;
  }

  .search-box {
    margin-left: 0;
    max-width: none;
  }

  .people-table {
    font-size: 14px;
  }

  .people-table th,
  .people-table td {
    padding: 8px 6px;
  }

  .details-grid {
    grid-template-columns: 1fr;
  }
}
</style>
