// Configuração de autenticação JWT para o frontend
import axios from 'axios';

// Configurar interceptor do axios para incluir token automaticamente
axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor de resposta para lidar com tokens expirados
axios.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response && error.response.status === 401) {
      // Token inválido ou expirado
      const errorCode = error.response.data?.code;
      
      if (errorCode === 'UNAUTHORIZED' || errorCode === 'INVALID_TOKEN') {
        // Limpar dados de autenticação
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        
        // Redirecionar para login se não estiver na página de login
        if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
          alert('Sua sessão expirou. Você será redirecionado para o login.');
          window.location.href = '/login';
        }
      }
    }
    
    return Promise.reject(error);
  }
);

// Funções auxiliares de autenticação
export const auth = {
  // Verificar se usuário está logado
  isLoggedIn() {
    const token = localStorage.getItem('auth_token');
    const user = localStorage.getItem('user');
    return !!(token && user);
  },

  // Obter dados do usuário
  getUser() {
    const userStr = localStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
  },

  // Obter token
  getToken() {
    return localStorage.getItem('auth_token');
  },

  // Fazer logout
  logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    window.location.href = '/login';
  },

  // Fazer login (salvar dados)
  login(token, user) {
    localStorage.setItem('auth_token', token);
    localStorage.setItem('user', JSON.stringify(user));
  }
};

// Exportar configuração padrão
export default auth;
