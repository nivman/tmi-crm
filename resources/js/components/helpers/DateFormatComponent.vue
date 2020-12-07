<template>
  <span>
  <div v-if="dateTime" class="has-text-centered">{{ format_date_time(dateTime) }}</div>
  <div v-if="date" class="has-text-centered">{{ format_date(date) }}</div>
  </span>

</template>

<script>

export default {
  name: 'DateFormatComponent',
  props: {
    dateTime: String,
    date: String
  },
  created: function () {
  },
  methods: {
    format_date_time (value) {

      if (value) {

        return moment(String(value)).format('DD/MM/YYYY HH:mm')
      }
    },
    format_date(value) {

      if (value) {
        return moment(String(value)).format('DD/MM/YYYY')
      }
    },
  },
}
</script>

<style scoped>

</style>