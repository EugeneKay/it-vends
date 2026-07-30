<!--
  -- typescript/CLOUD_STORAGE.md
  -- it-vends
  -->

Cloud Storage Configuration
===========================

The TypeScript Edge Function supports dynamic vendlist loading from multiple cloud storage providers. This allows you to update vending items without redeploying the function.

Build-Time Vendlist Generation
-------------------------------

At build time, the system automatically:

1. Scans the `vendlist/` directory for all `.txt` files
2. Combines items from all categories and subcategories
3. Identifies "special" items based on keywords
4. Generates a `dist/vendlist.json` file with all items organized by category

### Build-Time Process

```bash
npm run build
```

This triggers:
1. `prebuild` script: Runs `build-vendlist.ts`
   - Recursively reads all `.txt` files from `vendlist/` directory
   - Combines regular and special items
   - Outputs JSON to `dist/vendlist.json`

2. `build` script: Compiles TypeScript
   - Runs TypeScript compiler
   - Copies `vendlist.json` to output directory

### Generated Vendlist Format

```json
{
  "regular": ["item1", "item2", ...],
  "special": ["rare_item1", "rare_item2", ...],
  "categories": {
    "drinks": ["item1", "item2", ...],
    "snacks": ["item3", "item4", ...],
    ...
  },
  "metadata": {
    "generated": "2024-04-02T21:45:00.000Z",
    "itemCount": 710,
    "specialItemCount": 2
  }
}
```

Runtime Vendlist Loading
------------------------

### Local Storage (Development)

**Provider Name:** `local`

**Configuration:**
- `VENDLIST_PROVIDER=local` (default)
- `VENDLIST_PATH=/absolute/path/to/vendlist.json` (optional, defaults to searching in multiple locations)

**Search Paths (in order):**
1. `VENDLIST_PATH` environment variable
2. `{__dirname}/vendlist.json`
3. `{__dirname}/../dist/vendlist.json`
4. `{cwd}/vendlist.json`
5. `{cwd}/dist/vendlist.json`
6. `./vendlist.json`

**Usage:**
```bash
# Development (uses local dist/vendlist.json automatically)
npm start

# With custom path
VENDLIST_PATH=/etc/it-vends/vendlist.json npm start

# Via environment variable (JSON string)
VENDLIST_JSON='{"regular":["item1"],"special":[],...}' npm start
```

### AWS S3

**Provider Name:** `aws`

**Required Environment Variables:**
- `VENDLIST_PROVIDER=aws`
- `VENDLIST_BUCKET=my-vends-bucket` (required)
- `VENDLIST_PATH=vendlist.json` (optional, default: `vendlist.json`)
- `AWS_REGION=us-east-1` (optional, default: `us-east-1`)

**Dependencies:**
```bash
npm install aws-sdk
```

**IAM Policy Required:**
```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "s3:GetObject"
      ],
      "Resource": "arn:aws:s3:::my-vends-bucket/vendlist.json"
    }
  ]
}
```

**AWS Lambda Deployment:**
```bash
# Build with vendlist
npm run build

# Upload vendlist.json to S3
aws s3 cp dist/vendlist.json s3://my-vends-bucket/vendlist.json

# Set Lambda environment variables
aws lambda update-function-configuration \
  --function-name it-vends \
  --environment Variables={VENDLIST_PROVIDER=aws,VENDLIST_BUCKET=my-vends-bucket}
```

**Usage Example:**
```typescript
import { awsLambdaHandler } from 'it-vends-typescript';

export const handler = awsLambdaHandler;
```

### Google Cloud Storage (GCS)

**Provider Name:** `gcp`

**Required Environment Variables:**
- `VENDLIST_PROVIDER=gcp`
- `VENDLIST_BUCKET=my-vends-bucket` (required)
- `VENDLIST_PATH=vendlist.json` (optional, default: `vendlist.json`)
- `GCP_PROJECT_ID=my-project` (optional, uses default credentials)
- `GOOGLE_APPLICATION_CREDENTIALS=/path/to/service-account.json` (optional)

**Dependencies:**
```bash
npm install @google-cloud/storage
```

**IAM Role Required:**
```yaml
- roles/storage.objectViewer
```

**Cloud Run Deployment:**
```bash
# Create GCS bucket
gsutil mb gs://my-vends-bucket

# Upload vendlist.json
gsutil cp dist/vendlist.json gs://my-vends-bucket/vendlist.json

# Build and push Docker image
docker build -f typescript.Dockerfile -t gcr.io/my-project/it-vends:latest .
docker push gcr.io/my-project/it-vends:latest

# Deploy to Cloud Run
gcloud run deploy it-vends \
  --image gcr.io/my-project/it-vends:latest \
  --set-env-vars "VENDLIST_PROVIDER=gcp,VENDLIST_BUCKET=my-vends-bucket" \
  --region us-central1
```

**Usage Example:**
```typescript
import { gcpCloudRunHandler } from 'it-vends-typescript';
import http from 'http';

http.createServer(gcpCloudRunHandler).listen(process.env.PORT || 8080);
```

### Azure Blob Storage

**Provider Name:** `azure`

**Required Environment Variables:**
- `VENDLIST_PROVIDER=azure`
- `VENDLIST_BUCKET=my-container` (required)
- `VENDLIST_PATH=vendlist.json` (optional, default: `vendlist.json`)

**Connection Options (choose one):**
1. Connection String:
   ```
   AZURE_STORAGE_CONNECTION_STRING='DefaultEndpointsProtocol=https;AccountName=mystorageaccount;AccountKey=...;EndpointSuffix=core.windows.net'
   ```

2. Account Name and Key:
   ```
   AZURE_STORAGE_ACCOUNT_NAME=mystorageaccount
   AZURE_STORAGE_ACCOUNT_KEY=myaccountkey
   ```

**Dependencies:**
```bash
npm install @azure/storage-blob
```

**RBAC Role Required:**
- `Storage Blob Data Reader`

**Azure Functions Deployment:**
```bash
# Create storage account and container
az storage account create --name mystorageaccount --resource-group mygroup
az storage container create --name my-container --account-name mystorageaccount

# Upload vendlist.json
az storage blob upload --account-name mystorageaccount \
  --container-name my-container \
  --name vendlist.json \
  --file dist/vendlist.json

# Create function app
func azure functionapp publish it-vends --build remote

# Set environment variables
az functionapp config appsettings set \
  --name it-vends \
  --resource-group mygroup \
  --settings "VENDLIST_PROVIDER=azure" "VENDLIST_BUCKET=my-container"
```

**Usage Example:**
```typescript
import { azureFunctionsHandler } from 'it-vends-typescript';

export async function httpTrigger(context: any, req: any) {
    await azureFunctionsHandler(context, req);
}
```

### Cloudflare R2

**Provider Name:** `cloudflare`

**Required Environment Variables:**
- `VENDLIST_PROVIDER=cloudflare`
- `VENDLIST_BUCKET=my-bucket` (required)
- `VENDLIST_PATH=vendlist.json` (optional, default: `vendlist.json`)
- `CLOUDFLARE_ACCOUNT_ID=your-account-id` (required)
- `CLOUDFLARE_R2_ENDPOINT=https://{account-id}.r2.cloudflarestorage.com` (optional if ACCOUNT_ID set)
- `CLOUDFLARE_R2_ACCESS_KEY_ID=your-access-key` (required)
- `CLOUDFLARE_R2__ACCESS_KEY=your-secret-key` (required)

**Dependencies:**
```bash
npm install aws-sdk
```

**Cloudflare Workers Deployment:**
```bash
# Create R2 bucket
wrangler r2 bucket create my-bucket

# Upload vendlist.json
wrangler r2 object put my-bucket/vendlist.json --file dist/vendlist.json

# Deploy worker
wrangler deploy

# Set environment variables in wrangler.toml
```

**wrangler.toml Configuration:**
```toml
[env.production]
vars = { VENDLIST_PROVIDER = "cloudflare", VENDLIST_BUCKET = "my-bucket" }

[env.production.secrets]
CLOUDFLARE_R2_ACCESS_KEY_ID = ""  # Set via wrangler secret
CLOUDFLARE_R2_SECRET_ACCESS_KEY = ""  # Set via wrangler secret
CLOUDFLARE_ACCOUNT_ID = ""
```

**Usage Example:**
```typescript
import { cloudflareEdgeHandler } from 'it-vends-typescript';

export default {
    fetch: cloudflareEdgeHandler,
};
```

### Environment Variable Direct Loading

**Quickest Method for Small Deployments**

Pass the vendlist JSON directly via environment variable:

```bash
VENDLIST_JSON='{"regular":["item1","item2"],"special":[],"categories":{},"metadata":{"generated":"2024-04-02T00:00:00Z","itemCount":2,"specialItemCount":0}}' npm start
```

**For AWS Lambda:**
```bash
aws lambda update-function-configuration \
  --function-name it-vends \
  --environment Variables={VENDLIST_JSON=$(cat dist/vendlist.json | jq -c .)}
```

Updating Vendlist
-----------------

### Development Workflow

1. Edit `.txt` files in `vendlist/` directory
2. Re-run build:
   ```bash
   npm run build
   ```
3. Server will automatically use new `dist/vendlist.json` on next request

### Production Workflow

#### S3 Method (recommended for AWS)

```bash
# Generate new vendlist
npm run build

# Upload to S3
aws s3 cp dist/vendlist.json s3://my-vends-bucket/vendlist.json

# Lambda automatically picks up new version on next invocation
```

#### GCS Method (recommended for Google Cloud)

```bash
# Generate new vendlist
npm run build

# Upload to GCS
gsutil cp dist/vendlist.json gs://my-vends-bucket/vendlist.json

# Cloud Run automatically picks up new version on next request
```

#### Cloudflare R2 Method

```bash
# Generate new vendlist
npm run build

# Upload to R2
wrangler r2 object put my-bucket/vendlist.json --file dist/vendlist.json

# Workers automatically picks up new version on next request
```

Caching Considerations
---------------------

The vendlist is cached in memory after first load:

- First request: Loads from storage (may have latency)
- Subsequent requests: Uses cached version (milliseconds)

To force reload:

**Server/Lambda Environment Variable:**
```bash
# Set to force reload on startup
VENDLIST_FORCE_RELOAD=true
```

**Programmatically:**
```typescript
import { reloadVendlist } from 'it-vends-typescript';

// Later in code
await reloadVendlist();
```

Troubleshooting
---------------

### "Vendlist file not found" error

**Issue:** Local storage can't find vendlist.json

**Solutions:**
1. Ensure `npm run build` was executed
2. Check that `dist/vendlist.json` exists
3. Set explicit path: `VENDLIST_PATH=/absolute/path/to/vendlist.json`
4. Use environment variable: `VENDLIST_JSON='...'`

### "AWS SDK not available" error

**Issue:** AWS SDK not installed or configured

**Solutions:**
```bash
# Install AWS SDK
npm install aws-sdk

# Or install AWS SDK v3
npm install @aws-sdk/client-s3
```

### "Permission denied" errors in Cloud Storage

**Issue:** IAM/RBAC permissions insufficient

**Solutions:**
- Verify bucket/container access
- Check service account credentials
- Ensure service account has read permissions
- Verify environment variables are set correctly

### Performance Issues

**Issue:** Slow requests on first invocation

**Solutions:**
1. This is normal for serverless (cold start)
2. Vendlist caches in memory after first load
3. For AWS Lambda, use provisioned concurrency
4. For Cloud Run, increase min instances
5. Consider pre-loading vendlist in handler initialization

Example Architecture
--------------------

### Multi-Environment Setup

```bash
# Development - local file
VENDLIST_PROVIDER=local

# Staging - S3 with secrets
VENDLIST_PROVIDER=aws
VENDLIST_BUCKET=it-vends-staging
AWS_REGION=us-east-1

# Production - S3 with versioning
VENDLIST_PROVIDER=aws
VENDLIST_BUCKET=it-vends-prod
AWS_REGION=us-east-1
# Enable versioning on S3 bucket
```

### Disaster Recovery

```bash
# Backup vendlist.json regularly
aws s3 cp s3://my-vends-bucket/vendlist.json ./backups/vendlist-$(date +%Y%m%d).json

# Restore from backup
aws s3 cp ./backups/vendlist-20240402.json s3://my-vends-bucket/vendlist.json
```

License
-------

See LICENSE file in repository root.

Copyright 2024 by It Vends Authors.
