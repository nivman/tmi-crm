<template>
  <div style="height: 30px">
    <img
        v-if="isOn"
        style="height: 25px !important;"
        src="https://image.flaticon.com/icons/png/128/351/351667.png"
    />
    <img
        v-else
        style="height: 25px !important;"
        src="https://image.flaticon.com/icons/png/256/515/515077.png"
    />
  </div>
</template>
<script>
export default {
  props: {
    isOn: Boolean
  }
};
</script>
