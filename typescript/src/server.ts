// typescript/src/server.ts
// it-vends
//
// Local development server for testing the vending machine endpoint
// Compatible with Express-like interface for local development
//

import http from 'http';
import url from 'url';
import { parseRequest, executeVend } from './handlers';

const PORT = process.env.PORT || 8080;
const HOST = process.env.HOST || 'localhost';

/**
 * Create HTTP server for local testing
 */
const server = http.createServer(async (req: http.IncomingMessage, res: http.ServerResponse) => {
    const startTime = Date.now();

    // Parse URL and query parameters
    const parsedUrl = url.parse(req.url || '/', true);
    const pathname = parsedUrl.pathname || '/';
    const queryParams = parsedUrl.query as Record<string, any>;

    // CORS headers
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    // Handle preflight requests
    if (req.method === 'OPTIONS') {
        res.writeHead(200, { 'Content-Type': 'text/plain' });
        res.end();
        return;
    }

    // Only accept GET and POST methods
    if (req.method !== 'GET' && req.method !== 'POST') {
        res.writeHead(405, { 'Content-Type': 'text/plain' });
        res.end('Method Not Allowed');
        return;
    }

    // Route handler
    if (pathname === '/' || pathname === '/vend') {
        try {
            const request = parseRequest(queryParams);
            const response = await executeVend(request, startTime);

            res.writeHead(response.statusCode, response.headers);
            res.end(response.body);
        } catch (error) {
            const errorMsg = error instanceof Error ? error.message : 'Unknown error';
            console.error('Error processing request:', errorMsg);
            res.writeHead(500, { 'Content-Type': 'text/plain' });
            res.end(`Internal Server Error: ${errorMsg}`);
        }
        return;
    }

    // Health check endpoint
    if (pathname === '/health' || pathname === '/healthz') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ status: 'healthy', uptime: process.uptime() }));
        return;
    }

    // Info endpoint
    if (pathname === '/info') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(
            JSON.stringify({
                name: 'It Vends - TypeScript Edition',
                version: '2.0.0',
                endpoints: {
                    vend: 'GET /vend?action=vend&count=1&format=text&sep=lf',
                    formats: 'GET /vend?action=formats',
                    inventory: 'GET /vend?action=inventory',
                    health: 'GET /health',
                    info: 'GET /info',
                },
                parameters: {
                    action: 'vend | give | inventory | formats (default: vend)',
                    count: 'Number of items to vend, 1-100 (default: 1)',
                    format: 'text | json | php | serial | title (default: text)',
                    sep: 'Text separator: cr, lf (default), crlf, comma, tab, br, newline',
                },
            })
        );
        return;
    }

    // 404 Not Found
    res.writeHead(404, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ error: 'Not Found', message: `No route for ${pathname}` }));
});

// Start the server
server.listen(PORT as number, HOST as string, () => {
    console.log(`🍷 It Vends - TypeScript Edition running on http://${HOST}:${PORT}`);
    console.log(`   vend endpoint: http://${HOST}:${PORT}/vend`);
    console.log(`   info endpoint: http://${HOST}:${PORT}/info`);
    console.log(`   health endpoint: http://${HOST}:${PORT}/health`);
});

// Graceful shutdown
process.on('SIGTERM', () => {
    console.log('SIGTERM received, shutting down gracefully');
    server.close(() => {
        console.log('Server closed');
        process.exit(0);
    });
});

process.on('SIGINT', () => {
    console.log('SIGINT received, shutting down gracefully');
    server.close(() => {
        console.log('Server closed');
        process.exit(0);
    });
});

export default server;
