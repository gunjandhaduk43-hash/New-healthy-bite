/**
 * Healthy Bite — Demo Restaurant Data Source
 * Greenhouse Kitchen (Indiranagar · Bengaluru · Table 12)
 *
 * NOTE: Single source of truth. Structured to mirror real database/API entities
 * for seamless future PHP+MySQL integration.
 */

const HEALTHY_BITE_DATA = {
  restaurant: {
    id: 1,
    name: "Greenhouse Kitchen",
    tagline: "Fresh food. Clear choices.",
    subtitle: "Browse fresh, flavorful and nutrition-aware food made to order.",
    description: "Fresh food, clear choices and flavorful meals made to order with organic produce and verified macros.",
    currency: "₹",
    currency_code: "INR",
    current_table: "12",
    branch: {
      id: 101,
      name: "Indiranagar",
      city: "Bengaluru",
      full_address: "100ft Road, HAL 2nd Stage, Indiranagar, Bengaluru, Karnataka 560038",
      phone: "+91 80 4123 4567"
    }
  },

  categories: [
    { id: 1, name: "Main Meals", slug: "main-meals", icon: "bi-egg-fried", sort_order: 1 },
    { id: 2, name: "Bowls", slug: "bowls", icon: "bi-cup-straw", sort_order: 2 },
    { id: 3, name: "Wraps", slug: "wraps", icon: "bi-card-text", sort_order: 3 },
    { id: 4, name: "Coffee", slug: "coffee", icon: "bi-cup-hot", sort_order: 4 },
    { id: 5, name: "Beverages", slug: "beverages", icon: "bi-droplet", sort_order: 5 },
    { id: 6, name: "Desserts", slug: "desserts", icon: "bi-pie-chart", sort_order: 6 }
  ],

  quick_filters: [
    { id: "all", label: "All", icon: "bi-grid" },
    { id: "high-protein", label: "High Protein", icon: "bi-lightning-charge" },
    { id: "vegetarian", label: "Vegetarian", icon: "bi-flower1" },
    { id: "under-600-kcal", label: "Under 600 kcal", icon: "bi-fire" },
    { id: "low-sugar", label: "Low Sugar", icon: "bi-droplet-half" }
  ],

  food_items: [
    {
      id: 101,
      category_id: 2, // Bowls
      category_name: "Bowls",
      name: "Paneer Protein Bowl",
      description: "Warm bowl of grilled organic paneer cubes, fiber-rich brown rice, fresh steamed greens, edamame, and house herb dressing.",
      image: "https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80",
      fallback_image: "assets/images/foods/paneer-bowl.svg",
      food_type: "vegetarian",
      base_price: 249,
      dietary_tags: ["High Protein", "Vegetarian"],
      featured: true,
      popular: true,
      availability: true,
      ingredients: "Grilled Paneer, Brown Rice, Edamame, Steamed Broccoli, Bell Peppers, Cherry Tomatoes, Herb Dressing",
      allergens: "Dairy, Soy",
      nutrition: {
        calories: 520,
        protein: 35,
        carbs: 38,
        fat: 18,
        fiber: 9,
        sugar: 6,
        sodium: 420,
        caffeine: null
      },
      variants: [
        {
          id: 1011,
          food_item_id: 101,
          name: "Regular Portion",
          price_adjustment: 0,
          nutrition_adjustment: { calories: 0, protein: 0, carbs: 0, fat: 0, fiber: 0, sugar: 0 },
          is_default: true,
          available: true
        },
        {
          id: 1012,
          food_item_id: 101,
          name: "Large Portion (+₹50)",
          price_adjustment: 50,
          nutrition_adjustment: { calories: 120, protein: 10, carbs: 8, fat: 4, fiber: 2, sugar: 1 },
          is_default: false,
          available: true
        }
      ],
      customizations: [
        {
          id: 201,
          group_name: "Choose your base",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2011, name: "Brown Rice", price_adjustment: 0, nutrition_adjustment: { calories: 160, protein: 4, carbs: 32, fat: 1 }, is_default: true },
            { id: 2012, name: "Organic Quinoa", price_adjustment: 20, nutrition_adjustment: { calories: 180, protein: 6, carbs: 29, fat: 2 }, is_default: false }
          ]
        },
        {
          id: 202,
          group_name: "Primary Protein",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2021, name: "Farm Paneer (200g)", price_adjustment: 0, nutrition_adjustment: { calories: 180, protein: 18, carbs: 2, fat: 12 }, is_default: true },
            { id: 2022, name: "Smoked Organic Tofu", price_adjustment: 0, nutrition_adjustment: { calories: 140, protein: 16, carbs: 3, fat: 7 }, is_default: false }
          ]
        },
        {
          id: 203,
          group_name: "Select Sauce",
          type: "single",
          required: false,
          min_quantity: 0,
          max_quantity: 1,
          options: [
            { id: 2031, name: "Fresh Mint Herb Dip", price_adjustment: 0, nutrition_adjustment: { calories: 25, protein: 1, carbs: 2, fat: 1 }, is_default: true },
            { id: 2032, name: "Peri Peri Dressing", price_adjustment: 15, nutrition_adjustment: { calories: 45, protein: 1, carbs: 4, fat: 3 }, is_default: false }
          ]
        },
        {
          id: 204,
          group_name: "Gourmet Toppings",
          type: "multi",
          required: false,
          min_quantity: 0,
          max_quantity: 3,
          options: [
            { id: 2041, name: "Fresh Hass Avocado", price_adjustment: 60, nutrition_adjustment: { calories: 80, protein: 1, carbs: 4, fat: 7, fiber: 3 }, is_default: false },
            { id: 2042, name: "Roasted Pumpkin Seeds", price_adjustment: 25, nutrition_adjustment: { calories: 50, protein: 3, carbs: 1, fat: 4 }, is_default: false },
            { id: 2043, name: "Extra Grilled Paneer", price_adjustment: 40, nutrition_adjustment: { calories: 90, protein: 10, carbs: 1, fat: 6 }, is_default: false }
          ]
        }
      ]
    },

    {
      id: 102,
      category_id: 2, // Bowls
      category_name: "Bowls",
      name: "Chicken Rice Bowl",
      description: "Lean grilled chicken breast glazed with aromatic fine herbs, jasmine rice, steamed asparagus, and roasted sesame seeds.",
      image: "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80",
      fallback_image: "assets/images/foods/chicken-bowl.svg",
      food_type: "non-veg",
      base_price: 279,
      dietary_tags: ["High Protein"],
      featured: true,
      popular: true,
      availability: true,
      ingredients: "Lean Chicken Breast, Steamed Jasmine Rice, Grilled Asparagus, Carrots, Sesame Vinaigrette",
      allergens: "Sesame",
      nutrition: {
        calories: 580,
        protein: 42,
        carbs: 52,
        fat: 17,
        fiber: null, // intentionally null as specified in prompt
        sugar: null, // intentionally null
        sodium: 540,
        caffeine: null
      },
      variants: [
        {
          id: 1021,
          food_item_id: 102,
          name: "Standard (150g chicken)",
          price_adjustment: 0,
          nutrition_adjustment: { calories: 0, protein: 0, carbs: 0, fat: 0 },
          is_default: true,
          available: true
        },
        {
          id: 1022,
          food_item_id: 102,
          name: "Double Protein (+₹60)",
          price_adjustment: 60,
          nutrition_adjustment: { calories: 140, protein: 14, carbs: 0, fat: 4 },
          is_default: false,
          available: true
        }
      ],
      customizations: [
        {
          id: 211,
          group_name: "Grain Base",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2111, name: "Jasmine Rice", price_adjustment: 0, nutrition_adjustment: { calories: 150, protein: 3, carbs: 34, fat: 0 }, is_default: true },
            { id: 2112, name: "Tricolor Quinoa", price_adjustment: 25, nutrition_adjustment: { calories: 170, protein: 6, carbs: 28, fat: 2 }, is_default: false }
          ]
        },
        {
          id: 212,
          group_name: "Preparation Style",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2121, name: "Grilled Herb & Garlic", price_adjustment: 0, nutrition_adjustment: { calories: 20, protein: 0, carbs: 1, fat: 1 }, is_default: true },
            { id: 2122, name: "Smoked Chipotle Glaze", price_adjustment: 15, nutrition_adjustment: { calories: 40, protein: 0, carbs: 6, fat: 1 }, is_default: false }
          ]
        },
        {
          id: 213,
          group_name: "Add-ons",
          type: "multi",
          required: false,
          min_quantity: 0,
          max_quantity: 3,
          options: [
            { id: 2131, name: "Boiled Eggs (2 pcs)", price_adjustment: 35, nutrition_adjustment: { calories: 140, protein: 12, carbs: 1, fat: 9 }, is_default: false },
            { id: 2132, name: "Sautéed Wild Mushrooms", price_adjustment: 30, nutrition_adjustment: { calories: 35, protein: 2, carbs: 3, fat: 1 }, is_default: false },
            { id: 2133, name: "House Chunky Guacamole", price_adjustment: 55, nutrition_adjustment: { calories: 75, protein: 1, carbs: 3, fat: 7 }, is_default: false }
          ]
        }
      ]
    },

    {
      id: 103,
      category_id: 3, // Wraps
      category_name: "Wraps",
      name: "Grilled Paneer Wrap",
      description: "Whole wheat tortilla stuffed with spiced paneer tikka, crunchy bell peppers, shredded romaine lettuce, and hung curd dressing.",
      image: "https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=600&auto=format&fit=crop&q=80",
      fallback_image: "assets/images/foods/paneer-wrap.svg",
      food_type: "vegetarian",
      base_price: 219,
      dietary_tags: ["High Protein", "Vegetarian"],
      featured: false,
      popular: true,
      availability: true,
      ingredients: "Whole Wheat Tortilla, Marinated Paneer, Crunchy Peppers, Romaine Lettuce, Hung Curd Sauce",
      allergens: "Gluten, Dairy",
      nutrition: {
        calories: 460,
        protein: 25,
        carbs: 45,
        fat: 16,
        fiber: 6,
        sugar: 4,
        sodium: 380,
        caffeine: null
      },
      variants: [
        {
          id: 1031,
          food_item_id: 103,
          name: "Regular Wrap",
          price_adjustment: 0,
          nutrition_adjustment: { calories: 0, protein: 0, carbs: 0, fat: 0 },
          is_default: true,
          available: true
        },
        {
          id: 1032,
          food_item_id: 103,
          name: "Jumbo Wrap (+₹45)",
          price_adjustment: 45,
          nutrition_adjustment: { calories: 110, protein: 8, carbs: 12, fat: 3 },
          is_default: false,
          available: true
        }
      ],
      customizations: [
        {
          id: 221,
          group_name: "Wrap Bread",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2211, name: "Whole Wheat Tortilla", price_adjustment: 0, nutrition_adjustment: { calories: 140, protein: 4, carbs: 26, fat: 2 }, is_default: true },
            { id: 2212, name: "Multigrain Seeded Flatbread", price_adjustment: 15, nutrition_adjustment: { calories: 155, protein: 6, carbs: 24, fat: 4 }, is_default: false }
          ]
        },
        {
          id: 222,
          group_name: "Spiciness Level",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2221, name: "Mild & Tangy", price_adjustment: 0, nutrition_adjustment: {}, is_default: false },
            { id: 2222, name: "Medium Masala", price_adjustment: 0, nutrition_adjustment: {}, is_default: true },
            { id: 2223, name: "Fiery Hot", price_adjustment: 0, nutrition_adjustment: {}, is_default: false }
          ]
        },
        {
          id: 223,
          group_name: "Wrap Extras",
          type: "multi",
          required: false,
          min_quantity: 0,
          max_quantity: 2,
          options: [
            { id: 2231, name: "Extra Cheddar Slice", price_adjustment: 30, nutrition_adjustment: { calories: 80, protein: 5, carbs: 1, fat: 6 }, is_default: false },
            { id: 2232, name: "Pickled Jalapeños", price_adjustment: 15, nutrition_adjustment: { calories: 10, protein: 0, carbs: 2, fat: 0 }, is_default: false }
          ]
        }
      ]
    },

    {
      id: 104,
      category_id: 4, // Coffee
      category_name: "Coffee",
      name: "Iced Protein Coffee",
      description: "Cold-brewed Arabica espresso blended with pure isolate whey protein, cold milk, and natural vanilla bean extract.",
      image: "https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=600&auto=format&fit=crop&q=80",
      fallback_image: "assets/images/foods/protein-coffee.svg",
      food_type: "vegetarian",
      base_price: 165,
      dietary_tags: ["High Protein", "Vegetarian"],
      featured: true,
      popular: true,
      availability: true,
      ingredients: "Double Shot Arabica Espresso, Whey Isolate, Toned Milk, Madagascar Vanilla Extract",
      allergens: "Dairy",
      nutrition: {
        calories: 140,
        protein: 15,
        carbs: 8,
        fat: 5,
        fiber: null, // coffee has negligible fiber
        sugar: 4,
        sodium: 85,
        caffeine: 95
      },
      variants: [
        {
          id: 1041,
          food_item_id: 104,
          name: "Regular (350ml)",
          price_adjustment: 0,
          nutrition_adjustment: { calories: 0, protein: 0, carbs: 0, fat: 0, caffeine: 0 },
          is_default: true,
          available: true
        },
        {
          id: 1042,
          food_item_id: 104,
          name: "Grande (500ml) (+₹40)",
          price_adjustment: 40,
          nutrition_adjustment: { calories: 40, protein: 5, carbs: 2, fat: 1, caffeine: 35 },
          is_default: false,
          available: true
        }
      ],
      customizations: [
        {
          id: 231,
          group_name: "Milk Choice",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2311, name: "Fresh Toned Milk", price_adjustment: 0, nutrition_adjustment: { calories: 50, protein: 3, carbs: 4, fat: 2 }, is_default: true },
            { id: 2312, name: "Creamy Oat Milk", price_adjustment: 30, nutrition_adjustment: { calories: 45, protein: 2, carbs: 7, fat: 1 }, is_default: false },
            { id: 2313, name: "Organic Almond Milk", price_adjustment: 30, nutrition_adjustment: { calories: 30, protein: 1, carbs: 2, fat: 2 }, is_default: false }
          ]
        },
        {
          id: 232,
          group_name: "Sweetness",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2321, name: "No Added Sugar", price_adjustment: 0, nutrition_adjustment: { calories: 0, sugar: 0 }, is_default: true },
            { id: 2322, name: "25% Raw Brown Sugar", price_adjustment: 0, nutrition_adjustment: { calories: 18, carbs: 4, sugar: 4 }, is_default: false },
            { id: 2323, name: "Stevia Plant Extract", price_adjustment: 0, nutrition_adjustment: { calories: 0, carbs: 0, sugar: 0 }, is_default: false }
          ]
        },
        {
          id: 233,
          group_name: "Extras & Boosts",
          type: "multi",
          required: false,
          min_quantity: 0,
          max_quantity: 2,
          options: [
            { id: 2331, name: "Extra Espresso Shot", price_adjustment: 35, nutrition_adjustment: { calories: 5, caffeine: 65 }, is_default: false },
            { id: 2332, name: "Extra Whey Boost (+10g)", price_adjustment: 45, nutrition_adjustment: { calories: 45, protein: 10 }, is_default: false }
          ]
        }
      ]
    },

    {
      id: 105,
      category_id: 5, // Beverages
      category_name: "Beverages",
      name: "Banana Protein Smoothie",
      description: "Thick creamy blend of Robusta bananas, cold Greek yogurt, organic chia seeds, plant protein, and pure cinnamon.",
      image: "https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=600&auto=format&fit=crop&q=80",
      fallback_image: "assets/images/foods/banana-smoothie.svg",
      food_type: "vegetarian",
      base_price: 195,
      dietary_tags: ["High Protein", "Vegetarian"],
      featured: false,
      popular: true,
      availability: true,
      ingredients: "Fresh Bananas, Greek Yogurt, Plant Protein Blend, Chia Seeds, Cinnamon Dust",
      allergens: "Dairy",
      nutrition: {
        calories: 320,
        protein: 24,
        carbs: 42,
        fat: 4,
        fiber: 5,
        sugar: 18,
        sodium: 110,
        caffeine: null
      },
      variants: [
        {
          id: 1051,
          food_item_id: 105,
          name: "Standard (400ml)",
          price_adjustment: 0,
          nutrition_adjustment: { calories: 0, protein: 0, carbs: 0, fat: 0 },
          is_default: true,
          available: true
        },
        {
          id: 1052,
          food_item_id: 105,
          name: "Power Size (600ml) (+₹50)",
          price_adjustment: 50,
          nutrition_adjustment: { calories: 90, protein: 8, carbs: 12, fat: 1 },
          is_default: false,
          available: true
        }
      ],
      customizations: [
        {
          id: 241,
          group_name: "Base Liquid",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2411, name: "Greek Yogurt Blend", price_adjustment: 0, nutrition_adjustment: { calories: 60, protein: 6, carbs: 4, fat: 1 }, is_default: true },
            { id: 2412, name: "Pure Almond Milk", price_adjustment: 20, nutrition_adjustment: { calories: 35, protein: 1, carbs: 2, fat: 2 }, is_default: false }
          ]
        },
        {
          id: 242,
          group_name: "Nut Butter Addition",
          type: "single",
          required: false,
          min_quantity: 0,
          max_quantity: 1,
          options: [
            { id: 2421, name: "Smooth Peanut Butter", price_adjustment: 30, nutrition_adjustment: { calories: 95, protein: 4, carbs: 3, fat: 8 }, is_default: false },
            { id: 2422, name: "Creamy Almond Butter", price_adjustment: 45, nutrition_adjustment: { calories: 90, protein: 3, carbs: 3, fat: 8 }, is_default: false }
          ]
        },
        {
          id: 243,
          group_name: "Crunch & Seeds",
          type: "multi",
          required: false,
          min_quantity: 0,
          max_quantity: 2,
          options: [
            { id: 2431, name: "Organic Chia Seeds", price_adjustment: 20, nutrition_adjustment: { calories: 30, protein: 1, carbs: 2, fat: 2, fiber: 2 }, is_default: false },
            { id: 2432, name: "Toasted Oat Granola", price_adjustment: 25, nutrition_adjustment: { calories: 60, protein: 2, carbs: 10, fat: 2 }, is_default: false }
          ]
        }
      ]
    },

    {
      id: 106,
      category_id: 6, // Desserts
      category_name: "Desserts",
      name: "Chocolate Protein Brownie",
      description: "Dense, decadent fudge brownie crafted with Dutch dark cocoa, almond flour, and whey protein. 100% refined sugar-free.",
      image: "https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=80",
      fallback_image: "assets/images/foods/protein-brownie.svg",
      food_type: "vegetarian",
      base_price: 140,
      dietary_tags: ["Vegetarian", "High Protein"],
      featured: false,
      popular: true,
      availability: true,
      ingredients: "Almond Flour, Dark Cocoa (70%), Whey Isolate, Stevia, Free-range Eggs, Coconut Oil",
      allergens: "Nuts, Dairy, Egg",
      nutrition: {
        calories: 230,
        protein: 14,
        carbs: 22,
        fat: 8,
        fiber: 4,
        sugar: 8,
        sodium: 95,
        caffeine: null
      },
      variants: [
        {
          id: 1061,
          food_item_id: 106,
          name: "Single Slice",
          price_adjustment: 0,
          nutrition_adjustment: { calories: 0, protein: 0, carbs: 0, fat: 0 },
          is_default: true,
          available: true
        },
        {
          id: 1062,
          food_item_id: 106,
          name: "Warm with Low-Fat Whip (+₹35)",
          price_adjustment: 35,
          nutrition_adjustment: { calories: 40, protein: 1, carbs: 3, fat: 2 },
          is_default: false,
          available: true
        }
      ],
      customizations: [
        {
          id: 251,
          group_name: "Serving Style",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2511, name: "Warm & Gooey (Heated)", price_adjustment: 0, nutrition_adjustment: {}, is_default: true },
            { id: 2512, name: "Room Temperature", price_adjustment: 0, nutrition_adjustment: {}, is_default: false }
          ]
        },
        {
          id: 252,
          group_name: "Dessert Drizzle",
          type: "multi",
          required: false,
          min_quantity: 0,
          max_quantity: 2,
          options: [
            { id: 2521, name: "Dark Choco Drizzle (Sugar-Free)", price_adjustment: 20, nutrition_adjustment: { calories: 25, carbs: 2, fat: 2 }, is_default: false },
            { id: 2522, name: "Toasted Almond Flakes", price_adjustment: 20, nutrition_adjustment: { calories: 35, protein: 1, fat: 3 }, is_default: false }
          ]
        }
      ]
    },

    {
      id: 107,
      category_id: 6, // Desserts
      category_name: "Desserts",
      name: "Protein Ice Cream",
      description: "Artisanal low-fat churned ice cream infused with whey isolate and organic vanilla pods. Smooth, creamy, and guilt-free.",
      image: "https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=600&auto=format&fit=crop&q=80",
      fallback_image: "assets/images/foods/protein-icecream.svg",
      food_type: "vegetarian",
      base_price: 169,
      dietary_tags: ["High Protein", "Vegetarian"],
      featured: true,
      popular: true,
      availability: true,
      ingredients: "Low Fat Milk, Whey Isolate, Erythritol, Bourbon Vanilla Extract, Prebiotic Fiber",
      allergens: "Dairy",
      nutrition: {
        calories: 180,
        protein: 20,
        carbs: 16,
        fat: 5,
        fiber: null,
        sugar: 6,
        sodium: 70,
        caffeine: null
      },
      variants: [
        {
          id: 1071,
          food_item_id: 107,
          name: "Single Scoop (120g)",
          price_adjustment: 0,
          nutrition_adjustment: { calories: 0, protein: 0, carbs: 0, fat: 0 },
          is_default: true,
          available: true
        },
        {
          id: 1072,
          food_item_id: 107,
          name: "Double Scoop (240g) (+₹50)",
          price_adjustment: 50,
          nutrition_adjustment: { calories: 110, protein: 12, carbs: 10, fat: 3 },
          is_default: false,
          available: true
        }
      ],
      customizations: [
        {
          id: 261,
          group_name: "Flavour Selection",
          type: "single",
          required: true,
          min_quantity: 1,
          max_quantity: 1,
          options: [
            { id: 2611, name: "Belgian Dark Chocolate", price_adjustment: 0, nutrition_adjustment: { calories: 10, fat: 1 }, is_default: true },
            { id: 2612, name: "Bourbon Vanilla Bean", price_adjustment: 0, nutrition_adjustment: {}, is_default: false },
            { id: 2613, name: "Salted Caramel Crunch", price_adjustment: 15, nutrition_adjustment: { calories: 25, carbs: 4 }, is_default: false }
          ]
        },
        {
          id: 262,
          group_name: "Artisan Toppings",
          type: "multi",
          required: false,
          min_quantity: 0,
          max_quantity: 3,
          options: [
            { id: 2621, name: "Crushed Roasted Hazelnuts", price_adjustment: 25, nutrition_adjustment: { calories: 45, protein: 1, fat: 4 }, is_default: false },
            { id: 2622, name: "Sugar-Free Choco Fudge", price_adjustment: 30, nutrition_adjustment: { calories: 35, fat: 2 }, is_default: false },
            { id: 2623, name: "Fresh Berries Compote", price_adjustment: 30, nutrition_adjustment: { calories: 20, carbs: 5, sugar: 4 }, is_default: false }
          ]
        }
      ]
    }
  ]
};

// Freeze data to prevent unintentional prototype mutation
if (typeof Object.freeze === 'function') {
  Object.freeze(HEALTHY_BITE_DATA);
}
