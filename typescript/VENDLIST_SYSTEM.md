<!--
  -- typescript/VENDLIST_SYSTEM.md
  -- it-vends
  -->

Vendlist System
===============

Overview
--------

The It Vends TypeScript implementation features a sophisticated vendlist system that:

1. **Build-Time Generation**: Combines all `.txt` files from the `vendlist/` directory
2. **Dynamic Loading**: Loads vendlist from configurable cloud storage providers at runtime
3. **Multi-Provider Support**: AWS S3, Google Cloud Storage, Azure Blob Storage, Cloudflare R2
4. **Intelligent Categorization**: Automatically categorizes items and identifies special items
5. **Caching**: In-memory caching for performance

Architecture
-----------

### Build Time

```
vendlist/
  ├── animals/
  │   └── *.txt
  ├── drinks/
  │   └── *.txt
  ├── food/
  │   └── *.txt
  └── ...
     ↓
scripts/build-vendlist.ts (prebuild)
     ↓
dist/vendlist.json (generated)
```

### Runtime

```
Incoming Request
     ↓
parseRequest() → extract parameters
     ↓
executeVend() → process action
     ↓
vend() → fetch items
     ↓
loadVendlist() → load if not cached
     ↓
CloudStorageProvider (S3, GCS, Azure, R2, Local)
     ↓
Return items → format → respond
```

File Structure
-----------

### Source Files

```
typescript/src/
├── vendlist.ts           # Runtime vendlist interface (async functions)
├── cloud-storage.ts      # Cloud provider implementations
├── vend.ts               # Core vending logic (now async)
├── handlers.ts           # Cloud function handlers (updated for async)
├── server.ts             # Local HTTP server (updated for async)
├── index.ts              # Main export file
├── formats.ts            # Output formatting
└── ...

typescript/scripts/
└── build-vendlist.ts     # Build-time generator script
```

### Generated Files

```
typescript/dist/
├── vendlist.json         # Generated at build time
├── server.js
├── vend.js
├── cloud-storage.js
└── ...
```

Build-Time Generation
--------------------

### How It Works

1. **File Discovery**: Recursively scans `vendlist/` for all `.txt` files
2. **Item Parsing**: Reads each file, parses items (one per line)
3. **Comment Filtering**: Ignores lines starting with `#`
4. **Category Organization**: Groups by directory structure
5. **Special Detection**: Identifies items with special keywords:
   - "microwave popcorn"
   - "gelato (hand-churned)"
   - "limited"
   - "artisanal"
   - "vodka in a paper bag"
   - "single-origin"
   - "limited release"
   - "exclusive"
   - "rare"

### Running Build

```bash
npm run build
```

Or just the vendlist build:

```bash
npm run build-vendlist
```

### Build Output

```
✓ Generated vendlist with 710 items
  - Regular items: 708
  - Special items: 2
  - Categories: 8
  - Output: dist/vendlist.json
```

### Modifying Items

To add new items:

1. Create or edit `.txt` files in `vendlist/` subdirectories
2. Add one item per line
3. Run `npm run build`
4. Commit `dist/vendlist.json` to version control

### File Format Guidelines

Each `.txt` file should contain:
- One item per line
- Format: `<preposition> <noun>` (e.g., "a cold brew bottle")
- No trailing periods
- Optional: Comments starting with `#` are ignored

Example `drinks/coffee.txt`:
```
a double espresso shot
a cold brew bottle
a latte with oat milk
# This is a comment and will be ignored
a macchiato
```

Runtime Loading
--------------

### Load Priority

When `loadVendlist()` is called, the system tries in this order:

1. **Environment Variable**: `VENDLIST_JSON` (direct JSON string)
2. **Cloud Storage**: Uses `VENDLIST_PROVIDER` environment variable
3. **Local Storage**: Falls back to local filesystem

### Loading Flow

```typescript
export async function loadVendlist(): Promise<VendlistData> {
    // 1. Return if already cached
    if (vendlistData !== null) return vendlistData;
    
    // 2. Return if currently loading (avoid duplicate calls)
    if (vendlistPromise !== null) return vendlistPromise;
    
    // 3. Create new load promise
    vendlistPromise = (async () => {
        try {
            // Try env var first
            if (process.env.VENDLIST_JSON) {
                vendlistData = JSON.parse(process.env.VENDLIST_JSON);
                return vendlistData;
            }
            
            // Try cloud provider
            const provider = getConfiguredProvider();
            vendlistData = await provider.getVendlist();
            return vendlistData;
        } catch (error) {
            // Fallback to single "mystery item"
            vendlistData = fallback;
            return fallback;
        }
    })();
    
    return vendlistPromise;
}
```

### Data Structure

```typescript
interface VendlistData {
    regular: string[];           // Common items
    special: string[];           // Rare/special items
    categories: {                // Organized by category
        [category: string]: string[];
    };
    metadata: {
        generated: string;       // ISO timestamp
        itemCount: number;       // Total items
        specialItemCount: number;
    };
}
```

### Accessing Vendlist

```typescript
// Load and get regular items
const regularItems = await getRegularItems();

// Load and get special items
const specialItems = await getSpecialItems();

// Get items by category
const drinks = await getItemsByCategory('drinks');

// Get all categories
const categories = await getCategories();

// Get metadata
const metadata = await getMetadata();

// Force reload from storage
await reloadVendlist();

// Set vendlist manually (testing)
setVendlistData(myData);

// Get cached data (if loaded)
const data = getVendlistDataSync();
```

Vending Logic
-----------

### Updated `vend()` Function

Now async to support dynamic vendlist loading:

```typescript
export async function vend(
    qty: number = 0, 
    special: number = 10
): Promise<string | string[]> {
    // Load vendlist asynchronously
    const vendlistData = await loadVendlist();
    const regularItems = vendlistData.regular;
    const specialItems = vendlistData.special;
    
    const items: string[] = [];
    const count = qty === 0 ? 1 : Math.min(qty, 100);
    
    for (let i = 0; i < count; i++) {
        if (special && Math.random() * 100 <= special && specialItems.length > 0) {
            // Pick special item
            items.push(specialItems[Math.floor(Math.random() * specialItems.length)]);
        } else if (regularItems.length > 0) {
            // Pick regular item
            items.push(regularItems[Math.floor(Math.random() * regularItems.length)]);
        } else {
            // Fallback
            items.push('a mystery item');
        }
    }
    
    return qty === 0 ? items[0] : items;
}
```

### Handler Updates

All handlers are now async and await `executeVend()`:

```typescript
export async function awsLambdaHandler(event: any): Promise<any> {
    const startTime = Date.now();
    const queryParams = event.queryStringParameters || {};
    
    const request = parseRequest(queryParams);
    const response = await executeVend(request, startTime);  // Now awaited
    
    return {
        statusCode: response.statusCode,
        headers: response.headers,
        body: response.body,
        isBase64Encoded: false,
    };
}
```

Performance Characteristics
--------------------------

### First Request (Cold Start)

```
Time to First Item: ~5-50ms (local), ~100-500ms (cloud storage)
- Includes: Cloud storage download, in-memory cache population
```

### Subsequent Requests

```
Time to Item: ~1-5ms
- Uses: In-memory cached vendlist
- No additional I/O required
```

### Memory Usage

```
Vendlist Size: ~33 KB (710 items)
Memory Overhead: ~1-5 MB per Lambda/Function instance
Caching: Single instance per process
```

Development Workflow
-------------------

### 1. Add New Items

```bash
# Edit vendlist files
echo "a new item" >> vendlist/snacks/common.txt

# Rebuild vendlist.json
npm run build

# Test locally
npm start
curl http://localhost:8080/vend

# Commit changes
git add vendlist/ dist/vendlist.json
git commit -m "Add new item"
```

### 2. Update Production

**S3 Example:**
```bash
# Rebuild
npm run build

# Upload to S3
aws s3 cp dist/vendlist.json s3://my-bucket/vendlist.json

# New lambda invocations automatically use new vendlist
```

**Cloud Run Example:**
```bash
# Rebuild
npm run build

# Rebuild and push Docker image
docker build -f typescript.Dockerfile -t gcr.io/project/it-vends:latest .
docker push gcr.io/project/it-vends:latest

# Deploy new image
gcloud run deploy it-vends \
  --image gcr.io/project/it-vends:latest \
  --region us-central1
```

Testing
------

### Local Testing

```bash
npm run dev
curl http://localhost:8080/vend?count=5&format=json
```

### Docker Testing

```bash
docker build -f typescript.Dockerfile -t it-vends-typescript .
docker run -p 8080:8080 it-vends-typescript
curl http://localhost:8080/vend
```

### With Custom Vendlist

```bash
# Test with environment variable
VENDLIST_JSON='{"regular":["test item"],"special":[],"categories":{},"metadata":{"generated":"2024-01-01T00:00:00Z","itemCount":1,"specialItemCount":0}}' npm start

# Test with local file
VENDLIST_PATH=/path/to/custom-vendlist.json npm start
```

Troubleshooting
---------------

### Issue: Items are "a mystery item"

**Cause**: Vendlist failed to load

**Solution**:
1. Check build succeeded: `npm run build`
2. Verify `dist/vendlist.json` exists
3. Check logs for storage errors
4. Ensure proper provider is configured

### Issue: Build script hangs

**Cause**: Large vendlist directory or file system issues

**Solution**:
1. Check disk space
2. Verify file permissions
3. Run with timeout: `timeout 30 npm run build`

### Issue: Different items each deployment

**Cause**: Different vendlist.json versions deployed

**Solution**:
1. Ensure `npm run build` runs before deployment
2. Commit `dist/vendlist.json` to version control
3. Verify cloud storage upload succeeded

Future Enhancements
------------------

Potential improvements:

- [ ] Database backend for vendlist (MongoDB, PostgreSQL)
- [ ] REST API for vendlist management
- [ ] Admin panel for editing items
- [ ] Weighted item selection
- [ ] Time-based item availability
- [ ] User preferences/favorites
- [ ] Analytics/tracking
- [ ] Vendlist versioning/rollback
- [ ] CDN distribution for vendlist.json

License
-------

See LICENSE file in repository root.

Copyright 2024 by It Vends Authors.
