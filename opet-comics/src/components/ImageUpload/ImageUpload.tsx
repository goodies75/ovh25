import { useState, useRef } from 'react';
import { Button } from '../ui';
import './ImageUpload.css';

interface ImageUploadProps {
  currentImageUrl?: string;
  onImageChange: (imageUrl: string) => void;
  label?: string;
}

export default function ImageUpload({ currentImageUrl, onImageChange, label }: ImageUploadProps) {
  const [uploading, setUploading] = useState(false);
  const [preview, setPreview] = useState<string>(currentImageUrl || '');
  const [error, setError] = useState<string>('');
  const [useUrl, setUseUrl] = useState(false);
  const [urlInput, setUrlInput] = useState(currentImageUrl || '');

  const fileInputRef = useRef<HTMLInputElement>(null);
  const cameraInputRef = useRef<HTMLInputElement>(null);

  const handleFileSelect = async (file: File) => {
    if (!file) return;

    // Vérifier le type de fichier
    if (!file.type.startsWith('image/')) {
      setError('Veuillez sélectionner une image valide');
      return;
    }

    // Vérifier la taille (max 10 MB)
    if (file.size > 10 * 1024 * 1024) {
      setError('L\'image est trop volumineuse (max 10 MB)');
      return;
    }

    setError('');
    setUploading(true);

    try {
      // Créer un aperçu local
      const reader = new FileReader();
      reader.onload = (e) => {
        setPreview(e.target?.result as string);
      };
      reader.readAsDataURL(file);

      // Upload vers le serveur
      const formData = new FormData();
      formData.append('image', file);

      const response = await fetch('./upload-image.php', {
        method: 'POST',
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        onImageChange(result.url);
        setPreview(result.url);
        setError('');
      } else {
        throw new Error(result.error || 'Erreur lors de l\'upload');
      }
    } catch (err) {
      console.error('Erreur upload:', err);
      setError((err as Error).message || 'Erreur lors de l\'upload de l\'image');
      setPreview('');
    } finally {
      setUploading(false);
    }
  };

  const handleFileInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      handleFileSelect(file);
    }
  };

  const handleUrlSubmit = () => {
    if (urlInput.trim()) {
      onImageChange(urlInput.trim());
      setPreview(urlInput.trim());
      setError('');
    }
  };

  const handleRemoveImage = () => {
    setPreview('');
    setUrlInput('');
    onImageChange('');
    setError('');
    if (fileInputRef.current) fileInputRef.current.value = '';
    if (cameraInputRef.current) cameraInputRef.current.value = '';
  };

  return (
    <div className="image-upload">
      {label && <label className="image-upload__label">{label}</label>}

      {/* Aperçu de l'image */}
      {preview && (
        <div className="image-upload__preview">
          <img src={preview} alt="Aperçu" className="image-upload__preview-img" />
          <button
            type="button"
            onClick={handleRemoveImage}
            className="image-upload__remove"
            title="Supprimer l'image"
          >
            ✕
          </button>
        </div>
      )}

      {/* Erreur */}
      {error && <div className="image-upload__error">{error}</div>}

      {/* État d'upload */}
      {uploading && (
        <div className="image-upload__uploading">
          <div className="spinner"></div>
          <span>Upload en cours...</span>
        </div>
      )}

      {/* Boutons d'action */}
      {!preview && !uploading && (
        <div className="image-upload__actions">
          {/* Bouton pour sélectionner depuis la galerie */}
          <input
            ref={fileInputRef}
            type="file"
            accept="image/*"
            onChange={handleFileInputChange}
            className="image-upload__input"
            id="file-upload"
          />
          <label htmlFor="file-upload" className="btn btn--secondary image-upload__btn">
            📁 Galerie
          </label>

          {/* Bouton pour prendre une photo (mobile) */}
          <input
            ref={cameraInputRef}
            type="file"
            accept="image/*"
            capture="environment"
            onChange={handleFileInputChange}
            className="image-upload__input"
            id="camera-upload"
          />
          <label htmlFor="camera-upload" className="btn btn--secondary image-upload__btn">
            📸 Photo
          </label>

          {/* Toggle pour URL */}
          <button
            type="button"
            onClick={() => setUseUrl(!useUrl)}
            className="btn btn--secondary image-upload__btn"
          >
            {useUrl ? '📷 Retour' : '🔗 URL'}
          </button>
        </div>
      )}

      {/* Input URL */}
      {useUrl && !preview && (
        <div className="image-upload__url-section">
          <input
            type="text"
            placeholder="https://exemple.com/image.jpg"
            value={urlInput}
            onChange={(e) => setUrlInput(e.target.value)}
            className="form-input"
          />
          <Button
            type="button"
            onClick={handleUrlSubmit}
            disabled={!urlInput.trim()}
          >
            Valider
          </Button>
        </div>
      )}
    </div>
  );
}
