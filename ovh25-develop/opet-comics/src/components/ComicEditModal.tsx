import { useState, useEffect } from 'react';
import Modal from './Modal/Modal';
import { Button, Input, Textarea, Select } from './ui';
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
              onChange={(value) => setEditedFiche({...editedFiche, nom_serie: value, titre: value})}
            />
            <Input
              label="Numéro"
              value={editedFiche?.numero || ''}
              onChange={(value) => setEditedFiche({...editedFiche, numero: value})}
            />
          </div>

          <div className="form-row">
            <Input
              label="Année"
              type="number"
              value={editedFiche?.annee || ''}
              onChange={(value) => setEditedFiche({...editedFiche, annee: value})}
            />
            <Input
              label="Numéro d'édition"
              value={editedFiche?.numero_edition || ''}
              onChange={(value) => setEditedFiche({...editedFiche, numero_edition: value})}
            />
          </div>

          <Input
            label="Éditeur"
            value={editedFiche?.editeur || ''}
            onChange={(value) => setEditedFiche({...editedFiche, editeur: value})}
          />

          <Input
            label="Titre secondaire"
            value={editedFiche?.titre_secondaire || ''}
            onChange={(value) => setEditedFiche({...editedFiche, titre_secondaire: value})}
          />
        </div>

        {/* Section auteurs */}
        <div className="form-section">
          <h3>✍️ Auteurs</h3>
          
          <Input
            label="Auteur de la couverture"
            value={editedFiche?.auteur_couverture || ''}
            onChange={(value) => setEditedFiche({...editedFiche, auteur_couverture: value})}
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
              onChange={(value) => setEditedFiche({...editedFiche, etat: value})}
              options={etats}
            />
            <Input
              label="ISBN"
              value={editedFiche?.isbn || ''}
              onChange={(value) => setEditedFiche({...editedFiche, isbn: value})}
            />
          </div>

          <Input
            label="URL de l'image"
            value={editedFiche?.image_url || ''}
            onChange={(value) => setEditedFiche({...editedFiche, image_url: value})}
          />

          <Textarea
            label="Description"
            value={editedFiche?.description || ''}
            onChange={(value) => setEditedFiche({...editedFiche, description: value})}
            rows={4}
          />
        </div>
      </div>
      
      <div className="modal-actions">
        <Button onClick={handleSave}>Sauvegarder</Button>
        <Button variant="cancel" onClick={onClose}>Annuler</Button>
      </div>
    </Modal>
  );
}
