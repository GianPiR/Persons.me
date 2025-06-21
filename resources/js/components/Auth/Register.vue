<template>
  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <h2>📝 Registro</h2>
        <p>Crie sua conta para acessar o sistema</p>
      </div>
      
      <form @submit.prevent="register" class="register-form">
        <div class="form-group">
          <label for="name">Nome Completo:</label>
          <input 
            id="name"
            type="text" 
            v-model="name" 
            required 
            placeholder="Seu nome completo"
            :disabled="loading"
          />
          <div v-if="errors.name" class="field-error">{{ errors.name[0] }}</div>
        </div>
        
        <div class="form-group">
          <label for="email">Email:</label>
          <input 
            id="email"
            type="email" 
            v-model="email" 
            required 
            placeholder="seu@email.com"
            :disabled="loading"
          />
          <div v-if="errors.email" class="field-error">{{ errors.email[0] }}</div>
        </div>
        
        <div class="form-group">
          <label for="password">Senha:</label>
          <input 
            id="password"
            type="password" 
            v-model="password" 
            required 
            placeholder="Digite sua senha (mín. 6 caracteres)"
            :disabled="loading"
          />
          <div v-if="errors.password" class="field-error">{{ errors.password[0] }}</div>
        </div>
        
        <div class="form-group">
          <label for="password_confirmation">Confirmar Senha:</label>
          <input 
            id="password_confirmation"
            type="password" 
            v-model="passwordConfirmation" 
            required 
            placeholder="Confirme sua senha"
            :disabled="loading"
          />
        </div>
        
        <button type="submit" class="register-btn" :disabled="loading">
          <span v-if="loading">Registrando...</span>
          <span v-else>Registrar</span>
        </button>
        
        <div v-if="error" class="error-message">
          ❌ {{ error }}
        </div>
        
        <div v-if="success" class="success-message">
          ✅ {{ success }}
        </div>
      </form>
      
      <div class="register-footer">
        <p>Já tem conta? <router-link to="/login">Faça login</router-link></p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      passwordConfirmation: '',
      errors: {},
      error: null,
      success: null,
      loading: false
    };
  },
  methods: {
    async register() {
      this.clearMessages();
      
      // Validação básica
      if (this.password !== this.passwordConfirmation) {
        this.error = 'As senhas não coincidem';
        return;
      }
      
      if (this.password.length < 6) {
        this.error = 'A senha deve ter pelo menos 6 caracteres';
        return;
      }
      
      this.loading = true;
      
      try {
        const response = await axios.post('/api/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirmation,
        });
        
        if (response.data.status === 'success') {
          this.success = 'Conta criada com sucesso! Redirecionando...';
          
          // Salvar token no localStorage
          localStorage.setItem('auth_token', response.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.user));
          
          // Redirecionar para dashboard após 2 segundos
          setTimeout(() => {
            this.$router.push('/dashboard');
          }, 2000);
        } else {
          this.error = response.data.message || 'Erro no registro';
        }
      } catch (err) {
        console.error('Register error:', err);
        
        if (err.response && err.response.status === 422) {
          // Erros de validação
          this.errors = err.response.data.errors || {};
          this.error = 'Por favor, corrija os erros abaixo';
        } else {
          this.error = 'Erro ao criar conta. Tente novamente.';
        }
      } finally {
        this.loading = false;
      }
    },
    
    clearMessages() {
      this.error = null;
      this.success = null;
      this.errors = {};
    }
  },
  mounted() {
    // Se já estiver logado, redirecionar para dashboard
    if (localStorage.getItem('auth_token')) {
      this.$router.push('/dashboard');
    }
  }
};
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.register-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  padding: 40px;
  width: 100%;
  max-width: 450px;
}

.register-header {
  text-align: center;
  margin-bottom: 30px;
}

.register-header h2 {
  color: #333;
  margin-bottom: 10px;
  font-size: 28px;
}

.register-header p {
  color: #666;
  margin: 0;
}

.register-form {
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  color: #333;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 12px;
  border: 2px solid #e1e5e9;
  border-radius: 6px;
  font-size: 16px;
  transition: border-color 0.3s;
  box-sizing: border-box;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
}

.form-group input:disabled {
  background-color: #f8f9fa;
  cursor: not-allowed;
}

.field-error {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
}

.register-btn {
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.register-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.register-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.error-message {
  background: #fee;
  color: #c33;
  padding: 10px;
  border-radius: 6px;
  margin-top: 15px;
  text-align: center;
}

.success-message {
  background: #efe;
  color: #363;
  padding: 10px;
  border-radius: 6px;
  margin-top: 15px;
  text-align: center;
}

.register-footer {
  text-align: center;
  margin-top: 20px;
}

.register-footer p {
  color: #666;
  margin: 0;
}

.register-footer a {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.register-footer a:hover {
  text-decoration: underline;
}
</style>