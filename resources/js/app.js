import { createApp } from 'vue';
import router from './router';
import axios from 'axios';
import './auth'; // Importar configuração de autenticação JWT

// Import Bootstrap
import 'bootstrap';

// Configure Axios defaults
window.axios = axios;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.baseURL = 'http://localhost:8000';

// Import components
import PeopleList from './components/People/PeopleList.vue';
import PeopleForm from './components/People/PeopleForm.vue';
import Login from './components/Auth/Login.vue';
import Register from './components/Auth/Register.vue';

const app = createApp({
    template: '<router-view></router-view>'
});

// Register global components
app.component('people-list', PeopleList);
app.component('people-form', PeopleForm);
app.component('login', Login);
app.component('register', Register);

app.use(router);
app.mount('#app');
