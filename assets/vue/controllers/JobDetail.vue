<template>
  <div id="job-detail" style="width: 90%; margin: auto; padding-top: 20px; padding-bottom: 20px;">
    <a :href="backToJobsPath">Zpět</a>
    <div v-if="job">
      <h1>{{ job.title }}</h1>
      <div v-html="job.description"></div>
    </div>
    <div v-else>
      <h2>Inzerát neexistuje</h2>
    </div>
  </div>
</template>

<script>
export default {
  name: "JobDetail",
  props: {
    job: Object,
    page: Number,
  },
  computed: {
    backToJobsPath() {
      return `/?page=${this.page}`;
    },
  },
};
</script>

<style scoped>
#job-detail {
  width: 90%;
  margin: auto;
  padding-top: 20px;
  padding-bottom: 20px;
}
</style>
