import { useState } from 'react';
import { Button } from '../ui';
import Modal from '../Modal/Modal';
import './ComicDetailModal.css';

interface Fiche {
  id: number;
  nom_serie: string;
  numero: string;
  annee: string;
  numero_edition: string;
  editeur: string;
  auteur_couverture: string;
  autres_auteurs: string[];
  titre_secondaire: string;
  etat: string;
  isbn: string;
  description: string;
  image_url?: string;
  created_at: string;
}

interface ComicDetailModalProps {
  fiche: Fiche | null;
  isOpen: boolean;
  onClose: () => void;
  onEdit: (fiche: Fiche) => void;
  onDelete: (fiche: Fiche) => void;
}

export default function ComicDetailModal({ 
  fiche, 
  isOpen, 
  onClose, 
  onEdit, 
  onDelete 
}: ComicDetailModalProps) {
  const [showDeleteConfirm, setShowDeleteConfirm] = useState(false);

  if (!fiche) return null;

  const handleEditClick = () => {
    onEdit(fiche);
    onClose();
  };

  const handleDeleteClick = () => {
    setShowDeleteConfirm(true);
  };

  const handleDeleteConfirm = () => {
    onDelete(fiche);
    setShowDeleteConfirm(false);
    onClose();
  };

  const handleDeleteCancel = () => {
    setShowDeleteConfirm(false);
  };

  return (
    <>
      <Modal
        isOpen={isOpen}
        onClose={onClose}
        title={`${fiche.nom_serie} ${fiche.numero ? `#${fiche.numero}` : ''}`}
        actions={
          <div className="modal-actions-group">
            <Button onClick={handleEditClick} className="btn-edit">
              ✏️ Modifier
            </Button>
            <Button variant="danger" onClick={handleDeleteClick} className="btn-delete">
              🗑️ Supprimer
            </Button>
          </div>
        }
      >
        <div className="comic-detail-content">
          <div className="comic-detail-main">
            <div className="comic-detail-info">
              {fiche.titre_secondaire && (
                <h4 className="detail-titre-secondaire">{fiche.titre_secondaire}</h4>
              )}
              
              <div className="detail-grid">
                <div className="detail-item">
                  <span className="detail-label">📚 Éditeur :</span>
                  <span className="detail-value">{fiche.editeur || 'Non renseigné'}</span>
                </div>
                
                <div className="detail-item">
                  <span className="detail-label">📅 Année :</span>
                  <span className="detail-value">{fiche.annee || 'Non renseignée'}</span>
                </div>
                
                {fiche.numero_edition && (
                  <div className="detail-item">
                    <span className="detail-label">🔖 Édition :</span>
                    <span className="detail-value">#{fiche.numero_edition}</span>
                  </div>
                )}
                
                <div className="detail-item">
                  <span className="detail-label">⭐ État :</span>
                  <span className="detail-value">{fiche.etat}</span>
                </div>
                
                {fiche.isbn && (
                  <div className="detail-item">
                    <span className="detail-label">📖 ISBN :</span>
                    <span className="detail-value">{fiche.isbn}</span>
                  </div>
                )}
                
                {fiche.auteur_couverture && (
                  <div className="detail-item">
                    <span className="detail-label">🎨 Couverture :</span>
                    <span className="detail-value">{fiche.auteur_couverture}</span>
                  </div>
                )}
                
                {fiche.autres_auteurs && fiche.autres_auteurs.length > 0 && (
                  <div className="detail-item detail-item--full">
                    <span className="detail-label">✍️ Autres auteurs :</span>
                    <span className="detail-value">{fiche.autres_auteurs.join(', ')}</span>
                  </div>
                )}
              </div>
              
              {fiche.description && (
                <div className="detail-description">
                  <span className="detail-label">📝 Description :</span>
                  <p className="detail-description-text">{fiche.description}</p>
                </div>
              )}
              
              <div className="detail-meta">
                <span className="detail-date">
                  Ajouté le {new Date(fiche.created_at).toLocaleDateString('fr-FR')}
                </span>
              </div>
            </div>
            
            <div className="comic-detail-image">
              {fiche.image_url ? (
                <img
                  src={fiche.image_url}
                  alt={`${fiche.nom_serie} ${fiche.numero}`}
                  className="detail-image"
                  onError={(e) => {
                    (e.target as HTMLImageElement).src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjMwMCIgdmlld0JveD0iMCAwIDIwMCAzMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMzAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik04MCA4MEgxMjBWMjIwSDgwVjgwWiIgZmlsbD0iI0Q1REJEQiIvPgo8cGF0aCBkPSJNOTAgOTBIMTEwVjIxMEg5MFY5MFoiIGZpbGw9IiM5Q0E0QUYiLz4KPHRleHQgeD0iMTAwIiB5PSIyNDAiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZpbGw9IiM5Q0E0QUYiIGZvbnQtc2l6ZT0iMTIiPkNvbWljPC90ZXh0Pgo8L3N2Zz4=';
                  }}
                />
              ) : (
                <div className="detail-placeholder">
                  <div className="placeholder-icon">📚</div>
                  <div className="placeholder-text">Pas d'image</div>
                </div>
              )}
            </div>
          </div>
        </div>
      </Modal>

      {/* Modal de confirmation de suppression */}
      <Modal
        isOpen={showDeleteConfirm}
        onClose={handleDeleteCancel}
        title="Confirmer la suppression"
        actions={
          <>
            <Button variant="danger" onClick={handleDeleteConfirm}>
              Oui, supprimer
            </Button>
            <Button variant="cancel" onClick={handleDeleteCancel}>
              Annuler
            </Button>
          </>
        }
      >
        <p>Êtes-vous sûr de vouloir supprimer le comic "<strong>{fiche.nom_serie} {fiche.numero}</strong>" ?</p>
        <p className="modal-warning">Cette action est irréversible.</p>
      </Modal>
    </>
  );
}
