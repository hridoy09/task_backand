(function($) {
    $(document).ready(function() {
        $('select.select2').each(function() {
            var $this = $(this);

            $this.select2({
                placeholder: $this.data('placeholder'),
                minimumResultsForSearch: $this.data('minimum-results-for-search') || 10
            });
        });
        
        // -- table responsive -- //
        $('.table').each(function () {
            let $table = $(this);
            let headers = [];

            // collect th texts
            $table.find('thead th').each(function (i, th) {
                headers[i] = $(th).text().trim();
            });

            // assign as data-label
            $table.find('tbody tr').each(function () {
                $(this).find('td').each(function (i, td) {
                    $(td).attr('data-label', headers[i]);
                });
            });
        });
        
        // --- DATA COLLECTION ---
        const navLinks = [];
        $('#sidebar .list-unstyled.components > li > a').each(function() {
            const link = $(this);
            const href = link.attr('href');
            
            // Extract icon class from the first <i> or <svg>
            const iconElement = link.find('i, svg').first();
            const iconHtml = iconElement.length ? iconElement.prop('outerHTML') : '<i class="fas fa-link"></i>'; // Default icon
            
            // For main menu items
            if (href && href !== '#' && !link.hasClass('dropdown-toggle')) {
                const text = link.find('span').text().trim();
                if (text) {
                     navLinks.push({ href, text, iconHtml });
                }
            }
            
            // For submenu items
            const submenu = link.next('ul.collapse');
            if (submenu.length) {
                submenu.find('a').each(function() {
                    const subLink = $(this);
                    const subHref = subLink.attr('href');
                    const subText = subLink.text().trim().replace(/\s+/g, ' ');
                    if (subHref && subHref !== '#') {
                        navLinks.push({ href: subHref, text: subText, iconHtml });
                    }
                });
            }
        });

        // Include settings sidebar links injected via JSON
        const settingsDataEl = document.getElementById('spotlight-settings-data');
        if (settingsDataEl) {
            try {
                const extraLinks = JSON.parse(settingsDataEl.getAttribute('data-links') || '[]');
                extraLinks.forEach(item => {
                    if (item.href && item.text) {
                        navLinks.push({
                            href: item.href,
                            text: item.text,
                            iconHtml: item.iconHtml || '<i class="fas fa-cog"></i>',
                        });
                    }
                });
            } catch (error) {
                console.warn('Unable to parse settings spotlight data', error);
            }
        }

        // --- ELEMENT SELECTION ---
        const spotlightModal = new bootstrap.Modal($('#spotlightModal'));
        const searchInput    = $('#spotlight-search');
        const resultsList    = $('#spotlight-results');
        let   activeIndex    = -1;

        // --- CORE FUNCTIONS ---
        function renderResults(query) {
            resultsList.empty();

            const emptyStateHtml = `
                <div class="spotlight-empty-search text-center py-5">
                    <div class="d-flex justify-content-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="none" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="text-secondary opacity-50">
                            <circle cx="30" cy="30" r="12" />
                            <line x1="42" y1="42" x2="56" y2="56" />
                        </svg>
                    </div>
                    <p class="text-muted fs-6">${$('#spotlightModal').attr('data-empty-state')}</p>
                </div>
            `;
            
            if (!query.trim()) {
                activeIndex = -1;
                $('#spotlightModal').find('.list-unstyled').html(emptyStateHtml);
                return;
            }
            
            const filteredLinks = navLinks.filter(item => item.text.toLowerCase().includes(query.toLowerCase()));

            if (filteredLinks.length) {
                filteredLinks.forEach(item => {
                    const listItem = `
                        <li class="spotlight-result-item">
                            <a href="${item.href}">
                                <span class="result-icon">${item.iconHtml}</span>
                                <span class="result-text">${item.text}</span>
                            </a>
                        </li>`;
                    resultsList.append(listItem);
                });

                if (filteredLinks.length === 1) {
                    activeIndex = 0;
                } else {
                    activeIndex = -1; 
                }
                
                updateActiveSelection();
                
            } else {
                resultsList.append(emptyStateHtml);
                activeIndex = -1; 
            }
        }

        function updateActiveSelection() {
            $('.spotlight-result-item').removeClass('active');
            if (activeIndex > -1) {
                const activeItem = $('.spotlight-result-item').eq(activeIndex);
                activeItem.addClass('active');
                // Scroll the item into view if needed
                activeItem[0].scrollIntoView({ block: 'nearest' });
            }
        }

        // --- EVENT LISTENERS ---
        $('[name="admin_search"]').on('click', function(e) {
            e.preventDefault(); // Prevent any default form action
            spotlightModal.show();
        });
        
        // Open with Ctrl+K
        $(document).on('keydown', function(e) {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                spotlightModal.show();
            }
        });

        // Auto-focus input when modal opens
        $('#spotlightModal').on('shown.bs.modal', () => searchInput.focus());

        // Cleanup when modal closes
        $('#spotlightModal').on('hidden.bs.modal', () => {
            searchInput.val('');
            resultsList.empty();
        });

        // Live search on input
        searchInput.on('input', () => renderResults(searchInput.val()));

        // Keyboard navigation
        searchInput.on('keydown', function(e) {
            const items = $('.spotlight-result-item');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
                updateActiveSelection();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                updateActiveSelection();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex > -1) {
                    window.location.href = items.eq(activeIndex).find('a').attr('href');
                } else if (items.length > 0) {
                     // If no item is selected, go to the first result
                    window.location.href = items.first().find('a').attr('href');
                }
            }
        });
        
        // Mouse hover selects the item
        resultsList.on('mouseenter', '.spotlight-result-item', function() {
            activeIndex = $(this).index();
            updateActiveSelection();
        });
    });
})(jQuery);
