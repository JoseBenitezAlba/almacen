(() => {
    'use strict';
    const start = document.getElementById('start-code-camera');
    const stop = document.getElementById('stop-code-camera');
    const panel = document.getElementById('code-camera-panel');
    const video = document.getElementById('code-camera-video');
    const status = document.getElementById('camera-status');
    const input = document.getElementById('lookup-code');
    if (!start || !stop || !panel || !video || !status || !input) return;

    let stream = null;
    let timer = null;
    let session = 0;
    let detector = null;
    let ready = false;

    function closeCamera(message) {
        session += 1;
        clearTimeout(timer);
        if (stream) stream.getTracks().forEach(track => track.stop());
        stream = null;
        video.srcObject = null;
        panel.hidden = true;
        start.disabled = !ready;
        start.textContent = 'Probar cámara';
        if (message) status.textContent = message;
    }

    async function prepare() {
        if (!window.isSecureContext || !navigator.mediaDevices?.getUserMedia || !window.BarcodeDetector) {
            status.textContent = 'La cámara no está disponible aquí. Necesita HTTPS (o localhost), permiso y un navegador con lector compatible. Usa el código escrito.';
            return;
        }
        try {
            const supported = await window.BarcodeDetector.getSupportedFormats();
            const formats = ['ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_128', 'code_39', 'qr_code']
                .filter(format => supported.includes(format));
            if (!formats.length) throw new Error('unsupported');
            detector = new window.BarcodeDetector({ formats });
            ready = true;
            start.hidden = false;
            status.textContent = 'Puedes probar la cámara. La lectura depende de tu móvil, navegador y código; si falla, escribe el código.';
        } catch {
            status.textContent = 'Este navegador no ofrece un lector compatible. Usa el código escrito.';
        }
    }

    start.addEventListener('click', async () => {
        if (!ready || start.disabled) return;
        closeCamera();
        const current = session;
        start.disabled = true;
        panel.hidden = false;
        status.textContent = 'Esperando permiso de cámara. Puedes cerrar y escribir el código en cualquier momento.';
        try {
            const acquired = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' } }, audio: false
            });
            if (current !== session) {
                acquired.getTracks().forEach(track => track.stop());
                return;
            }
            stream = acquired;
            video.srcObject = stream;
            await video.play();
            if (current !== session) return;
            status.textContent = 'Enfoca un solo código con buena luz. Si no se lee, cierra la cámara y escríbelo.';
            async function scan() {
                if (current !== session) return;
                try {
                    const codes = await detector.detect(video);
                    if (current !== session) return;
                    if (codes.length > 1) {
                        status.textContent = 'Hay varios códigos a la vista. Enfoca solo el del producto.';
                    } else if (codes.length === 1) {
                        const code = codes[0].rawValue;
                        if (typeof code !== 'string' || !code.trim() || code.length > 50) {
                            closeCamera('El contenido no sirve como código del catálogo (máximo 50 caracteres). Escríbelo manualmente.');
                            return;
                        }
                        input.value = code;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        closeCamera('Código leído. Revísalo y pulsa Buscar producto. El stock no ha cambiado.');
                        input.focus();
                        return;
                    }
                    timer = setTimeout(scan, 300);
                } catch {
                    if (current === session) closeCamera('No se ha podido leer la imagen. Puedes volver a probar o escribir el código.');
                }
            }
            timer = setTimeout(scan, 300);
        } catch (error) {
            if (current !== session) return;
            const message = error.name === 'NotAllowedError'
                ? 'Permiso de cámara denegado. Puedes escribir el código sin dar permiso.'
                : 'No se ha podido abrir la cámara. Puede estar ocupada o no disponible. Escribe el código.';
            closeCamera(message);
        }
    });
    stop.addEventListener('click', () => closeCamera('Cámara cerrada. Puedes escribir el código.'));
    document.getElementById('code-lookup-form').addEventListener('submit', () => closeCamera());
    window.addEventListener('pagehide', () => closeCamera());
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) closeCamera('Cámara cerrada al salir de la pantalla. Puedes escribir el código.');
    });
    prepare();
})();
