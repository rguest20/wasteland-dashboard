<template>
  <div>
    <EntitySearch
      :placeholder="`Search ${active.label} by name...`"
      v-model="query"
    />

    <div class="layout">
      <EntitySidebar
        :entities="entities"
        :active-key="activeKey"
        @select="switchEntity"
      />

      <main class="content">
        <div class="content-head">
          <h1 class="title">{{ active.title }}</h1>
          <button class="cta" type="button" @click="redirectToCreate">{{ active.cta }}</button>
        </div>

        <EntityList :active-key="active.key" :items="filteredItems" />

        <p class="status" :class="{ error: hasError }">{{ status }}</p>
      </main>
    </div>
  </div>
</template>

<script>
import EntitySidebar from './EntitySidebar.vue';
import EntitySearch from './EntitySearch.vue';
import EntityList from './EntityList.vue';

export default {
  components: { EntitySidebar, EntitySearch, EntityList },

  data() {
    return {
      entities: [
        { key: 'locations', label: 'Locations', title: 'Wasteland Locations', cta: 'Add Location', ctaEndpoint: '/locations/new', endpoint: '/api/locations' },
        { key: 'NPCs', label: 'NPCs', title: 'Wasteland NPCs', cta: 'Add NPC', ctaEndpoint: '/npcs/new', endpoint: '/api/npcs' },
        { key: 'roles', label: 'Roles', title: 'Wasteland Roles', cta: 'Add Role', ctaEndpoint: '/roles/new', endpoint: '/api/roles' },
      ],
      activeKey: 'locations',
      query: '',
      items: [],
      loading: false,
      hasError: false,
    };
  },

  computed: {
    active() {
      return this.entities.find((e) => e.key === this.activeKey) || this.entities[0];
    },

    filteredItems() {
      const q = this.query.toLowerCase();
      if (!q) return this.items;

      return this.items.filter((item) => {
        const haystack = [item.name, item.description, item.role, item.location]
          .filter(Boolean)
          .join(' ')
          .toLowerCase();
        return haystack.includes(q);
      });
    },

    status() {
      const label = this.active.label.toLowerCase();
      if (this.hasError) return `Failed to load ${label}.`;
      if (this.loading) return `Loading ${label}...`;
      if (this.filteredItems.length === 0) return `No matching ${label} found.`;
      return `${this.filteredItems.length} ${label} shown.`;
    },
  },

  methods: {
    async switchEntity(key) {
      if (this.activeKey === key) return;
      this.activeKey = key;
      this.query = '';
      await this.loadItems();
    },

    async loadItems() {
      this.loading = true;
      this.hasError = false;

      try {
        const response = await fetch(this.active.endpoint, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error(`Request failed (${response.status})`);
        this.items = await response.json();
      } catch (e) {
        console.error(e);
        this.items = [];
        this.hasError = true;
      } finally {
        this.loading = false;
      }
    },
    redirectToCreate() {
      window.location.href = this.active.ctaEndpoint;
    },
  },

  mounted() {
    this.loadItems();
  },
};
</script>
