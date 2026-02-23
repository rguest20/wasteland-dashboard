<template>
  <aside class="sidebar">
    <template v-if="entities.length > 0">
        <div class="side-block">
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
        </div>
    </template>
    
    <div class="side-block">
        <p class="side-title">Navigation</p>
        <nav class="sidebar-nav">
      <a
        class="side-btn"
        :class="{ active: currentNav === 'dashboard' }"
        href="/"
      >
        Dashboard
      </a>
      <a
        class="side-btn"
        :class="{ active: currentNav === 'role_new' }"
        href="/roles/new"
      >
        New Role
      </a>
      <a
        class="side-btn"
        :class="{ active: currentNav === 'location_new' }"
        href="/locations/new"
      >
        New Location
      </a>
      <a
        class="side-btn"
        :class="{ active: currentNav === 'npc_new' }"
        href="/npcs/new"
      >
        New NPC
      </a>
        </nav>
    </div>
  </aside>
</template>

<script>
export default {
  props: {
    entities: { type: Array, default: () => [] },
    activeKey: { type: String, default: '' },
    activeNav: { type: String, default: '' },
  },
  computed: {
    currentNav() {
      const explicit = String(this.activeNav || '').trim();
      if (explicit !== '') {
        return explicit;
      }

      const path = window.location.pathname;
      if (path === '/' || path === '/dashboard') return 'dashboard';
      if (path === '/roles/new') return 'role_new';
      if (path === '/locations/new') return 'location_new';
      if (path === '/npcs/new') return 'npc_new';
      return '';
    },
  },
  emits: ['select'],
};
</script>
