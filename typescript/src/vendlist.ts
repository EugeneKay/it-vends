// typescript/src/vendlist.ts
// it-vends
//
// Vendlist configuration - items are loaded dynamically from storage
// Build-time: vendlist.json is generated from vendlist/ directory
// Runtime: vendlist can be loaded from cloud storage providers
//

import { VendlistData } from './cloud-storage';

/**
 * Valid output formats
 */
export const formats: string[] = ['text', 'title', 'json', 'serial', 'php'];

/**
 * Text separators
 */
export const textSeps: Record<string, string> = {
    cr: '\r',
    lf: '\n',
    crlf: '\r\n',
    comma: ',',
    tab: '\t',
    newline: '\n',
    br: '<br />',
};

/**
 * Constants
 */
export const ITEMLIMIT = 100;
export const DEFAULT_SPECIAL_RATE = 10; // Percentage
export const EOL = '\n';

/**
 * Runtime vendlist data - loaded from storage
 */
let vendlistData: VendlistData | null = null;
let vendlistPromise: Promise<VendlistData> | null = null;

/**
 * Load vendlist from configured storage provider
 */
export async function loadVendlist(): Promise<VendlistData> {
    // Return cached version if already loaded
    if (vendlistData !== null) {
        return vendlistData;
    }

    // Return existing promise if already loading
    if (vendlistPromise !== null) {
        return vendlistPromise;
    }

    // Create loading promise
    vendlistPromise = (async () => {
        try {
            // Try to load from environment variable first (for lambda/cloud functions)
            if (process.env.VENDLIST_JSON) {
                const data = JSON.parse(process.env.VENDLIST_JSON) as VendlistData;
                vendlistData = data;
                return data;
            }

            // Try to load from cloud storage provider
            const { getConfiguredProvider } = require('./cloud-storage');
            const provider = getConfiguredProvider();
            const data = await provider.getVendlist();
            vendlistData = data;
            return data;
        } catch (error) {
            console.error('Failed to load vendlist from storage:', error);
            // Return fallback vendlist to prevent complete failure
            const fallback: VendlistData = {
                items: ['a mystery item'],
                categories: {},
                metadata: {
                    generated: new Date().toISOString(),
                    itemCount: 1,
                },
            };
            vendlistData = fallback;
            return fallback;
        } finally {
            vendlistPromise = null;
        }
    })();

    return vendlistPromise;
}

/**
 * Get all items
 */
export async function getItems(): Promise<string[]> {
    const data = await loadVendlist();
    return data.items;
}

/**
 * Get items by category
 */
export async function getItemsByCategory(category: string): Promise<string[]> {
    const data = await loadVendlist();
    return data.categories[category] || [];
}

/**
 * Get all categories
 */
export async function getCategories(): Promise<string[]> {
    const data = await loadVendlist();
    return Object.keys(data.categories);
}

/**
 * Get vendlist metadata
 */
export async function getMetadata(): Promise<VendlistData['metadata']> {
    const data = await loadVendlist();
    return data.metadata;
}

/**
 * Force reload vendlist from storage (useful for cache invalidation)
 */
export async function reloadVendlist(): Promise<VendlistData> {
    vendlistData = null;
    vendlistPromise = null;
    return loadVendlist();
}

/**
 * Set vendlist data manually (useful for testing)
 */
export function setVendlistData(data: VendlistData): void {
    vendlistData = data;
}

/**
 * Get vendlist data directly (if already loaded)
 */
export function getVendlistDataSync(): VendlistData | null {
    return vendlistData;
}

// Export types
export type { VendlistData } from './cloud-storage';
