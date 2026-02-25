(() => {
  if (!window.Vue) {
    console.error('Vue global not found. Did the Vue script load?');
    return;
  }

  if (!window['vue3-sfc-loader']) {
    console.error('vue3-sfc-loader not found. Did the loader script load?');
    return;
  }

  const options = {
    moduleCache: { vue: window.Vue },

    async getFile(url) {
      const response = await fetch(url, { cache: 'no-store' });
      if (!response.ok) {
        throw new Error(`${url} ${response.status} ${response.statusText}`);
      }
      return {
        getContentData: (asBinary) =>
          asBinary ? response.arrayBuffer() : response.text(),
      };
    },

    addStyle(textContent) {
      const style = document.createElement('style');
      style.textContent = textContent;
      document.head.appendChild(style);
    },
  };

  const { loadModule } = window['vue3-sfc-loader'];

  async function mount() {
    const targets = document.querySelectorAll('[data-sidebar-app]');
    if (targets.length === 0) {
      return;
    }

    const componentUrl = `/components/EntitySidebar.vue?v=${Date.now()}`;
    const EntitySidebar = await loadModule(componentUrl, options);

    targets.forEach((target) => {
      const activeNav = target.dataset.activeNav || '';

      window.Vue.createApp(EntitySidebar, {
        activeNav,
      }).mount(target);
    });
  }

  async function run() {
    try {
      await mount();
    } catch (err) {
      console.error('Failed to mount sidebar component:', err);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
})();
