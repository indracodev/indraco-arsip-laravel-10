const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('desktopApi', {
    getConfig: () => ipcRenderer.invoke('get-config'),
    saveConfig: (newUrl) => ipcRenderer.invoke('save-config', newUrl),
    reloadApp: () => ipcRenderer.invoke('reload-app'),
    openSettingsWindow: () => ipcRenderer.invoke('open-settings-window'),
    closeSettingsWindow: () => ipcRenderer.invoke('close-settings-window')
});
