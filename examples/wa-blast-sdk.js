/**
 * WA Blast JavaScript SDK
 * 
 * SDK untuk integrasi dengan API WA Blast dari aplikasi JavaScript/Node.js
 */

class WABlastSDK {
    constructor(config) {
        this.baseUrl = config.baseUrl || 'http://localhost:8000';
        this.apiKey = config.apiKey;
        this.timeout = config.timeout || 30000;
        this.version = config.version || 'v1';
    }

    /**
     * Kirim request ke API
     */
    async request(method, endpoint, data = null) {
        const url = `${this.baseUrl}/api/${this.version}${endpoint}`;
        
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        if (this.apiKey) {
            headers['X-API-Key'] = this.apiKey;
        }

        const options = {
            method: method,
            headers: headers,
            timeout: this.timeout
        };

        if (data && ['POST', 'PUT', 'PATCH'].includes(method)) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `HTTP ${response.status}`);
            }

            return result;
        } catch (error) {
            throw new Error(`API Error: ${error.message}`);
        }
    }

    /**
     * Cek status sistem
     */
    async getSystemStatus() {
        return this.request('GET', '/integration/system-status');
    }

    /**
     * Dapatkan daftar session WhatsApp
     */
    async getWhatsAppSessions() {
        return this.request('GET', '/whatsapp/sessions');
    }

    /**
     * Buat session WhatsApp baru
     */
    async createWhatsAppSession(name, phoneNumber) {
        return this.request('POST', '/whatsapp/sessions', {
            name: name,
            phone_number: phoneNumber
        });
    }

    /**
     * Dapatkan QR code untuk session
     */
    async getQRCode(sessionId) {
        return this.request('GET', `/whatsapp/sessions/${sessionId}/qr`);
    }

    /**
     * Kirim pesan WhatsApp
     */
    async sendMessage(sessionId, toNumber, message, messageType = 'text') {
        return this.request('POST', `/whatsapp/sessions/${sessionId}/send`, {
            to_number: toNumber,
            message: message,
            message_type: messageType
        });
    }

    /**
     * Kirim pesan template
     */
    async sendTemplateMessage(sessionId, toNumber, template, variables) {
        return this.request('POST', '/integration/send-template', {
            session_id: sessionId,
            to_number: toNumber,
            template: template,
            variables: variables
        });
    }

    /**
     * Kirim pesan bulk
     */
    async bulkSend(sessionId, messages) {
        return this.request('POST', '/integration/bulk-send', {
            session_id: sessionId,
            messages: messages
        });
    }

    /**
     * Buat campaign blast
     */
    async createBlastCampaign(name, message, phoneNumbers, sessionId) {
        return this.request('POST', '/blast/campaigns', {
            name: name,
            message: message,
            phone_numbers: phoneNumbers,
            session_id: sessionId
        });
    }

    /**
     * Mulai campaign blast
     */
    async startBlastCampaign(campaignId) {
        return this.request('POST', `/blast/campaigns/${campaignId}/start`);
    }

    /**
     * Dapatkan statistik campaign
     */
    async getCampaignStatistics(campaignId) {
        return this.request('GET', `/blast/campaigns/${campaignId}/statistics`);
    }

    /**
     * Import kontak
     */
    async importContacts(contacts, overwriteExisting = false) {
        return this.request('POST', '/integration/import-contacts', {
            contacts: contacts,
            overwrite_existing: overwriteExisting
        });
    }

    /**
     * Export kontak
     */
    async exportContacts(format = 'json', group = null, status = null) {
        const params = new URLSearchParams({
            format: format,
            ...(group && { group }),
            ...(status && { status })
        });

        return this.request('GET', `/integration/export-contacts?${params}`);
    }

    /**
     * Dapatkan daftar kontak
     */
    async getContacts(search = null, group = null, status = null) {
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (group) params.append('group', group);
        if (status) params.append('status', status);

        return this.request('GET', `/phonebook?${params}`);
    }

    /**
     * Tambah kontak baru
     */
    async addContact(name, phoneNumber, email = null, group = null, notes = null) {
        return this.request('POST', '/phonebook', {
            name: name,
            phone_number: phoneNumber,
            email: email,
            group: group,
            notes: notes,
            is_active: true
        });
    }

    /**
     * Dapatkan daftar grup
     */
    async getGroups() {
        return this.request('GET', '/phonebook-groups');
    }

    /**
     * Cari kontak
     */
    async searchContacts(query) {
        return this.request('GET', `/phonebook-search?q=${encodeURIComponent(query)}`);
    }
}

// Contoh penggunaan di browser
if (typeof window !== 'undefined') {
    window.WABlastSDK = WABlastSDK;
}

// Contoh penggunaan di Node.js
if (typeof module !== 'undefined' && module.exports) {
    module.exports = WABlastSDK;
}

// Contoh penggunaan
async function example() {
    try {
        // Inisialisasi SDK
        const waBlast = new WABlastSDK({
            baseUrl: 'https://your-wa-blast-domain.com',
            apiKey: 'your-api-key-here'
        });

        // 1. Cek status sistem
        console.log('=== Status Sistem ===');
        const status = await waBlast.getSystemStatus();
        console.log(status);

        // 2. Dapatkan session WhatsApp
        console.log('\n=== Session WhatsApp ===');
        const sessions = await waBlast.getWhatsAppSessions();
        console.log(sessions);

        // 3. Kirim pesan template
        console.log('\n=== Kirim Pesan Template ===');
        const templateResult = await waBlast.sendTemplateMessage(
            1, // session_id
            '6281234567890', // to_number
            'Halo {name}, ada promo menarik untuk Anda: {promo_message}',
            {
                name: 'John Doe',
                promo_message: 'Diskon 50% untuk semua produk!'
            }
        );
        console.log(templateResult);

        // 4. Kirim pesan bulk
        console.log('\n=== Kirim Pesan Bulk ===');
        const bulkResult = await waBlast.bulkSend(1, [
            {
                to_number: '6281234567890',
                message: 'Halo, ini pesan pertama'
            },
            {
                to_number: '6281234567891',
                message: 'Halo, ini pesan kedua'
            }
        ]);
        console.log(bulkResult);

        // 5. Buat campaign blast
        console.log('\n=== Buat Campaign Blast ===');
        const campaignResult = await waBlast.createBlastCampaign(
            'Campaign Promo',
            'Halo {name}, ada promo menarik untuk Anda!',
            ['6281234567890', '6281234567891'],
            1
        );
        console.log(campaignResult);

        // 6. Import kontak
        console.log('\n=== Import Kontak ===');
        const importResult = await waBlast.importContacts([
            {
                name: 'John Doe',
                phone_number: '6281234567890',
                email: 'john@example.com',
                group: 'VIP'
            },
            {
                name: 'Jane Smith',
                phone_number: '6281234567891',
                email: 'jane@example.com',
                group: 'Regular'
            }
        ]);
        console.log(importResult);

    } catch (error) {
        console.error('Error:', error.message);
    }
}

// Contoh integrasi dengan React
class ReactWABlastIntegration {
    constructor(config) {
        this.sdk = new WABlastSDK(config);
    }

    async sendNotification(userId, message) {
        // Ambil data user dari state atau API
        const user = await this.getUser(userId);
        
        if (!user) {
            throw new Error('User not found');
        }

        return this.sdk.sendTemplateMessage(
            1,
            user.phone_number,
            'Halo {name}, {message}',
            {
                name: user.name,
                message: message
            }
        );
    }

    async sendBulkNotification(userIds, message) {
        const users = await this.getUsers(userIds);
        const messages = users.map(user => ({
            to_number: user.phone_number,
            message: `Halo ${user.name}, ${message}`
        }));

        return this.sdk.bulkSend(1, messages);
    }

    async getUser(userId) {
        // Implementasi untuk mendapatkan data user
        return { name: 'John Doe', phone_number: '6281234567890' };
    }

    async getUsers(userIds) {
        // Implementasi untuk mendapatkan data users
        return [
            { name: 'John Doe', phone_number: '6281234567890' },
            { name: 'Jane Smith', phone_number: '6281234567891' }
        ];
    }
}

// Contoh integrasi dengan Vue.js
class VueWABlastIntegration {
    constructor(config) {
        this.sdk = new WABlastSDK(config);
    }

    async sendOrderNotification(order) {
        return this.sdk.sendTemplateMessage(
            1,
            order.customer.phone_number,
            'Terima kasih {name}, pesanan Anda dengan ID {order_id} telah dikonfirmasi. Total: {total}',
            {
                name: order.customer.name,
                order_id: order.order_number,
                total: new Intl.NumberFormat('id-ID').format(order.total)
            }
        );
    }

    async sendPromoCampaign(customers, promoMessage) {
        const messages = customers.map(customer => ({
            to_number: customer.phone_number,
            message: `Halo ${customer.name}, ${promoMessage}`
        }));

        return this.sdk.bulkSend(1, messages);
    }
}

// Export untuk berbagai environment
if (typeof window !== 'undefined') {
    window.ReactWABlastIntegration = ReactWABlastIntegration;
    window.VueWABlastIntegration = VueWABlastIntegration;
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        WABlastSDK,
        ReactWABlastIntegration,
        VueWABlastIntegration
    };
} 