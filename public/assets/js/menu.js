/**
 * HEALTHY BITE — Customer Menu & Live Filtering
 */

const Menu = {
    init() {
        this.setupSearch();
        this.setupCategoryChips();
        this.setupFoodCardClicks();
    },

    setupSearch() {
        const searchInput = document.getElementById('foodSearchInput');
        const clearBtn = document.getElementById('searchClearBtn');

        if (!searchInput) return;

        searchInput.addEventListener('input', Utils.debounce((e) => {
            const query = e.target.value.trim().toLowerCase();
            State.searchTerm = query;

            if (clearBtn) {
                clearBtn.style.display = query.length > 0 ? 'block' : 'none';
            }

            this.filterFoodCards();
        }, 150));

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                State.searchTerm = '';
                clearBtn.style.display = 'none';
                this.filterFoodCards();
            });
        }
    },

    setupCategoryChips() {
        const chips = document.querySelectorAll('.category-pill-btn, .category-chip');
        chips.forEach(chip => {
            chip.addEventListener('click', () => {
                chips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');

                State.selectedCategory = chip.getAttribute('data-category-id') || 'all';
                this.filterFoodCards();

                // Smooth scroll category pill into view on mobile
                chip.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            });
        });
    },

    setDietFilter(diet, targetBtn) {
        State.dietFilter = diet || 'all';
        document.querySelectorAll('.diet-filter-chip').forEach(btn => {
            if (btn.getAttribute('data-diet') === diet) {
                btn.classList.add('active');
                btn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            } else {
                btn.classList.remove('active');
            }
        });
        this.filterFoodCards();
    },

    setupFoodCardClicks() {
        document.querySelectorAll('.food-card').forEach(card => {
            card.addEventListener('click', () => {
                const foodId = card.getAttribute('data-food-id');
                if (foodId && window.FoodDetails) {
                    FoodDetails.open(foodId);
                }
            });
        });
    },

    filterFoodCards() {
        const query = (State.searchTerm || '').toLowerCase();
        const selectedCat = State.selectedCategory || 'all';
        const dietFilter = State.dietFilter || 'all';
        const sections = document.querySelectorAll('.menu-section, .food-section');
        let totalVisibleCards = 0;

        sections.forEach(sec => {
            const cards = sec.querySelectorAll('.food-card');
            const sectionCatId = sec.getAttribute('data-section-category-id');
            let sectionVisibleCount = 0;

            cards.forEach(card => {
                const cardCatId = card.getAttribute('data-category-id');
                const cardName = (card.getAttribute('data-food-name') || '').toLowerCase();
                const cardDesc = (card.getAttribute('data-food-desc') || '').toLowerCase();
                const cardIngr = (card.getAttribute('data-food-ingredients') || '').toLowerCase();
                const foodType = (card.getAttribute('data-food-type') || '').toLowerCase();
                const protein = parseFloat(card.getAttribute('data-protein') || '0');

                const matchesCat = (selectedCat === 'all' || cardCatId === String(selectedCat));
                const matchesSearch = (query === '' || 
                    cardName.includes(query) || 
                    cardDesc.includes(query) || 
                    cardIngr.includes(query)
                );

                let matchesDiet = true;
                if (dietFilter === 'veg') {
                    matchesDiet = (foodType === 'veg' || foodType === 'vegetarian' || foodType === 'vegan');
                } else if (dietFilter === 'high-protein') {
                    matchesDiet = (protein >= 25);
                } else if (dietFilter === 'non-veg') {
                    matchesDiet = (foodType === 'non_veg' || foodType === 'non-vegetarian' || foodType === 'nonveg' || foodType === 'non_vegetarian');
                } else if (dietFilter === 'vegan') {
                    matchesDiet = (foodType === 'vegan');
                } else if (dietFilter === 'jain') {
                    matchesDiet = (foodType === 'jain');
                }

                if (matchesCat && matchesSearch && matchesDiet) {
                    card.style.display = 'flex';
                    sectionVisibleCount++;
                    totalVisibleCards++;
                } else {
                    card.style.display = 'none';
                }
            });

            sec.style.display = sectionVisibleCount > 0 ? 'block' : 'none';
        });

        // Toggle empty state
        const emptyState = document.getElementById('menuEmptyState');
        if (emptyState) {
            emptyState.style.display = totalVisibleCards === 0 ? 'block' : 'none';
        }
    }
};

window.Menu = Menu;

window.openFoodDetails = function (foodId) {
    if (window.FoodDetails) {
        FoodDetails.open(foodId);
    }
};

window.filterByCategory = function (catId, targetBtn) {
    State.selectedCategory = String(catId);
    const chips = document.querySelectorAll('.category-pill-btn, .category-chip');
    chips.forEach(c => {
        if (c.getAttribute('data-category-id') === String(catId)) {
            c.classList.add('active');
        } else {
            c.classList.remove('active');
        }
    });

    Menu.filterFoodCards();

    // Smooth scroll to section
    const sec = document.getElementById(`cat-section-${catId}`);
    if (sec) {
        sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

window.clearSearch = function () {
    const searchInput = document.getElementById('foodSearchInput');
    const clearBtn = document.getElementById('searchClearBtn');
    if (searchInput) searchInput.value = '';
    if (clearBtn) clearBtn.style.display = 'none';
    State.searchTerm = '';
    State.selectedCategory = 'all';
    State.dietFilter = 'all';

    const chips = document.querySelectorAll('.category-pill-btn, .category-chip');
    chips.forEach((c, idx) => {
        if (idx === 0) c.classList.add('active');
        else c.classList.remove('active');
    });

    document.querySelectorAll('.diet-filter-chip').forEach((btn, idx) => {
        if (idx === 0) btn.classList.add('active');
        else btn.classList.remove('active');
    });

    Menu.filterFoodCards();
};

