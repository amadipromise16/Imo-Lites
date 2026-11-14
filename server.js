/**
 * Production Static & Media Server for Hostinger Node.js Deployments
 * Imolites in Diaspora Social Club USA
 */
const http = require('http');
const fs = require('fs');
const path = require('path');
const url = require('url');

const PORT = process.env.PORT || 3000;
const ROOT_DIR = __dirname;

const MIME_TYPES = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'application/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.webp': 'image/webp',
  '.svg': 'image/svg+xml',
  '.mp4': 'video/mp4',
  '.mp3': 'audio/mpeg',
  '.ico': 'image/x-icon',
  '.txt': 'text/plain; charset=utf-8',
  '.pdf': 'application/pdf'
};

const server = http.createServer((req, res) => {
  // CORS & Security headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS');
  res.setHeader('X-Content-Type-Options', 'nosniff');

  if (req.method === 'OPTIONS') {
    res.writeHead(204);
    res.end();
    return;
  }

  const parsedUrl = url.parse(req.url, true);
  let pathname = decodeURIComponent(parsedUrl.pathname);

  // Normalize path & prevent directory traversal
  let safePath = path.normalize(pathname).replace(/^(\.\.[\/\\])+/, '');
  if (safePath === '/' || safePath === '\\') {
    safePath = 'index.html';
  } else if (safePath.startsWith('/') || safePath.startsWith('\\')) {
    safePath = safePath.slice(1);
  }

  let filePath = path.join(ROOT_DIR, safePath);

  // Check if file exists, or if extension should be resolved
  let resolvedFile = null;

  if (fs.existsSync(filePath) && fs.statSync(filePath).isFile()) {
    resolvedFile = filePath;
  } else if (fs.existsSync(filePath + '.html')) {
    resolvedFile = filePath + '.html';
  } else if (safePath.endsWith('.php')) {
    // Fallback: request for .php served by .html counterpart on Node.js hosting
    const htmlEquivalent = filePath.replace(/\.php$/i, '.html');
    if (fs.existsSync(htmlEquivalent)) {
      resolvedFile = htmlEquivalent;
    }
  } else if (fs.existsSync(path.join(filePath, 'index.html'))) {
    resolvedFile = path.join(filePath, 'index.html');
  }

  if (!resolvedFile) {
    res.writeHead(404, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end('<h1>404 Not Found</h1><p>The requested page was not found on this server.</p><a href="/">Return Home</a>');
    return;
  }

  const ext = path.extname(resolvedFile).toLowerCase();
  const contentType = MIME_TYPES[ext] || 'application/octet-stream';

  const stat = fs.statSync(resolvedFile);
  const totalSize = stat.size;

  // Cache-Control headers
  if (ext === '.html' || ext === '.json') {
    res.setHeader('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
    res.setHeader('Pragma', 'no-cache');
  } else if (ext === '.css' || ext === '.js') {
    res.setHeader('Cache-Control', 'no-cache, must-revalidate');
  } else {
    res.setHeader('Cache-Control', 'public, max-age=86400');
  }

  // Byte range request handling (crucial for iOS and Android video/audio streaming)
  const range = req.headers.range;
  if (range && (ext === '.mp4' || ext === '.mp3')) {
    const parts = range.replace(/bytes=/, '').split('-');
    const start = parseInt(parts[0], 10);
    const end = parts[1] ? parseInt(parts[1], 10) : totalSize - 1;

    if (start >= totalSize || end >= totalSize) {
      res.writeHead(416, {
        'Content-Range': `bytes */${totalSize}`
      });
      res.end();
      return;
    }

    const chunksize = (end - start) + 1;
    const fileStream = fs.createReadStream(resolvedFile, { start, end });

    res.writeHead(206, {
      'Content-Range': `bytes ${start}-${end}/${totalSize}`,
      'Accept-Ranges': 'bytes',
      'Content-Length': chunksize,
      'Content-Type': contentType
    });

    fileStream.pipe(res);
    return;
  }

  // Full file streaming
  res.writeHead(200, {
    'Content-Length': totalSize,
    'Content-Type': contentType,
    'Accept-Ranges': 'bytes'
  });

  if (req.method === 'HEAD') {
    res.end();
    return;
  }

  fs.createReadStream(resolvedFile).pipe(res);
});

server.listen(PORT, () => {
  console.log(`Imolites Production Server running on port ${PORT}`);
});
