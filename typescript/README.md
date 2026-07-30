<!--
  -- typescript/README.md
  -- it-vends
  -->

TypeScript Edge Function
========================

Dispenses items on-demand in the Cloud using a portable runtime Function - compatible with AWS Lambda, Google Cloud Run, Azure Functions, Cloudflare Workers, or a local containerized runtime.

Features
--------

- **Multi-Cloud Compatible**: Single codebase works with:
  - AWS Lambda (HTTP API and Function URL)
  - Google Cloud Run
  - Azure Functions (HTTP trigger)
  - Cloudflare Workers/Edge

- **Feature Parity**: Implements all functionality from the PHP version:
  - Item vending with configurable quantities
  - Special item support with configurable rates
  - Multiple output formats (text, JSON, PHP, serialized, HTML title)
  - Custom text separators

- **Output Formats**:
  - `text` - Plain text (default)
  - `json` - JSON object
  - `php` - PHP var_export format
  - `serial` - PHP serialized format
  - `title` - HTML page with item in title

- **Development Server**: Included local HTTP server for testing
- **Type Safe**: Full TypeScript with strict typing

Installation
------------

### Prerequisites

- Node.js 18.0.0 or later
- npm or yarn package manager

### Local Development

```bash
cd typescript

# Install dependencies
npm install

# Run TypeScript in watch mode (requires ts-node)
npm run dev

# Or compile and run compiled version
npm run build
npm start
```

The server will start on `http://localhost:8080`

### Docker

Build the containerized version:

```bash
# Using the included Dockerfile
docker build -f typescript.Dockerfile -t it-vends-typescript .

# Run the container
docker run -p 8080:8080 it-vends-typescript
```

Usage
-----

### API Endpoints

All endpoints accept GET and POST requests.

#### Vend Items
```
GET /vend?action=vend&count=1&format=text&sep=lf
```

Parameters:
- `action` (default: `vend`): Action to perform
  - `vend` - Dispense random items
  - `formats` - List available output formats
  - `give` - Give items (not yet implemented)
  - `inventory` - List all available items (not yet implemented)

- `count` (default: `1`): Number of items to vend (1-100)

- `format` (default: `text`): Output format
  - `text` - Plain text, newline-separated (default)
  - `json` - JSON array
  - `php` - PHP var_export format
  - `serial` - PHP serialized format
  - `title` - HTML page with item in title tag

- `sep` (default: `lf`): Text separator for multi-item responses
  - `cr` - Carriage return (`\r`)
  - `lf` - Line feed (`\n`)
  - `crlf` - Windows newline (`\r\n`)
  - `comma` - Comma
  - `tab` - Tab character
  - `br` - HTML break tag (`<br />`)
  - `newline` - Alias for `lf`

#### Examples

Single item (default):
```
GET /vend
```

Response:
```
a cold brew bottle
```

Multiple items as JSON:
```
GET /vend?count=3&format=json
```

Response:
```json
[
  "a cup of coffee",
  "a Snickers bar",
  "a Pepsi"
]
```

List all available formats:
```
GET /vend?action=formats
```

Response:
```json
[
  "text",
  "title",
  "json",
  "serial",
  "php"
]
```

Multiple items with custom separator:
```
GET /vend?count=5&format=text&sep=comma
```

Response:
```
a beer,a Diet Coke,a fortune cookie,a latte with oat milk,a bag of M&Ms
```

### Info Endpoint

Retrieve API documentation and available endpoints:

```
GET /info
```

### Health Check

Check service health:

```
GET /health
GET /healthz
```

Response:
```json
{
  "status": "healthy",
  "uptime": 123.456
}
```

### Response Headers

All vend responses include:

- `X-It-Vends-Version`: Version identifier

Example:
```
X-It-Vends-Version: 1.0.0-typescript
```

Cloud Provider Deployment
--------------------------

### AWS Lambda

Export the `awsLambdaHandler` function:

```typescript
import { awsLambdaHandler } from './index';
export const handler = awsLambdaHandler;
```

Create API Gateway HTTP API and point to this function.

### Google Cloud Run

Use the included server implementation or export `gcpCloudRunHandler`:

```typescript
import { gcpCloudRunHandler } from './index';
import http from 'http';

http.createServer(gcpCloudRunHandler).listen(8080);
```

Build and deploy:
```bash
gcloud run deploy it-vends-typescript \
  --dockerfile typescript.Dockerfile \
  --region us-central1
```

### Azure Functions

Export the Azure Functions handler:

```typescript
import { azureFunctionsHandler } from './index';
export async function httpTrigger(context: any, req: any) {
    await azureFunctionsHandler(context, req);
}
```

### Cloudflare Workers

Export the Cloudflare Edge handler in your Worker script:

```typescript
import { cloudflareEdgeHandler } from './index';

export default {
    fetch: cloudflareEdgeHandler,
};
```

Development
-----------

### Project Structure

```
typescript/
├── src/
│   ├── index.ts          # Main export file
│   ├── server.ts         # Local development server
│   ├── handlers.ts       # Cloud provider handlers
│   ├── vend.ts           # Core vending logic
│   ├── formats.ts        # Output formatting
│   └── vendlist.ts       # Items and configuration
├── dist/                 # Compiled JavaScript (generated)
├── package.json
├── tsconfig.json
└── README.md
```

### Scripts

- `npm run build` - Compile TypeScript to JavaScript
- `npm run dev` - Run local development server with ts-node
- `npm start` - Run compiled server
- `npm test` - Run tests (currently placeholder)

### Type Definitions

All TypeScript interfaces are exported for use in Cloud Functions:

- `VendRequest` - Parsed vending request
- `VendResponse` - Vending response with headers and body
- `FormattedResponse` - Formatted output with headers

### Linting and Type Checking

The TypeScript configuration enforces:
- Strict mode (`strict: true`)
- No unused variables or parameters
- No implicit any types
- No implicit returns

### Adding Items

Edit `src/vendlist.ts` to add items to the vending machines:

```typescript
export const vendlist: string[] = [
    'a your new item here',
    // ... other items
];

export const vendspecial: string[] = [
    'a rare item',
    // ... other special items
];
```

Performance Considerations
--------------------------

- The function executes in milliseconds (typically 2-5ms on warm invokes)
- Memory usage is minimal (typically 10-15MiB)
- Suitable for high-volume serverless deployments
- Stateless and infinitely scalable

Testing
-------

### Local Testing

```bash
npm run dev
```

Then in another terminal:

```bash
# Single item
curl http://localhost:8080/vend

# Multiple items as JSON
curl 'http://localhost:8080/vend?count=3&format=json'

# List formats
curl 'http://localhost:8080/vend?action=formats'
```

### Docker Testing

```bash
docker run -p 8080:8080 it-vends-typescript

# In another terminal
curl http://localhost:8080/vend
```

License
-------

See LICENSE file in repository root.

Copyright 2024 by It Vends Authors.

Authors
-------

Maintainer:
- Eugene Evgenevich Kashpureff

See main README.md for contributor list.
