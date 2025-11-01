import axios from 'axios';

async function testSendMessage() {
    try {
        const sessionId = 'c47e68d4-008e-4edf-b1d2-1b0288c0bb04';
        const to = '6285159205506';
        const message = 'Test pesan langsung';
        
        console.log('Sending message to:', to);
        console.log('Message:', message);
        
        const response = await axios.post(
            `http://127.0.0.1:3000/sessions/${sessionId}/send-simple`,
            {
                to: to,
                message: message
            },
            {
                headers: {
                    'X-API-Key': 'your_secure_api_key_here',
                    'Content-Type': 'application/json'
                }
            }
        );
        
        console.log('Response:', response.data);
    } catch (error) {
        console.error('Error:', error.response ? error.response.data : error.message);
    }
}

testSendMessage();