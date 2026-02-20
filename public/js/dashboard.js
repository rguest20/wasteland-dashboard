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
      const response = await fetch(url);
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
    try {
      const App = await loadModule('/components/DashboardApp.vue', options);
      window.Vue.createApp(App).mount('#app');
    } catch (err) {
      console.error('Failed to mount dashboard app:', err);
      const el = document.getElementById('app');
      if (el) {
        el.innerHTML = '<p style="padding:1rem">Failed to load dashboard.</p>';
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();