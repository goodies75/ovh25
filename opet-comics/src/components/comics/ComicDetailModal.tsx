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
  isOpen: boolean;
  onClose: () => void;
  fiche: Fiche | null;
  onEdit: (fiche: Fiche) => void;
  onDelete: (id: number) => void;
}

export default function ComicDetailModal({
  isOpen,
  onClose,
  fiche,
  onEdit,
  onDelete
}: ComicDetailModalProps) {
  if (!fiche) return null;

  const handleEdit = () => {
    onEdit(fiche);
    onClose();
  };

  const handleDelete = () => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer "${fiche.nom_serie}" ?`)) {
      onDelete(fiche.id);
      onClose();
    }
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={fiche.nom_serie}
      actions={
        <div className="detail-modal-actions">
          <Button onClick={handleEdit} className="btn--primary">
            ✏️ Modifier
          </Button>
          <Button onClick={handleDelete} variant="danger">
            🗑️ Supprimer
          </Button>
          <Button onClick={onClose} variant="cancel">
            Fermer
          </Button>
        </div>
      }
    >
      <div className="comic-detail-content">
        <div className="detail-main-info">
          {fiche.image_url && (
            <div className="detail-image">
              <img src={fiche.image_url} alt={fiche.nom_serie} />
            </div>
          )}

          <div className="detail-info">
            <div className="detail-field">
              <label>Série :</label>
              <span>{fiche.nom_serie}</span>
            </div>

            <div className="detail-field">
              <label>Numéro :</label>
              <span>{fiche.numero || 'Non renseigné'}</span>
            </div>

            {fiche.titre_secondaire && (
              <div className="detail-field">
                <label>Titre secondaire :</label>
                <span>{fiche.titre_secondaire}</span>
              </div>
            )}

            <div className="detail-field">
              <label>Année de publication :</label>
              <span>{fiche.annee || 'Non renseignée'}</span>
            </div>

            <div className="detail-field">
              <label>Éditeur :</label>
              <span>{fiche.editeur || 'Non renseigné'}</span>
            </div>

            <div className="detail-field">
              <label>Numéro d'édition :</label>
              <span>{fiche.numero_edition || 'Non renseigné'}</span>
            </div>

            <div className="detail-field">
              <label>Auteur couverture :</label>
              <span>{fiche.auteur_couverture || 'Non renseigné'}</span>
            </div>

            {fiche.autres_auteurs && Array.isArray(fiche.autres_auteurs) && fiche.autres_auteurs.length > 0 && (
              <div className="detail-field">
                <label>Autres auteurs :</label>
                <span>{fiche.autres_auteurs.join(', ')}</span>
              </div>
            )}

            <div className="detail-field">
              <label>État :</label>
              <span>{fiche.etat || 'Non renseigné'}</span>
            </div>

            {fiche.isbn && (
              <div className="detail-field">
                <label>ISBN :</label>
                <span>{fiche.isbn}</span>
              </div>
            )}
          </div>
        </div>

        {fiche.description && (
          <div className="detail-description">
            <label>Description :</label>
            <p>{fiche.description}</p>
          </div>
        )}

        <div className="detail-meta">
          <small>Ajouté le : {new Date(fiche.created_at).toLocaleDateString('fr-FR')}</small>
        </div>
      </div>
    </Modal>
  );
}
