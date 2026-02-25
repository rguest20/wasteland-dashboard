(() => {
  function wireCollectionsForm() {
    const holders = document.querySelectorAll('[data-collection-holder]');
    if (holders.length === 0) {
      return;
    }

    holders.forEach((holder) => {
      const addAction = holder.dataset.addAction;
      if (!addAction) {
        return;
      }

      const addButton = document.querySelector(`[data-action="${addAction}"]`);
      if (!addButton) {
        return;
      }

      addButton.addEventListener('click', () => {
        const prototype = holder.dataset.prototype;
        const index = Number(holder.dataset.index || 0);
        const rowHtml = prototype.replace(/__name__/g, String(index));

        holder.insertAdjacentHTML('beforeend', rowHtml);
        holder.dataset.index = String(index + 1);
      });

      holder.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof Element)) {
          return;
        }

        if (target.matches('[data-action="remove-item"]')) {
          event.preventDefault();
          const row = target.closest('.npc-skill-row, .npc-knowledge-row');
          if (row) {
            row.remove();
          }
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', wireCollectionsForm);
  } else {
    wireCollectionsForm();
  }
})();
