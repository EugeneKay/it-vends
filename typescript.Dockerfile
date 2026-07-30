#
# typescript.Dockerfile
# it-vends
#
# Build: docker build -f typescript.Dockerfile -t it-vends-typescript .
# Run: docker run -p 8080:8080 it-vends-typescript
#

## Build stage
FROM node:20-alpine AS builder

# Environment setup
WORKDIR /build

# Copy package files
COPY typescript/package.json typescript/tsconfig.json ./

# Install build dependencies
RUN npm install && \
    npm install -D typescript ts-node @types/node

# Copy source code and build scripts
COPY typescript/src ./src
COPY typescript/scripts ./scripts
COPY vendlist ../vendlist

# Build TypeScript to JavaScript
RUN npm run build

## Runtime stage
FROM node:20-alpine AS runtime

# Set metadata
LABEL maintainer="Eugene Evgenevich Kashpureff <eugene@kashpureff.cloud>"
LABEL description="It Vends - TypeScript Edge Function (Multi-Cloud Compatible)"

# Environment setup
WORKDIR /app

# Copy built application and dependencies from builder
COPY --from=builder /build/dist ./
COPY --from=builder /build/node_modules ./node_modules

# Set environment variables
ENV NODE_ENV=production
ENV PORT=8080
ENV HOST=0.0.0.0

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD node -e "require('http').get('http://localhost:8080/health', (r) => {process.exit(r.statusCode === 200 ? 0 : 1)})"

# Expose port
EXPOSE 8080

# Entry point - run the local development server
CMD ["node", "server.js"]
