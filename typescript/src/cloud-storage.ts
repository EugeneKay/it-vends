// typescript/src/cloud-storage.ts
// it-vends
//
// Cloud storage providers for loading vendlist from object store
// Supports: AWS S3, Google Cloud Storage, Azure Blob Storage, Cloudflare R2
//

/**
 * Vendlist data structure
 */
export interface VendlistData {
    items: string[];
    categories: Record<string, string[]>;
    metadata: {
        generated: string;
        itemCount: number;
    };
}

/**
 * Cloud storage configuration
 */
export interface CloudStorageConfig {
    provider: 'aws' | 'gcp' | 'azure' | 'cloudflare' | 'local';
    bucket?: string;
    path?: string;
    region?: string;
    credentials?: Record<string, any>;
}

/**
 * Abstract base class for cloud storage providers
 */
export abstract class CloudStorageProvider {
    protected config: CloudStorageConfig;

    constructor(config: CloudStorageConfig) {
        this.config = config;
    }

    abstract getVendlist(): Promise<VendlistData>;
}

/**
 * AWS S3 storage provider
 */
export class S3StorageProvider extends CloudStorageProvider {
    private s3: any;

    constructor(config: CloudStorageConfig) {
        super(config);
        this.initializeS3();
    }

    private initializeS3() {
        try {
            // Lazy load AWS SDK to avoid dependency when not using S3
            const AWS = require('aws-sdk');
            this.s3 = new AWS.S3({
                region: this.config.region || process.env.AWS_REGION || 'us-east-1',
            });
        } catch (error) {
            console.warn('AWS SDK not available for S3 storage');
        }
    }

    async getVendlist(): Promise<VendlistData> {
        if (!this.s3) {
            throw new Error('AWS SDK not available');
        }

        const bucket = this.config.bucket || process.env.VENDLIST_BUCKET;
        const key = this.config.path || 'vendlist.json';

        if (!bucket) {
            throw new Error('S3 bucket not configured');
        }

        try {
            const params = { Bucket: bucket, Key: key };
            const response = await this.s3.getObject(params).promise();
            const data = JSON.parse(response.Body.toString('utf-8'));
            console.log(`Loaded vendlist from S3: s3://${bucket}/${key}`);
            return data as VendlistData;
        } catch (error) {
            throw new Error(`Failed to load vendlist from S3: ${error instanceof Error ? error.message : String(error)}`);
        }
    }
}

/**
 * Google Cloud Storage provider
 */
export class GCSStorageProvider extends CloudStorageProvider {
    private storage: any;

    constructor(config: CloudStorageConfig) {
        super(config);
        this.initializeGCS();
    }

    private initializeGCS() {
        try {
            // Lazy load Google Cloud Storage SDK
            const { Storage } = require('@google-cloud/storage');
            this.storage = new Storage({
                projectId: process.env.GCP_PROJECT_ID,
                keyFilename: process.env.GOOGLE_APPLICATION_CREDENTIALS,
            });
        } catch (error) {
            console.warn('@google-cloud/storage not available for GCS storage');
        }
    }

    async getVendlist(): Promise<VendlistData> {
        if (!this.storage) {
            throw new Error('Google Cloud Storage SDK not available');
        }

        const bucket = this.config.bucket || process.env.VENDLIST_BUCKET;
        const path = this.config.path || 'vendlist.json';

        if (!bucket) {
            throw new Error('GCS bucket not configured');
        }

        try {
            const file = this.storage.bucket(bucket).file(path);
            const [exists] = await file.exists();

            if (!exists) {
                throw new Error(`File not found: gs://${bucket}/${path}`);
            }

            const [contents] = await file.download();
            const data = JSON.parse(contents.toString('utf-8'));
            console.log(`Loaded vendlist from GCS: gs://${bucket}/${path}`);
            return data as VendlistData;
        } catch (error) {
            throw new Error(`Failed to load vendlist from GCS: ${error instanceof Error ? error.message : String(error)}`);
        }
    }
}

/**
 * Azure Blob Storage provider
 */
export class AzureBlobStorageProvider extends CloudStorageProvider {
    private blobClient: any;

    constructor(config: CloudStorageConfig) {
        super(config);
        this.initializeAzure();
    }

    private initializeAzure() {
        try {
            // Lazy load Azure Storage SDK
            const { BlobServiceClient } = require('@azure/storage-blob');
            const connectionString = process.env.AZURE_STORAGE_CONNECTION_STRING;

            if (connectionString) {
                this.blobClient = BlobServiceClient.fromConnectionString(connectionString);
            } else {
                const accountName = process.env.AZURE_STORAGE_ACCOUNT_NAME;
                const accountKey = process.env.AZURE_STORAGE_ACCOUNT_KEY;

                if (accountName && accountKey) {
                    const connectionStr = `DefaultEndpointsProtocol=https;AccountName=${accountName};AccountKey=${accountKey};EndpointSuffix=core.windows.net`;
                    this.blobClient = BlobServiceClient.fromConnectionString(connectionStr);
                }
            }
        } catch (error) {
            console.warn('@azure/storage-blob not available for Azure Blob Storage');
        }
    }

    async getVendlist(): Promise<VendlistData> {
        if (!this.blobClient) {
            throw new Error('Azure Storage SDK not available or not configured');
        }

        const container = this.config.bucket || process.env.VENDLIST_CONTAINER;
        const blob = this.config.path || 'vendlist.json';

        if (!container) {
            throw new Error('Azure container not configured');
        }

        try {
            const containerClient = this.blobClient.getContainerClient(container);
            const blockBlobClient = containerClient.getBlockBlobClient(blob);
            const downloadBlockBlobResponse = await blockBlobClient.download(0);
            const downloaded = await (downloadBlockBlobResponse.readableStreamBody as any).text();
            const data = JSON.parse(downloaded);
            console.log(`Loaded vendlist from Azure: ${container}/${blob}`);
            return data as VendlistData;
        } catch (error) {
            throw new Error(`Failed to load vendlist from Azure: ${error instanceof Error ? error.message : String(error)}`);
        }
    }
}

/**
 * Cloudflare R2 storage provider (S3-compatible)
 */
export class CloudflareR2StorageProvider extends CloudStorageProvider {
    private s3: any;

    constructor(config: CloudStorageConfig) {
        super(config);
        this.initializeR2();
    }

    private initializeR2() {
        try {
            // Use AWS SDK with Cloudflare R2 endpoint
            const AWS = require('aws-sdk');
            const endpoint = process.env.CLOUDFLARE_R2_ENDPOINT || `https://${process.env.CLOUDFLARE_ACCOUNT_ID}.r2.cloudflarestorage.com`;

            this.s3 = new AWS.S3({
                endpoint,
                accessKeyId: process.env.CLOUDFLARE_R2_ACCESS_KEY_ID,
                secretAccessKey: process.env.CLOUDFLARE_R2_SECRET_ACCESS_KEY,
                s3ForcePathStyle: true,
                signatureVersion: 'v4',
            });
        } catch (error) {
            console.warn('AWS SDK not available for Cloudflare R2 storage');
        }
    }

    async getVendlist(): Promise<VendlistData> {
        if (!this.s3) {
            throw new Error('Cloudflare R2 not configured');
        }

        const bucket = this.config.bucket || process.env.VENDLIST_BUCKET;
        const key = this.config.path || 'vendlist.json';

        if (!bucket) {
            throw new Error('R2 bucket not configured');
        }

        try {
            const params = { Bucket: bucket, Key: key };
            const response = await this.s3.getObject(params).promise();
            const data = JSON.parse(response.Body.toString('utf-8'));
            console.log(`Loaded vendlist from Cloudflare R2: r2://${bucket}/${key}`);
            return data as VendlistData;
        } catch (error) {
            throw new Error(`Failed to load vendlist from Cloudflare R2: ${error instanceof Error ? error.message : String(error)}`);
        }
    }
}

/**
 * Local file system provider (for testing)
 */
export class LocalStorageProvider extends CloudStorageProvider {
    async getVendlist(): Promise<VendlistData> {
        try {
            // In Node.js environment, load from filesystem
            if (typeof require !== 'undefined') {
                const path_module = require('path');
                const fs = require('fs');

                // Try multiple paths to find vendlist.json
                const possiblePaths = [
                    this.config.path,
                    path_module.join(__dirname, 'vendlist.json'),
                    path_module.join(__dirname, '../dist/vendlist.json'),
                    path_module.join(process.cwd(), 'vendlist.json'),
                    path_module.join(process.cwd(), 'dist/vendlist.json'),
                    'vendlist.json',
                ].filter((p): p is string => p !== undefined);

                for (const filePath of possiblePaths) {
                    if (fs.existsSync(filePath)) {
                        const data = JSON.parse(fs.readFileSync(filePath, 'utf-8'));
                        console.log(`Loaded vendlist from local file: ${filePath}`);
                        return data as VendlistData;
                    }
                }

                throw new Error(`Vendlist file not found in any of: ${possiblePaths.join(', ')}`);
            }

            // In browser/Cloudflare Workers environment, fetch from server
            const response = await fetch(this.config.path || '/vendlist.json');
            if (!response.ok) {
                throw new Error(`Failed to fetch vendlist: ${response.statusText}`);
            }

            const data = await response.json();
            console.log(`Loaded vendlist from: ${this.config.path}`);
            return data as VendlistData;
        } catch (error) {
            throw new Error(`Failed to load local vendlist: ${error instanceof Error ? error.message : String(error)}`);
        }
    }
}

/**
 * Factory function to create appropriate cloud storage provider
 */
export function createStorageProvider(config: CloudStorageConfig): CloudStorageProvider {
    switch (config.provider) {
        case 'aws':
            return new S3StorageProvider(config);
        case 'gcp':
            return new GCSStorageProvider(config);
        case 'azure':
            return new AzureBlobStorageProvider(config);
        case 'cloudflare':
            return new CloudflareR2StorageProvider(config);
        case 'local':
        default:
            return new LocalStorageProvider(config);
    }
}

/**
 * Get configured cloud storage provider from environment
 * Automatically detects provider based on environment variables
 */
export function getConfiguredProvider(): CloudStorageProvider {
    const provider = process.env.VENDLIST_PROVIDER || process.env.CLOUD_PROVIDER || 'local';

    const config: CloudStorageConfig = {
        provider: provider as any,
        bucket: process.env.VENDLIST_BUCKET,
        path: process.env.VENDLIST_PATH || 'vendlist.json',
        region: process.env.AWS_REGION,
        credentials: process.env.VENDLIST_CREDENTIALS ? JSON.parse(process.env.VENDLIST_CREDENTIALS) : undefined,
    };

    return createStorageProvider(config);
}
