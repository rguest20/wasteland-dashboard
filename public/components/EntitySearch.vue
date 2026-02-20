<template>
  <header class="search-bar">
    <input
      class="search-input"
      type="search"
      :placeholder="placeholder"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value.trim())"
    >
  </header>
</template>

<script>
export default {
  props: {
    modelValue: { type: String, default: '' },
    placeholder: { type: String, required: true },
  },
  emits: ['update:modelValue'],
};
</script>