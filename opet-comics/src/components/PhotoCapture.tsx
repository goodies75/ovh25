import React, { useState, useRef, useCallback } from 'react';
import './PhotoCapture.css';

interface PhotoCaptureProps {
  onPhotoCapture: (imageData: string, fileName: string) => void;
  onCancel: () => void;
}

const PhotoCapture: React.FC<PhotoCaptureProps> = ({ onPhotoCapture, onCancel }) => {
  const [isCamera, setIsCamera] = useState(false);
  const [stream, setStream] = useState<MediaStream | null>(null);
  const [capturedImage, setCapturedImage] = useState<string | null>(null);
  const videoRef = useRef<HTMLVideoElement>(null);
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  // ========== DÉMARRAGE DE LA CAMÉRA ==========
  const startCamera = useCallback(async () => {
    try {
      const mediaStream = await navigator.mediaDevices.getUserMedia({
        video: {
          facingMode: 'environment', // Caméra arrière par défaut
          width: { ideal: 1920 },
          height: { ideal: 1080 }
        }
      });
      
      setStream(mediaStream);
      setIsCamera(true);
      
      if (videoRef.current) {
        videoRef.current.srcObject = mediaStream;
      }
    } catch (error) {
      console.error('Erreur accès caméra:', error);
      alert('Impossible d\'accéder à la caméra. Utilisez l\'upload de fichier.');
    }
  }, []);

  // ========== ARRÊT DE LA CAMÉRA ==========
  const stopCamera = useCallback(() => {
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
      setStream(null);
    }
    setIsCamera(false);
    setCapturedImage(null);
  }, [stream]);

  // ========== CAPTURE D'UNE PHOTO ==========
  const capturePhoto = useCallback(() => {
    if (!videoRef.current || !canvasRef.current) return;

    const video = videoRef.current;
    const canvas = canvasRef.current;
    const context = canvas.getContext('2d');

    if (!context) return;

    // Définir la taille du canvas
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Dessiner l'image vidéo sur le canvas
    context.drawImage(video, 0, 0);

    // Convertir en image compressée
    const imageData = canvas.toDataURL('image/jpeg', 0.8); // Qualité 80%
    setCapturedImage(imageData);
  }, []);

  // ========== COMPRESSION D'IMAGE ==========
  const compressImage = (file: File): Promise<string> => {
    return new Promise((resolve) => {
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      const img = new Image();

      img.onload = () => {
        // Calculer les nouvelles dimensions (max 1200px)
        const maxSize = 1200;
        let { width, height } = img;

        if (width > height && width > maxSize) {
          height = (height * maxSize) / width;
          width = maxSize;
        } else if (height > maxSize) {
          width = (width * maxSize) / height;
          height = maxSize;
        }

        canvas.width = width;
        canvas.height = height;

        // Redessiner l'image redimensionnée
        ctx?.drawImage(img, 0, 0, width, height);

        // Convertir en JPEG compressé
        resolve(canvas.toDataURL('image/jpeg', 0.8));
      };

      img.src = URL.createObjectURL(file);
    });
  };

  // ========== UPLOAD DE FICHIER ==========
  const handleFileUpload = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (!file) return;

    // Vérifier le type de fichier
    if (!file.type.startsWith('image/')) {
      alert('Veuillez sélectionner une image');
      return;
    }

    // Vérifier la taille (max 10MB)
    if (file.size > 10 * 1024 * 1024) {
      alert('L\'image est trop volumineuse (max 10MB)');
      return;
    }

    try {
      const compressedImage = await compressImage(file);
      setCapturedImage(compressedImage);
    } catch (error) {
      console.error('Erreur compression:', error);
      alert('Erreur lors du traitement de l\'image');
    }
  };

  // ========== CONFIRMATION DE LA PHOTO ==========
  const confirmPhoto = () => {
    if (!capturedImage) return;

    const fileName = `comic_cover_${Date.now()}.jpg`;
    onPhotoCapture(capturedImage, fileName);
    stopCamera();
  };

  // ========== RECOMMENCER ==========
  const retakePhoto = () => {
    setCapturedImage(null);
  };

  // ========== RENDU DU COMPOSANT ==========
  return (
    <div className="photo-capture-overlay">
      <div className="photo-capture-modal">
        <div className="photo-capture-header">
          <h3>📸 Photo de Couverture</h3>
          <button onClick={onCancel} className="close-btn">✕</button>
        </div>

        <div className="photo-capture-content">
          {!isCamera && !capturedImage && (
            // ========== CHOIX DU MODE ==========
            <div className="photo-mode-selection">
              <button onClick={startCamera} className="camera-btn">
                📷 Prendre une Photo
              </button>
              
              <div className="divider">ou</div>
              
              <label htmlFor="file-upload" className="file-upload-btn">
                📁 Choisir un Fichier
              </label>
              <input
                id="file-upload"
                ref={fileInputRef}
                type="file"
                accept="image/*"
                capture="environment"
                onChange={handleFileUpload}
                style={{ display: 'none' }}
              />
            </div>
          )}

          {isCamera && !capturedImage && (
            // ========== MODE CAMÉRA ==========
            <div className="camera-view">
              <video
                ref={videoRef}
                autoPlay
                playsInline
                muted
                className="camera-preview"
              />
              <div className="camera-controls">
                <button onClick={stopCamera} className="cancel-btn">
                  Annuler
                </button>
                <button onClick={capturePhoto} className="capture-btn">
                  📸 Capturer
                </button>
              </div>
            </div>
          )}

          {capturedImage && (
            // ========== APERÇU DE LA PHOTO ==========
            <div className="photo-preview">
              <img src={capturedImage} alt="Aperçu" className="preview-image" />
              <div className="preview-controls">
                <button onClick={retakePhoto} className="retake-btn">
                  🔄 Recommencer
                </button>
                <button onClick={confirmPhoto} className="confirm-btn">
                  ✅ Utiliser cette Photo
                </button>
              </div>
            </div>
          )}
        </div>

        {/* Canvas caché pour la capture */}
        <canvas ref={canvasRef} style={{ display: 'none' }} />
      </div>
    </div>
  );
};

export default PhotoCapture;
