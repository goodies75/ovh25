import { useState } from 'react';
import FicheForm from '../FicheForm';
import PinProtection from '../security/PinProtection';
import './AddPage.css';

export default function AddPage() {
  const [showPinProtection, setShowPinProtection] = useState(false);
  const [isAuthorized, setIsAuthorized] = useState(false);

  // Vérifier si l'utilisateur est déjà autorisé
  const checkAuthorization = () => {
    const session = sessionStorage.getItem('admin_session');
    if (session) {
      const sessionData = JSON.parse(session);
      if (sessionData.authorized && sessionData.expires > Date.now()) {
        setIsAuthorized(true);
        return true;
      }
    }
    return false;
  };

  const handleAddClick = () => {
    if (checkAuthorization()) {
      setIsAuthorized(true);
    } else {
      setShowPinProtection(true);
    }
  };

  const handlePinSuccess = () => {
    setIsAuthorized(true);
    setShowPinProtection(false);
  };

  const handlePinClose = () => {
    setShowPinProtection(false);
  };

  // Si autorisé, afficher le formulaire directement
  if (isAuthorized) {
    return (
      <div className="add-page">
        <div className="add-header">
          <h2>Ajouter un Comic</h2>
          <p>Enrichissez votre collection en ajoutant un nouveau comic avec toutes ses informations</p>
        </div>

        <div className="add-content">
          <FicheForm />
        </div>

        <div className="add-tips">
          <div className="tips-card">
            <h3>💡 Conseils pour un ajout optimal</h3>
            <ul className="tips-list">
              <li>📖 <strong>Titre :</strong> Utilisez le nom exact de la série</li>
              <li>🔢 <strong>Numéro :</strong> Indiquez le numéro du tome ou de l'épisode</li>
              <li>📅 <strong>Année :</strong> Année de publication originale</li>
              <li>🏢 <strong>Éditeur :</strong> Nom de la maison d'édition</li>
              <li>🎨 <strong>Auteurs :</strong> Ajoutez tous les contributeurs importants</li>
              <li>🖼️ <strong>Image :</strong> URL d'une image de couverture de qualité</li>
            </ul>
          </div>
        </div>
      </div>
    );
  }

  // Sinon, afficher la page de protection
  return (
    <div className="add-page">
      <div className="add-header">
        <h2>🔒 Accès Protégé</h2>
        <p>L'ajout de comics nécessite une autorisation</p>
      </div>

      <div className="add-content">
        <div className="protection-notice">
          <div className="protection-card">
            <h3>🛡️ Zone d'Administration</h3>
            <p>Pour ajouter un nouveau comic à votre collection, vous devez vous authentifier.</p>
            <button
              className="auth-button"
              onClick={handleAddClick}
            >
              🔐 S'authentifier pour ajouter
            </button>
          </div>
        </div>
      </div>

      {/* Modal de protection PIN */}
      <PinProtection
        isOpen={showPinProtection}
        onClose={handlePinClose}
        onSuccess={handlePinSuccess}
        action="ajouter"
        comicTitle="un nouveau comic"
      />
    </div>
  );
}
