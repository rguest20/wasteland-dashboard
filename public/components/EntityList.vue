<template>
  <section class="cards">
    <article
      v-for="item in items"
      :key="`${activeKey}-${item.id ?? item.name}`"
      :class="`card ${cardWidth()}`"
      @click="redirect(`/${activeKey.toLowerCase()}/${item.id ?? ''}`)"
    >
      <h3>{{ item.name || 'Unknown' }}</h3>

      <div class="stats">
        <template v-if="activeKey === 'locations'">
            <div class="stat"><span>Defence</span><span>{{ item.defence }}</span></div>
            <div class="stat"><span>Food</span><span>{{ item.food }}</span></div>
            <div class="stat"><span>Morale</span><span>{{ item.morale }}</span></div>
            <div class="stat"><span>Standing</span><span>{{ item.standing }}</span></div>
        </template>

        <template v-else-if="activeKey === 'NPCs'">
            <div class="stat"><span>Role</span><span>{{ item.role || 'Unassigned' }}</span></div>
            <div class="stat"><span>Location</span><span>{{ item.location || 'Unassigned' }}</span></div>
            <div class="stat"><span>Created</span><span>{{ item.created_at || '-' }}</span></div>
        </template>

        <template v-else-if="activeKey === 'roles'">
            <div class="stat"><span>Name</span><span>{{ item.name }}</span></div>
            <div class="stat"><span>Description</span><span>{{ item.description || '-' }}</span></div>
        </template>

        <template v-else>
            <div class="stat"><span>Description</span><span>{{ item.description || '-' }}</span></div>
            <div class="stat"><span>ID</span><span>{{ item.id ?? '-' }}</span></div>
        </template>
      </div>
    </article>
  </section>
</template>

<script>
export default {
  props: {
    activeKey: { type: String, required: true },
    items: { type: Array, required: true },
  },
  methods: {
    cardWidth() {
      if (this.activeKey === 'NPCs') {
        return 'wide';
      } else {
        return 'standard';
      }
    },
    redirect(path) {
      window.location.href = path;
    },
  },
};
</script>