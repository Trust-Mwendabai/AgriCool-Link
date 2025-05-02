/**
 * Filter Manager for AgriCool Link Marketplace
 * Manages filter history and saved preferences
 */

class FilterManager {
    constructor() {
        // Initialize properties
        this.storageKey = 'agricool_filter_history';
        this.maxHistoryItems = 5;
        this.currentFilter = {};
        
        // Initialize on page load
        this.init();
    }
    
    /**
     * Initialize the Filter Manager
     */
    init() {
        // Get current filter parameters from URL
        this.currentFilter = this.getFilterFromURL();
        
        // Only save to history if we have active filters
        if (this.hasActiveFilters()) {
            this.saveToHistory();
        }
        
        // Render history in the modal
        this.renderFilterHistory();
        
        // Set up event listeners
        this.setupEventListeners();
    }
    
    /**
     * Get current filter parameters from URL
     */
    getFilterFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const filter = {};
        
        // Extract all filter parameters
        if (urlParams.has('category')) filter.category = urlParams.get('category');
        if (urlParams.has('search')) filter.search = urlParams.get('search');
        if (urlParams.has('location')) filter.location = urlParams.get('location');
        if (urlParams.has('farmer')) filter.farmer = urlParams.get('farmer');
        if (urlParams.has('price_range')) filter.price_range = urlParams.get('price_range');
        if (urlParams.has('storage_status')) filter.storage_status = urlParams.get('storage_status');
        if (urlParams.has('freshness')) filter.freshness = urlParams.get('freshness');
        if (urlParams.has('sort')) filter.sort = urlParams.get('sort');
        
        // Add timestamp and generate label
        filter.timestamp = new Date().toISOString();
        filter.label = this.generateFilterLabel(filter);
        
        return filter;
    }
    
    /**
     * Check if current filter has any active parameters
     */
    hasActiveFilters() {
        return Object.keys(this.currentFilter).length > 2; // More than just timestamp and label
    }
    
    /**
     * Save current filter to history
     */
    saveToHistory() {
        // Get existing history
        let history = this.getFilterHistory();
        
        // Check if this filter is already in history (avoid duplicates)
        const isDuplicate = history.some(item => this.isEqualFilter(item, this.currentFilter));
        
        if (!isDuplicate) {
            // Add current filter to history
            history.unshift(this.currentFilter);
            
            // Limit history size
            if (history.length > this.maxHistoryItems) {
                history = history.slice(0, this.maxHistoryItems);
            }
            
            // Save updated history
            localStorage.setItem(this.storageKey, JSON.stringify(history));
        }
    }
    
    /**
     * Compare two filters for equality (ignoring timestamp)
     */
    isEqualFilter(filter1, filter2) {
        const keys = ['category', 'search', 'location', 'farmer', 'price_range', 
                     'storage_status', 'freshness', 'sort'];
        
        return keys.every(key => {
            // Both undefined or null or empty
            if (!filter1[key] && !filter2[key]) return true;
            // Same values
            return filter1[key] === filter2[key];
        });
    }
    
    /**
     * Get filter history from localStorage
     */
    getFilterHistory() {
        const history = localStorage.getItem(this.storageKey);
        return history ? JSON.parse(history) : [];
    }
    
    /**
     * Generate a human-readable label for the filter
     */
    generateFilterLabel(filter) {
        const parts = [];
        
        if (filter.category) parts.push('Category');
        if (filter.search) parts.push(`"${filter.search}"`);
        if (filter.location) parts.push(filter.location);
        if (filter.farmer) parts.push('Farmer');
        if (filter.price_range) parts.push('Price');
        if (filter.storage_status && filter.storage_status !== 'any') parts.push('Storage');
        if (filter.freshness && filter.freshness !== 'any') parts.push('Fresh');
        
        if (parts.length === 0) return 'All Products';
        return parts.join(' + ');
    }
    
    /**
     * Render filter history in the modal
     */
    renderFilterHistory() {
        const historyContainer = document.getElementById('filterHistory');
        if (!historyContainer) return;
        
        const history = this.getFilterHistory();
        
        // Clear container
        historyContainer.innerHTML = '';
        
        // If no history, show message
        if (history.length === 0) {
            historyContainer.innerHTML = '<p class="text-muted mb-0">No recent filters</p>';
            return;
        }
        
        // Create filter history items
        history.forEach((filter, index) => {
            const filterUrl = this.buildFilterUrl(filter);
            const dateStr = this.formatDate(filter.timestamp);
            
            const filterItem = document.createElement('div');
            filterItem.className = 'filter-history-item';
            filterItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <a href="${filterUrl}" class="filter-history-link">
                        <span class="filter-label">${filter.label}</span>
                        <small class="text-muted d-block">${dateStr}</small>
                    </a>
                    <div class="filter-actions">
                        <button class="btn btn-sm btn-outline-success save-filter" data-index="${index}" title="Save filter">
                            <i class="bi bi-bookmark"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger remove-filter" data-index="${index}" title="Remove from history">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            `;
            
            historyContainer.appendChild(filterItem);
        });
    }
    
    /**
     * Format date for display
     */
    formatDate(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        
        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins} ${diffMins === 1 ? 'minute' : 'minutes'} ago`;
        
        const diffHours = Math.floor(diffMins / 60);
        if (diffHours < 24) return `${diffHours} ${diffHours === 1 ? 'hour' : 'hours'} ago`;
        
        const diffDays = Math.floor(diffHours / 24);
        if (diffDays < 7) return `${diffDays} ${diffDays === 1 ? 'day' : 'days'} ago`;
        
        return date.toLocaleDateString();
    }
    
    /**
     * Build URL for a filter
     */
    buildFilterUrl(filter) {
        const url = new URL(window.location.origin + window.location.pathname);
        const params = new URLSearchParams();
        
        if (filter.category) params.append('category', filter.category);
        if (filter.search) params.append('search', filter.search);
        if (filter.location) params.append('location', filter.location);
        if (filter.farmer) params.append('farmer', filter.farmer);
        if (filter.price_range) params.append('price_range', filter.price_range);
        if (filter.storage_status) params.append('storage_status', filter.storage_status);
        if (filter.freshness) params.append('freshness', filter.freshness);
        if (filter.sort) params.append('sort', filter.sort);
        
        url.search = params.toString();
        return url.toString();
    }
    
    /**
     * Set up event listeners
     */
    setupEventListeners() {
        document.addEventListener('click', (event) => {
            // Remove filter from history
            if (event.target.closest('.remove-filter')) {
                const index = event.target.closest('.remove-filter').dataset.index;
                this.removeFilterFromHistory(index);
                event.preventDefault();
                return;
            }
            
            // Save filter to favorites
            if (event.target.closest('.save-filter')) {
                const index = event.target.closest('.save-filter').dataset.index;
                this.saveFilterToFavorites(index);
                event.preventDefault();
                return;
            }
        });
    }
    
    /**
     * Remove filter from history
     */
    removeFilterFromHistory(index) {
        let history = this.getFilterHistory();
        history.splice(index, 1);
        localStorage.setItem(this.storageKey, JSON.stringify(history));
        this.renderFilterHistory();
    }
    
    /**
     * Save filter to favorites
     */
    saveFilterToFavorites(index) {
        const history = this.getFilterHistory();
        const filter = history[index];
        
        // Get saved filters
        let savedFilters = this.getSavedFilters();
        
        // Check if already saved
        const isDuplicate = savedFilters.some(item => this.isEqualFilter(item, filter));
        
        if (!isDuplicate) {
            // Prompt for a custom name
            const name = prompt('Enter a name for this filter:', filter.label);
            if (!name) return;
            
            // Add to saved filters
            filter.name = name;
            savedFilters.push(filter);
            localStorage.setItem('agricool_saved_filters', JSON.stringify(savedFilters));
            
            // Update saved filters display
            this.renderSavedFilters();
            
            // Show success message
            alert('Filter saved successfully!');
        } else {
            alert('This filter is already saved.');
        }
    }
    
    /**
     * Get saved filters
     */
    getSavedFilters() {
        const savedFilters = localStorage.getItem('agricool_saved_filters');
        return savedFilters ? JSON.parse(savedFilters) : [];
    }
    
    /**
     * Render saved filters
     */
    renderSavedFilters() {
        const container = document.getElementById('savedFilters');
        if (!container) return;
        
        const savedFilters = this.getSavedFilters();
        
        // Clear container
        container.innerHTML = '';
        
        // If no saved filters, show message
        if (savedFilters.length === 0) {
            container.innerHTML = '<p class="text-muted mb-0">No saved filters</p>';
            return;
        }
        
        // Create saved filter items
        savedFilters.forEach((filter, index) => {
            const filterUrl = this.buildFilterUrl(filter);
            
            const filterItem = document.createElement('div');
            filterItem.className = 'saved-filter-item';
            filterItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <a href="${filterUrl}" class="saved-filter-link">
                        <i class="bi bi-bookmark-fill text-success me-2"></i>
                        <span class="filter-name">${filter.name}</span>
                    </a>
                    <button class="btn btn-sm btn-outline-danger remove-saved-filter" data-index="${index}" title="Remove saved filter">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(filterItem);
        });
        
        // Add event listeners for remove buttons
        document.querySelectorAll('.remove-saved-filter').forEach(button => {
            button.addEventListener('click', (event) => {
                const index = event.currentTarget.dataset.index;
                this.removeSavedFilter(index);
            });
        });
    }
    
    /**
     * Remove saved filter
     */
    removeSavedFilter(index) {
        let savedFilters = this.getSavedFilters();
        savedFilters.splice(index, 1);
        localStorage.setItem('agricool_saved_filters', JSON.stringify(savedFilters));
        this.renderSavedFilters();
    }
}

// Initialize the filter manager when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.filterManager = new FilterManager();
});
