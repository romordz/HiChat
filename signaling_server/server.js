const WebSocket = require('ws');
const PORT = 3000;

const wss = new WebSocket.Server({ 
    port: PORT,
    host: '0.0.0.0'
});

wss.on('connection', (ws, req) => {
    const clientIP = req.socket.remoteAddress;
    console.log(`Nueva conexión establecida desde IP: ${clientIP}`);
    
    ws.on('message', (message) => {
        try {
            // Convertir el mensaje a string si es un Buffer o Blob
            const messageString = message.toString();
            console.log('Mensaje recibido:', messageString);
            
            // Parsear el mensaje como JSON
            const data = JSON.parse(messageString);
            console.log('Datos parseados:', data);
            
            // Reenviar el mensaje a todos los demás clientes
            wss.clients.forEach((client) => {
                if (client !== ws && client.readyState === WebSocket.OPEN) {
                    console.log('Reenviando mensaje a otro cliente');
                    // Asegurarse de que el mensaje se envía como string
                    client.send(messageString);
                }
            });
        } catch (error) {
            console.error('Error procesando mensaje:', error);
        }
    });
    
    ws.on('close', (code, reason) => {
        console.log(`Cliente desconectado. Código: ${code}, Razón: ${reason}`);
    });

    ws.on('error', (error) => {
        console.error('Error en WebSocket:', error);
    });
});

console.log(`Servidor de señalización WebSocket iniciado en el puerto ${PORT}`);