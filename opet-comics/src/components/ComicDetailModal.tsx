import Modal from './Modal/Modal';
import { Button } from './ui';
import './ComicDetailModal.css';

interface ComicDetailModalProps {
  isOpen: boolean;
  onClose: () => void;
  fiche: any;
  onEdit: () => void;
  onDelete: () => void;
}

export default function ComicDetailModal({ isOpen, onClose, fiche, onEdit, onDelete }: ComicDetailModalProps) {
  if (!fiche) return null;
  
  // Compatibilité entre ancien format (titre) et nouveau format (nom_serie)
  const titre = fiche.nom_serie || fiche.titre || 'Sans titre';
  
  return (
    <Modal isOpen={isOpen} onClose={onClose} title={titre}>
      <div className="comic-detail-modal-body">
        {/* Titre en haut */}
        <div className="comic-detail-title">
          <strong>Titre :</strong> {titre}
        </div>
        
        {/* Layout desktop: Image à gauche, infos à droite */}
        <div className="comic-detail-content">
          {/* Image à gauche */}
          <div className="comic-detail-image">
            {fiche.image_url ? (
              <img src={fiche.image_url} alt={titre} />
            ) : (
              <div className="comic-placeholder">
                <span>📚</span>
                <p>Pas d'image</p>
              </div>
            )}
          </div>
          
          {/* Infos à droite */}
          <div className="comic-detail-info">
            <div className="detail-field">
              <strong>Série :</strong> {fiche.nom_serie || 'Non renseigné'}
            </div>
            
            <div className="detail-field">
              <strong>Numéro :</strong> {fiche.numero || 'Non renseigné'}
            </div>
            
            {fiche.titre_secondaire && (
              <div className="detail-field">
                <strong>Titre secondaire :</strong> {fiche.titre_secondaire}
              </div>
            )}
            
            <div className="detail-field">
              <strong>Année :</strong> {fiche.annee || 'Non renseignée'}
            </div>
            
            <div className="detail-field">
              <strong>Éditeur :</strong> {fiche.editeur || 'Non renseigné'}
            </div>
            
            <div className="detail-field">
              <strong>Numéro d'édition :</strong> {fiche.numero_edition || 'Non renseigné'}
            </div>
            
            <div className="detail-field">
              <strong>Auteur couverture :</strong> {fiche.auteur_couverture || 'Non renseigné'}
            </div>
            
            <div className="detail-field">
              <strong>État :</strong> {fiche.etat || 'Non renseigné'}
            </div>
            
            {fiche.isbn && (
              <div className="detail-field">
                <strong>ISBN :</strong> {fiche.isbn}
              </div>
            )}
            
            {fiche.autres_auteurs && Array.isArray(fiche.autres_auteurs) && fiche.autres_auteurs.length > 0 && (
              <div className="detail-field">
                <strong>Autres auteurs :</strong> {fiche.autres_auteurs.join(', ')}
              </div>
            )}
            
            {fiche.description && (
              <div className="detail-field">
                <strong>Description :</strong> {fiche.description}
              </div>
            )}
          </div>
        </div>
        
        {/* Actions */}
        <div className="comic-detail-actions">
          <Button onClick={onEdit} className="btn--primary">
            ✏️ Modifier
          </Button>
          <Button onClick={onDelete} variant="danger">
            🗑️ Supprimer
          </Button>
          <Button onClick={onClose} variant="cancel">
            Fermer
          </Button>
        </div>
      </div>
    </Modal>
  );
}
