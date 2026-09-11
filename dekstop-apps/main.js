const { app, BrowserWindow, ipcMain, Menu } = require('electron');
const path = require('path');
const fs = require('fs');

let mainWindow = null;
let settingsWindow = null;

// Helper to locate config.json
function getConfigFilePath() {
    const packagedPath = path.join(process.resourcesPath, 'config.json');
    if (fs.existsSync(packagedPath)) {
        return packagedPath;
    }
    return path.join(__dirname, 'config.json');
}

// Read Configuration
function loadConfig() {
    const configPath = getConfigFilePath();
    const defaultConfig = {
        app_name: 'INDRACO DMS Desktop Client',
        version: '1.0.0',
        is_configured: false,
        server_config: {
            target_url: 'http://127.0.0.1:8000',
            connection_timeout_ms: 5000
        },
        window_settings: {
            title: 'DMS PT Indraco - Desktop Edition',
            width: 1366,
            height: 768,
            min_width: 1024,
            min_height: 600,
            maximized_on_start: true
        }
    };

    try {
        if (fs.existsSync(configPath)) {
            const rawData = fs.readFileSync(configPath, 'utf8');
            return JSON.parse(rawData);
        }
    } catch (err) {
        console.error('Error reading config.json:', err);
    }
    return defaultConfig;
}

// Save Configuration
function saveConfig(newConfig) {
    try {
        const configPath = getConfigFilePath();
        fs.writeFileSync(configPath, JSON.stringify(newConfig, null, 2), 'utf8');
        return true;
    } catch (err) {
        console.error('Error saving config.json:', err);
        return false;
    }
}

// Custom Native Menu Bar: ONLY "Setting" and "View"
function createApplicationMenu() {
    const template = [
        {
            label: 'Setting',
            submenu: [
                {
                    label: 'Ubah Target Server URL...',
                    accelerator: 'CmdOrCtrl+S',
                    click: () => {
                        openSettingsWindow();
                    }
                },
                {
                    label: 'Muat Ulang Aplikasi (Reload)',
                    accelerator: 'CmdOrCtrl+R',
                    click: (item, focusedWindow) => {
                        if (focusedWindow) focusedWindow.reload();
                    }
                },
                { type: 'separator' },
                {
                    label: 'Keluar Aplikasi (Exit)',
                    accelerator: 'Alt+F4',
                    click: () => {
                        app.quit();
                    }
                }
            ]
        },
        {
            label: 'View',
            submenu: [
                {
                    label: 'Toggle Fullscreen',
                    accelerator: 'F11',
                    click: (item, focusedWindow) => {
                        if (focusedWindow) {
                            focusedWindow.setFullScreen(!focusedWindow.isFullScreen());
                        }
                    }
                },
                {
                    label: 'Toggle Developer Tools',
                    accelerator: 'CmdOrCtrl+Shift+I',
                    click: (item, focusedWindow) => {
                        if (focusedWindow) focusedWindow.webContents.toggleDevTools();
                    }
                },
                { type: 'separator' },
                { role: 'resetZoom', label: 'Reset Zoom' },
                { role: 'zoomIn', label: 'Zoom In' },
                { role: 'zoomOut', label: 'Zoom Out' }
            ]
        }
    ];

    const menu = Menu.buildFromTemplate(template);
    Menu.setApplicationMenu(menu);
}

// Open Settings Modal Window
function openSettingsWindow() {
    if (settingsWindow) {
        settingsWindow.focus();
        return;
    }

    settingsWindow = new BrowserWindow({
        width: 520,
        height: 400,
        parent: mainWindow || null,
        modal: true,
        resizable: false,
        title: 'Pengaturan Target Server URL',
        autoHideMenuBar: true,
        webPreferences: {
            preload: path.join(__dirname, 'preload.js'),
            nodeIntegration: false,
            contextIsolation: true,
            webSecurity: true
        }
    });

    settingsWindow.setMenu(null);
    settingsWindow.loadFile(path.join(__dirname, 'settings.html'));

    settingsWindow.on('closed', () => {
        settingsWindow = null;
    });
}

function createWindow() {
    const config = loadConfig();
    const windowSettings = config.window_settings || {};
    const serverConfig = config.server_config || {};

    mainWindow = new BrowserWindow({
        width: windowSettings.width || 1366,
        height: windowSettings.height || 768,
        minWidth: windowSettings.min_width || 1024,
        minHeight: windowSettings.min_height || 600,
        title: windowSettings.title || 'DMS PT Indraco - Desktop Edition',
        autoHideMenuBar: false,
        webPreferences: {
            preload: path.join(__dirname, 'preload.js'),
            nodeIntegration: false,
            contextIsolation: true,
            webSecurity: true
        }
    });

    // Build Menu with only Setting and View
    createApplicationMenu();

    if (windowSettings.maximized_on_start) {
        mainWindow.maximize();
    }

    const targetUrl = serverConfig.target_url || 'http://127.0.0.1:8000';

    mainWindow.loadURL(targetUrl).catch((err) => {
        console.warn('Failed to load target URL:', targetUrl, err);
        mainWindow.loadFile(path.join(__dirname, 'offline.html'));
    });

    mainWindow.webContents.on('did-fail-load', (event, errorCode, errorDescription, validatedURL) => {
        if (validatedURL === targetUrl || validatedURL.startsWith(targetUrl)) {
            mainWindow.loadFile(path.join(__dirname, 'offline.html'));
        }
    });

    // Check if initial setup is required on first launch
    if (!config.is_configured) {
        setTimeout(() => {
            openSettingsWindow();
        }, 800);
    }

    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}

// IPC Handlers
ipcMain.handle('get-config', () => {
    return loadConfig();
});

ipcMain.handle('save-config', (event, newServerUrl) => {
    const config = loadConfig();
    config.server_config.target_url = newServerUrl;
    config.is_configured = true;
    const success = saveConfig(config);
    
    if (success) {
        if (settingsWindow) {
            settingsWindow.close();
        }
        if (mainWindow) {
            mainWindow.loadURL(newServerUrl).catch(() => {
                mainWindow.loadFile(path.join(__dirname, 'offline.html'));
            });
        }
    }
    return success;
});

ipcMain.handle('reload-app', () => {
    if (mainWindow) {
        const config = loadConfig();
        const targetUrl = config.server_config.target_url || 'http://127.0.0.1:8000';
        mainWindow.loadURL(targetUrl).catch(() => {
            mainWindow.loadFile(path.join(__dirname, 'offline.html'));
        });
    }
});

ipcMain.handle('open-settings-window', () => {
    openSettingsWindow();
});

ipcMain.handle('close-settings-window', () => {
    if (settingsWindow) {
        settingsWindow.close();
    }
});

app.whenReady().then(() => {
    createWindow();

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createWindow();
        }
    });
});

app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});
