// typescript/src/formats.ts
// it-vends
//
// Output formatting utilities supporting multiple formats
//

import { formats, textSeps } from './vendlist';

/**
 * Response headers for format output
 */
export interface ResponseHeaders {
    [key: string]: string;
}

/**
 * Formatted response with body and headers
 */
export interface FormattedResponse {
    body: string;
    headers: ResponseHeaders;
}

/**
 * Format data for output in various types
 * @param data - Data to be formatted
 * @param format - Output format to use (text, json, php, serial, title)
 * @param separator - Text separator type (for text and title formats)
 * @returns FormattedResponse with body and appropriate headers
 */
export function format(
    data: string | string[] | Record<string, any>,
    outputFormat: string = 'text',
    separator: string = 'lf'
): FormattedResponse {
    // Ensure the format we're after is supported
    const selectedFormat = (formats as string[]).includes(outputFormat) ? outputFormat : 'text';

    // Get the text separator
    const sep = (textSeps as Record<string, string>)[separator] || textSeps['lf'];

    let body = '';
    const headers: ResponseHeaders = {};

    // Generate output based on format chosen
    switch (selectedFormat) {
        case 'php':
            body = varExport(data);
            headers['Content-Type'] = 'text/plain; charset=utf-8';
            break;

        case 'serial':
            body = serialize(data);
            headers['Content-Type'] = 'text/plain; charset=utf-8';
            break;

        case 'json':
            body = JSON.stringify(data, null, 2);
            headers['Content-Type'] = 'application/json';
            break;

        case 'title':
            {
                const vend = Array.isArray(data) ? data.join(sep) : String(data);
                body = `<!DOCTYPE HTML>\n<html>\n<head>\n<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">\n<title>${escapeHtml(vend)}</title>\n</head>\n</html>`;
                headers['Content-Type'] = 'text/html; charset=UTF-8';
            }
            break;

        case 'text':
        default:
            {
                const vend = Array.isArray(data) ? data.join(sep) : String(data);
                body = vend;
                headers['Content-Type'] = 'text/plain; charset=utf-8';
            }
            break;
    }

    return { body, headers };
}

/**
 * Simple PHP var_export implementation
 * @param data - Data to export
 * @returns String representation
 */
export function varExport(data: any, indent: number = 0): string {
    const spaces = ' '.repeat(indent);
    const innerSpaces = ' '.repeat(indent + 2);

    if (data === null) {
        return 'NULL';
    }

    if (typeof data === 'boolean') {
        return data ? 'true' : 'false';
    }

    if (typeof data === 'number') {
        return String(data);
    }

    if (typeof data === 'string') {
        return `'${data.replace(/\\/g, '\\\\').replace(/'/g, "\\'")}'`;
    }

    if (Array.isArray(data)) {
        if (data.length === 0) {
            return 'array ()';
        }
        const items = data.map((item, index) => `${innerSpaces}${index} => ${varExport(item, indent + 2)}`);
        return `array (\n${items.join(',\n')},\n${spaces})`;
    }

    if (typeof data === 'object') {
        const entries = Object.entries(data);
        if (entries.length === 0) {
            return 'array ()';
        }
        const items = entries.map(
            ([key, value]) => `${innerSpaces}'${key}' => ${varExport(value, indent + 2)}`
        );
        return `array (\n${items.join(',\n')},\n${spaces})`;
    }

    return 'NULL';
}

/**
 * Simple PHP serialize implementation
 * @param data - Data to serialize
 * @returns Serialized string
 */
export function serialize(data: any): string {
    if (data === null) {
        return 'N;';
    }

    if (typeof data === 'boolean') {
        return `b:${data ? 1 : 0};`;
    }

    if (typeof data === 'number') {
        if (Number.isInteger(data)) {
            return `i:${data};`;
        }
        return `d:${data};`;
    }

    if (typeof data === 'string') {
        return `s:${data.length}:"${data}";`;
    }

    if (Array.isArray(data)) {
        let result = `a:${data.length}:{`;
        data.forEach((item, index) => {
            result += `i:${index};${serialize(item)}`;
        });
        result += '}';
        return result;
    }

    if (typeof data === 'object') {
        const entries = Object.entries(data);
        let result = `a:${entries.length}:{`;
        entries.forEach(([key, value]) => {
            result += `s:${key.length}:"${key}";${serialize(value)}`;
        });
        result += '}';
        return result;
    }

    return 'N;';
}

/**
 * Escape HTML special characters
 * @param str - String to escape
 * @returns Escaped string
 */
export function escapeHtml(str: string): string {
    const htmlEscapeMap: Record<string, string> = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    };
    return str.replace(/[&<>"']/g, (char) => htmlEscapeMap[char] || char);
}
