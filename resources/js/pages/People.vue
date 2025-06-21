<template>
  <div class="people-page">
    <people-list />
  </div>
</template>

<script>
import PeopleList from '../components/People/PeopleList.vue';

export default {
  name: 'People',
  components: {
    PeopleList
  },
  mounted() {
    // Verificar se está autenticado
    const token = localStorage.getItem('auth_token');
    if (!token) {
      this.$router.push('/login');
    }
  }
};
</script>

<style scoped>
.people-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 20px 0;
}
</style>
