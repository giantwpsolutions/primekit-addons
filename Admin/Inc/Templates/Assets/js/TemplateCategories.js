/**
 * Loads and displays template categories with their counts
 *
 * This function handles:
 * - Loading categories via AJAX from WordPress backend
 * - Displaying loading states and error messages
 * - Creating category radio buttons with template counts
 * - Loading template type tabs with counts
 * - Organizing templates by category and type
 *
 * @requires jQuery
 * @requires primekitTemplates.ajaxurl - WordPress AJAX URL
 * @requires primekitTemplates.nonce - WordPress security nonce
 *
 * The function expects a response containing:
 * - templates: Array of template objects with id, categories, and type properties
 * - categories: Array of category strings
 *
 * @throws {Error} If category radio container element is not found
 * @returns {void}
 */

function loadTemplateCategories() {
  const categoryRadios = document.querySelector(
    "#primekit-category-radios"
  );
  if (!categoryRadios) {
    console.error("Category radio container not found.");
    return;
  }

  // Clear previous categories
  categoryRadios.innerHTML = "";

  jQuery.ajax({
    url: primekitTemplates.ajaxurl,
    type: "POST",
    data: {
      action: "primekit_get_template_categories",
      nonce: primekitTemplates.nonce,
    },
    success: function (response) {
      console.log("category: ", response);

      if (
        response.success &&
        response.data &&
        Array.isArray(response.data.templates) &&
        Array.isArray(response.data.categories)
      ) {
        // Load types into tab
        loadTemplateTypes(response.data.templates);

        // Populate category radio buttons
        const templates = response.data.templates;
        const categoryToTemplatesMap = {};

        response.data.categories.forEach((category) => {
          categoryToTemplatesMap[category] = new Set();
        });

        templates.forEach((template) => {
          if (Array.isArray(template.categories)) {
            template.categories.forEach((category) => {
              if (categoryToTemplatesMap[category]) {
                categoryToTemplatesMap[category].add(template.id);
              }
            });
          }
          categoryToTemplatesMap["All"].add(template.id);
        });

        // Add categories as radio buttons
        response.data.categories.forEach((category, index) => {
          const count = categoryToTemplatesMap[category]?.size || 0;
          const radioItem = createCategoryRadio(category, count, index === 0);
          categoryRadios.appendChild(radioItem);
        });

        // Bind change event to radios
        categoryRadios.querySelectorAll('input[type="radio"]').forEach((radio) => {
          radio.addEventListener("change", function () {
            if (this.checked) {
              primekitNamespace.selectedCategory = this.value.toLowerCase();
              primekitNamespace.filterTemplates();
            }
          });
        });
      }
    },
    error: function () {
      console.error("Failed to load categories");
    },
  });

  /**
   * Create a category radio button element
   *
   * @param {string} category - The category name to display
   * @param {number} count - The number of templates in this category
   * @param {boolean} checked - Whether this radio should be checked by default
   * @returns {HTMLLabelElement} A label element containing radio button and text
   */
  function createCategoryRadio(category, count, checked = false) {
    const label = document.createElement("label");
    label.className = "primekit-radio-item";

    const input = document.createElement("input");
    input.type = "radio";
    input.name = "primekit-category";
    input.value = category.toLowerCase();
    input.checked = checked;

    const span = document.createElement("span");
    span.className = "primekit-radio-label";

    const categoryName = document.createElement("span");
    categoryName.className = "primekit-radio-name";
    categoryName.textContent = category;

    const categoryCount = document.createElement("span");
    categoryCount.className = "primekit-radio-count";
    categoryCount.textContent = count;

    span.appendChild(categoryName);
    span.appendChild(categoryCount);
    label.appendChild(input);
    label.appendChild(span);

    return label;
  }

  /**
   * Loads and displays template type tabs with counts
   *
   * This function:
   * - Takes an array of template objects as input
   * - Counts templates per type (page, section, popup etc)
   * - Creates tab elements showing template counts by type
   * - Maps internal type names to display labels
   * - Clears and updates the tab list in the UI
   *
   * @param {Array} templates - Array of template objects with type property
   * @throws {Error} If tab list element is not found
   * @returns {void}
   */
  function loadTemplateTypes(templates) {
    const tabList = document.querySelector(".primekit-templates-popup-tab ul");

    if (!tabList) {
      console.error("Tab list not found");
      return;
    }

    // Clear previous content
    tabList.innerHTML = "";

    // Count templates per type
    const typeCounts = {};
    templates.forEach((template) => {
      const type = template.type || "unknown";
      typeCounts[type] = (typeCounts[type] || 0) + 1;
    });

    // Map internal types to display names (optional)
    const typeLabels = {
      page: "Templates",
      section: "Sections",
      popup: "Popups",
      unknown: "Others",
    };

    // Create and insert tab items
    Object.entries(typeCounts).forEach(([type, count]) => {
      const li = document.createElement("li");
      const a = document.createElement("a");
      a.href = "#";
      a.setAttribute("data-type", type);
      // a.textContent = `${typeLabels[type] || type} (${count})`; // count is not needed here so we disabled it
      a.textContent = `${typeLabels[type] || type}`;
      li.appendChild(a);
      tabList.appendChild(li);
    });

    //Bind click events **after** rendering tabs
    tabList.querySelectorAll("a").forEach((tab) => {
      tab.addEventListener("click", function (e) {
        e.preventDefault();
        //Visual highlight for active tab
        tabList
          .querySelectorAll("a")
          .forEach((t) => t.classList.remove("active"));
        this.classList.add("active");
        const selectedType = this.getAttribute("data-type");
        primekitNamespace.selectedType = selectedType;
        primekitNamespace.filterTemplates();
      });
    });
  }
}
