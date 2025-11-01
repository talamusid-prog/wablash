# WhatsApp Engine Usage Guide

## Common Issues and Solutions

### 1. Session Disconnection When Sending Messages

**Problem**: The WhatsApp session disconnects when sending messages, causing send failures.

**Solution**: 
- The improved code now automatically attempts to reconnect sessions before sending messages
- If a session is not found or disconnected, the system will try to reconnect it automatically
- Sessions are checked for connectivity before each message send operation

### 2. WhatsApp Engine Crashes

**Problem**: The Node.js WhatsApp engine crashes or stops responding.

**Solution**:
- Use the `monitor-whatsapp-engine.php` script to automatically restart the engine if it crashes
- Run the engine using the `start-whatsapp-engine.bat` file for easier management
- Check the engine logs in `whatsapp-engine/engine.log` for error details

## Best Practices

### Starting the System

1. **Start the WhatsApp Engine**:
   ```
   cd whatsapp-engine
   node server.js
   ```
   
   Or use the batch file:
   ```
   start-whatsapp-engine.bat
   ```

2. **Monitor the Engine** (optional but recommended):
   ```
   php monitor-whatsapp-engine.php
   ```

3. **Ensure the Laravel application is running**:
   ```
   php artisan serve
   ```

### Sending Messages

1. **Make sure your WhatsApp session is connected**:
   - Check the session status in the database
   - Use the reconnect feature if needed

2. **Use the improved sendMessage method**:
   - The service now automatically handles session reconnection
   - Error handling is more robust

### Troubleshooting

#### If messages still fail to send:

1. **Check session status**:
   ```bash
   php artisan tinker
   >>> App\Models\WhatsAppSession::first()
   ```

2. **Reconnect the session**:
   ```bash
   php reconnect-session.php
   ```

3. **Test with the improved script**:
   ```bash
   php test-improved-send.php
   ```

#### If the engine keeps crashing:

1. **Check the engine logs**:
   - Look at `whatsapp-engine/engine.log`

2. **Restart with monitoring**:
   ```bash
   php monitor-whatsapp-engine.php
   ```

3. **Check system resources**:
   - The WhatsApp engine can be resource-intensive
   - Ensure adequate RAM and CPU availability

## Configuration

### Environment Variables

Ensure these are set correctly in your `.env` file:

```env
WHATSAPP_ENGINE_URL=http://127.0.0.1:3000
WHATSAPP_ENGINE_API_KEY=your_api_key
```

And in `whatsapp-engine/.env`:

```env
PORT=3000
API_KEY=your_api_key
```

## Testing

Use the test scripts to verify functionality:

1. `check-session.php` - Check session status
2. `reconnect-session.php` - Reconnect a session
3. `test-improved-send.php` - Test message sending with improvements
4. `monitor-whatsapp-engine.php` - Monitor and auto-restart engine