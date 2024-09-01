<template>
  <div id="jobs">
    <div v-if="formattedJobs.length === 0">
      <h2>Nebyly nalezeny žádné inzeráty</h2>
    </div>
    <div v-else>
      <h2>Inzeráty</h2>

      <table class="table table-striped">
        <thead>
        <tr>
          <th>Místo</th>
          <th>Datum vytvoření</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="job in formattedJobs" :key="job.id">
          <td>
            <a :href="jobDetailPath(job.id)">{{ job.title }}</a>
          </td>
          <td>{{ job.date_created }}</td>
        </tr>
        </tbody>
      </table>

      <div v-if="totalPages > 1" class="pagination">
        <a
            v-if="currentPage > 1"
            class="page-link"
            @click.prevent="goToPage(currentPage - 1)"
            href="#"
        >Předchozí</a
        >

        <a
            v-for="page in totalPages"
            :key="page"
            :class="['page-link', { active: page === currentPage }]"
            :style="{ color: page === currentPage ? 'black' : 'inherit' }"
            @click.prevent="goToPage(page)"
        >{{ page }}</a
        >

        <a
            v-if="currentPage < totalPages"
            class="page-link"
            @click.prevent="goToPage(currentPage + 1)"
            href="#"
        >Další</a
        >
      </div>
    </div>
  </div>
</template>

<script>
import { format } from 'date-fns';

export default {
  name: "JobsList",
  props: {
    jobs: String,
    totalJobs: Number,
    totalPages: Number,
    currentPage: Number,
  },
  computed: {
    parsedJobs() {
      try {
        return JSON.parse(this.jobs);
      } catch (e) {
        console.error("Chyba při parsování JSONU:", e);
        return [];
      }
    },
    formattedJobs() {
      return this.parsedJobs.map(job => {
        return {
          ...job,
          date_created: format(new Date(job.date_created), 'dd.MM.yyyy'),
        };
      });
    },
  },
  methods: {
    jobDetailPath(id) {
      return `/job/${id}?page=${this.currentPage}`;
    },
    goToPage(page) {
      window.location.href = `/?page=${page}`;
    },
  },
};
</script>

<style scoped>
.pagination .active {
  font-weight: bold;
}
</style>
