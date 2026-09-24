/**
 * Healthy Bite — Search Bar Component
 * Live filtering across names, descriptions, ingredients, categories, and dietary tags
 */

const SearchBarComponent = {
  render(containerId = "search-bar-container", onSearchChange = null) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = `
      <div class="search-container">
        <div class="search-input-wrapper">
          <i class="bi bi-search search-icon"></i>
          <input 
            type="text" 
            id="global-search-input" 
            class="search-input" 
            placeholder="Search food, ingredients or cuisine" 
            value="${AppState.getSearchQuery()}"
            autocomplete="off"
          />
          <button type="button" id="search-clear-btn" class="search-clear-btn" title="Clear search" aria-label="Clear search">
            <i class="bi bi-x"></i>
          </button>
        </div>
      </div>
    `;

    const input = container.querySelector("#global-search-input");
    const clearBtn = container.querySelector("#search-clear-btn");

    const updateClearBtn = () => {
      if (input.value.trim().length > 0) {
        clearBtn.classList.add("visible");
      } else {
        clearBtn.classList.remove("visible");
      }
    };

    updateClearBtn();

    input.addEventListener("input", (e) => {
      const val = e.target.value;
      updateClearBtn();
      AppState.setSearchQuery(val);
      if (typeof onSearchChange === "function") {
        onSearchChange(val);
      }
    });

    clearBtn.addEventListener("click", () => {
      input.value = "";
      updateClearBtn();
      input.focus();
      AppState.setSearchQuery("");
      if (typeof onSearchChange === "function") {
        onSearchChange("");
      }
    });
  }
};
