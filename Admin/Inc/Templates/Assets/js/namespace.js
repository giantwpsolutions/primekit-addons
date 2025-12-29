(function ($, elementor) {
  "use strict";
  window.primekitNamespace = {
    selectedCategory: "all",
    selectedType: "all",
    searchQuery: "",
    templatesPerPage: 21,
    currentPage: 1,

    loadTemplates() {
      const modalContent = document.getElementById(
        "primekit-templates-modal-content"
      );
      if (!modalContent) {
        console.error("Modal content element not found.");
        return;
      }

      // Clear existing content
      modalContent.innerHTML = "";

      // Fetch templates using WordPress site URL
      const siteUrl = "https://demo.primekitaddons.com/";
      fetch(`${siteUrl}/wp-json/primekit/v1/templates`)
        .then((response) => {
          if (!response.ok) {
            throw new Error("Failed to fetch templates.");
          }
          return response.json();
        })
        .then((data) => {
          console.log("Templates loaded:", data);

          // Hide loading spinner
          this.hideLoadingSpinner();

          this.templates = data; //  Store globally for filtering
          this.renderTemplates(data); // move rendering into a dedicated method
        })
        .catch((error) => {
          console.error("Error loading templates:", error);

          // Hide loading spinner
          this.hideLoadingSpinner();

          modalContent.innerHTML = `<p>Failed to load templates. Please try again later.</p>`;
        });
    },

    showModal() {
      console.log("showModal called");
      const modalElement = document.getElementById("primekit-template-modal");
      console.log("Modal element:", modalElement);
      if (!modalElement) {
        console.error("Modal element not found.");
        return;
      }
      this.selectedCategory = "all";
      this.selectedType = "all";
      this.searchQuery = "";

      console.log("Showing modal with fadeIn");
      // Show the modal using jQuery
      jQuery(modalElement).css('display', 'block').fadeIn(300).attr('aria-hidden', 'false');

      // Show loading spinner
      this.showLoadingSpinner();

      // Load templates when the modal is opened
      this.loadTemplates();

      // Wait briefly to ensure modal is fully in DOM
      setTimeout(() => {
        loadTemplateCategories();
        // Bind click event to insert button
        this.bindSearchInput(); // Bind search input event
      }, 100); // Call the function to load template categories

      // Bind close events
      this.bindCloseEvents();
    },

    bindCloseEvents() {
      const modalElement = document.getElementById("primekit-template-modal");
      if (!modalElement) return;

      // Close on overlay click
      jQuery(modalElement).find('.modal__overlay').off('click').on('click', function(e) {
        if (e.target === this) {
          primekitNamespace.closeModal();
        }
      });

      // Close on close button click
      jQuery(modalElement).find('.modal__close').off('click').on('click', function(e) {
        e.preventDefault();
        primekitNamespace.closeModal();
      });

      // Close on ESC key
      jQuery(document).off('keyup.primekitmodal').on('keyup.primekitmodal', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
          primekitNamespace.closeModal();
        }
      });
    },

    closeModal() {
      const modalElement = document.getElementById("primekit-template-modal");
      if (modalElement) {
        // Remove focus from any element inside the modal before closing
        if (document.activeElement && modalElement.contains(document.activeElement)) {
          document.activeElement.blur();
        }

        jQuery(modalElement).fadeOut(300, function() {
          jQuery(this).attr('aria-hidden', 'true');
        });
        jQuery(document).off('keyup.primekitmodal');
      }
    },


    /** Binds input event to search field with debouncing
     * Updates searchQuery and filters templates when user types
     * Uses 200ms delay to avoid excessive filtering while typing
     **/
    bindSearchInput() {
      const input = document.getElementById("primekit-templates-search");
      if (!input) return;

      let timeout;
      input.addEventListener("input", function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
          primekitNamespace.searchQuery = this.value;
          primekitNamespace.filterTemplates();
        }, 200);
      });
    },

    /**
     * Renders template items in the modal content area
     * @param {Array} templates - Array of template objects to render
     * Each template object should contain:
     * - thumbnail: URL of template preview image
     * - title: Template name/title
     * - id: Unique template identifier
     *
     * If templates array is empty, displays "No templates found" message
     * Otherwise generates HTML for each template with image, title and insert button
     */
    renderTemplates(templates) {
      const modalContent = document.getElementById(
        "primekit-templates-modal-content"
      );
      if (!modalContent) return;

      if (!Array.isArray(templates) || templates.length === 0) {
        modalContent.innerHTML = "<p>No templates found.</p>";
        return;
      }

      const start = (this.currentPage - 1) * this.templatesPerPage;
      const end = start + this.templatesPerPage;
      const paginated = templates.slice(start, end);

      let templateHTML = "";
      paginated.forEach((template) => {
        // Check if template is less than a month old
        const isNew = template.modified_at
          ? (() => {
              const createdDate = new Date(
                template.modified_at.replace(" ", "T")
              );
              const currentDate = new Date();
              const diffTime = currentDate - createdDate;
              return diffTime < 30 * 24 * 60 * 60 * 1000;
            })()
          : false;
        // Determine version display - Hidden
        let versionDisplay = "";
        const proIcon = `
        <p title="This is a premium template available exclusively in PrimeKit Pro" class="primekit-template-pro">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 30 30" fill="none">
            <rect width="30" height="30" rx="15" fill="url(#paint0_linear_10_17)"></rect>
            <path d="M8.81031 17.2931C8.54203 15.5492 8.27374 13.8054 8.00546 12.0615C7.94596 11.6749 8.38581 11.4113 8.69868 11.6459C9.53454 12.2728 10.3704 12.8997 11.2062 13.5266C11.4814 13.733 11.8735 13.6658 12.0643 13.3796L14.1519 10.2482C14.3725 9.91727 14.8587 9.91727 15.0793 10.2482L17.1669 13.3796C17.3577 13.6658 17.7498 13.7329 18.025 13.5266C18.8608 12.8997 19.6966 12.2728 20.5325 11.6459C20.8454 11.4113 21.2852 11.6749 21.2258 12.0615C20.9575 13.8054 20.6892 15.5492 20.4209 17.2931H8.81031Z" fill="white"></path>
            <path d="M19.8158 20.1957H9.41587C9.08157 20.1957 8.81055 19.9247 8.81055 19.5904V18.2606H20.4212V19.5904C20.4211 19.9247 20.1501 20.1957 19.8158 20.1957Z" fill="white"></path>
            <defs>
              <linearGradient id="paint0_linear_10_17" x1="6.96174e-06" y1="14.9999" x2="30.0001" y2="14.9999" gradientUnits="userSpaceOnUse">
                <stop stop-color="#0049E7"></stop>
                <stop offset="1" stop-color="#C835F8"></stop>
              </linearGradient>
            </defs>
          </svg>
        </p>
        `;

        const freeIcon = `
        <p title="This template is available for all PrimeKit users" class="primekit-template-free">
          <svg
            id="fi_6188570"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 500 500"
            width="40"
            height="40"
          >
            <path 
              d="M263.465 21.646c11.491-7.252 26.547-5.088 35.529 5.108 7.052 8.005 18.078 11.243 28.339 8.321 13.068-3.721 26.905 2.598 32.65 14.911 4.511 9.668 14.178 15.88 24.846 15.968 13.587.111 25.083 10.072 27.127 23.506 1.605 10.547 9.13 19.232 19.341 22.321 13.006 3.935 21.229 16.731 19.406 30.196-1.432 10.572 3.342 21.025 12.269 26.866 11.37 7.44 15.656 22.034 10.113 34.44-4.352 9.74-2.717 21.115 4.203 29.234 8.814 10.342 8.814 25.552 0 35.894-6.92 8.12-8.555 19.494-4.203 29.234 5.543 12.406 1.258 27.001-10.113 34.44-8.927 5.841-13.701 16.294-12.269 26.866 1.824 13.465-6.4 26.261-19.406 30.196-10.211 3.089-17.737 11.774-19.341 22.321-2.044 13.433-13.54 23.394-27.127 23.506-10.668.087-20.335 6.3-24.846 15.968-5.746 12.313-19.582 18.632-32.65 14.911-10.26-2.922-21.286.316-28.339 8.321-8.982 10.196-24.038 12.361-35.529 5.108-9.022-5.694-20.513-5.694-29.535 0-11.491 7.252-26.547 5.087-35.529-5.108-7.052-8.005-18.078-11.243-28.339-8.321-13.068 3.721-26.905-2.598-32.65-14.911-4.511-9.668-14.178-15.88-24.846-15.968-13.587-.111-25.083-10.072-27.127-23.506-1.605-10.547-9.13-19.232-19.341-22.321-13.006-3.935-21.229-16.731-19.406-30.196 1.432-10.572-3.342-21.025-12.269-26.866-11.37-7.44-15.656-22.034-10.113-34.44 4.352-9.74 2.717-21.115-4.203-29.234-8.814-10.342-8.814-25.553 0-35.894 6.92-8.12 8.555-19.494 4.203-29.234-5.543-12.406-1.258-27.001 10.113-34.44 8.927-5.841 13.701-16.294 12.269-26.866-1.824-13.465 6.4-26.261 19.406-30.196 10.211-3.089 17.737-11.774 19.341-22.321 2.044-13.433 13.54-23.394 27.127-23.506 10.668-.087 20.335-6.3 24.846-15.968 5.746-12.313 19.582-18.632 32.65-14.911 10.26 2.922 21.287-.316 28.339-8.321 8.982-10.196 24.038-12.36 35.529-5.108 9.022 5.694 20.513 5.694 29.535 0z"
              fill="#0fa50f"
            />
            <g fill="#fff">
              <path d="M100.953 214.849v32.207h38.97v19.323h-38.97v38.648h-22.223v-109.502h69.889v19.324z" />
              <path d="M164.238 195.525h42.513c25.766 0 38.648 12.025 38.648 36.071 0 14.279-5.369 24.799-16.103 31.562l15.62 41.869h-24.316l-12.722-36.716h-21.418v36.716h-22.223v-109.502zm42.835 53.785c5.045 0 8.91-1.582 11.594-4.75 2.682-3.166 4.026-7.433 4.026-12.803 0-5.367-1.396-9.581-4.187-12.641-2.792-3.06-6.71-4.589-11.755-4.589h-20.29v34.783z" />
              <path d="M286.3 214.849v25.604h38.971v19.001h-38.971v26.248h48.633v19.324h-70.855v-109.501h70.855v19.324z" />
              <path d="M375.51 214.849v25.604h38.971v19.001h-38.971v26.248h48.633v19.324h-70.855v-109.501h70.855v19.324z" />
            </g>
          </svg>
        </p>
        `;

        const icon = template.is_pro ? proIcon : freeIcon;
        const baseUrl = primekitTemplates.pluginUrl.replace(/\/$/, '');
        const loaderIcon = baseUrl + '/Admin/Assets/img/Icon.png';

        templateHTML += `
          <div class="primekit-template">
          <div class="primekit-template-info">
          ${versionDisplay}
          ${icon}
          </div>
            <div class="primekit-template-image-wrapper">
              <div class="primekit-image-loader">
                <img src="${loaderIcon}" alt="Loading..." class="primekit-loader-spinner">
              </div>
              <img src="${template.thumbnail}" alt="${template.title}" class="primekit-template-thumbnail" onload="this.parentElement.querySelector('.primekit-image-loader').style.display='none'" onerror="this.parentElement.querySelector('.primekit-image-loader').style.display='none'">
            </div>
            <div class="primekit-template-content">
              <h3>${template.title}</h3>
              <div class="primekit-templates-buttons">
                <button class="primekit-template-insert" data-template-id="${template.id}">
                  Insert
                </button>
                <button class="primekit-template-preview" data-template-id="${template.id}">
                  <a href="${template.demo_url}" target="_blank">
                    Preview
                  </a>
                </button>
              </div>
            </div>
          </div>
        `;
      });

      if (this.currentPage === 1) {
        modalContent.innerHTML = ""; // Clear only on first render
      }

      modalContent.insertAdjacentHTML("beforeend", templateHTML);

      if (end < templates.length) {
        const loadMoreBtn = document.createElement("button");
        loadMoreBtn.textContent = "Load More";
        loadMoreBtn.className = "primekit-load-more";

        modalContent.appendChild(loadMoreBtn); // Append first

        const observer = new IntersectionObserver((entries) => {
          console.log("Observer triggered", entries[0].isIntersecting);
          if (entries[0].isIntersecting) {
            this.currentPage++;
            observer.disconnect(); // Avoid multiple triggers
            loadMoreBtn.remove(); // Optional: remove old button
            this.renderTemplates(templates); // Load next set
          }
        });

        observer.observe(loadMoreBtn);
      }
    },
    /**
     * Filters templates based on selected type, category and search query
     * Applies three levels of filtering:
     * 1. By template type if selectedType is not "all"
     * 2. By category if selectedCategory is not "all"
     * 3. By search query if searchQuery is not empty
     *
     * Uses the stored this.templates array as source
     * Calls renderTemplates() with filtered results
     */
    filterTemplates() {
      let filtered = this.templates;

      if (this.selectedType !== "all") {
        filtered = filtered.filter((tpl) => tpl.type === this.selectedType);
      }

      if (this.selectedCategory !== "all") {
        filtered = filtered.filter(
          (tpl) =>
            Array.isArray(tpl.categories) &&
            tpl.categories.some(
              (cat) => cat.toLowerCase() === this.selectedCategory
            )
        );
      }

      if (this.searchQuery.trim() !== "") {
        const q = this.searchQuery.toLowerCase();
        filtered = filtered.filter((tpl) =>
          tpl.title.toLowerCase().includes(q)
        );
      }
      this.currentPage = 1;
      this.renderTemplates(filtered);
    },

    insertTemplate(templateId) {
      console.log(`Inserting template with ID: ${templateId}`);

      // Show insertion loader
      this.showInsertionLoader();

      // Use Elementor's built-in template import via the library manager
      elementor.templates.requestTemplateContent('primekit-library', templateId, {
        data: {},
        success: (data) => {
          console.log("Template content loaded:", data);

          // Insert template content directly into the page
          if (data.content && Array.isArray(data.content)) {
            // Import each element from the template
            data.content.forEach((element) => {
              elementor.getPreviewView().addChildModel(element);
            });

            console.log("Template inserted successfully.");

            // Hide loader and close modal
            this.hideInsertionLoader();
            this.closeModal();
          } else {
            console.error("Invalid template data structure");
            this.hideInsertionLoader();
            alert("Invalid template data. Please try again.");
          }
        },
        error: (error) => {
          console.error("Error loading template:", error);
          this.hideInsertionLoader();
          alert("Failed to load template. Please check your internet connection and try again.");
        }
      });
    },

    showInsertionLoader() {
      const baseUrl = primekitTemplates.pluginUrl.replace(/\/$/, '');
      const loaderIcon = baseUrl + '/Admin/Assets/img/Icon.png';

      // Find the modal container element (the actual modal box)
      const modalContainer = document.querySelector('#primekit-template-modal .modal__container');
      if (!modalContainer) {
        console.error('Modal container not found');
        return;
      }

      const loaderHTML = `
        <div class="primekit-insertion-loader-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.98); display: flex; align-items: center; justify-content: center; z-index: 9999999;">
          <div style="text-align: center;">
            <img src="${loaderIcon}" alt="Inserting template..." style="width: 120px; height: 120px; animation: primekit-spin 1s linear infinite; margin: 0 auto 25px; display: block;">
            <p style="margin: 0; font-size: 20px; color: #333; font-weight: 600; letter-spacing: 0.5px;">Inserting template...</p>
          </div>
        </div>
        <style>
          @keyframes primekit-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
          }
        </style>
      `;

      // Add loader to the modal container (the actual modal box)
      modalContainer.insertAdjacentHTML('beforeend', loaderHTML);
      console.log('Insertion loader added to modal container');
    },

    hideInsertionLoader() {
      const loader = document.querySelector('.primekit-insertion-loader-overlay');
      if (loader) {
        loader.remove();
      }
    },

    showLoadingSpinner() {
      const baseUrl = primekitTemplates.pluginUrl.replace(/\/$/, '');
      const loaderIcon = baseUrl + '/Admin/Assets/img/Icon.png';

      // Find the modal container element (the actual modal box)
      const modalContainer = document.querySelector('#primekit-template-modal .modal__container');
      if (!modalContainer) {
        console.error('Modal container not found');
        return;
      }

      const loaderHTML = `
        <div class="primekit-loading-spinner-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.98); display: flex; align-items: center; justify-content: center; z-index: 9999999;">
          <div style="text-align: center;">
            <img src="${loaderIcon}" alt="Loading templates..." style="width: 120px; height: 120px; animation: primekit-spin 1s linear infinite; margin: 0 auto 25px; display: block;">
            <p style="margin: 0; font-size: 20px; color: #333; font-weight: 600; letter-spacing: 0.5px;">Loading templates...</p>
          </div>
        </div>
        <style>
          @keyframes primekit-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
          }
        </style>
      `;

      // Add loader to the modal container (the actual modal box)
      modalContainer.insertAdjacentHTML('beforeend', loaderHTML);
      console.log('Loading spinner added to modal container');
    },

    hideLoadingSpinner() {
      const loader = document.querySelector('.primekit-loading-spinner-overlay');
      if (loader) {
        loader.remove();
      }
    },

    transformElementorContent(content) {
      if (!content || !Array.isArray(content)) {
        console.error("Invalid content structure for Elementor.");
        return null;
      }

      try {
        return content.map((element) => this.transformElement(element));
      } catch (error) {
        console.error("Error transforming content:", error);
        return null;
      }
    },

    transformElement(element) {
      if (!element || typeof element !== "object") {
        throw new Error("Invalid element structure");
      }

      const transformedElement = {
        id: element.id || elementor.helpers.getUniqueID(),
        elType: element.elType || "section",
        settings: element.settings || {},
        elements: [],
      };

      if (element.widgetType) {
        transformedElement.widgetType = element.widgetType;
      }

      if (Array.isArray(element.elements)) {
        transformedElement.elements = element.elements.map((child) =>
          this.transformElement(child)
        );
      }

      return transformedElement;
    },
  };
})(window.jQuery, window.elementor);
