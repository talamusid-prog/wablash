// Contoh implementasi WhatsApp Engine untuk Contact Grabber
// File ini menunjukkan bagaimana WhatsApp Engine harus mengimplementasikan endpoint grabber kontak

const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode');

class WhatsAppContactGrabber {
    constructor() {
        this.sessions = new Map();
    }

    /**
     * Grab kontak grup dari session WhatsApp
     */
    async grabGroupContacts(sessionId) {
        try {
            const session = this.sessions.get(sessionId);
            if (!session || !session.client) {
                throw new Error('Session tidak ditemukan atau tidak terhubung');
            }

            const client = session.client;
            
            // Ambil semua grup
            const chats = await client.getChats();
            const groups = chats.filter(chat => chat.isGroup);

            const groupContacts = [];
            
            for (const group of groups) {
                try {
                    // Ambil informasi detail grup
                    const participants = await group.participants;
                    const groupInfo = {
                        id: group.id._serialized,
                        jid: group.id._serialized,
                        name: group.name,
                        subject: group.name,
                        desc: group.description || '',
                        description: group.description || '',
                        participants_count: participants.length,
                        size: participants.length,
                        is_admin: group.isMeAdmin || false,
                        profile_picture: group.profilePicUrl || null,
                        picture: group.profilePicUrl || null,
                        participants: participants.map(p => {
                            // Coba ambil nomor telepon dari berbagai sumber
                            let phoneNumber = null;
                            
                            // Coba dari p.id.user (biasanya berisi nomor)
                            if (p.id.user && p.id.user !== 'status' && p.id.user.length >= 10) {
                                phoneNumber = p.id.user;
                            }
                            // Coba dari p.number jika ada
                            else if (p.number && p.number.length >= 10) {
                                phoneNumber = p.number;
                            }
                            // Coba dari p.phone jika ada
                            else if (p.phone && p.phone.length >= 10) {
                                phoneNumber = p.phone;
                            }
                            
                            return {
                                id: p.id._serialized,
                                jid: p.id._serialized,
                                name: p.name || p.pushname || 'Unknown',
                                pushname: p.pushname || '',
                                phone: phoneNumber,
                                number: phoneNumber,
                                is_admin: p.isAdmin || false,
                                profile_picture: null
                            };
                        })
                    };
                    
                    groupContacts.push(groupInfo);
                } catch (error) {
                    console.error(`Error getting group info for ${group.name}:`, error);
                    // Lanjutkan dengan grup berikutnya
                }
            }

            // Debug: Log sample participant data
            if (groupContacts.length > 0 && groupContacts[0].participants.length > 0) {
                console.log('Sample participant data:', {
                    group_name: groupContacts[0].name,
                    participant_count: groupContacts[0].participants.length,
                    sample_participant: groupContacts[0].participants[0]
                });
            }

            return {
                success: true,
                data: {
                    groups: groupContacts,
                    total_groups: groupContacts.length
                }
            };

        } catch (error) {
            console.error('Error grabbing group contacts:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Grab kontak individual dari session WhatsApp
     */
    async grabIndividualContacts(sessionId) {
        try {
            const session = this.sessions.get(sessionId);
            if (!session || !session.client) {
                throw new Error('Session tidak ditemukan atau tidak terhubung');
            }

            const client = session.client;
            
            // Ambil semua kontak
            const contacts = await client.getContacts();
            
            // Filter hanya kontak individual (bukan grup)
            const individualContacts = contacts.filter(contact => 
                !contact.id.server && contact.id.user !== 'status'
            );

            const contactList = [];
            
            for (const contact of individualContacts) {
                try {
                    const contactInfo = {
                        id: contact.id._serialized,
                        jid: contact.id._serialized,
                        name: contact.name || contact.pushname || 'Unknown Contact',
                        pushname: contact.pushname || '',
                        phone: contact.id.user,
                        number: contact.id.user,
                        is_admin: false,
                        profile_picture: contact.profilePicUrl || null,
                        picture: contact.profilePicUrl || null,
                        group_id: null // Kontak individual tidak memiliki group_id
                    };
                    
                    contactList.push(contactInfo);
                } catch (error) {
                    console.error(`Error getting contact info for ${contact.id.user}:`, error);
                    // Lanjutkan dengan kontak berikutnya
                }
            }

            return {
                success: true,
                data: {
                    contacts: contactList,
                    total_contacts: contactList.length
                }
            };

        } catch (error) {
            console.error('Error grabbing individual contacts:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Grab semua kontak (grup dan individual) dari session WhatsApp
     */
    async grabAllContacts(sessionId) {
        try {
            const session = this.sessions.get(sessionId);
            if (!session || !session.client) {
                throw new Error('Session tidak ditemukan atau tidak terhubung');
            }

            const client = session.client;
            
            // Ambil semua kontak dan grup
            const [contacts, chats] = await Promise.all([
                client.getContacts(),
                client.getChats()
            ]);

            // Filter kontak individual
            const individualContacts = contacts.filter(contact => 
                !contact.id.server && contact.id.user !== 'status'
            );

            // Filter grup
            const groups = chats.filter(chat => chat.isGroup);

            const contactList = [];
            const groupList = [];
            
            // Proses kontak individual
            for (const contact of individualContacts) {
                try {
                    const contactInfo = {
                        id: contact.id._serialized,
                        jid: contact.id._serialized,
                        name: contact.name || contact.pushname || 'Unknown Contact',
                        pushname: contact.pushname || '',
                        phone: contact.id.user,
                        number: contact.id.user,
                        is_admin: false,
                        profile_picture: contact.profilePicUrl || null,
                        picture: contact.profilePicUrl || null,
                        group_id: null
                    };
                    
                    contactList.push(contactInfo);
                } catch (error) {
                    console.error(`Error getting contact info for ${contact.id.user}:`, error);
                }
            }

            // Proses grup
            for (const group of groups) {
                try {
                    const participants = await group.participants;
                    const groupInfo = {
                        id: group.id._serialized,
                        jid: group.id._serialized,
                        name: group.name,
                        subject: group.name,
                        desc: group.description || '',
                        description: group.description || '',
                        participants_count: participants.length,
                        size: participants.length,
                        is_admin: group.isMeAdmin || false,
                        profile_picture: group.profilePicUrl || null,
                        picture: group.profilePicUrl || null,
                        participants: participants.map(p => {
                            // Coba ambil nomor telepon dari berbagai sumber
                            let phoneNumber = null;
                            
                            // Coba dari p.id.user (biasanya berisi nomor)
                            if (p.id.user && p.id.user !== 'status' && p.id.user.length >= 10) {
                                phoneNumber = p.id.user;
                            }
                            // Coba dari p.number jika ada
                            else if (p.number && p.number.length >= 10) {
                                phoneNumber = p.number;
                            }
                            // Coba dari p.phone jika ada
                            else if (p.phone && p.phone.length >= 10) {
                                phoneNumber = p.phone;
                            }
                            
                            return {
                                id: p.id._serialized,
                                jid: p.id._serialized,
                                name: p.name || p.pushname || 'Unknown',
                                pushname: p.pushname || '',
                                phone: phoneNumber,
                                number: phoneNumber,
                                is_admin: p.isAdmin || false,
                                profile_picture: null,
                                group_id: group.id._serialized // Tambahkan group_id untuk peserta grup
                            };
                        })
                    };
                    
                    groupList.push(groupInfo);
                } catch (error) {
                    console.error(`Error getting group info for ${group.name}:`, error);
                }
            }

            return {
                success: true,
                data: {
                    contacts: contactList,
                    groups: groupList,
                    total_contacts: contactList.length,
                    total_groups: groupList.length
                }
            };

        } catch (error) {
            console.error('Error grabbing all contacts:', error);
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Tambahkan endpoint ke Express router
     */
    addRoutes(router) {
        // Endpoint untuk grab kontak grup
        router.get('/sessions/:sessionId/groups', async (req, res) => {
            try {
                const { sessionId } = req.params;
                const result = await this.grabGroupContacts(sessionId);
                
                if (result.success) {
                    res.json({
                        success: true,
                        data: result.data
                    });
                } else {
                    res.status(400).json({
                        success: false,
                        error: result.error
                    });
                }
            } catch (error) {
                console.error('Error in grab groups endpoint:', error);
                res.status(500).json({
                    success: false,
                    error: 'Internal server error'
                });
            }
        });

        // Endpoint untuk grab kontak individual
        router.get('/sessions/:sessionId/contacts', async (req, res) => {
            try {
                const { sessionId } = req.params;
                const result = await this.grabIndividualContacts(sessionId);
                
                if (result.success) {
                    res.json({
                        success: true,
                        data: result.data
                    });
                } else {
                    res.status(400).json({
                        success: false,
                        error: result.error
                    });
                }
            } catch (error) {
                console.error('Error in grab contacts endpoint:', error);
                res.status(500).json({
                    success: false,
                    error: 'Internal server error'
                });
            }
        });

        // Endpoint untuk grab semua kontak
        router.get('/sessions/:sessionId/all-contacts', async (req, res) => {
            try {
                const { sessionId } = req.params;
                const result = await this.grabAllContacts(sessionId);
                
                if (result.success) {
                    res.json({
                        success: true,
                        data: result.data
                    });
                } else {
                    res.status(400).json({
                        success: false,
                        error: result.error
                    });
                }
            } catch (error) {
                console.error('Error in grab all contacts endpoint:', error);
                res.status(500).json({
                    success: false,
                    error: 'Internal server error'
                });
            }
        });
    }
}

module.exports = WhatsAppContactGrabber;

// Contoh penggunaan dalam server.js
/*
const express = require('express');
const WhatsAppContactGrabber = require('./contact-grabber-example');

const app = express();
const contactGrabber = new WhatsAppContactGrabber();

// Tambahkan routes
contactGrabber.addRoutes(app);

// Middleware untuk API key authentication
app.use('/sessions', (req, res, next) => {
    const apiKey = req.headers['x-api-key'];
    if (apiKey !== 'wa_blast_api_key_2024') {
        return res.status(401).json({
            success: false,
            error: 'Invalid API key'
        });
    }
    next();
});

app.listen(3000, () => {
    console.log('WhatsApp Engine running on port 3000');
});
*/ 