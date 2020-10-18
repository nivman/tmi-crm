<template>
  <div class="has-text-centered">{{ format_date(date) }}</div>
</template>

<script>

export default {
  name: 'DateFormatComponent',
  props: {
    date: String
  },
  created: function () {
  },
  methods: {
    format_date (value) {

      if (value) {
        return moment(String(value)).format('DD/MM/YYYY hh:mm')
      }
    },

  },
}
</script>

<style scoped>

</style>