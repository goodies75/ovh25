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
  const edition = fiche.editeur || fiche.numero_edition || '';
  
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
          {fiche.image_url && (
            <div className="comic-detail-image">
              <img 
                src={fiche.image_url} 
                alt={titre} 
                style={{maxWidth:'100%', maxHeight: '300px', borderRadius: '8px', boxShadow: '0 2px 8px rgba(0,0,0,0.1)'}} 
              />
            </div>
          )}
          
          {/* Informations à droite */}
          <div className="comic-detail-info">
            {edition && <div><strong>Édition :</strong> {edition}</div>}
            {fiche.numero && <div><strong>Numéro :</strong> {fiche.numero}</div>}
            {fiche.annee && <div><strong>Année :</strong> {fiche.annee}</div>}
            {fiche.auteur_couverture && <div><strong>Auteur couverture :</strong> {fiche.auteur_couverture}</div>}
            {fiche.description && <div><strong>Description :</strong> {fiche.description}</div>}
          </div>
        </div>
      </div>
      
      {/* Boutons en bas */}
      <div className="comic-detail-modal-actions">
        <Button onClick={onEdit}>Modifier</Button>
        <Button variant="danger" onClick={onDelete}>Supprimer</Button>
      </div>
    </Modal>
  );
}
