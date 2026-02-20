<template>
  <aside class="sidebar">
    <p class="side-title">Entities</p>
    <nav class="sidebar-nav">
      <button
        v-for="entity in entities"
        :key="entity.key"
        class="side-btn"
        :class="{ active: activeKey === entity.key }"
        type="button"
        @click="$emit('select', entity.key)"
      >
        {{ entity.label }}
      </button>
    </nav>
  </aside>
</template>

<script>
export default {
  props: {
    entities: { type: Array, required: true },
    activeKey: { type: String, required: true },
  },
  emits: ['select'],
};
</script>