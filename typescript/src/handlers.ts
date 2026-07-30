// typescript/src/handlers.ts
// it-vends
//
// Cloud provider-specific request/response handlers
//

import { vend } from './vend';
import { format } from './formats';
import { formats } from './vendlist';

/**
 * Unified request context for all cloud providers
 */
export interface VendRequest {
    action: string;
    count: number;
    format: string;
    separator: string;
}

/**
 * Unified response context
 */
export interface VendResponse {
    statusCode: number;
    body: string;
    headers: Record<string, string>;
}

/**
 * Parse incoming request and extract vending parameters
 * @param queryParams - Query parameters object
 * @returns VendRequest object
 */
export function parseRequest(queryParams: Record<string, any>): VendRequest {
    const action = String(queryParams.action || 'vend').toLowerCase();
    const count = Math.max(0, Math.min(parseInt(String(queryParams.count || '1'), 10) || 1, 100));
    const requestFormat = String(queryParams.format || 'text').toLowerCase();
    const separator = String(queryParams.sep || 'lf').toLowerCase();

    return {
        action,
        count,
        format: (formats as string[]).includes(requestFormat) ? requestFormat : 'text',
        separator,
    };
}

/**
 * Execute vending request and generate response
 * @param request - VendRequest object
 * @returns Promise<VendResponse> object
 */
export async function executeVend(request: VendRequest): Promise<VendResponse> {
    let responseData: string | string[] | Record<string, any>;
    const headers: Record<string, string> = {
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'X-It-Vends-Version': '2.0.0-typescript',
    };

    // Process action
    switch (request.action) {
        case 'formats':
            // List valid output formats
            responseData = formats;
            break;

        case 'give':
            // Item giving not yet supported
            responseData = 'Item giving is currently not supported. Sorry';
            break;

        case 'inventory':
            // List items in the machine - not implemented yet
            responseData = 'Inventory listing is not yet implemented';
            break;

        case 'vend':
        default:
            // IT VENDS!
            responseData = await vend(request.count);
            break;
    }

    // Format response
    const formatted = format(responseData, request.format, request.separator);

    // Merge headers
    const allHeaders = { ...headers, ...formatted.headers };

    return {
        statusCode: 200,
        body: formatted.body,
        headers: allHeaders,
    };
}

/**
 * Handle AWS Lambda request
 * AWS Lambda handler signature for HTTP API
 */
export async function awsLambdaHandler(event: any): Promise<any> {
    const queryParams = event.queryStringParameters || event.query || {};

    const request = parseRequest(queryParams);
    const response = await executeVend(request);

    return {
        statusCode: response.statusCode,
        headers: response.headers,
        body: response.body,
        isBase64Encoded: false,
    };
}

/**
 * Handle Google Cloud Run request
 * Cloud Run uses standard Node.js http.IncomingMessage and http.ServerResponse
 */
export async function gcpCloudRunHandler(req: any, res: any): Promise<void> {
    const url = new URL(req.url || '', `http://${req.headers.host || 'localhost'}`);
    const queryParams: Record<string, any> = {};

    url.searchParams.forEach((value, key) => {
        queryParams[key] = value;
    });

    const request = parseRequest(queryParams);
    const response = await executeVend(request);

    res.statusCode = response.statusCode;
    Object.entries(response.headers).forEach(([key, value]) => {
        res.setHeader(key, value);
    });
    res.end(response.body);
}

/**
 * Handle Azure Functions request
 * Azure Functions HTTP trigger context
 */
export async function azureFunctionsHandler(context: any, req: any): Promise<void> {
    const queryParams = req.query || {};

    const request = parseRequest(queryParams);
    const response = await executeVend(request);

    context.res = {
        status: response.statusCode,
        headers: response.headers,
        body: response.body,
    };
}

/**
 * Handle Cloudflare Edge request
 * Cloudflare Workers use the Fetch API
 */
export async function cloudflareEdgeHandler(request: Request): Promise<Response> {
    const url = new URL(request.url);
    const queryParams: Record<string, any> = {};

    url.searchParams.forEach((value, key) => {
        queryParams[key] = value;
    });

    const vendRequest = parseRequest(queryParams);
    const response = await executeVend(vendRequest);

    return new Response(response.body, {
        status: response.statusCode,
        headers: response.headers,
    });
}
