<template>
  <div class="people-form-container">
    <div class="form-header">
      <h2>{{ isEdit ? '✏️ Editar Pessoa' : '➕ Cadastrar Nova Pessoa' }}</h2>
      <button @click="goBack" class="btn btn-secondary">
        ← Voltar
      </button>
    </div>

    <div class="form-card">
      <form @submit.prevent="submitForm" class="people-form">
        <div class="form-row">
          <div class="form-group">
            <label for="name">Nome Completo *</label>
            <input 
              id="name"
              type="text" 
              v-model="person.name" 
              required 
              placeholder="Digite o nome completo"
              :disabled="loading"
              maxlength="255"
            />
            <div v-if="errors.name" class="field-error">{{ errors.name }}</div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="type">Tipo de Pessoa *</label>
            <select 
              id="type"
              v-model="person.type" 
              required 
              :disabled="loading"
              @change="onTypeChange"
            >
              <option value="">Selecione o tipo</option>
              <option value="individual">Pessoa Física</option>
              <option value="legal_entity">Pessoa Jurídica</option>
            </select>
            <div v-if="errors.type" class="field-error">{{ errors.type }}</div>
          </div>

          <div class="form-group">
            <label for="cpf">{{ cpfLabel }} *</label>
            <input 
              id="cpf"
              type="text" 
              v-model="person.cpf" 
              required 
              :placeholder="cpfPlaceholder"
              :disabled="loading"
              @input="formatCpf"
              maxlength="18"
            />
            <div v-if="errors.cpf" class="field-error">{{ errors.cpf }}</div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="phone">Telefone</label>
            <input 
              id="phone"
              type="text" 
              v-model="person.phone" 
              placeholder="(11) 99999-9999"
              :disabled="loading"
              @input="formatPhone"
              maxlength="15"
            />
            <div v-if="errors.phone" class="field-error">{{ errors.phone }}</div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input 
              id="email"
              type="email" 
              v-model="person.email" 
              placeholder="email@exemplo.com"
              :disabled="loading"
              maxlength="255"
            />
            <div v-if="errors.email" class="field-error">{{ errors.email }}</div>
          </div>
        </div>

        <div v-if="error" class="error-message">
          ❌ {{ error }}
        </div>

        <div v-if="success" class="success-message">
          ✅ {{ success }}
        </div>

        <div class="form-actions">
          <button type="button" @click="goBack" class="btn btn-secondary" :disabled="loading">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary" :disabled="loading || !isFormValid">
            {{ loading ? 'Salvando...' : (isEdit ? 'Atualizar' : 'Cadastrar') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PeopleForm',
  props: {
    personId: {
      type: [String, Number],
      default: null
    }
  },
  data() {
    return {
      person: {
        name: '',
        cpf: '',
        type: '',
        phone: '',
        email: ''
      },
      errors: {},
      loading: false,
      error: null,
      success: null,
      isEdit: false
    };
  },
  computed: {
    cpfLabel() {
      return this.person.type === 'legal_entity' ? 'CNPJ' : 'CPF';
    },
    cpfPlaceholder() {
      return this.person.type === 'legal_entity' ? '12.345.678/0001-90' : '123.456.789-00';
    },
    isFormValid() {
      return this.person.name && 
             this.person.cpf && 
             this.person.type &&
             this.isValidCpf();
    }
  },
  methods: {
    async loadPerson() {
      if (!this.personId) return;
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get(`/api/people/${this.personId}`);
        
        if (response.data.status === 'success') {
          this.person = { ...response.data.data };
          this.isEdit = true;
        } else {
          this.error = response.data.message || 'Erro ao carregar dados da pessoa';
        }
      } catch (error) {
        console.error('Erro ao carregar pessoa:', error);
        this.error = 'Erro ao carregar dados da pessoa';
      } finally {
        this.loading = false;
      }
    },
    
    async submitForm() {
      this.clearMessages();
      
      if (!this.validateForm()) {
        return;
      }
      
      this.loading = true;
      
      try {
        let response;
        
        if (this.isEdit) {
          response = await axios.put(`/api/people/${this.personId}`, this.person);
        } else {
          response = await axios.post('/api/people', this.person);
        }
        
        if (response.data.status === 'success') {
          this.success = this.isEdit ? 'Pessoa atualizada com sucesso!' : 'Pessoa cadastrada com sucesso!';
          
          // Redirecionar após 2 segundos
          setTimeout(() => {
            this.$router.push('/people');
          }, 2000);
        } else {
          this.error = response.data.message || 'Erro ao salvar pessoa';
        }
      } catch (error) {
        console.error('Erro ao salvar pessoa:', error);
        this.error = 'Erro ao salvar pessoa. Verifique os dados e tente novamente.';
        
        // Se há erros de validação específicos
        if (error.response && error.response.data && error.response.data.errors) {
          this.errors = error.response.data.errors;
        }
      } finally {
        this.loading = false;
      }
    },
    
    validateForm() {
      this.errors = {};
      
      // Validar nome
      if (!this.person.name || this.person.name.trim().length < 2) {
        this.errors.name = 'Nome deve ter pelo menos 2 caracteres';
      }
      
      // Validar CPF/CNPJ
      if (!this.person.cpf) {
        this.errors.cpf = 'CPF/CNPJ é obrigatório';
      } else if (!this.isValidCpf()) {
        this.errors.cpf = this.person.type === 'legal_entity' ? 'CNPJ inválido' : 'CPF inválido';
      }
      
      // Validar tipo
      if (!this.person.type) {
        this.errors.type = 'Tipo de pessoa é obrigatório';
      }
      
      // Validar email se fornecido
      if (this.person.email && !this.isValidEmail(this.person.email)) {
        this.errors.email = 'Email inválido';
      }
      
      return Object.keys(this.errors).length === 0;
    },
    
    isValidCpf() {
      const cpf = this.person.cpf.replace(/\D/g, '');
      
      if (this.person.type === 'legal_entity') {
        // Validação básica CNPJ (14 dígitos)
        return cpf.length === 14;
      } else {
        // Validação básica CPF (11 dígitos)
        return cpf.length === 11;
      }
    },
    
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },
    
    formatCpf() {
      let value = this.person.cpf.replace(/\D/g, '');
      
      if (this.person.type === 'legal_entity') {
        // Formato CNPJ: 12.345.678/0001-90
        value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
      } else {
        // Formato CPF: 123.456.789-00
        value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
      }
      
      this.person.cpf = value;
    },
    
    formatPhone() {
      let value = this.person.phone.replace(/\D/g, '');
      
      // Formato: (11) 99999-9999
      if (value.length >= 11) {
        value = value.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
      } else if (value.length >= 7) {
        value = value.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
      } else if (value.length >= 3) {
        value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
      }
      
      this.person.phone = value;
    },
    
    onTypeChange() {
      // Limpar CPF quando trocar o tipo
      this.person.cpf = '';
      if (this.errors.cpf) {
        delete this.errors.cpf;
      }
    },
    
    clearMessages() {
      this.error = null;
      this.success = null;
      this.errors = {};
    },
    
    goBack() {
      this.$router.push('/people');
    }
  },
  
  mounted() {
    console.log('PeopleForm mounted - personId:', this.personId);
    if (this.personId) {
      this.loadPerson();
    } else {
      console.log('Modo criação - novo usuário');
    }
  },
  
  watch: {
    personId: {
      handler(newId) {
        if (newId) {
          this.loadPerson();
        } else {
          this.isEdit = false;
          this.person = {
            name: '',
            cpf: '',
            type: '',
            phone: '',
            email: ''
          };
        }
      },
      immediate: true
    }
  }
};
</script>

<style scoped>
.people-form-container {
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
}

.form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 15px;
}

.form-header h2 {
  color: #333;
  margin: 0;
  font-size: 24px;
}

.form-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  padding: 30px;
}

.people-form {
  display: grid;
  gap: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-row:first-child {
  grid-template-columns: 1fr;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 8px;
  color: #333;
  font-weight: 500;
  font-size: 14px;
}

.form-group input,
.form-group select {
  padding: 12px;
  border: 2px solid #e1e5e9;
  border-radius: 6px;
  font-size: 16px;
  transition: border-color 0.3s, box-shadow 0.3s;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group input:disabled,
.form-group select:disabled {
  background-color: #f8f9fa;
  cursor: not-allowed;
  opacity: 0.7;
}

.field-error {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
}

.error-message {
  background: #fee;
  color: #c33;
  padding: 15px;
  border-radius: 6px;
  text-align: center;
}

.success-message {
  background: #efe;
  color: #363;
  padding: 15px;
  border-radius: 6px;
  text-align: center;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 500;
  text-decoration: none;
  display: inline-block;
  transition: all 0.2s;
  min-width: 120px;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #5a6268;
  transform: translateY(-1px);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Responsive */
@media (max-width: 768px) {
  .form-header {
    flex-direction: column;
    align-items: stretch;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .form-actions {
    flex-direction: column-reverse;
  }
  
  .btn {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .people-form-container {
    padding: 10px;
  }
  
  .form-card {
    padding: 20px;
  }
  
  .form-header h2 {
    font-size: 20px;
  }
}
</style>