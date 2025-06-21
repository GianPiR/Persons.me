<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h2>🔐 Login</h2>
        <p>Acesse o sistema de gerenciamento de pessoas</p>
      </div>

      <form @submit.prevent="login" class="login-form">
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
        </div>

        <div class="form-group">
          <label for="password">Senha:</label>
          <input
            id="password"
            type="password"
            v-model="password"
            required
            placeholder="Digite sua senha"
            :disabled="loading"
          />
        </div>

        <button type="submit" class="login-btn" :disabled="loading">
          <span v-if="loading">Entrando...</span>
          <span v-else>Entrar</span>
        </button>

        <div v-if="error" class="error-message">
          ❌ {{ error }}
        </div>

        <div v-if="success" class="success-message">
          ✅ {{ success }}
        </div>
      </form>

      <div class="login-footer">
        <p>Não tem conta? <router-link to="/register">Registre-se</router-link></p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      email: '',
      password: '',
      error: null,
      success: null,
      loading: false
    };
  },
  methods: {
    async login() {
      this.error = null;
      this.success = null;
      this.loading = true;

      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password,
        });

        if (response.data.status === 'success') {
          this.success = 'Login realizado com sucesso!';

          // Salvar token no localStorage
          localStorage.setItem('auth_token', response.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.user));

          // Redirecionar para dashboard após 1 segundo
          setTimeout(() => {
            this.$router.push('/dashboard');
          }, 1000);
        } else {
          this.error = response.data.message || 'Erro no login';
        }
      } catch (err) {
        console.error('Login error:', err);
        this.error = 'Email ou senha inválidos. Tente novamente.';
      } finally {
        this.loading = false;
      }
    },
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
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.login-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  padding: 40px;
  width: 100%;
  max-width: 400px;
}

.login-header {
  text-align: center;
  margin-bottom: 30px;
}

.login-header h2 {
  color: #333;
  margin-bottom: 10px;
  font-size: 28px;
}

.login-header p {
  color: #666;
  margin: 0;
}

.login-form {
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

.login-btn {
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

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.login-btn:disabled {
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

.login-footer {
  text-align: center;
  margin-top: 20px;
}

.login-footer p {
  color: #666;
  margin: 0;
}

.login-footer a {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.login-footer a:hover {
  text-decoration: underline;
}
</style>
