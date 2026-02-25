<template>
  <div>
    <header class="search-bar">
      <h1 class="title">{{ heading }}</h1>
    </header>

    <div class="layout">
      <aside class="sidebar">
        <p class="side-title">Navigation</p>
        <nav class="sidebar-nav">
          <button
            class="side-btn"
            type="button"
            @click="back()"
          >
            Back
          </button>
          <a
            v-if="entity && !loading && !hasError"
            class="side-btn cta"
            :href="updateUrl"
          >
            Edit {{ entityTypeLabel }}
          </a>
        </nav>
      </aside>

      <main class="content">
        <section class="cards" v-if="entity && !loading && !hasError">
          <article class="card">
            <h3>{{ entity.name || entity.title || `${entityTypeLabel} ${entityId}` }}</h3>
            <div class="stats">
              <template v-if="entityType === 'locations'">
                <div class="stat"><span>Defence</span><span>{{ entity.defence }}</span></div>
                <div class="stat"><span>Food</span><span>{{ entity.food }}</span></div>
                <div class="stat"><span>Morale</span><span>{{ entity.morale }}</span></div>
                <div class="stat"><span>Standing</span><span>{{ entity.standing }}</span></div>
                <div class="nested-section">
                  <p class="nested-title">NPCs Here</p>
                  <div class="nested-cards" v-if="(entity.npcs || []).length > 0">
                    <article class="nested-card" v-for="npc in entity.npcs" :key="`loc-npc-${npc.id}`">
                      <a class="related-link" :href="`/npcs/${npc.id}`">{{ npc.name }}</a>
                      <p class="nested-meta">
                        <span v-if="npc.role_id">
                          <a class="related-link" :href="`/roles/${npc.role_id}`">{{ npc.role }}</a>
                        </span>
                        <span v-else>{{ npc.role || 'Unassigned role' }}</span>
                      </p>
                    </article>
                  </div>
                  <p class="nested-empty" v-else>No NPCs assigned to this location.</p>
                </div>
              </template>
              <template v-else-if="entityType === 'npcs'">
                <div class="stat">
                  <span>Role</span>
                  <span>
                    <a v-if="entity.role_id" class="related-link" :href="`/roles/${entity.role_id}`">{{ entity.role }}</a>
                    <template v-else>{{ entity.role || 'Unassigned' }}</template>
                  </span>
                </div>
                <div class="stat">
                  <span>Location</span>
                  <span>
                    <a v-if="entity.location_id" class="related-link" :href="`/locations/${entity.location_id}`">{{ entity.location }}</a>
                    <template v-else>{{ entity.location || 'Unassigned' }}</template>
                  </span>
                </div>
                <div class="stat"><span>Notes</span><span>{{ entity.notes || '-' }}</span></div>
                <div class="table">
                  <div class="table-row">
                    <div class="table-cell">
                      <div class="table-half"><span>Str</span></div>
                      <div class="table-half"><span>{{ entity.strength ?? '-' }}</span></div>
                    </div>
                    <div class="table-cell">
                      <div class="table-half"><span>Per</span></div>
                      <div class="table-half"><span>{{ entity.perception ?? '-' }}</span></div>
                    </div>
                    <div class="table-cell">
                      <div class="table-half"><span>End</span></div>
                      <div class="table-half"><span>{{ entity.endurance ?? '-' }}</span></div>
                    </div>
                    <div class="table-cell">
                      <div class="table-half"><span>Cha</span></div>
                      <div class="table-half"><span>{{ entity.charisma ?? '-' }}</span></div>
                    </div>
                    <div class="table-cell">
                      <div class="table-half"><span>Int</span></div>
                      <div class="table-half"><span>{{ entity.intelligence ?? '-' }}</span></div>
                    </div>
                    <div class="table-cell">
                      <div class="table-half"><span>Agi</span></div>
                      <div class="table-half"><span>{{ entity.agility ?? '-' }}</span></div>
                    </div>
                    <div class="table-cell">
                      <div class="table-half"><span>Lck</span></div>
                      <div class="table-half"><span>{{ entity.luck ?? '-' }}</span></div>
                    </div>
                  </div>
                </div>
                <div class="nested-section">
                  <p class="nested-title">Skills</p>
                  <div class="skill-cards" v-if="(entity.skills || []).length > 0">
                    <article class="skill-card" v-for="skill in entity.skills" :key="`npc-skill-${skill.id}-${skill.level}`">
                      <p class="skill-name">{{ skill.name || 'Unknown Skill' }}</p>
                      <p class="skill-level">Level {{ skill.level ?? '-' }}</p>
                    </article>
                  </div>
                  <p class="nested-empty" v-else>No skills assigned.</p>
                </div>
                <div class="nested-section">
                  <p class="nested-title">Knowledge</p>
                  <div class="nested-cards wide" v-if="(entity.knowledge || []).length > 0">
                    <article class="nested-card" v-for="knowledge in entity.knowledge" :key="`npc-knowledge-${knowledge.id}`">
                      <p class="knowledge-name">{{ knowledge.title || 'Unknown Knowledge' }}</p>
                      <pre class="knowledge-description">{{ knowledge.description || 'No description' }}</pre>
                      <p class="knowledge-category">{{ knowledge.category || 'Uncategorized' }}</p>
                      <p v-if="knowledge.world_secret_id" class="knowledge-secret-tag">
                        <a class="related-link" :href="`/worldsecrets/${knowledge.world_secret_id}`">
                          {{ knowledge.world_secret_title || 'Unknown Secret' }}
                        </a>
                      </p>
                    </article>
                  </div>
                  <p class="nested-empty" v-else>No knowledge assigned.</p>
                </div>
                <div class="stat"><span>Created</span><span>{{ entity.created_at || '-' }}</span></div>
              </template>
              <template v-else-if="entityType === 'roles'">
                <div class="stat"><span>Description</span><span>{{ entity.description || '-' }}</span></div>
                <div class="stat"><span>ID</span><span>{{ entity.id ?? '-' }}</span></div>
              </template>
              <template v-else-if="entityType === 'worldsecrets'">
                <div class="stat"><span>Category</span><span>{{ entity.category || '-' }}</span></div>
                <div class="stat"><span>Description</span><span>{{ entity.description || '-' }}</span></div>
                <div class="nested-section">
                  <p class="nested-title">Knowledge Tied To This Secret</p>
                  <div class="nested-cards" v-if="(entity.knowledge || []).length > 0">
                    <article class="nested-card" v-for="knowledge in entity.knowledge" :key="`secret-knowledge-${knowledge.id}`">
                      <p class="knowledge-name">{{ knowledge.title || 'Unknown Knowledge' }}</p>
                      <p class="knowledge-category">{{ knowledge.category || 'Uncategorized' }}</p>
                      <p class="nested-meta">
                        <span v-if="knowledge.npc_id">
                          <a class="related-link" :href="`/npcs/${knowledge.npc_id}`">{{ knowledge.npc_name || 'Unknown NPC' }}</a>
                        </span>
                        <span v-else>Unassigned NPC</span>
                      </p>
                    </article>
                  </div>
                  <p class="nested-empty" v-else>No knowledge linked.</p>
                </div>
              </template>
            </div>
          </article>
        </section>

        <p class="status" :class="{ error: hasError }">{{ status }}</p>
        <p class="status" v-if="hasError">{{ errorMessage }}</p>
      </main>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      entity: null,
      entityType: '',
      entityId: null,
      loading: false,
      status: 'Loading...',
      hasError: false,
    };
  },
  computed: {
    entityTypeLabel() {
      if (this.entityType === 'npcs') return 'NPC';
      if (this.entityType === 'locations') return 'Location';
      if (this.entityType === 'roles') return 'Role';
      if (this.entityType === 'worldsecrets') return 'World Secret';
      return 'Entity';
    },
    heading() {
      if (this.loading) return 'Loading...';
      if (this.hasError) return 'Entity Details';
      if (!this.entity) return 'Entity Details';
      return `${this.entityTypeLabel} Details`;
    },
    updateUrl() {
      if (!this.entityType || !this.entityId) {
        return '#';
      }

      return `/${this.entityType}/${this.entityId}/update`;
    },
  },
  methods: {
    resolveRouteContext() {
      const parts = window.location.pathname.split('/').filter(Boolean);
      this.entityType = parts[0] || '';
      this.entityId = parts[1] || '';
    },
    async loadEntity() {
      this.resolveRouteContext();

      if (!['npcs', 'locations', 'roles', 'worldsecrets'].includes(this.entityType) || !this.entityId) {
        this.entity = null;
        this.hasError = true;
        this.status = 'Unsupported detail URL.';
        this.errorMessage = 'The URL does not match any known entity type or is missing an ID.';
        return;
      }

      this.loading = true;
      this.hasError = false;
      this.status = `Loading ${this.entityTypeLabel.toLowerCase()}...`;
      this.errorMessage = '';

      try {
        const response = await fetch(`/api/${this.entityType}/${this.entityId}`, {
          headers: { Accept: 'application/json' },
        });
        if (!response.ok) {
          throw new Error(`Request failed (${response.status})`);
        }

        this.entity = await response.json();
        this.status = ``;
      } catch (error) {
        this.errorMessage = error.message;
        this.entity = null;
        this.hasError = true;
        this.status = `Failed to load ${this.entityTypeLabel.toLowerCase()}.`;
      } finally {
        this.loading = false;
      }
    },
    back() {
      window.history.back();
    },
  },
  mounted() {
    this.loadEntity();
  },
};
</script>
