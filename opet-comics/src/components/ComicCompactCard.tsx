import { Card } from './ui';
import './ComicCompactCard.css';

interface ComicCompactCardProps {
  fiche: {
    id: number;
    titre?: string;
    nom_serie?: string;
    numero?: string;
    editeur?: string;
    annee?: string;
    image_url?: string;
  };
  onClick: () => void;
}

export default function ComicCompactCard({ fiche, onClick }: ComicCompactCardProps) {
  // Normaliser les données pour compatibilité legacy/nouveau
  const titre = fiche.nom_serie || fiche.titre || 'Sans titre';
  const numero = fiche.numero;
  const editeur = fiche.editeur;
  const annee = fiche.annee;
  const imageUrl = fiche.image_url;

  return (
    <Card className="comic-compact-card" onClick={onClick}>
      <div className="compact-info">
        <div className="compact-header">
          <h3 className="compact-title">
            {titre}
            {numero && <span className="compact-numero">#{numero}</span>}
          </h3>
        </div>

        <div className="compact-details">
          {editeur && (
            <div className="compact-publisher">
              <span className="compact-icon">🏢</span>
              <span className="compact-text">{editeur}</span>
            </div>
          )}
          {annee && (
            <div className="compact-year">
              <span className="compact-icon">📅</span>
              <span className="compact-text">{annee}</span>
            </div>
          )}
        </div>

        <div className="compact-click-hint">
          <span>Cliquer pour voir les détails →</span>
        </div>
      </div>
      <div className="compact-image-container">
        {imageUrl ? (
          <img
            src={imageUrl}
            alt={titre}
            className="compact-image"
            onError={(e) => {
              const img = e.target as HTMLImageElement;
              img.style.display = 'none';
              const container = img.parentElement;
              const placeholder = container?.querySelector('.compact-placeholder') as HTMLElement;
              if (placeholder) {
                placeholder.style.display = 'flex';
              }
            }}
          />
        ) : null}
        <div className="compact-placeholder" style={{ display: imageUrl ? 'none' : 'flex' }}>
          <span className="compact-placeholder-icon">📚</span>
        </div>
      </div>


    </Card>
  );
}
