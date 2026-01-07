import { Save, Construction, CheckCircle, RefreshCw } from 'lucide-react';
import Modal from '../Modal/Modal';
import { Button } from '../ui';
import './ComicEditModal.css';

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

interface ComicEditModalProps {
  fiche: Fiche | null;
  isOpen: boolean;
  onClose: () => void;
  onSave: (fiche: Fiche) => void;
}

export default function ComicEditModal({ 
  fiche, 
  isOpen, 
  onClose, 
  onSave 
}: ComicEditModalProps) {
  if (!fiche) return null;

  // Pour l'instant, on affiche juste un message
  // TODO: Intégrer le formulaire d'édition
  const handleSave = () => {
    // Ici on sauvegarderait les modifications
    onSave(fiche);
    onClose();
  };

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title={`Modifier : ${fiche.nom_serie} ${fiche.numero}`}
      actions={
        <div className="edit-modal-actions">
          <Button onClick={handleSave} className="btn-save">
            <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
              <Save size={18} />
              <span>Sauvegarder</span>
            </div>
          </Button>
          <Button variant="cancel" onClick={onClose}>
            Annuler
          </Button>
        </div>
      }
    >
      <div className="edit-modal-content">
        <div className="edit-placeholder">
          <h3 style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
            <Construction size={22} />
            <span>Fonctionnalité d'édition</span>
          </h3>
          <p>L'édition des comics sera disponible dans une prochaine version.</p>
          <p>Pour le moment, vous pouvez :</p>
          <ul>
            <li style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
              <CheckCircle size={16} />
              <span>Voir tous les détails du comic</span>
            </li>
            <li style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
              <CheckCircle size={16} />
              <span>Supprimer le comic</span>
            </li>
            <li style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
              <RefreshCw size={16} />
              <span>Ajouter de nouveaux comics</span>
            </li>
          </ul>
          <div className="current-data">
            <h4>Données actuelles :</h4>
            <p><strong>Titre :</strong> {fiche.nom_serie}</p>
            <p><strong>Numéro :</strong> {fiche.numero}</p>
            <p><strong>Éditeur :</strong> {fiche.editeur}</p>
            <p><strong>Année :</strong> {fiche.annee}</p>
          </div>
        </div>
      </div>
    </Modal>
  );
}
