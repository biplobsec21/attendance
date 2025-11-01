// public/js/soldiers/performanceUtils.js

/**
 * Performance monitoring utility
 */
export class PerformanceMonitor {
    constructor() {
        this.marks = new Map();
        this.measures = new Map();
    }

    mark(name) {
        this.marks.set(name, performance.now());
    }

    measure(measureName, startMark, endMark) {
        const start = this.marks.get(startMark);
        const end = this.marks.get(endMark);

        if (start && end) {
            const duration = end - start;
            this.measures.set(measureName, duration);
            console.log(`⏱️ ${measureName}: ${duration.toFixed(2)}ms`);

            // Log warning if operation takes too long
            if (duration > 50) {
                console.warn(`⚠️ Slow operation: ${measureName} took ${duration.toFixed(2)}ms`);
            }
        }
    }

    clear() {
        this.marks.clear();
        this.measures.clear();
    }
}

/**
 * Virtual scrolling for large datasets
 */
export class VirtualScroll {
    constructor(container, itemHeight, renderCallback) {
        this.container = container;
        this.itemHeight = itemHeight;
        this.renderCallback = renderCallback;
        this.visibleItems = [];
        this.scrollTop = 0;

        this.init();
    }

    init() {
        this.container.addEventListener('scroll', this.handleScroll.bind(this));
        this.updateVisibleItems();
    }

    handleScroll() {
        this.scrollTop = this.container.scrollTop;
        requestAnimationFrame(() => this.updateVisibleItems());
    }

    updateVisibleItems() {
        const containerHeight = this.container.clientHeight;
        const startIndex = Math.floor(this.scrollTop / this.itemHeight);
        const endIndex = Math.min(
            startIndex + Math.ceil(containerHeight / this.itemHeight) + 5, // Buffer
            this.totalItems
        );

        this.renderCallback(startIndex, endIndex);
    }

    setTotalItems(total) {
        this.totalItems = total;
        this.container.style.height = `${total * this.itemHeight}px`;
        this.updateVisibleItems();
    }
}

/**
 * Memoization utility for expensive functions
 */
export function memoize(fn) {
    const cache = new Map();
    return (...args) => {
        const key = JSON.stringify(args);
        if (cache.has(key)) {
            return cache.get(key);
        }
        const result = fn(...args);
        cache.set(key, result);
        return result;
    };
}
