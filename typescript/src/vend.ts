// typescript/src/vend.ts
// it-vends
//
// Core vending logic and utilities
//

import { loadVendlist, ITEMLIMIT } from './vendlist';

/**
 * Vend some items (or a single one)
 * @param qty - Number of items to return. If 0, return 1 item as string, not array
 * @returns 0-indexed array of items (minimum 1), OR string if qty == 0
 */
export async function vend(qty: number = 0): Promise<string | string[]> {
    const count = qty === 0 ? 1 : Math.min(qty, ITEMLIMIT);
    const vendedItems: string[] = [];

    // Load vendlist data
    const vendlistData = await loadVendlist();
    const availableItems = vendlistData.items;

    if (availableItems.length === 0) {
        // Fallback if no items available
        vendedItems.push('a mystery item');
        return qty === 0 ? vendedItems[0] : vendedItems;
    }

    for (let i = 0; i < count; i++) {
        // Pick a random item from the list
        vendedItems.push(availableItems[Math.floor(Math.random() * availableItems.length)]);
    }

    // Send items out
    return qty === 0 ? vendedItems[0] : vendedItems;
}

/**
 * Convert bytes to human-readable format using SI binary prefixes
 * @param bytes - Number of bytes
 * @returns Human-readable byte size string
 */
export function parseParams(params: string | Record<string, any>): Record<string, any> {
    if (typeof params === 'object') {
        return params;
    }

    const result: Record<string, any> = {};
    if (!params) return result;

    const pairs = params.split('&');
    for (const pair of pairs) {
        const [key, value] = pair.split('=');
        if (key) {
            result[decodeURIComponent(key)] = decodeURIComponent(value || '');
        }
    }
    return result;
}
