import { useState, useEffect } from 'react';
import Modal from './Modal/Modal';
import { Button, Input, Textarea, Select } from './ui';
import PhotoCapture from './PhotoCapture';
import './ComicEditModal.css';

interface ComicEditModalProps {
  isOpen: boolean;
  onClose: () => void;
  fiche: any;
  onSave: (updatedFiche: any) => void;
}

export default function ComicEditModal({ isOpen, onClose, fiche, onSave }: ComicEditModalProps) {
  const [editedFiche, setEditedFiche] = useState<any>({});
  const [nouvelAuteur, setNouvelAuteur] = useState("");
  const [showPhotoCapture, setShowPhotoCapture] = useState(false);
  const [isUploadingImage, setIsUploadingImage] = useState(false);

  const etats = [
    { value: "Neuf", label: "Neuf" },
    { value: "Très bon", label: "Très bon" },
    { value: "Bon", label: "Bon" },
    { value: "Moyen", label: "Moyen" },
    { value: "Abîmé", label: "Abîmé" }
  ];

  // Synchroniser les données quand la modal s'ouvre avec une nouvelle fiche
  useEffect(() => {
    if (fiche && isOpen) {
      console.log('Fiche reçue pour édition:', fiche); // Debug
      setEditedFiche({
        ...fiche,
        // Normaliser les champs pour compatibilité legacy/nouveau
        nom_serie: fiche.nom_serie || fiche.titre || '',
        autres_auteurs: fiche.autres_auteurs || []
      });
    }
  }, [fiche, isOpen]);

  const ajouterAuteur = () => {
    if (nouvelAuteur.trim() && !editedFiche.autres_auteurs?.includes(nouvelAuteur.trim())) {
      setEditedFiche({
        ...editedFiche,
        autres_auteurs: [...(editedFiche.autres_auteurs || []), nouvelAuteur.trim()]
      });
      setNouvelAuteur("");
    }
  };

  const supprimerAuteur = (index: number) => {
    setEditedFiche({
      ...editedFiche,
      autres_auteurs: editedFiche.autres_auteurs?.filter((_: any, i: number) => i !== index) || []
    });
  };

  const handleImageUpload = async (imageDataUrl: string, fileName: string) => {
    setIsUploadingImage(true);
    try {
      // Convertir la data URL en blob
      const response = await fetch(imageDataUrl);
      const blob = await response.blob();

      // Créer FormData pour envoyer le fichier
      const formData = new FormData();
      formData.append('image', blob, fileName || 'photo.jpg');

      const uploadResponse = await fetch('./upload-image.php', {
        method: 'POST',
        body: formData // Pas de headers Content-Type, laissons le navigateur le définir
      });

      const result = await uploadResponse.json();
      if (result.success) {
        setEditedFiche({
          ...editedFiche,
          image_url: result.url
        });
      } else {
        alert('Erreur lors de l\'upload de l\'image: ' + (result.error || 'Erreur inconnue'));
      }
    } catch (error) {
      console.error('Erreur upload:', error);
      alert('Erreur lors de l\'upload de l\'image: ' + (error instanceof Error ? error.message : 'Erreur inconnue'));
    } finally {
      setIsUploadingImage(false);
      setShowPhotoCapture(false);
    }
  };

  const openPhotoCapture = () => {
    setShowPhotoCapture(true);
  };

  const closePhotoCapture = () => {
    setShowPhotoCapture(false);
  };

  const removeImage = () => {
    setEditedFiche({
      ...editedFiche,
      image_url: ""
    });
  };

  const handleSave = () => {
    console.log('Sauvegarde des données:', editedFiche); // Debug
    onSave(editedFiche);
    onClose();
  };

  if (!fiche) return null;

  return (
    <Modal isOpen={isOpen} onClose={onClose} title="Modifier le Comic" variant="light">
      <div className="edit-form">

        {/* Section principale */}
        <div className="form-section">
          <h3>📖 Informations principales</h3>

          <div className="form-row">
            <Input
              label="Nom de série"
              value={editedFiche?.nom_serie || editedFiche?.titre || ''}
              onChange={(value) => setEditedFiche({ ...editedFiche, nom_serie: value, titre: value })}
            />
            <Input
              label="Numéro"
              value={editedFiche?.numero || ''}
              onChange={(value) => setEditedFiche({ ...editedFiche, numero: value })}
            />
          </div>

          <div className="form-row">
            <Input
              label="Année"
              type="number"
              value={editedFiche?.annee || ''}
              onChange={(value) => setEditedFiche({ ...editedFiche, annee: value })}
            />
            <Input
              label="Numéro d'édition"
              value={editedFiche?.numero_edition || ''}
              onChange={(value) => setEditedFiche({ ...editedFiche, numero_edition: value })}
            />
          </div>

          <Input
            label="Éditeur"
            value={editedFiche?.editeur || ''}
            onChange={(value) => setEditedFiche({ ...editedFiche, editeur: value })}
          />

          <Input
            label="Titre secondaire"
            value={editedFiche?.titre_secondaire || ''}
            onChange={(value) => setEditedFiche({ ...editedFiche, titre_secondaire: value })}
          />
        </div>

        {/* Section auteurs */}
        <div className="form-section">
          <h3>✍️ Auteurs</h3>

          <Input
            label="Auteur de la couverture"
            value={editedFiche?.auteur_couverture || ''}
            onChange={(value) => setEditedFiche({ ...editedFiche, auteur_couverture: value })}
          />

          <div className="auteurs-input-group">
            <label className="input-label">Autres auteurs</label>
            <div className="auteurs-input">
              <Input
                label="Nouvel auteur"
                value={nouvelAuteur}
                onChange={setNouvelAuteur}
                placeholder="Nom de l'auteur"
              />
              <Button
                type="button"
                onClick={ajouterAuteur}
                className="btn-add-author"
              >
                ➕
              </Button>
            </div>

            {editedFiche.autres_auteurs?.length > 0 && (
              <div className="auteurs-list">
                {editedFiche.autres_auteurs.map((auteur: string, index: number) => (
                  <span key={index} className="auteur-tag">
                    {auteur}
                    <button
                      type="button"
                      onClick={() => supprimerAuteur(index)}
                      className="btn-remove-author"
                    >
                      ❌
                    </button>
                  </span>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* Section détails */}
        <div className="form-section">
          <h3>📋 Détails</h3>

          <div className="form-row">
            <Select
              label="État"
              value={editedFiche?.etat || 'Très bon'}
              onChange={(value) => setEditedFiche({ ...editedFiche, etat: value })}
              options={etats}
            />
            <Input
              label="ISBN"
              value={editedFiche?.isbn || ''}
              onChange={(value) => setEditedFiche({ ...editedFiche, isbn: value })}
            />
          </div>

          {/* Section Image */}
          <div className="image-section">
            <label className="form-label">Image de couverture</label>

            {editedFiche?.image_url && (
              <div className="image-preview-container">
                <img
                  src={editedFiche.image_url}
                  alt="Aperçu"
                  className="image-preview"
                />
                <button
                  type="button"
                  onClick={removeImage}
                  className="remove-image-btn"
                  title="Supprimer l'image"
                >
                  ×
                </button>
              </div>
            )}

            <div className="image-controls">
              <Input
                label="URL de l'image"
                value={editedFiche?.image_url || ''}
                onChange={(value) => setEditedFiche({ ...editedFiche, image_url: value })}
                placeholder="https://exemple.com/image.jpg"
              />

              <div className="upload-buttons">
                <Button
                  onClick={openPhotoCapture}
                  disabled={isUploadingImage}
                  variant="primary"
                >
                  {isUploadingImage ? 'Upload...' : '📷 Télécharger une image'}
                </Button>
              </div>
            </div>
          </div>

          <Textarea
            label="Description"
            value={editedFiche?.description || ''}
            onChange={(value) => setEditedFiche({ ...editedFiche, description: value })}
            rows={4}
          />
        </div>
      </div>

      <div className="modal-actions">
        <Button onClick={handleSave}>Sauvegarder</Button>
        <Button variant="cancel" onClick={onClose}>Annuler</Button>
      </div>

      {/* PhotoCapture pour upload d'image */}
      {showPhotoCapture && (
        <PhotoCapture
          onPhotoCapture={handleImageUpload}
          onCancel={closePhotoCapture}
        />
      )}
    </Modal>
  );
}
