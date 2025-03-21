class UserMonitoring {
    constructor() {
        this.captureInterval = 5000; // 5 giây
        this.isEnabled = false;
    }

    startCapturing() {
        this.isEnabled = true;
        this.captureLoop();
    }

    async captureLoop() {
        while (this.isEnabled) {
            await this.captureScreen();
            await new Promise(resolve => setTimeout(resolve, this.captureInterval));
        }
    }

    async captureScreen() {
        try {
            const stream = await navigator.mediaDevices.getDisplayMedia({
                video: { mediaSource: "screen" }
            });
            
            const track = stream.getVideoTracks()[0];
            const imageCapture = new ImageCapture(track);
            const bitmap = await imageCapture.grabFrame();
            
            // Convert to base64
            const canvas = document.createElement('canvas');
            canvas.width = bitmap.width;
            canvas.height = bitmap.height;
            const context = canvas.getContext('2d');
            context.drawImage(bitmap, 0, 0);
            
            const base64Image = canvas.toDataURL('image/jpeg', 0.5);
            
            // Send to server
            await axios.post('/api/monitoring/screenshot', {
                screenshot: base64Image
            });

            // Cleanup
            track.stop();
            stream.getTracks().forEach(track => track.stop());
        } catch (error) {
            console.error('Screen capture failed:', error);
        }
    }

    stop() {
        this.isEnabled = false;
    }
} 