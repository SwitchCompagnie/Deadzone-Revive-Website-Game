/**
 * Simple WebSocket Server for The Last Stand: Dead Zone
 * 
 * This is a basic WebSocket server that runs on localhost.
 * Configure the port in .env file with WEBSOCKET_PORT (default: 8080)
 * 
 * Install dependencies:
 * npm install ws
 * 
 * Run the server:
 * node websocket-server.js
 */

const WebSocket = require('ws');
const http = require('http');

// Load environment configuration
const PORT = process.env.WEBSOCKET_PORT || 8080;
const HOST = process.env.WEBSOCKET_HOST || 'localhost';

// Create HTTP server
const server = http.createServer((req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/plain' });
    res.end('WebSocket Server Running\n');
});

// Create WebSocket server
const wss = new WebSocket.Server({ server });

// Store connected clients
const clients = new Map();

wss.on('connection', (ws, req) => {
    const clientId = generateClientId();
    clients.set(clientId, ws);
    
    console.log(`[${new Date().toISOString()}] Client connected: ${clientId} (Total: ${clients.size})`);
    
    // Send welcome message
    ws.send(JSON.stringify({
        type: 'connection',
        message: 'Connected to Dead Zone WebSocket Server',
        clientId: clientId,
        timestamp: Date.now()
    }));
    
    // Handle incoming messages
    ws.on('message', (message) => {
        try {
            const data = JSON.parse(message);
            console.log(`[${new Date().toISOString()}] Message from ${clientId}:`, data);
            
            // Echo the message back (modify this based on your game server logic)
            ws.send(JSON.stringify({
                type: 'echo',
                originalMessage: data,
                timestamp: Date.now()
            }));
            
            // Broadcast to other clients if needed
            // broadcastMessage(clientId, data);
            
        } catch (error) {
            console.error('Error parsing message:', error);
            ws.send(JSON.stringify({
                type: 'error',
                message: 'Invalid JSON format',
                timestamp: Date.now()
            }));
        }
    });
    
    // Handle connection close
    ws.on('close', () => {
        clients.delete(clientId);
        console.log(`[${new Date().toISOString()}] Client disconnected: ${clientId} (Total: ${clients.size})`);
    });
    
    // Handle errors
    ws.on('error', (error) => {
        console.error(`[${new Date().toISOString()}] WebSocket error for ${clientId}:`, error);
        clients.delete(clientId);
    });
    
    // Send ping every 30 seconds to keep connection alive
    const pingInterval = setInterval(() => {
        if (ws.readyState === WebSocket.OPEN) {
            ws.ping();
        } else {
            clearInterval(pingInterval);
        }
    }, 30000);
    
    ws.on('pong', () => {
        console.log(`[${new Date().toISOString()}] Pong received from ${clientId}`);
    });
});

// Broadcast message to all clients except sender
function broadcastMessage(senderId, data) {
    clients.forEach((client, clientId) => {
        if (clientId !== senderId && client.readyState === WebSocket.OPEN) {
            client.send(JSON.stringify({
                type: 'broadcast',
                from: senderId,
                data: data,
                timestamp: Date.now()
            }));
        }
    });
}

// Generate unique client ID
function generateClientId() {
    return `client_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
}

// Start the server
server.listen(PORT, HOST, () => {
    console.log(`WebSocket Server running on ${HOST}:${PORT}`);
    console.log(`You can test the connection by visiting http://${HOST}:${PORT}`);
    console.log('\nPress Ctrl+C to stop the server');
});

// Graceful shutdown
process.on('SIGINT', () => {
    console.log('\n\nShutting down WebSocket server...');
    
    // Close all client connections
    clients.forEach((client, clientId) => {
        console.log(`Closing connection for ${clientId}`);
        client.close(1000, 'Server shutting down');
    });
    
    // Close the server
    wss.close(() => {
        console.log('WebSocket server closed');
        server.close(() => {
            console.log('HTTP server closed');
            process.exit(0);
        });
    });
});
