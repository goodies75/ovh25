import { Plus, Lightbulb } from 'lucide-react';
import FicheForm from '../FicheForm';
import './AddPage.css';

export default function AddPage() {
  return (
    <div className="add-page">
      <div className="add-header">
        <h2 style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
          <Plus size={28} />
          <span>Ajouter un Comic</span>
        </h2>
        <p>Enrichissez votre collection en ajoutant un nouveau comic avec toutes ses informations</p>
      </div>
      
      <div className="add-content">
        <FicheForm />
      </div>
      
      <div className="add-tips">
        <div className="tips-card">
          <h3 style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
            <Lightbulb size={22} />
            <span>Conseils pour un ajout optimal</span>
          </h3>
          <ul className="tips-list">
            <li><strong>Titre :</strong> Utilisez le nom exact de la série</li>
            <li><strong>Numéro :</strong> Indiquez le numéro du tome ou de l'épisode</li>
            <li><strong>Année :</strong> Année de publication originale</li>
            <li><strong>Éditeur :</strong> Nom de la maison d'édition</li>
            <li><strong>Auteurs :</strong> Ajoutez tous les contributeurs importants</li>
            <li><strong>Image :</strong> URL d'une image de couverture de qualité</li>
          </ul>
        </div>
      </div>
    </div>
  );
}
